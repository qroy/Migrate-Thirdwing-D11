<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 incremental node source plugin - CORRECTED VERSION.
 *
 * Supports timestamp-based filtering for incremental migration.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_incremental_node",
 *   source_module = "node"
 * )
 */
class D6IncrementalNode extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid', 'vid', 'type', 'language', 'title', 'uid', 'status',
        'created', 'changed', 'comment', 'promote', 'moderate', 'sticky'
      ]);

    // Join with node_revisions for body content
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'teaser');
    $query->addField('nr', 'format');

    // Apply incremental filtering based on configuration
    $this->applyIncrementalFilters($query);

    // Apply content type filter if specified
    if (!empty($this->configuration['node_type'])) {
      $query->condition('n.type', $this->configuration['node_type']);
    }

    // Apply content types filter (multiple types)
    if (!empty($this->configuration['node_types'])) {
      $query->condition('n.type', $this->configuration['node_types'], 'IN');
    }

    return $query->orderBy('n.nid');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Revision ID'),
      'type' => $this->t('Content type'),
      'language' => $this->t('Language'),
      'title' => $this->t('Title'),
      'uid' => $this->t('User ID'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'comment' => $this->t('Comment setting'),
      'promote' => $this->t('Promote to front page'),
      'moderate' => $this->t('Moderate'),
      'sticky' => $this->t('Sticky'),
      'body' => $this->t('Body'),
      'teaser' => $this->t('Teaser'),
      'format' => $this->t('Input format'),
      'taxonomy_terms' => $this->t('Taxonomy terms'),
      'upload_files' => $this->t('Upload module files'),
      'images' => $this->t('Image files for migration'),
      'files' => $this->t('General files for migration'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');
    $type = $row->getSourceProperty('type');

    // Add CCK fields for this content type
    $this->addCckFields($row, $nid, $vid, $type);
    
    // Add taxonomy terms
    $this->addTaxonomyTerms($row, $nid);
    
    // Add file attachments
    $this->addFileAttachments($row, $nid, $vid);

    return TRUE;
  }

  /**
   * Apply incremental filtering to the query.
   */
  protected function applyIncrementalFilters($query) {
    $config = $this->configuration;

    // Filter by changed date (most common incremental case)
    if (!empty($config['since_changed'])) {
      $since_timestamp = $this->parseDateFilter($config['since_changed']);
      if ($since_timestamp) {
        $query->condition('n.changed', $since_timestamp, '>=');
      }
    }

    // Filter by created date (for new content only)
    if (!empty($config['since_created'])) {
      $since_timestamp = $this->parseDateFilter($config['since_created']);
      if ($since_timestamp) {
        $query->condition('n.created', $since_timestamp, '>=');
      }
    }

    // Filter by date range
    if (!empty($config['date_range'])) {
      $range = $config['date_range'];
      if (!empty($range['start'])) {
        $start_timestamp = $this->parseDateFilter($range['start']);
        if ($start_timestamp) {
          $query->condition('n.changed', $start_timestamp, '>=');
        }
      }
      if (!empty($range['end'])) {
        $end_timestamp = $this->parseDateFilter($range['end']);
        if ($end_timestamp) {
          $query->condition('n.changed', $end_timestamp, '<=');
        }
      }
    }

    // Filter by specific node IDs (for targeted sync)
    if (!empty($config['node_ids'])) {
      $nids = is_array($config['node_ids']) ? $config['node_ids'] : [$config['node_ids']];
      $query->condition('n.nid', $nids, 'IN');
    }
  }

  /**
   * Parse date filter into timestamp.
   */
  protected function parseDateFilter($date_input) {
    if (is_numeric($date_input)) {
      return (int) $date_input;
    }
    
    try {
      return strtotime($date_input);
    } catch (\Exception $e) {
      \Drupal::logger('thirdwing_migrate')->warning('Invalid date filter: @date', [
        '@date' => $date_input
      ]);
      return FALSE;
    }
  }

  /**
   * Add CCK fields to the row.
   * CORRECTED: Improved error handling and table detection.
   */
  protected function addCckFields(Row $row, $nid, $vid, $type) {
    // First try to get fields from content_type_X table
    $content_type_table = 'content_type_' . $type;
    
    if ($this->getDatabase()->schema()->tableExists($content_type_table)) {
      try {
        $content_query = $this->select($content_type_table, 'ct')
          ->condition('ct.nid', $nid)
          ->condition('ct.vid', $vid);
        
        $content_query->fields('ct');
        $content_data = $content_query->execute()->fetchAssoc();
        
        if ($content_data) {
          foreach ($content_data as $field_name => $value) {
            // Skip primary keys
            if (!in_array($field_name, ['nid', 'vid'])) {
              $row->setSourceProperty($field_name, $value);
            }
          }
        }
      } catch (\Exception $e) {
        \Drupal::logger('thirdwing_migrate')->warning('Error loading content type fields for @type node @nid: @error', [
          '@type' => $type,
          '@nid' => $nid,
          '@error' => $e->getMessage()
        ]);
      }
    }

    // Then get fields from individual content_field_X tables
    $this->addIndividualCckFields($row, $nid, $vid, $type);
  }

  /**
   * Add individual CCK fields from content_field_X tables.
   * CORRECTED: Uses actual field discovery instead of assumptions.
   */
  protected function addIndividualCckFields(Row $row, $nid, $vid, $type) {
    try {
      // Get field definitions for this content type from content_node_field_instance
      $field_query = $this->select('content_node_field_instance', 'cnfi')
        ->fields('cnfi', ['field_name', 'widget_type'])
        ->condition('cnfi.type_name', $type);
      
      $fields = $field_query->execute()->fetchAllKeyed();

      foreach ($fields as $field_name => $widget_type) {
        $field_table = 'content_field_' . str_replace('field_', '', $field_name);
        
        // Check if field table exists
        if ($this->getDatabase()->schema()->tableExists($field_table)) {
          try {
            $field_query = $this->select($field_table, 'cf')
              ->condition('cf.nid', $nid)
              ->condition('cf.vid', $vid);
            
            $field_query->fields('cf');
            $field_data = $field_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
            
            if (!empty($field_data)) {
              // Handle multiple values (delta-based fields)
              if (count($field_data) > 1) {
                $row->setSourceProperty($field_name . '_data', $field_data);
              } else {
                // Single value - set individual field columns
                foreach ($field_data[0] as $column => $value) {
                  if (!in_array($column, ['nid', 'vid', 'delta'])) {
                    $row->setSourceProperty($column, $value);
                  }
                }
              }
            }
          } catch (\Exception $e) {
            \Drupal::logger('thirdwing_migrate')->debug('Error loading field @field for node @nid: @error', [
              '@field' => $field_name,
              '@nid' => $nid,
              '@error' => $e->getMessage()
            ]);
          }
        }
      }
    } catch (\Exception $e) {
      \Drupal::logger('thirdwing_migrate')->warning('Error discovering CCK fields for @type: @error', [
        '@type' => $type,
        '@error' => $e->getMessage()
      ]);
    }
  }

  /**
   * Add taxonomy terms to the row.
   */
  protected function addTaxonomyTerms(Row $row, $nid) {
    try {
      $term_query = $this->select('term_node', 'tn')
        ->fields('tn', ['tid'])
        ->condition('tn.nid', $nid);
      
      $tids = $term_query->execute()->fetchCol();
      
      if (!empty($tids)) {
        // Get term details
        $term_details_query = $this->select('term_data', 'td')
          ->fields('td', ['tid', 'name', 'vid'])
          ->condition('td.tid', $tids, 'IN');
        
        $terms = $term_details_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $row->setSourceProperty('taxonomy_terms', $terms);
      }
    } catch (\Exception $e) {
      \Drupal::logger('thirdwing_migrate')->warning('Error loading taxonomy terms for node @nid: @error', [
        '@nid' => $nid,
        '@error' => $e->getMessage()
      ]);
    }
  }

  /**
   * Add file attachments to the row.
   * CORRECTED: Handles both upload module files and CCK file fields.
   */
  protected function addFileAttachments(Row $row, $nid, $vid) {
    $all_files = [];
    $all_images = [];

    // Check for upload module files
    if ($this->getDatabase()->schema()->tableExists('upload')) {
      try {
        $file_query = $this->select('upload', 'u')
          ->fields('u', ['fid', 'description', 'list', 'weight'])
          ->condition('u.nid', $nid);
        
        $file_query->leftJoin('files', 'f', 'u.fid = f.fid');
        $file_query->addField('f', 'filename');
        $file_query->addField('f', 'filepath');
        $file_query->addField('f', 'filemime');
        $file_query->addField('f', 'filesize');
        
        $upload_files = $file_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
        
        if (!empty($upload_files)) {
          $row->setSourceProperty('upload_files', $upload_files);
          $all_files = array_merge($all_files, $upload_files);
        }
      } catch (\Exception $e) {
        \Drupal::logger('thirdwing_migrate')->warning('Error loading upload files for node @nid: @error', [
          '@nid' => $nid,
          '@error' => $e->getMessage()
        ]);
      }
    }

    // Check for CCK file fields
    $this->addCckFileFields($row, $nid, $vid, $all_files, $all_images);
    
    // Set processed file arrays
    $row->setSourceProperty('files', $all_files);
    $row->setSourceProperty('images', $all_images);
  }

  /**
   * Add CCK file fields (field_files, field_afbeeldingen, etc.).
   */
  protected function addCckFileFields(Row $row, $nid, $vid, &$all_files, &$all_images) {
    $file_fields = [
      'content_field_files' => 'files',
      'content_field_afbeeldingen' => 'images',
    ];

    foreach ($file_fields as $table => $type) {
      if ($this->getDatabase()->schema()->tableExists($table)) {
        try {
          $field_query = $this->select($table, 'cff')
            ->condition('cff.nid', $nid)
            ->condition('cff.vid', $vid);
          
          $field_query->fields('cff');
          
          // Join with files table for file details
          $fid_column = str_replace('content_field_', 'field_', $table) . '_fid';
          $field_query->leftJoin('files', 'f', "cff.{$fid_column} = f.fid");
          $field_query->addField('f', 'filename');
          $field_query->addField('f', 'filepath');
          $field_query->addField('f', 'filemime');
          $field_query->addField('f', 'filesize');
          
          $field_files = $field_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
          
          foreach ($field_files as $file_data) {
            if (!empty($file_data[$fid_column])) {
              $file_info = [
                'fid' => $file_data[$fid_column],
                'filename' => $file_data['filename'],
                'filepath' => $file_data['filepath'],
                'filemime' => $file_data['filemime'],
                'filesize' => $file_data['filesize'],
                'description' => $this->parseFileDescription($file_data),
                'source_field' => $table,
              ];
              
              // Categorize as image or general file
              if ($type === 'images' || $this->isImageMimeType($file_data['filemime'])) {
                $all_images[] = $file_info;
              } else {
                $all_files[] = $file_info;
              }
            }
          }
        } catch (\Exception $e) {
          \Drupal::logger('thirdwing_migrate')->debug('Error loading @table for node @nid: @error', [
            '@table' => $table,
            '@nid' => $nid,
            '@error' => $e->getMessage()
          ]);
        }
      }
    }
  }

  /**
   * Parse file description from CCK field data.
   */
  protected function parseFileDescription($file_data) {
    $data_column = str_replace('_fid', '_data', array_keys($file_data)[0]);
    
    if (!empty($file_data[$data_column])) {
      $parsed = unserialize($file_data[$data_column]);
      if (is_array($parsed) && isset($parsed['description'])) {
        return trim($parsed['description']);
      }
    }
    
    return '';
  }

  /**
   * Check if MIME type is an image.
   */
  protected function isImageMimeType($mime_type) {
    return strpos($mime_type, 'image/') === 0;
  }
}