<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 incremental file source plugin.
 *
 * Supports timestamp-based filtering for incremental file migration.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_incremental_file",
 *   source_module = "system"
 * )
 */
class D6IncrementalFile extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('files', 'f')
      ->fields('f', [
        'fid', 'uid', 'filename', 'filepath', 'filemime', 'filesize',
        'status', 'timestamp'
      ]);

    // Apply incremental filtering
    $this->applyIncrementalFilters($query);

    return $query;
  }

  /**
   * Apply incremental filtering to the query.
   */
  protected function applyIncrementalFilters($query) {
    $config = $this->configuration;

    // Filter by timestamp (when file was added/modified)
    if (!empty($config['since_timestamp'])) {
      $since_timestamp = $this->parseDateFilter($config['since_timestamp']);
      if ($since_timestamp) {
        $query->condition('f.timestamp', $since_timestamp, '>=');
      }
    }

    // Filter by date range
    if (!empty($config['date_range'])) {
      $range = $config['date_range'];
      if (!empty($range['start'])) {
        $start_timestamp = $this->parseDateFilter($range['start']);
        if ($start_timestamp) {
          $query->condition('f.timestamp', $start_timestamp, '>=');
        }
      }
      if (!empty($range['end'])) {
        $end_timestamp = $this->parseDateFilter($range['end']);
        if ($end_timestamp) {
          $query->condition('f.timestamp', $end_timestamp, '<=');
        }
      }
    }

    // Filter by specific file IDs
    if (!empty($config['file_ids'])) {
      $fids = is_array($config['file_ids']) ? $config['file_ids'] : [$config['file_ids']];
      $query->condition('f.fid', $fids, 'IN');
    }

    // Filter by file status
    if (isset($config['status'])) {
      $query->condition('f.status', $config['status']);
    }

    // Filter by MIME type
    if (!empty($config['mime_types'])) {
      $mime_types = is_array($config['mime_types']) ? $config['mime_types'] : [$config['mime_types']];
      $query->condition('f.filemime', $mime_types, 'IN');
    }

    // Filter by file extension
    if (!empty($config['extensions'])) {
      $extensions = is_array($config['extensions']) ? $config['extensions'] : [$config['extensions']];
      $conditions = $query->orConditionGroup();
      
      foreach ($extensions as $ext) {
        $conditions->condition('f.filename', '%.' . $ext, 'LIKE');
      }
      
      $query->condition($conditions);
    }

    // Filter files used in specific content types
    if (!empty($config['used_in_content_types'])) {
      $content_types = is_array($config['used_in_content_types']) ? $config['used_in_content_types'] : [$config['used_in_content_types']];
      
      // Find files used in upload module
      $subquery = $this->select('upload', 'u')
        ->fields('u', ['fid']);
      
      $subquery->leftJoin('node', 'n', 'u.nid = n.nid');
      $subquery->condition('n.type', $content_types, 'IN');
      
      $query->condition('f.fid', $subquery, 'IN');
    }

    // Filter orphaned files (not used anywhere)
    if (!empty($config['orphaned_only'])) {
      $this->addOrphanedFilesCondition($query);
    }

    // Filter files by user
    if (!empty($config['user_ids'])) {
      $uids = is_array($config['user_ids']) ? $config['user_ids'] : [$config['user_ids']];
      $query->condition('f.uid', $uids, 'IN');
    }
  }

  /**
   * Add condition to find orphaned files.
   */
  protected function addOrphanedFilesCondition($query) {
    // Files not used in upload module
    $upload_subquery = $this->select('upload', 'u')
      ->fields('u', ['fid']);
    
    // Files not used as user pictures
    $picture_subquery = $this->select('users', 'users')
      ->fields('users', ['picture'])
      ->condition('users.picture', 0, '>');

    // Files not used in CCK fields (this is complex, simplified version)
    // In real implementation, you'd check all content_type_* tables for file fields

    $query->condition('f.fid', $upload_subquery, 'NOT IN');
    $query->condition('f.fid', $picture_subquery, 'NOT IN');
  }

  /**
   * Parse date filter string into timestamp.
   */
  protected function parseDateFilter($date_filter) {
    if (empty($date_filter)) {
      return NULL;
    }

    // Handle Unix timestamps
    if (is_numeric($date_filter)) {
      return (int) $date_filter;
    }

    // Handle relative date shortcuts
    $shortcuts = [
      'yesterday' => '-1 day',
      'last-week' => '-1 week',
      'last-month' => '-1 month',
      'last-year' => '-1 year',
      'today' => '0 days',
    ];

    if (isset($shortcuts[$date_filter])) {
      $date_filter = $shortcuts[$date_filter];
    }

    // Try to parse as strtotime
    $timestamp = strtotime($date_filter);
    if ($timestamp === FALSE) {
      \Drupal::logger('thirdwing_migrate')->warning('Invalid date filter: @filter', ['@filter' => $date_filter]);
      return NULL;
    }

    return $timestamp;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('File ID'),
      'uid' => $this->t('User ID'),
      'filename' => $this->t('Filename'),
      'filepath' => $this->t('File path'),
      'filemime' => $this->t('File MIME type'),
      'filesize' => $this->t('File size'),
      'status' => $this->t('Status'),
      'timestamp' => $this->t('Timestamp'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $fid = $row->getSourceProperty('fid');

    // Add file usage information
    $this->addFileUsage($row, $fid);

    // Add file metadata
    $this->addFileMetadata($row);

    // Determine file category based on usage and type
    $this->categorizeFile($row);

    return parent::prepareRow($row);
  }

  /**
   * Add file usage information.
   */
  protected function addFileUsage(Row $row, $fid) {
    $usage = [];

    // Check upload module usage
    if ($this->getDatabase()->schema()->tableExists('upload')) {
      $upload_query = $this->select('upload', 'u')
        ->fields('u', ['nid', 'description', 'list', 'weight'])
        ->condition('u.fid', $fid);
      
      $upload_query->leftJoin('node', 'n', 'u.nid = n.nid');
      $upload_query->addField('n', 'type', 'node_type');
      $upload_query->addField('n', 'title', 'node_title');
      
      $upload_usage = $upload_query->execute()->fetchAll();
      
      if (!empty($upload_usage)) {
        $usage['upload'] = $upload_usage;
      }
    }

    // Check user picture usage
    $picture_query = $this->select('users', 'u')
      ->fields('u', ['uid', 'name'])
      ->condition('u.picture', $fid);
    
    $picture_usage = $picture_query->execute()->fetchAll();
    
    if (!empty($picture_usage)) {
      $usage['user_picture'] = $picture_usage;
    }

    // Check CCK field usage (simplified - would need to check all content tables)
    $this->addCckFileUsage($usage, $fid);

    $row->setSourceProperty('file_usage', $usage);
  }

  /**
   * Add CCK field file usage.
   */
  protected function addCckFileUsage(array &$usage, $fid) {
    // Get all content type tables
    $content_tables = $this->getDatabase()->select('content_node_field_instance', 'cnfi')
      ->fields('cnfi', ['type_name', 'field_name'])
      ->condition('cnfi.widget_type', ['filefield_widget', 'imagefield_widget'], 'IN')
      ->execute()
      ->fetchAll();

    foreach ($content_tables as $field_info) {
      $table_name = 'content_type_' . $field_info->type_name;
      $field_name = $field_info->field_name;
      
      if ($this->getDatabase()->schema()->tableExists($table_name)) {
        $field_query = $this->select($table_name, 'ct')
          ->condition("ct.{$field_name}_fid", $fid);
        
        $field_query->leftJoin('node', 'n', 'ct.nid = n.nid');
        $field_query->addField('n', 'nid');
        $field_query->addField('n', 'title');
        $field_query->addField('n', 'type');
        
        $field_usage = $field_query->execute()->fetchAll();
        
        if (!empty($field_usage)) {
          $usage['cck'][$field_name] = $field_usage;
        }
      }
    }
  }

  /**
   * Add file metadata.
   */
  protected function addFileMetadata(Row $row) {
    $filepath = $row->getSourceProperty('filepath');
    $filename = $row->getSourceProperty('filename');
    $filemime = $row->getSourceProperty('filemime');

    // Extract file extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $row->setSourceProperty('file_extension', $extension);

    // Determine file category by MIME type
    $category = 'generic';
    if (strpos($filemime, 'image/') === 0) {
      $category = 'image';
    } elseif (strpos($filemime, 'audio/') === 0) {
      $category = 'audio';
    } elseif (strpos($filemime, 'video/') === 0) {
      $category = 'video';
    } elseif (in_array($filemime, ['application/pdf', 'application/msword', 'application/vnd.ms-excel'])) {
      $category = 'document';
    }

    $row->setSourceProperty('file_category', $category);

    // Check if file exists
    $full_path = DRUPAL_ROOT . '/' . $filepath;
    $row->setSourceProperty('file_exists', file_exists($full_path));
  }

  /**
   * Categorize file based on usage and metadata.
   */
  protected function categorizeFile(Row $row) {
    $usage = $row->getSourceProperty('file_usage');
    $category = $row->getSourceProperty('file_category');
    $extension = $row->getSourceProperty('file_extension');

    // Determine destination media bundle
    $media_bundle = 'generic';

    if (!empty($usage['user_picture'])) {
      $media_bundle = 'image';
    } elseif ($category === 'image') {
      $media_bundle = 'image';
    } elseif ($category === 'audio') {
      $media_bundle = 'audio';
    } elseif ($category === 'video') {
      $media_bundle = 'video';
    } elseif ($category === 'document' || in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx'])) {
      $media_bundle = 'document';
    }

    $row->setSourceProperty('destination_media_bundle', $media_bundle);
  }

}