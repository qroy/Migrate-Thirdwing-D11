<?php

/**
 * @file
 * Webform source plugins for D6 to D11 migration.
 * File: thirdwing_migrate/src/Plugin/migrate/source/D6WebformForms.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 6 webform forms source plugin.
 *
 * @MigrateSource(
 *   id = "d6_webform_forms",
 *   source_module = "webform"
 * )
 */
class D6WebformForms extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('webform', 'w')
      ->fields('w', [
        'nid',
        'confirmation',
        'teaser', 
        'submit_text',
        'submit_limit',
        'submit_interval',
        'confirmation_format',
        'submit_notice',
        'allow_draft',
        'redirect_url',
        'block',
        'status',
        'auto_save',
        'total_submit_limit',
        'total_submit_interval',
      ]);

    // Join with node table to get title and body
    $query->leftJoin('node', 'n', 'w.nid = n.nid');
    $query->addField('n', 'title');
    $query->addField('n', 'created');
    $query->addField('n', 'changed');
    $query->addField('n', 'uid');

    // Join with node_revisions to get body
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');

    // Only migrate published webforms
    $query->condition('n.status', 1);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'title' => $this->t('Webform title'),
      'body' => $this->t('Webform description'),
      'confirmation' => $this->t('Confirmation message'),
      'submit_text' => $this->t('Submit button text'),
      'submit_limit' => $this->t('Submit limit per user'),
      'allow_draft' => $this->t('Allow draft submissions'),
      'redirect_url' => $this->t('Redirect URL after submission'),
      'status' => $this->t('Webform status'),
      'total_submit_limit' => $this->t('Total submit limit'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'uid' => $this->t('Author user ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'w',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');

    // Get webform components
    $components = $this->getDatabase()->select('webform_component', 'wc')
      ->fields('wc')
      ->condition('nid', $nid)
      ->orderBy('weight')
      ->execute()
      ->fetchAll();

    $row->setSourceProperty('components', $components);

    // Get email settings
    $emails = $this->getDatabase()->select('webform_emails', 'we')
      ->fields('we')
      ->condition('nid', $nid)
      ->execute()
      ->fetchAll();

    $row->setSourceProperty('emails', $emails);

    // Get access roles
    $roles = $this->getDatabase()->select('webform_roles', 'wr')
      ->fields('wr', ['rid'])
      ->condition('nid', $nid)
      ->execute()
      ->fetchCol();

    $row->setSourceProperty('access_roles', $roles);

    return parent::prepareRow($row);
  }

}