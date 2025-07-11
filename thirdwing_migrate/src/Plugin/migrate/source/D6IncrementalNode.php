<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 incremental node source plugin.
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

    return $query;
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

    // Exclude specific node IDs if needed
    if (!empty($config['exclude_node_ids'])) {
      $exclude_nids = is_array($config['exclude_node_ids']) ? $config['exclude_node_ids'] : [$config['exclude_node_ids']];
      $query->condition('n.nid', $exclude_nids, 'NOT IN');
    }
  }

  /**
   * Parse date filter string into timestamp.
   * 
   * Supports formats like:
   * - "2025-01-01" (ISO date)
   * - "yesterday", "last-week", "last-month"
   * - Unix timestamps
   * - Relative formats like "-7 days", "-1 month"
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
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Revision ID'),
      'type' => $this->t('Type'),
      'language' => $this->t('Language code'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Authored by (uid)'),
      'status' => $this->t('Published'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'comment' => $this->t('Comment setting'),
      'promote' => $this->t('Promoted to front page'),
      'moderate' => $this->t('In moderation queue'),
      'sticky' => $this->t('Sticky at top of lists'),
      'body' => $this->t('Body'),
      'teaser' => $this->t('Teaser'),
      'format' => $this->t('Format'),
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
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');
    $type = $row->getSourceProperty('type');

    // Add CCK fields for this content type
    $this->addCckFields($row, $nid, $vid, $type);

    // Add taxonomy terms
    $this->addTaxonomyTerms($row, $nid);

    // Add file attachments
    $this->addFileAttachments($row, $nid);

    return parent::prepareRow($row);
  }

  /**
   * Add CCK fields to the row.
   */
  protected function addCckFields(Row $row, $nid, $vid, $type) {
    // Get field definitions for this content type
    $field_query = $this->select('content_node_field_instance', 'cnfi')
      ->fields('cnfi', ['field_name', 'widget_type'])
      ->condition('cnfi.type_name', $type);
    
    $fields = $field_query->execute()->fetchAllKeyed();

    foreach ($fields as $field_name => $widget_type) {
      $table_name = 'content_' . $type;
      
      // Check if table exists
      if ($this->getDatabase()->schema()->tableExists($table_name)) {
        $field_query = $this->select($table_name, 'ct')
          ->condition('ct.nid', $nid)
          ->condition('ct.vid', $vid);
        
        // Add all columns that start with this field name
        $columns = $this->getDatabase()->schema()->getFieldNames($table_name);
        $field_columns = array_filter($columns, function($col) use ($field_name) {
          return strpos($col, $field_name . '_') === 0;
        });
        
        if (!empty($field_columns)) {
          $field_query->fields('ct', $field_columns);
          $field_data = $field_query->execute()->fetchAssoc();
          
          if ($field_data) {
            foreach ($field_data as $column => $value) {
              $row->setSourceProperty($column, $value);
            }
          }
        }
      }
    }
  }

  /**
   * Add taxonomy terms to the row.
   */
  protected function addTaxonomyTerms(Row $row, $nid) {
    $term_query = $this->select('term_node', 'tn')
      ->fields('tn', ['tid'])
      ->condition('tn.nid', $nid);
    
    $tids = $term_query->execute()->fetchCol();
    
    if (!empty($tids)) {
      // Get term details
      $term_details_query = $this->select('term_data', 'td')
        ->fields('td', ['tid', 'name', 'vid'])
        ->condition('td.tid', $tids, 'IN');
      
      $terms = $term_details_query->execute()->fetchAll();
      $row->setSourceProperty('taxonomy_terms', $terms);
    }
  }

  /**
   * Add file attachments to the row.
   */
  protected function addFileAttachments(Row $row, $nid) {
    // Check for upload module files
    if ($this->getDatabase()->schema()->tableExists('upload')) {
      $file_query = $this->select('upload', 'u')
        ->fields('u', ['fid', 'description', 'list', 'weight'])
        ->condition('u.nid', $nid);
      
      $file_query->leftJoin('files', 'f', 'u.fid = f.fid');
      $file_query->addField('f', 'filename');
      $file_query->addField('f', 'filepath');
      $file_query->addField('f', 'filemime');
      $file_query->addField('f', 'filesize');
      
      $files = $file_query->execute()->fetchAll();
      
      if (!empty($files)) {
        $row->setSourceProperty('upload_files', $files);
      }
    }
  }

}