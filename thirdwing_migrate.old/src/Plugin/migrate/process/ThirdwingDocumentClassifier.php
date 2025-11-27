<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/process/ThirdwingDocumentClassifier.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Classify documents based on source content type and file extension.
 *
 * @MigrateProcessPlugin(
 *   id = "thirdwing_document_classifier"
 * )
 */
class ThirdwingDocumentClassifier extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value) || count($value) < 3) {
      return 'overig';
    }

    [$source_content_type, $file_extension, $repertoire_attachment] = $value;
    
    // Get additional context from the row
    $field_name = $row->getSourceProperty('source_field_name');
    $usage_type = $row->getSourceProperty('type');
    $usage_id = $row->getSourceProperty('usage_id');

    // Priority 1: Verslag documents
    if ($source_content_type === 'verslag') {
      return 'verslag';
    }

    // Priority 2: Huiswerk files (attached to activities via field_huiswerk)
    if ($this->isHuiswerkFile($source_content_type, $field_name, $usage_type, $usage_id)) {
      return 'huiswerk';
    }

    // Priority 3: Sheet music (MuseScore files or repertoire attachments)
    if ($file_extension === 'mscz' || $repertoire_attachment) {
      return 'partituur';
    }

    // Priority 4: Sheet music from repertoire context
    $sheet_music_extensions = ['pdf', 'mid', 'kar'];
    if (in_array($file_extension, $sheet_music_extensions) && $source_content_type === 'repertoire') {
      return 'partituur';
    }

    // Default: General document
    return 'overig';
  }

  /**
   * Check if file is a huiswerk (homework) file.
   *
   * Huiswerk files are documents attached to Activiteit nodes via field_huiswerk.
   *
   * @param string $source_content_type
   *   The source content type.
   * @param string $field_name
   *   The field name used for attachment.
   * @param string $usage_type
   *   The file usage type.
   * @param int $usage_id
   *   The file usage ID (node ID).
   *
   * @return bool
   *   TRUE if this is a huiswerk file.
   */
  protected function isHuiswerkFile($source_content_type, $field_name, $usage_type, $usage_id) {
    // Method 1: Direct field name detection
    if ($field_name === 'field_huiswerk') {
      return TRUE;
    }

    // Method 2: Check if file is attached to an activiteit node via field_huiswerk
    if ($usage_type === 'node' && !empty($usage_id)) {
      return $this->checkHuiswerkAttachment($usage_id);
    }

    // Method 3: Source content type indicates activiteit context
    if ($source_content_type === 'activiteit' && $this->isHuiswerkContext($usage_id)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Check if a node ID corresponds to an activiteit with huiswerk attachment.
   *
   * @param int $nid
   *   The node ID to check.
   *
   * @return bool
   *   TRUE if this node is an activiteit with huiswerk files.
   */
  protected function checkHuiswerkAttachment($nid) {
    if (!$nid) {
      return FALSE;
    }

    try {
      // Get database connection
      $database = \Drupal::database();
      
      // Check if this node is an activiteit type
      $query = $database->select('node', 'n')
        ->fields('n', ['type'])
        ->condition('n.nid', $nid)
        ->range(0, 1);
      
      $node_type = $query->execute()->fetchField();
      
      if ($node_type !== 'activiteit') {
        return FALSE;
      }

      // Check if this activiteit has huiswerk files in content_type_activiteit table
      $huiswerk_query = $database->select('content_type_activiteit', 'cta')
        ->fields('cta', ['field_huiswerk_fid'])
        ->condition('cta.nid', $nid)
        ->condition('cta.field_huiswerk_fid', 0, '<>')
        ->range(0, 1);
      
      $huiswerk_fid = $huiswerk_query->execute()->fetchField();
      
      return !empty($huiswerk_fid);
      
    } catch (\Exception $e) {
      // Log error but don't fail migration
      \Drupal::logger('thirdwing_migrate')->debug('Error checking huiswerk attachment for nid @nid: @error', [
        '@nid' => $nid,
        '@error' => $e->getMessage()
      ]);
      return FALSE;
    }
  }

  /**
   * Check if usage context indicates huiswerk files.
   *
   * @param int $usage_id
   *   The usage ID (typically node ID).
   *
   * @return bool
   *   TRUE if context suggests huiswerk files.
   */
  protected function isHuiswerkContext($usage_id) {
    // Additional contextual checks can be added here
    // For now, rely on the more specific methods above
    return FALSE;
  }
}