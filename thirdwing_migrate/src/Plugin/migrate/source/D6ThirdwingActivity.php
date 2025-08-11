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

    // REMOVED: No longer transform instrument values in source plugin
    // The migration YAML will handle the proper mapping using static_map plugin
    
    // REMOVED: No longer transform sleep group values in source plugin  
    // The migration YAML will handle the proper mapping using static_map plugin

    // REMOVED: Incorrect workflow state transformation
    // We preserve the original D6 workflow state IDs (10,11,12,13,14,15,16,17,18,19,20)
    // The migration YAML will handle the proper mapping to D11 content moderation states

    $this->getRelatedMedia($row);
    $this->getRelatedProgram($row);
    $this->getAccessTerms($row);

    return TRUE;
  }

  /**
   * Get related media files for the activity.
   */
  protected function getRelatedMedia(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    // Get images
    $image_query = $this->select('content_field_afbeeldingen', 'cfa')
      ->fields('cfa', ['field_afbeeldingen_fid', 'field_afbeeldingen_data'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $images = $image_query->execute()->fetchAll();
    $row->setSourceProperty('images', $images);

    // Get files
    $file_query = $this->select('content_field_files', 'cff')
      ->fields('cff', ['field_files_fid', 'field_files_data'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $files = $file_query->execute()->fetchAll();
    $row->setSourceProperty('files', $files);

    // Get background image
    $background_fid = $row->getSourceProperty('field_background_fid');
    if ($background_fid) {
      $row->setSourceProperty('field_background_fid', $background_fid);
    }
  }

  /**
   * Get related program references.
   */
  protected function getRelatedProgram(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $program_query = $this->select('content_field_programma2', 'cfp')
      ->fields('cfp', ['field_programma2_nid'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $programs = $program_query->execute()->fetchAll();
    $row->setSourceProperty('program_items', $programs);
  }

  /**
   * Get taxonomy terms for access control.
   */
  protected function getAccessTerms(Row $row) {
    $nid = $row->getSourceProperty('nid');

    $term_query = $this->select('term_node', 'tn')
      ->fields('tn', ['tid'])
      ->condition('nid', $nid);
    $terms = $term_query->execute()->fetchAll();
    $row->setSourceProperty('taxonomy_terms', $terms);
  }
}