<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingLocation.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Locations.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_location",
 *   source_module = "node"
 * )
 */
class D6ThirdwingLocation extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'locatie')
      ->orderBy('n.nid');

    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'format');

    $query->leftJoin('content_type_locatie', 'ctl', 'n.nid = ctl.nid AND n.vid = ctl.vid');
    $query->fields('ctl');

    $query->leftJoin('content_field_l_routelink', 'cfrl', 'n.nid = cfrl.nid AND n.vid = cfrl.vid');
    $query->addField('cfrl', 'field_l_routelink_url');
    $query->addField('cfrl', 'field_l_routelink_title');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'title' => $this->t('Title'),
      'body' => $this->t('Body'),
      'field_l_adres_value' => $this->t('Address'),
      'field_l_plaats_value' => $this->t('City'),
      'field_l_postcode_value' => $this->t('Postal code'),
      'field_l_routelink_url' => $this->t('Route URL'),
      'field_l_routelink_title' => $this->t('Route title'),
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
    return parent::prepareRow($row);
  }
}