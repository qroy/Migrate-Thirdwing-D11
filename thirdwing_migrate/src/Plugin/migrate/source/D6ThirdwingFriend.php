<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingFriend.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Friends (sponsors).
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_friend",
 *   source_module = "node"
 * )
 */
class D6ThirdwingFriend extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'vriend')
      ->orderBy('n.nid');

    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'format');

    $query->leftJoin('workflow_node', 'w', 'n.nid = w.nid');
    $query->addField('w', 'sid', 'workflow_stateid');

    $query->leftJoin('content_type_vriend', 'ctv', 'n.nid = ctv.nid AND n.vid = ctv.vid');
    $query->fields('ctv');

    $query->leftJoin('content_field_woonplaats', 'cfw', 'n.nid = cfw.nid AND n.vid = cfw.vid');
    $query->addField('cfw', 'field_woonplaats_value');

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
      'workflow_stateid' => $this->t('Workflow state'),
      'field_website_url' => $this->t('Website URL'),
      'field_website_title' => $this->t('Website title'),
      'field_vriend_soort_value' => $this->t('Friend type'),
      'field_vriend_benaming_value' => $this->t('Friend designation'),
      'field_vriend_tot_value' => $this->t('Friend until'),
      'field_vriend_vanaf_value' => $this->t('Friend since'),
      'field_vriend_lengte_value' => $this->t('Friend duration'),
      'field_woonplaats_value' => $this->t('City'),
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

    // Transform friend type
    $type_map = [
      'financieel' => 1,
      'niet-financieel' => 2,
      'materieel' => 3,
    ];
    $type = $row->getSourceProperty('field_vriend_soort_value');
    if (isset($type_map[$type])) {
      $row->setSourceProperty('field_vriend_soort_value', $type_map[$type]);
    }

    // Transform designation
    $designation_map = [
      'vriend' => 1,
      'vriendin' => 2,
      'vrienden' => 3,
    ];
    $designation = $row->getSourceProperty('field_vriend_benaming_value');
    if (isset($designation_map[$designation])) {
      $row->setSourceProperty('field_vriend_benaming_value', $designation_map[$designation]);
    }

    // Transform workflow states
    $workflow_map = [
      11 => 9,  // Actief
      12 => 10, // Verlopen
      13 => 11, // Inactief
    ];
    
    $workflow_state = $row->getSourceProperty('workflow_stateid');
    if (isset($workflow_map[$workflow_state])) {
      $row->setSourceProperty('workflow_stateid', $workflow_map[$workflow_state]);
    }

    $this->getRelatedMedia($row);

    return TRUE;
  }

  protected function getRelatedMedia(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $image_query = $this->select('content_field_afbeeldingen', 'cfa')
      ->fields('cfa', ['field_afbeeldingen_fid', 'field_afbeeldingen_data'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $images = $image_query->execute()->fetchAll();
    $row->setSourceProperty('images', $images);
  }
}