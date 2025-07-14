<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Audio files - CORRECTED VERSION.
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

    // Join with node_revisions for body content
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'teaser');
    $query->addField('nr', 'format');

    // CORRECTED: Join with content_type_audio (contains content-specific fields)
    $query->leftJoin('content_type_audio', 'cta', 'n.nid = cta.nid AND n.vid = cta.vid');
    $query->fields('cta'); // This includes field_mp3_fid and field_audio_bijz_value

    // Join with files table for MP3 file details
    $query->leftJoin('files', 'f', 'cta.field_mp3_fid = f.fid');
    $query->fields('f', ['filename', 'filepath', 'filemime', 'filesize']);

    // CORRECTED: Join with shared field tables (fields used across content types)
    $query->leftJoin('content_field_audio_type', 'cfat', 'n.nid = cfat.nid AND n.vid = cfat.vid');
    $query->addField('cfat', 'field_audio_type_value');

    $query->leftJoin('content_field_audio_uitvoerende', 'cfau', 'n.nid = cfau.nid AND n.vid = cfau.vid');
    $query->addField('cfau', 'field_audio_uitvoerende_value');

    $query->leftJoin('content_field_datum', 'cfd', 'n.nid = cfd.nid AND n.vid = cfd.vid');
    $query->addField('cfd', 'field_datum_value');

    $query->leftJoin('content_field_repertoire', 'cfr', 'n.nid = cfr.nid AND n.vid = cfr.vid');
    $query->addField('cfr', 'field_repertoire_nid');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Revision ID'),
      'title' => $this->t('Title'),
      'uid' => $this->t('User ID'),
      'status' => $this->t('Status'),
      'created' => $this->t('Created'),
      'changed' => $this->t('Changed'),
      'body' => $this->t('Body'),
      'teaser' => $this->t('Teaser'),
      'format' => $this->t('Input format'),
      
      // Audio-specific fields from content_type_audio
      'field_mp3_fid' => $this->t('MP3 file ID'),
      'field_mp3_list' => $this->t('MP3 list setting'),
      'field_mp3_data' => $this->t('MP3 data'),
      'field_audio_bijz_value' => $this->t('Audio notes'),
      
      // File details
      'filename' => $this->t('Filename'),
      'filepath' => $this->t('File path'),
      'filemime' => $this->t('File MIME type'),
      'filesize' => $this->t('File size'),
      
      // Shared fields from separate tables
      'field_audio_type_value' => $this->t('Audio type'),
      'field_audio_uitvoerende_value' => $this->t('Performer'),
      'field_datum_value' => $this->t('Date'),
      'field_repertoire_nid' => $this->t('Repertoire reference'),
      
      // Computed fields
      'access_terms' => $this->t('Access terms'),
      'related_activities' => $this->t('Related activities'),
      'audio_files' => $this->t('Audio file data for media migration'),
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

    // Transform media type values to consistent format
    $this->transformAudioType($row);
    
    // Get access control terms
    $this->getAccessTerms($row);
    
    // Get related activities
    $this->getRelatedActivities($row);
    
    // Prepare audio file data for media migration
    $this->prepareAudioFileData($row);

    return TRUE;
  }

  /**
   * Transform audio type values to consistent format.
   */
  protected function transformAudioType(Row $row) {
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
      $row->setSourceProperty('field_audio_type_mapped', $type_map[$media_type]);
    } else {
      $row->setSourceProperty('field_audio_type_mapped', 100); // Default to "Overig"
    }
  }

  /**
   * Get access control terms for this audio node.
   */
  protected function getAccessTerms(Row $row) {
    $nid = $row->getSourceProperty('nid');

    $term_query = $this->select('term_node', 'tn')
      ->fields('tn', ['tid'])
      ->condition('tn.nid', $nid);
    
    // Join with term_data to filter by vocabulary ID 4 (access vocabulary)
    $term_query->leftJoin('term_data', 'td', 'tn.tid = td.tid');
    $term_query->condition('td.vid', 4);
    
    $terms = $term_query->execute()->fetchCol();
    $row->setSourceProperty('access_terms', $terms);
  }

  /**
   * Get related activities for this audio node.
   */
  protected function getRelatedActivities(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    $activity_query = $this->select('content_field_ref_activiteit', 'cfra')
      ->fields('cfra', ['field_ref_activiteit_nid'])
      ->condition('cfra.nid', $nid)
      ->condition('cfra.vid', $vid);
    
    $activities = $activity_query->execute()->fetchCol();
    $row->setSourceProperty('related_activities', $activities);
  }

  /**
   * Prepare audio file data for media entity migration.
   */
  protected function prepareAudioFileData(Row $row) {
    $mp3_fid = $row->getSourceProperty('field_mp3_fid');
    $audio_files = [];

    if (!empty($mp3_fid)) {
      $audio_files[] = [
        'fid' => $mp3_fid,
        'description' => $row->getSourceProperty('field_audio_bijz_value') ?: '',
      ];
    }

    $row->setSourceProperty('audio_files', $audio_files);
  }
}