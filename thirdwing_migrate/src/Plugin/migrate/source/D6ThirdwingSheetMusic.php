<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingSheetMusic.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Sheet Music files.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_sheet_music",
 *   source_module = "file"
 * )
 */
class D6ThirdwingSheetMusic extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('files', 'f')
      ->fields('f')
      ->condition('f.filepath', '%repertoire%', 'LIKE')
      ->orderBy('f.fid');

    $query->leftJoin('content_field_partij_band', 'b', 'b.field_partij_band_fid = f.fid');
    $query->leftJoin('content_field_partij_koor_l', 'k', 'k.field_partij_koor_l_fid = f.fid');
    $query->leftJoin('content_field_partij_tekst', 't', 't.field_partij_tekst_fid = f.fid');

    $query->addField('b', 'nid', 'bnid');
    $query->addField('k', 'nid', 'knid');
    $query->addField('t', 'nid', 'tnid');
    $query->addField('b', 'field_partij_band_data');
    $query->addField('k', 'field_partij_koor_l_data');
    $query->addField('t', 'field_partij_tekst_data');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('File ID'),
      'uid' => $this->t('User ID'),
      'filename' => $this->t('Filename'),
      'filepath' => $this->t('File path'),
      'filemime' => $this->t('File MIME type'),
      'filesize' => $this->t('File size'),
      'status' => $this->t('Status'),
      'timestamp' => $this->t('Timestamp'),
      'bnid' => $this->t('Band sheet music node ID'),
      'knid' => $this->t('Choir sheet music node ID'),
      'tnid' => $this->t('Text sheet music node ID'),
      'field_partij_band_data' => $this->t('Band sheet music data'),
      'field_partij_koor_l_data' => $this->t('Choir sheet music data'),
      'field_partij_tekst_data' => $this->t('Text sheet music data'),
      'sheet_music_type' => $this->t('Sheet music type'),
      'repertoire_nid' => $this->t('Repertoire node ID'),
      'access_terms' => $this->t('Access terms'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
        'alias' => 'f',
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

    if ($row->getSourceProperty('bnid')) {
      $this->processBandSheetMusic($row);
    } elseif ($row->getSourceProperty('knid')) {
      $this->processChoirSheetMusic($row);
    } elseif ($row->getSourceProperty('tnid')) {
      $this->processTextSheetMusic($row);
    }

    return TRUE;
  }

  protected function processBandSheetMusic(Row $row) {
    $data = $row->getSourceProperty('field_partij_band_data');
    $repertoire_nid = $row->getSourceProperty('bnid');

    if (strpos($data, 'Toetsen') !== false || strpos($data, 'Piano') !== false) {
      $type = 11;
    } elseif (strpos($data, 'Gitaar') !== false) {
      $type = 12;
    } elseif (strpos($data, 'Bas') !== false) {
      $type = 13;
    } elseif (strpos($data, 'Drums') !== false) {
      $type = 14;
    } else {
      $type = 10;
    }

    $row->setSourceProperty('sheet_music_type', $type);
    $row->setSourceProperty('repertoire_nid', $repertoire_nid);
    $row->setSourceProperty('access_terms', [217, 86, 28, 85]);
  }

  protected function processChoirSheetMusic(Row $row) {
    $data = $row->getSourceProperty('field_partij_koor_l_data');
    $repertoire_nid = $row->getSourceProperty('knid');

    // Determine choir sheet music type based on data
    if (strpos($data, 'Sopraan') !== false) {
      $type = 1;
    } elseif (strpos($data, 'Alt') !== false) {
      $type = 2;
    } elseif (strpos($data, 'Tenor') !== false) {
      $type = 3;
    } elseif (strpos($data, 'Bas') !== false) {
      $type = 4;
    } else {
      $type = 1; // Default to Sopraan
    }

    $row->setSourceProperty('sheet_music_type', $type);
    $row->setSourceProperty('repertoire_nid', $repertoire_nid);
    $row->setSourceProperty('access_terms', [217, 86, 28, 85]);
  }

  protected function processTextSheetMusic(Row $row) {
    $data = $row->getSourceProperty('field_partij_tekst_data');
    $repertoire_nid = $row->getSourceProperty('tnid');

    if (strpos($data, 'Tekst en koorregie') !== false) {
      $type = 5;
    } elseif (strpos($data, 'Koorregie') !== false) {
      $type = 4;
    } else {
      $type = 3;
    }

    $row->setSourceProperty('sheet_music_type', $type);
    $row->setSourceProperty('repertoire_nid', $repertoire_nid);
    $row->setSourceProperty('access_terms', [217, 86, 28, 85]);
  }
}