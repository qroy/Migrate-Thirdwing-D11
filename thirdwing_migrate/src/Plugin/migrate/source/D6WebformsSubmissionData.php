<?php

/**
 * @file
 * File: thirdwing_migrate/src/Plugin/migrate/source/D6WebformSubmissionData.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6 webform submission data source plugin.
 *
 * @MigrateSource(
 *   id = "d6_webform_submission_data",
 *   source_module = "webform"
 * )
 */
class D6WebformSubmissionData extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('webform_submitted_data', 'wsd')
      ->fields('wsd', [
        'nid',
        'sid',
        'cid', 
        'no',
        'data',
      ]);

    // Only migrate data for existing submissions
    $query->innerJoin('webform_submissions', 'ws', 'wsd.sid = ws.sid');
    $query->innerJoin('webform', 'w', 'wsd.nid = w.nid');
    $query->innerJoin('node', 'n', 'w.nid = n.nid');
    $query->condition('n.status', 1);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Webform node ID'),
      'sid' => $this->t('Submission ID'),
      'cid' => $this->t('Component ID'),
      'no' => $this->t('Value number'),
      'data' => $this->t('Submitted data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'sid' => [
        'type' => 'integer',
        'alias' => 'wsd',
      ],
      'cid' => [
        'type' => 'integer', 
        'alias' => 'wsd',
      ],
      'no' => [
        'type' => 'string',
        'alias' => 'wsd',
      ],
    ];
  }

}