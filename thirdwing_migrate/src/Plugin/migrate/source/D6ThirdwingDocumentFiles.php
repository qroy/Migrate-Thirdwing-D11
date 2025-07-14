<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Document files - CORRECTED VERSION.
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

    // CORRECTED: Use file extension mapping instead of LIKE queries on filemime
    // This is more efficient and accurate
    $query->condition('f.status', 1); // Only active files

    // Join with file usage to understand context
    $query->leftJoin('file_usage', 'fu', 'fu.fid = f.fid');
    $query->addField('fu', 'module');
    $query->addField('fu', 'type');
    $query->addField('fu', 'id', 'usage_id');

    // Join with field_files data to get descriptions
    $query->leftJoin('content_field_files', 'cff', 'cff.field_files_fid = f.fid');
    $query->addField('cff', 'field_files_data');
    $query->addField('cff', 'nid', 'field_files_nid');
    $query->addField('cff', 'vid', 'field_files_vid');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'fid' => $this->t('Bestand ID'),
      'uid' => $this->t('Gebruiker ID'),
      'filename' => $this->t('Bestandsnaam'),
      'filepath' => $this->t('Bestandspad'),
      'filemime' => $this->t('Bestandstype'),
      'filesize' => $this->t('Bestandsgrootte'),
      'status' => $this->t('Status'),
      'timestamp' => $this->t('Tijdstempel'),
      'module' => $this->t('Gebruiks module'),
      'type' => $this->t('Gebruiks type'),
      'usage_id' => $this->t('Gebruiks ID'),
      'field_files_data' => $this->t('Bestand veld data'),
      'field_files_nid' => $this->t('Bestand veld node ID'),
      'field_files_vid' => $this->t('Bestand veld revisie ID'),
      'field_files_description' => $this->t('Bestand omschrijving uit D6'),
      'source_content_type' => $this->t('Bron inhoudstype'),
      'file_extension' => $this->t('Bestand extensie'),
      'repertoire_attachment' => $this->t('Is repertoire bijlage'),
      'verslag_taxonomy_term' => $this->t('Verslag taxonomie term'),
      'document_date' => $this->t('Document datum'),
      'repertoire_nid' => $this->t('Gerelateerd repertoire node ID'),
      'access_terms' => $this->t('Toegang controle termen'),
      'document_classification' => $this->t('Document type classificatie'),
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
    $filename = $row->getSourceProperty('filename');

    // Extract file extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $row->setSourceProperty('file_extension', $extension);

    // CORRECTED: Filter for document extensions only
    if (!$this->isDocumentFile($extension)) {
      return FALSE;
    }

    // Parse field_files_data for description
    $this->parseFieldFilesData($row);

    // Determine source content type and context
    $this->determineSourceContext($row);

    // Classify document type
    $this->classifyDocument($row);

    // Set access terms based on file type and context
    $this->setAccessTerms($row);

    return TRUE;
  }

  /**
   * Check if file extension indicates a document file.
   * CORRECTED: More comprehensive and accurate extension checking.
   */
  protected function isDocumentFile($extension) {
    $document_extensions = [
      // Standard documents
      'pdf', 'doc', 'docx', 'txt', 'rtf',
      'xls', 'xlsx', 'ods', 'odt',
      // MuseScore files (remain in document bundle)
      'mscz', 'mscx',
      // Other document types
      'ppt', 'pptx', 'csv',
    ];

    return in_array($extension, $document_extensions);
  }

  /**
   * Parse D6 field_files_data for description and metadata.
   */
  protected function parseFieldFilesData(Row $row) {
    $data = $row->getSourceProperty('field_files_data');
    $description = '';

    if ($data) {
      try {
        $parsed = unserialize($data);
        if (is_array($parsed) && isset($parsed['description'])) {
          $description = trim($parsed['description']);
        }
      } catch (\Exception $e) {
        // Log parsing error but continue
        \Drupal::logger('thirdwing_migrate')->debug('Failed to parse file data for fid @fid', [
          '@fid' => $row->getSourceProperty('fid')
        ]);
      }
    }

    $row->setSourceProperty('field_files_description', $description);
  }

  /**
   * Determine the source context for proper classification.
   * CORRECTED: More robust context detection with error handling.
   */
  protected function determineSourceContext(Row $row) {
    $fid = $row->getSourceProperty('fid');
    $extension = $row->getSourceProperty('file_extension');
    $module = $row->getSourceProperty('module');
    $type = $row->getSourceProperty('type');
    $usage_id = $row->getSourceProperty('usage_id');
    $field_files_nid = $row->getSourceProperty('field_files_nid');

    // Initialize defaults
    $row->setSourceProperty('source_content_type', 'document');
    $row->setSourceProperty('repertoire_attachment', FALSE);

    // Check if file is attached to verslag content type
    if ($type === 'node' && !empty($usage_id)) {
      $verslag_info = $this->checkVerslagAttachment($usage_id);
      if ($verslag_info) {
        $row->setSourceProperty('source_content_type', 'verslag');
        $row->setSourceProperty('verslag_taxonomy_term', $verslag_info['taxonomy_term']);
        $row->setSourceProperty('document_date', $verslag_info['date']);
        return;
      }
    }

    // Check if file is attached via field_files to a verslag
    if (!empty($field_files_nid)) {
      $verslag_info = $this->checkVerslagAttachment($field_files_nid);
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
  }

  /**
   * Check if file is attached to verslag content type.
   * CORRECTED: Added proper error handling and more robust checking.
   */
  protected function checkVerslagAttachment($nid) {
    if (!$nid) {
      return FALSE;
    }

    try {
      $query = $this->select('node', 'n')
        ->fields('n', ['created', 'nid'])
        ->condition('n.nid', $nid)
        ->condition('n.type', 'verslag');

      $result = $query->execute()->fetchAssoc();

      if ($result) {
        // Get verslag taxonomy term if it exists
        $term_tid = $this->getVerslagTaxonomyTerm($nid);
        
        return [
          'taxonomy_term' => $term_tid,
          'date' => date('Y-m-d', $result['created']),
          'nid' => $result['nid'],
        ];
      }
    } catch (\Exception $e) {
      \Drupal::logger('thirdwing_migrate')->debug('Error checking verslag attachment for nid @nid: @error', [
        '@nid' => $nid,
        '@error' => $e->getMessage()
      ]);
    }

    return FALSE;
  }

  /**
   * Get verslag taxonomy term.
   */
  protected function getVerslagTaxonomyTerm($nid) {
    try {
      // Join with taxonomy to get verslag type (assuming vocabulary ID 3 for verslag types)
      $term_query = $this->select('term_node', 'tn')
        ->fields('tn', ['tid'])
        ->condition('tn.nid', $nid);
      
      $term_query->leftJoin('term_data', 'td', 'td.tid = tn.tid');
      $term_query->condition('td.vid', 3); // Verslag taxonomy vocabulary
      
      return $term_query->execute()->fetchField();
    } catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Check if file is attached to repertoire.
   * CORRECTED: Check all possible repertoire file field tables.
   */
  protected function checkRepertoireAttachment($fid) {
    // Check various repertoire file fields from schema analysis
    $repertoire_fields = [
      'content_field_partij_band' => 'field_partij_band_fid',
      'content_field_partij_koor_l' => 'field_partij_koor_l_fid',
      'content_field_partij_tekst' => 'field_partij_tekst_fid',
      'content_field_files' => 'field_files_fid', // General files attached to repertoire
    ];

    foreach ($repertoire_fields as $table => $field) {
      if ($this->getDatabase()->schema()->tableExists($table)) {
        try {
          $query = $this->select($table, 't')
            ->fields('t', ['nid'])
            ->condition("t.$field", $fid)
            ->range(0, 1);

          // Verify it's actually a repertoire node
          $query->leftJoin('node', 'n', 't.nid = n.nid');
          $query->condition('n.type', 'repertoire');

          $result = $query->execute()->fetchAssoc();
          if ($result) {
            return ['nid' => $result['nid'], 'field' => $field];
          }
        } catch (\Exception $e) {
          // Continue to next field if this one fails
          continue;
        }
      }
    }

    return FALSE;
  }

  /**
   * Classify document based on source and file type.
   */
  protected function classifyDocument(Row $row) {
    $source_type = $row->getSourceProperty('source_content_type');
    $extension = $row->getSourceProperty('file_extension');
    $repertoire_attachment = $row->getSourceProperty('repertoire_attachment');

    $classification = 'overig'; // Default

    if ($source_type === 'verslag') {
      $classification = 'verslag';
    } elseif ($source_type === 'repertoire' || $repertoire_attachment) {
      $classification = 'partituur';
    } elseif ($extension === 'mscz') {
      $classification = 'partituur';
    } elseif (in_array($extension, ['pdf', 'doc', 'docx'])) {
      $classification = 'document';
    }

    $row->setSourceProperty('document_classification', $classification);
  }

  /**
   * Set access terms based on file type and context.
   * CORRECTED: More granular access control based on document classification.
   */
  protected function setAccessTerms(Row $row) {
    $source_type = $row->getSourceProperty('source_content_type');
    $classification = $row->getSourceProperty('document_classification');

    // Default access terms - more restrictive for documents
    $access_terms = [217, 86, 28, 85]; // Leden, Bestuur, Beheer, etc.

    switch ($classification) {
      case 'verslag':
        // Verslagen - most restrictive access (board and admin only)
        $access_terms = [86, 28, 85]; // Bestuur, Beheer only
        break;
        
      case 'partituur':
        // Sheet music - accessible to band and choir members
        $access_terms = [217, 86, 28, 85, 218]; // Include band members
        break;
        
      case 'document':
        // General documents - standard member access
        $access_terms = [217, 86, 28, 85];
        break;
        
      default:
        // Other documents - standard access
        $access_terms = [217, 86, 28, 85];
        break;
    }

    $row->setSourceProperty('access_terms', $access_terms);
  }
}