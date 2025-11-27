<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingVideo.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Video files (YouTube).
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_video",
 *   source_module = "node"
 * )
 */
class D6ThirdwingVideo extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'video')
      ->orderBy('n.nid');

    $query->leftJoin('content_field_video', 'cfv', 'n.nid = cfv.nid AND n.vid = cfv.vid');
    $query->addField('cfv', 'field_video_embed');
    $query->addField('cfv', 'field_video_title');
    $query->addField('cfv', 'field_video_description');

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
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'title' => $this->t('Title'),
      'uid' => $this->t('User ID'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created'),
      'changed' => $this->t('Changed'),
      'field_video_embed' => $this->t('Video embed URL'),
      'field_video_title' => $this->t('Video title'),
      'field_video_description' => $this->t('Video description'),
      'field_audio_type_value' => $this->t('Media type'),
      'field_audio_uitvoerende_value' => $this->t('Performer'),
      'field_datum_value' => $this->t('Date'),
      'field_repertoire_nid' => $this->t('Repertoire reference'),
      'access_terms' => $this->t('Access terms'),
      'related_activities' => $this->t('Related activities'),
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

    // Same transformations as audio
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