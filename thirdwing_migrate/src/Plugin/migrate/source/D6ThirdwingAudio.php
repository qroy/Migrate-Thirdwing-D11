<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingAudio.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Audio files.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_audio",
 *   source_module = "node"
 * )
 */
class D6ThirdwingAudio extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'audio')
      ->orderBy('n.nid');

    $query->leftJoin('content_type_audio', 'cta', 'n.nid = cta.nid AND n.vid = cta.vid');
    $query->fields('cta');

    $query->leftJoin('files', 'f', 'cta.field_mp3_fid = f.fid');
    $query->fields('f', ['filename', 'filepath', 'filemime', 'filesize']);

    $query->leftJoin('content_field_audio_type', 't', 'n.nid = t.nid AND n.vid = t.vid');
    $query->addField('t', 'field_audio_type_value');

    $query->leftJoin('content_field_audio_uitvoerende', 'u', 'n.nid = u.nid AND n.vid = u.vid');
    $query->addField('u', 'field_audio_uitvoerende_value');

    $query->leftJoin('content_field_datum', 'd', 'n.nid = d.nid AND n.vid = d.vid');
    $query->addField('d', 'field_datum_value');

    $query->leftJoin('content_field_repertoire', 'r', 'n.nid = r.nid AND n.vid = r.vid');
    $query->addField('r', 'field_repertoire_nid');

    return $query;
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

    // Transform media type values
    $type_map = [
      'Uitvoering' => 1,
      'Repetitie' => 2,
      'Oefenbestand' => 3,
      'Origineel' => 4,
      'Uitzending' => 5,
      'Overig' => 100,
    ];

    $media_type = $row->getSourceProperty('field_audio_type_value');
    if (isset($type_map[$media_type])) {
      $row->setSourceProperty('field_audio_type_value', $type_map[$media_type]);
    }

    $this->getAccessTerms($row);
    $this->getRelatedActivities($row);

    return TRUE;
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

  protected function getRelatedActivities(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $activity_query = $this->select('content_field_ref_activiteit', 'cfra')
      ->fields('cfra', ['field_ref_activiteit_nid'])
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    $activities = $activity_query->execute()->fetchCol();
    $row->setSourceProperty('related_activities', $activities);
  }
}