<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingActivity.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Activities.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_activity",
 *   source_module = "node"
 * )
 */
class D6ThirdwingActivity extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'activiteit')
      ->orderBy('n.nid');

    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'format');

    $query->leftJoin('workflow_node', 'w', 'n.nid = w.nid');
    $query->addField('w', 'sid', 'workflow_stateid');

    $query->leftJoin('content_type_activiteit', 'cta', 'n.nid = cta.nid AND n.vid = cta.vid');
    $query->fields('cta');

    $query->leftJoin('content_field_datum', 'cfd', 'n.nid = cfd.nid AND n.vid = cfd.vid');
    $query->addField('cfd', 'field_datum_value', 'field_datum');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'title' => $this->t('Title'),
      'body' => $this->t('Body'),
      'format' => $this->t('Text format'),
      'created' => $this->t('Created'),
      'changed' => $this->t('Changed'),
      'uid' => $this->t('Author'),
      'status' => $this->t('Published'),
      'workflow_stateid' => $this->t('Workflow state'),
      'field_datum' => $this->t('Activity date'),
      'field_tijd_aanwezig_value' => $this->t('Coordinator attendance time'),
      'field_keyboard_value' => $this->t('Keyboard status'),
      'field_gitaar_value' => $this->t('Guitar status'),
      'field_basgitaar_value' => $this->t('Bass status'),
      'field_drums_value' => $this->t('Drums status'),
      'field_vervoer_value' => $this->t('Transport'),
      'field_sleepgroep_value' => $this->t('Sleep group'),
      'field_sleepgroep_aanwezig_value' => $this->t('Sleep group attendance'),
      'field_kledingcode_value' => $this->t('Dress code'),
      'field_locatie_nid' => $this->t('Location reference'),
      'field_l_bijzonderheden_value' => $this->t('Location notes'),
      'field_ledeninfo_value' => $this->t('Member info'),
      'field_ledeninfo_format' => $this->t('Member info format'),
      'field_bijzonderheden_value' => $this->t('General notes'),
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

    // Transform instrument status values (+/?/-/v → 1/0/3/2)
    $search = ['+', '?', '-', 'v'];
    $replace = ['1', '0', '3', '2'];
    
    foreach (['field_keyboard_value', 'field_gitaar_value', 'field_basgitaar_value', 'field_drums_value'] as $field) {
      $value = $row->getSourceProperty($field);
      if ($value) {
        $row->setSourceProperty($field, str_replace($search, $replace, $value));
      }
    }

    // Transform sleep group values (I/II/III/IV/V/* → 1/2/3/4/5/9)
    $sleepgroup_map = [
      'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5, '*' => 9,
    ];
    
    $sleepgroup = $row->getSourceProperty('field_sleepgroep_value');
    if (isset($sleepgroup_map[$sleepgroup])) {
      $row->setSourceProperty('field_sleepgroep_value', $sleepgroup_map[$sleepgroup]);
    }

    // Transform workflow states
    $workflow_map = [
      15 => 2, // Concept
      20 => 3, // Published  
      17 => 4, // Recommended
      18 => 5, // Archive
      19 => 6, // No Archive
      16 => 7, // Trash
    ];
    
    $workflow_state = $row->getSourceProperty('workflow_stateid');
    if (isset($workflow_map[$workflow_state])) {
      $row->setSourceProperty('workflow_stateid', $workflow_map[$workflow_state]);
    }

    $this->getRelatedMedia($row);
    $this->getRelatedProgram($row);
    $this->getAccessTerms($row);

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

    $file_query = $this->select('content_field_files', 'cff')
      ->fields('cff', ['field_files_fid', 'field_files_data'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $files = $file_query->execute()->fetchAll();
    $row->setSourceProperty('files', $files);

    $background_fid = $row->getSourceProperty('field_background_fid');
    if ($background_fid) {
      $row->setSourceProperty('background_image', $background_fid);
    }
  }

  protected function getRelatedProgram(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $program_query = $this->select('content_field_programma2', 'cfp')
      ->fields('cfp', ['field_programma2_nid'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $program_items = $program_query->execute()->fetchCol();
    $row->setSourceProperty('program_items', $program_items);
  }

  protected function getAccessTerms(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $term_query = $this->select('term_node', 'tn')
      ->fields('tn', ['tid'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $term_query->leftJoin('term_data', 'td', 'tn.tid = td.tid');
    $term_query->condition('td.vid', 4);
    $terms = $term_query->execute()->fetchCol();
    $row->setSourceProperty('access_terms', $terms);
  }
}