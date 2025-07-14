<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 Thirdwing program source plugin.
 *
 * Handles migration of program content type with all CCK fields and relationships.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_program",
 *   source_module = "node"
 * )
 */
class D6ThirdwingProgram extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid', 'vid', 'type', 'language', 'title', 'uid', 'status',
        'created', 'changed', 'comment', 'promote', 'moderate', 'sticky'
      ])
      ->condition('n.type', 'programma');

    // Join with node_revisions for body content
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'teaser');
    $query->addField('nr', 'format');

    // Program-specific fields
    $query->leftJoin('content_type_programma', 'ctp', 'n.vid = ctp.vid');
    $query->addField('ctp', 'field_datum_value');
    $query->addField('ctp', 'field_prog_type_value');

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
      'field_datum_value' => $this->t('Program date'),
      'field_prog_type_value' => $this->t('Program type'),
      'field_locatie_nid' => $this->t('Location reference'),
      'repertoire_items' => $this->t('Repertoire references'),
      'program_items' => $this->t('Program item references'),
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

    // Get location reference
    $location_nid = $this->getNodeReference($nid, $vid, 'field_locatie');
    $row->setSourceProperty('field_locatie_nid', $location_nid);

    // Get repertoire references
    $repertoire_items = $this->getNodeReferences($nid, $vid, 'field_repertoire');
    $row->setSourceProperty('repertoire_items', $repertoire_items);

    // Get program item references (self-references)
    $program_items = $this->getNodeReferences($nid, $vid, 'field_programma2');
    $row->setSourceProperty('program_items', $program_items);

    // Get taxonomy terms
    $taxonomy_terms = $this->getTaxonomyTerms($nid, $vid);
    $row->setSourceProperty('taxonomy_terms', $taxonomy_terms);

    // Clean up data
    $this->cleanupRowData($row);

    return parent::prepareRow($row);
  }

  /**
   * Get a single node reference field value.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   * @param string $field_name
   *   Field name.
   *
   * @return int|null
   *   Referenced node ID or null.
   */
  protected function getNodeReference($nid, $vid, $field_name) {
    $table_name = 'content_' . $field_name;
    
    try {
      $query = $this->select($table_name, 'cf')
        ->fields('cf', [$field_name . '_nid'])
        ->condition('cf.nid', $nid)
        ->condition('cf.vid', $vid)
        ->range(0, 1);

      $result = $query->execute()->fetchField();
      return $result ? (int) $result : null;
    } catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Get multiple node reference field values.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   * @param string $field_name
   *   Field name.
   *
   * @return array
   *   Array of node reference data.
   */
  protected function getNodeReferences($nid, $vid, $field_name) {
    $table_name = 'content_' . $field_name;
    
    try {
      $query = $this->select($table_name, 'cf')
        ->fields('cf', [
          'nid', 'vid', 'delta',
          $field_name . '_nid'
        ])
        ->condition('cf.nid', $nid)
        ->condition('cf.vid', $vid)
        ->orderBy('cf.delta');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
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
      'title', 'body', 'teaser', 'field_prog_type_value'
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

    // Ensure arrays are properly formatted
    $array_fields = ['repertoire_items', 'program_items', 'taxonomy_terms'];
    foreach ($array_fields as $field) {
      $value = $row->getSourceProperty($field);
      if (!is_array($value)) {
        $row->setSourceProperty($field, []);
      }
    }

    // Handle reference fields
    $reference_fields = ['field_locatie_nid'];
    foreach ($reference_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value !== null && $value !== '') {
        $row->setSourceProperty($field, (int) $value);
      }
    }
  }
}