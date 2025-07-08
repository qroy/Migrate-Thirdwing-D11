<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingReport.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Report files (verslagen).
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_report",
 *   source_module = "node"
 * )
 */
class D6ThirdwingReport extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n')
      ->condition('n.type', 'verslag')
      ->orderBy('f.timestamp');  // Fixed: removed undefined $this->newOnly

    $query->innerJoin('content_field_files', 'a', 'a.nid = n.nid AND a.vid = n.vid');
    $query->leftJoin('files', 'f', 'f.fid = a.field_files_fid');
    $query->fields('f');

    $query->leftJoin('content_field_datum', 'd', 'd.nid = n.nid AND d.vid = n.vid');
    $query->addField('d', 'field_datum_value', 'field_verslag_datum');

    $query->leftJoin('term_node', 'soort', 'soort.nid = n.nid AND soort.vid = n.vid');
    $query->leftJoin('term_data', 'soortterm', 'soortterm.tid = soort.tid');
    $query->condition('soortterm.vid', 9);
    $query->addField('soort', 'tid', 'field_verslag_soort');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('File ID'),
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'uid' => $this->t('User ID'),
      'filename' => $this->t('Filename'),
      'filepath' => $this->t('File path'),
      'filemime' => $this->t('File MIME type'),
      'filesize' => $this->t('File size'),
      'timestamp' => $this->t('File timestamp'),
      'field_verslag_datum' => $this->t('Report date'),
      'field_verslag_soort' => $this->t('Report type'),
      'field_toegang' => $this->t('Access terms'),
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

    $this->getAccessTerms($row);

    // Transform report type values
    $report_type_map = [
      131 => 10, // Bestuursvergaderingen
      129 => 20, // Muziekcommissie  
      201 => 70, // Commissie Publieke Relaties
      220 => 40, // Commissie Koorregie
      202 => 60, // Commissie Interne Relaties
      218 => 80, // Feestcommissie
      132 => 50, // Commissie Concerten
      130 => 30, // Algemene ledenvergaderingen
    ];

    $report_type = $row->getSourceProperty('field_verslag_soort');
    if (isset($report_type_map[$report_type])) {
      $row->setSourceProperty('field_verslag_soort', $report_type_map[$report_type]);
    }

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
    $row->setSourceProperty('field_toegang', $terms);
  }
}