<?php

/**
 * @file
 * File: thirdwing_migrate/src/Plugin/migrate/source/D6WebformSubmissions.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6 webform submissions source plugin.
 *
 * @MigrateSource(
 *   id = "d6_webform_submissions",
 *   source_module = "webform"
 * )
 */
class D6WebformSubmissions extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('webform_submissions', 'ws')
      ->fields('ws', [
        'sid',
        'nid', 
        'uid',
        'submitted',
        'remote_addr',
        'is_draft',
      ]);

    // Only migrate submissions for existing webforms
    $query->innerJoin('webform', 'w', 'ws.nid = w.nid');
    $query->innerJoin('node', 'n', 'w.nid = n.nid');
    $query->condition('n.status', 1);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'sid' => $this->t('Submission ID'),
      'nid' => $this->t('Webform node ID'),
      'uid' => $this->t('User ID'),
      'submitted' => $this->t('Submission timestamp'),
      'remote_addr' => $this->t('Remote IP address'),
      'is_draft' => $this->t('Is draft submission'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'sid' => [
        'type' => 'integer',
        'alias' => 'ws',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $sid = $row->getSourceProperty('sid');

    // Get submission data
    $data = $this->getDatabase()->select('webform_submitted_data', 'wsd')
      ->fields('wsd', ['cid', 'no', 'data'])
      ->condition('sid', $sid)
      ->execute()
      ->fetchAll();

    $submission_data = [];
    foreach ($data as $item) {
      if (!isset($submission_data[$item->cid])) {
        $submission_data[$item->cid] = [];
      }
      $submission_data[$item->cid][$item->no] = $item->data;
    }

    $row->setSourceProperty('submission_data', $submission_data);

    return parent::prepareRow($row);
  }

}