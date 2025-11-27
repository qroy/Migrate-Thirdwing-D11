<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6 node source with custom field handling.
 *
 * @MigrateSource(
 *   id = "thirdwing_node_custom",
 *   source_module = "node"
 * )
 */
class ThirdWingNode extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Base query voor nodes
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid',
        'vid',
        'type',
        'language',
        'title',
        'uid',
        'status',
        'created',
        'changed',
        'comment',
        'promote',
        'moderate',
        'sticky',
        'tnid',
        'translate',
      ]);

    // Filter op node type indien geconfigureerd
    if (isset($this->configuration['node_type'])) {
      $query->condition('n.type', $this->configuration['node_type']);
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'type' => $this->t('Type'),
      'language' => $this->t('Language'),
      'title' => $this->t('Title'),
      'uid' => $this->t('User ID'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'comment' => $this->t('Comment status'),
      'promote' => $this->t('Promoted to front page'),
      'moderate' => $this->t('Moderation status'),
      'sticky' => $this->t('Sticky at top of lists'),
      'tnid' => $this->t('Translation node ID'),
      'translate' => $this->t('Translate'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');

    // Haal node revisions op
    $vid = $row->getSourceProperty('vid');

    // Haal body field op
    $body = $this->select('node_revisions', 'nr')
      ->fields('nr', ['body', 'teaser', 'format'])
      ->condition('vid', $vid)
      ->execute()
      ->fetchAssoc();
    
    if ($body) {
      $row->setSourceProperty('body', $body['body']);
      $row->setSourceProperty('teaser', $body['teaser']);
      $row->setSourceProperty('format', $body['format']);
    }

    // Haal taxonomy terms op
    $terms = $this->select('term_node', 'tn')
      ->fields('tn', ['tid'])
      ->condition('nid', $nid)
      ->execute()
      ->fetchCol();
    
    if (!empty($terms)) {
      $row->setSourceProperty('taxonomy', $terms);
    }

    // Haal CCK fields op (als je deze nog nodig hebt)
    // Dit is een voorbeeld - pas aan voor jouw specifieke fields
    $this->getFields($row, 'node', $nid, $vid);

    return parent::prepareRow($row);
  }

  /**
   * Get CCK field data.
   */
  protected function getFields(Row $row, $entity_type, $entity_id, $revision_id = NULL) {
    // Query content_node_field table om field instances te vinden
    $field_query = $this->select('content_node_field_instance', 'cnfi')
      ->fields('cnfi', ['field_name', 'type_name'])
      ->condition('type_name', $row->getSourceProperty('type'))
      ->execute();

    foreach ($field_query as $field_info) {
      $field_name = $field_info['field_name'];
      $table_name = 'content_type_' . $field_info['type_name'];

      // Check if table exists
      if ($this->getDatabase()->schema()->tableExists($table_name)) {
        $field_data = $this->select($table_name, 'ct')
          ->fields('ct')
          ->condition('nid', $entity_id)
          ->condition('vid', $revision_id)
          ->execute()
          ->fetchAssoc();

        if ($field_data) {
          // Extract field columns
          foreach ($field_data as $column => $value) {
            if (strpos($column, $field_name) === 0) {
              $row->setSourceProperty($column, $value);
            }
          }
        }
      }
    }
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

}
