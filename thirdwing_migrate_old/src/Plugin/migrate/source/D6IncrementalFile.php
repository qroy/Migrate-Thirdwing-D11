<?php
// FIXED: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6IncrementalFile.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Updated file source with corrected media bundle categorization.
 *
 * Key fixes:
 * - MIDI files (.mid, .kar) moved to audio bundle
 * - MuseScore files (.mscz) remain in document bundle
 * - Sheet music bundle removed
 * - 4-bundle architecture: image, document, audio, video
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_incremental_file",
 *   source_module = "file"
 * )
 */
class D6IncrementalFile extends SqlBase {

  /**
   * FIXED: File extension to media bundle mapping.
   */
  protected static $bundleMapping = [
    // Image bundle
    'jpg' => 'image',
    'jpeg' => 'image', 
    'png' => 'image',
    'gif' => 'image',
    'webp' => 'image',
    'bmp' => 'image',
    'tiff' => 'image',
    
    // Document bundle (includes MuseScore files)
    'pdf' => 'document',
    'doc' => 'document',
    'docx' => 'document',
    'txt' => 'document',
    'rtf' => 'document',
    'xls' => 'document',
    'xlsx' => 'document',
    'ppt' => 'document',
    'pptx' => 'document',
    'mscz' => 'document', // FIXED: MuseScore files in document bundle
    'xml' => 'document',
    
    // Audio bundle (includes MIDI files)
    'mp3' => 'audio',
    'wav' => 'audio',
    'ogg' => 'audio',
    'flac' => 'audio',
    'aac' => 'audio',
    'm4a' => 'audio',
    'wma' => 'audio',
    'mid' => 'audio', // FIXED: MIDI files moved to audio bundle
    'kar' => 'audio', // FIXED: Karaoke MIDI files in audio bundle
    'midi' => 'audio',
    
    // Video bundle
    'mp4' => 'video',
    'avi' => 'video',
    'mov' => 'video',
    'wmv' => 'video',
    'flv' => 'video',
    'webm' => 'video',
    'mkv' => 'video',
    '3gp' => 'video',
  ];

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('files', 'f')
      ->fields('f')
      ->condition('f.status', 1)
      ->orderBy('f.fid');

    // FIXED: Add incremental filtering based on configuration
    $since_timestamp = $this->configuration['since_timestamp'] ?? null;
    if ($since_timestamp) {
      $query->condition('f.timestamp', $since_timestamp, '>=');
    }

    // Date range filtering if specified
    $date_range = $this->configuration['date_range'] ?? [];
    if (!empty($date_range['start'])) {
      $query->condition('f.timestamp', strtotime($date_range['start']), '>=');
    }
    if (!empty($date_range['end'])) {
      $query->condition('f.timestamp', strtotime($date_range['end']), '<=');
    }

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
      'file_extension' => $this->t('File extension'),
      'media_bundle' => $this->t('Target media bundle'),
      'source_changed' => $this->t('Source changed timestamp'),
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

    // FIXED: Extract file extension and determine media bundle
    $filename = $row->getSourceProperty('filename');
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $row->setSourceProperty('file_extension', $file_extension);

    // FIXED: Determine media bundle based on corrected mapping
    $media_bundle = self::$bundleMapping[$file_extension] ?? 'document';
    $row->setSourceProperty('media_bundle', $media_bundle);

    // Set source changed timestamp for incremental tracking
    $row->setSourceProperty('source_changed', $row->getSourceProperty('timestamp'));

    // Clean up file path for D11 compatibility
    $filepath = $row->getSourceProperty('filepath');
    if ($filepath) {
      // Convert D6 file paths to D11 format
      $clean_path = str_replace(['sites/default/files/', 'sites/thirdwing.nl/files/'], '', $filepath);
      $row->setSourceProperty('clean_filepath', $clean_path);
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) {
    return $this->query()->countQuery()->execute()->fetchField();
  }

  /**
   * Get files by media bundle type for targeted migration.
   *
   * @param string $bundle
   *   The media bundle type (image, document, audio, video).
   *
   * @return array
   *   Array of file extensions for the specified bundle.
   */
  public static function getExtensionsForBundle($bundle) {
    return array_keys(array_filter(self::$bundleMapping, function($mapped_bundle) use ($bundle) {
      return $mapped_bundle === $bundle;
    }));
  }

  /**
   * FIXED: Get the correct media bundle for a file extension.
   *
   * @param string $extension
   *   The file extension.
   *
   * @return string
   *   The media bundle name.
   */
  public static function getBundleForExtension($extension) {
    return self::$bundleMapping[strtolower($extension)] ?? 'document';
  }
}