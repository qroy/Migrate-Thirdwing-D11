<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 Thirdwing news source plugin.
 *
 * Handles migration of news content type with all CCK fields and relationships.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_news",
 *   source_module = "node"
 * )
 */
class D6ThirdwingNews extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid', 'vid', 'type', 'language', 'title', 'uid', 'status',
        'created', 'changed', 'comment', 'promote', 'moderate', 'sticky'
      ])
      ->condition('n.type', 'nieuws');

    // Join with node_revisions for body content
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'teaser');
    $query->addField('nr', 'format');

    // Join with workflow if present
    $query->leftJoin('workflow_node', 'wn', 'n.nid = wn.nid');
    $query->addField('wn', 'sid', 'workflow_stateid');

    // News-specific fields
    $query->leftJoin('content_type_nieuws', 'ctn', 'n.vid = ctn.vid');
    $query->addField('ctn', 'field_datum_value');

    // Order by node ID for consistent processing
    $query->orderBy('n.nid');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'type' => $this->t('Content type'),
      'language' => $this->t('Language'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Author ID'),
      'status' => $this->t('Published status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'comment' => $this->t('Comment status'),
      'promote' => $this->t('Promoted to front page'),
      'moderate' => $this->t('Moderation status'),
      'sticky' => $this->t('Sticky status'),
      'body' => $this->t('Body content'),
      'teaser' => $this->t('Teaser'),
      'format' => $this->t('Text format'),
      'workflow_stateid' => $this->t('Workflow state ID'),
      'field_datum_value' => $this->t('News date'),
      'images' => $this->t('Attached images'),
      'files' => $this->t('Attached files'),
      'taxonomy_terms' => $this->t('Taxonomy terms'),
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

    // Get attached images
    $images = $this->getAttachedFiles($nid, $vid, 'field_afbeeldingen');
    $row->setSourceProperty('images', $images);

    // Get attached files
    $files = $this->getAttachedFiles($nid, $vid, 'field_files');
    $row->setSourceProperty('files', $files);

    // Get taxonomy terms
    $taxonomy_terms = $this->getTaxonomyTerms($nid, $vid);
    $row->setSourceProperty('taxonomy_terms', $taxonomy_terms);

    // Clean up data
    $this->cleanupRowData($row);

    return parent::prepareRow($row);
  }

  /**
   * Get attached files for a specific field.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   * @param string $field_name
   *   Field name.
   *
   * @return array
   *   Array of file data.
   */
  protected function getAttachedFiles($nid, $vid, $field_name) {
    $table_name = 'content_' . $field_name;
    
    try {
      $query = $this->select($table_name, 'cf')
        ->fields('cf', [
          'nid', 'vid', 'delta',
          $field_name . '_fid',
          $field_name . '_list',
          $field_name . '_data'
        ])
        ->condition('cf.nid', $nid)
        ->condition('cf.vid', $vid)
        ->orderBy('cf.delta');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      // Table might not exist or field might not have files
      return [];
    }
  }

  /**
   * Get taxonomy terms for the node.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   *
   * @return array
   *   Array of taxonomy term data.
   */
  protected function getTaxonomyTerms($nid, $vid) {
    try {
      $query = $this->select('term_node', 'tn')
        ->fields('tn', ['tid'])
        ->condition('tn.nid', $nid);

      // Join with term_data to get additional term info
      $query->leftJoin('term_data', 'td', 'tn.tid = td.tid');
      $query->addField('td', 'name');
      $query->addField('td', 'vid', 'vocabulary_id');
      $query->addField('td', 'weight');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Clean up row data for consistent migration.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   */
  protected function cleanupRowData(Row $row) {
    // Handle null values for string fields
    $string_fields = [
      'title', 'body', 'teaser'
    ];
    
    foreach ($string_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null) {
        $row->setSourceProperty($field, '');
      }
    }

    // Handle date fields - ensure proper format
    $date_fields = ['field_datum_value'];
    foreach ($date_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null || $value === '') {
        $row->setSourceProperty($field, null);
      }
    }

    // Handle workflow state
    $workflow_state = $row->getSourceProperty('workflow_stateid');
    if ($workflow_state === null) {
      $row->setSourceProperty('workflow_stateid', 1); // Default to published
    }

    // Ensure arrays are properly formatted
    $array_fields = ['images', 'files', 'taxonomy_terms'];
    foreach ($array_fields as $field) {
      $value = $row->getSourceProperty($field);
      if (!is_array($value)) {
        $row->setSourceProperty($field, []);
      }
    }
  }
}