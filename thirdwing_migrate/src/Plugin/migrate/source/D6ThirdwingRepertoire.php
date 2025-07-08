<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingRepertoire.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Repertoire.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_repertoire", 
 *   source_module = "node"
 * )
 */
class D6ThirdwingRepertoire extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'repertoire')
      ->orderBy('n.nid');

    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');

    $query->leftJoin('content_type_repertoire', 'ctr', 'n.nid = ctr.nid AND n.vid = ctr.vid');
    $query->fields('ctr');

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
      'field_klapper_value' => $this->t('Active in folder'),
      'field_audio_nummer_value' => $this->t('Number'),
      'field_audio_seizoen_value' => $this->t('Season'),
      'field_rep_genre_value' => $this->t('Genre'),
      'field_rep_uitv_jaar_value' => $this->t('Performance year'),
      'field_rep_uitv_value' => $this->t('Performer'),
      'field_rep_componist_value' => $this->t('Composer'),
      'field_rep_componist_jaar_value' => $this->t('Composer year'),
      'field_rep_arr_value' => $this->t('Arranger'),
      'field_rep_arr_jaar_value' => $this->t('Arranger year'),
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

    // Transform season values
    $season = $row->getSourceProperty('field_audio_seizoen_value');
    if ($season == 'Kerst') {
      $row->setSourceProperty('field_audio_seizoen_value', 2);
    } elseif ($season == 'Regulier') {
      $row->setSourceProperty('field_audio_seizoen_value', 1);
    }

    // Transform genre values
    $genre_map = [
      'Pop' => 1,
      'Musical / Film' => 2,
      'Geestelijk / Gospel' => 3,
    ];
    $genre = $row->getSourceProperty('field_rep_genre_value');
    if (isset($genre_map[$genre])) {
      $row->setSourceProperty('field_rep_genre_value', $genre_map[$genre]);
    }

    // Transform active status
    $active = $row->getSourceProperty('field_klapper_value');
    $row->setSourceProperty('field_klapper_value', ($active === 'Ja') ? 1 : 0);

    return TRUE;
  }
}