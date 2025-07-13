<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingDocumentFiles.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Document files.
 *
 * Handles all document files including:
 * - General documents (PDFs, DOCs, etc.)
 * - MuseScore files (.mscz) - moved from sheet music bundle
 * - Verslag files from verslag content type
 * - Repertoire attached files
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_document_files",
 *   source_module = "file"
 * )
 */
class D6ThirdwingDocumentFiles extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('files', 'f')
      ->fields('f')
      ->orderBy('f.fid');

    // Include files that are documents based on extension or usage
    $document_extensions = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'mscz'];
    $conditions = $query->orConditionGroup();
    
    foreach ($document_extensions as $ext) {
      $conditions->condition('f.filemime', "%$ext%", 'LIKE');
    }
    
    $query->condition($conditions);

    // Join with file usage to understand context
    $query->leftJoin('file_usage', 'fu', 'fu.fid = f.fid');
    $query->addField('fu', 'module');
    $query->addField('fu', 'type');
    $query->addField('fu', 'id', 'usage_id');

    // Join with field_files data to get descriptions
    $query->leftJoin('content_field_files', 'cff', 'cff.field_files_fid = f.fid');
    $query->addField('cff', 'field_files_data');
    $query->addField('cff', 'nid', 'field_files_nid');

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
      'field_files_description' => $this->t('File description from D6'),
      'source_content_type' => $this->t('Source content type'),
      'file_extension' => $this->t('File extension'),
      'repertoire_attachment' => $this->t('Is repertoire attachment'),
      'verslag_taxonomy_term' => $this->t('Verslag taxonomy term'),
      'document_date' => $this->t('Document date'),
      'repertoire_nid' => $this->t('Related repertoire node ID'),
      'access_terms' => $this->t('Access control terms'),
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

    $fid = $row->getSourceProperty('fid');
    $filepath = $row->getSourceProperty('filepath');
    $filename = $row->getSourceProperty('filename');

    // Extract file extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $row->setSourceProperty('file_extension', $extension);

    // Parse field_files_data for description
    $this->parseFieldFilesData($row);

    // Determine source content type and context
    $this->determineSourceContext($row);

    // Set access terms based on file type and context
    $this->setAccessTerms($row);

    return TRUE;
  }

  /**
   * Parse D6 field_files_data for description and metadata.
   */
  protected function parseFieldFilesData(Row $row) {
    $data = $row->getSourceProperty('field_files_data');
    $description = '';

    if ($data) {
      $parsed = unserialize($data);
      if (is_array($parsed) && isset($parsed['description'])) {
        $description = trim($parsed['description']);
      }
    }

    $row->setSourceProperty('field_files_description', $description);
  }

  /**
   * Determine the source context for proper classification.
   */
  protected function determineSourceContext(Row $row) {
    $fid = $row->getSourceProperty('fid');
    $extension = $row->getSourceProperty('file_extension');
    $module = $row->getSourceProperty('module');
    $type = $row->getSourceProperty('type');
    $usage_id = $row->getSourceProperty('usage_id');

    // Check if file is attached to verslag content type
    if ($type === 'node') {
      $verslag_info = $this->checkVerslagAttachment($usage_id);
      if ($verslag_info) {
        $row->setSourceProperty('source_content_type', 'verslag');
        $row->setSourceProperty('verslag_taxonomy_term', $verslag_info['taxonomy_term']);
        $row->setSourceProperty('document_date', $verslag_info['date']);
        return;
      }
    }

    // Check if file is attached to repertoire (MuseScore files or general attachments)
    $repertoire_info = $this->checkRepertoireAttachment($fid);
    if ($repertoire_info || $extension === 'mscz') {
      $row->setSourceProperty('source_content_type', 'repertoire');
      $row->setSourceProperty('repertoire_attachment', TRUE);
      $row->setSourceProperty('repertoire_nid', $repertoire_info['nid'] ?? NULL);
      return;
    }

    // Default to general document
    $row->setSourceProperty('source_content_type', 'document');
    $row->setSourceProperty('repertoire_attachment', FALSE);
  }

  /**
   * Check if file is attached to verslag content type.
   */
  protected function checkVerslagAttachment($nid) {
    if (!$nid) {
      return FALSE;
    }

    $query = $this->select('node', 'n')
      ->fields('n', ['created'])
      ->condition('n.nid', $nid)
      ->condition('n.type', 'verslag');

    // Join with taxonomy to get verslag type
    $query->leftJoin('term_node', 'tn', 'tn.nid = n.nid');
    $query->leftJoin('term_data', 'td', 'td.tid = tn.tid AND td.vid = 3'); // Verslag taxonomy vid
    $query->addField('td', 'tid');

    $result = $query->execute()->fetchAssoc();

    if ($result) {
      return [
        'taxonomy_term' => $result['tid'],
        'date' => date('Y-m-d', $result['created']),
      ];
    }

    return FALSE;
  }

  /**
   * Check if file is attached to repertoire.
   */
  protected function checkRepertoireAttachment($fid) {
    // Check various repertoire file fields
    $repertoire_fields = [
      'content_field_partij_band' => 'field_partij_band_fid',
      'content_field_partij_koor_l' => 'field_partij_koor_l_fid',
      'content_field_partij_tekst' => 'field_partij_tekst_fid',
      'content_field_mp3' => 'field_mp3_fid',
    ];

    foreach ($repertoire_fields as $table => $field) {
      $query = $this->select($table, 't')
        ->fields('t', ['nid'])
        ->condition("t.$field", $fid)
        ->range(0, 1);

      $result = $query->execute()->fetchAssoc();
      if ($result) {
        return ['nid' => $result['nid']];
      }
    }

    return FALSE;
  }

  /**
   * Set access terms based on file type and context.
   */
  protected function setAccessTerms(Row $row) {
    $source_type = $row->getSourceProperty('source_content_type');
    $extension = $row->getSourceProperty('file_extension');

    // Default access terms - more restrictive for documents
    $access_terms = [217, 86, 28, 85]; // Leden, Bestuur, Beheer, etc.

    if ($source_type === 'verslag') {
      // Verslagen - more restrictive access
      $access_terms = [86, 28, 85]; // Bestuur, Beheer only
    } elseif ($source_type === 'repertoire' || $extension === 'mscz') {
      // Sheet music - accessible to band and choir members
      $access_terms = [217, 86, 28, 85, 218]; // Include band members
    }

    $row->setSourceProperty('access_terms', $access_terms);
  }
}