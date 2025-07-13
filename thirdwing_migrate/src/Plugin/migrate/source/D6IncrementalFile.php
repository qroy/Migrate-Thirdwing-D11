<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6IncrementalFile.php (UPDATED)

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Updated file source with new media bundle categorization.
 *
 * Key changes:
 * - MIDI files (.mid, .kar) moved to audio bundle
 * - MuseScore files (.mscz) remain in document bundle
 * - Sheet music bundle removed
 * - 4-bundle architecture: image, document, audio, video
 */
class D6IncrementalFile extends SqlBase {

  /**
   * File extension to media bundle mapping.
   */
  protected $extensionBundleMap = [
    // Image bundle
    'jpg' => 'image',
    'jpeg' => 'image',
    'png' => 'image',
    'gif' => 'image',
    'webp' => 'image',
    
    // Document bundle (includes MuseScore)
    'pdf' => 'document',
    'doc' => 'document',
    'docx' => 'document',
    'txt' => 'document',
    'xls' => 'document',
    'xlsx' => 'document',
    'mscz' => 'document', // MuseScore files stay in document bundle
    
    // Audio bundle (includes MIDI)
    'mp3' => 'audio',
    'wav' => 'audio',
    'ogg' => 'audio',
    'm4a' => 'audio',
    'aac' => 'audio',
    'mid' => 'audio', // MIDI files moved to audio bundle
    'kar' => 'audio', // Karaoke files moved to audio bundle
    
    // Video bundle
    'mp4' => 'video',
    'avi' => 'video',
    'mov' => 'video',
    'wmv' => 'video',
    'flv' => 'video',
  ];

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('files', 'f')
      ->fields('f')
      ->orderBy('f.fid');

    // Add file usage information
    $query->leftJoin('file_usage', 'fu', 'fu.fid = f.fid');
    $query->addField('fu', 'module');
    $query->addField('fu', 'type');
    $query->addField('fu', 'id', 'usage_id');

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
      'file_category' => $this->t('File category'),
      'file_extension' => $this->t('File extension'),
      'destination_media_bundle' => $this->t('Destination media bundle'),
      'file_usage' => $this->t('File usage information'),
      'file_exists' => $this->t('File exists on disk'),
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

    $filename = $row->getSourceProperty('filename');
    $filepath = $row->getSourceProperty('filepath');
    $filemime = $row->getSourceProperty('filemime');

    // Extract file extension
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $row->setSourceProperty('file_extension', $extension);

    // Categorize file by MIME type and extension
    $this->categorizeFile($row);

    // Get file usage information
    $this->processFileUsage($row);

    // Check if file exists on disk
    $this->checkFileExists($row);

    return TRUE;
  }

  /**
   * Categorize file based on extension and MIME type.
   */
  protected function categorizeFile(Row $row) {
    $extension = $row->getSourceProperty('file_extension');
    $filemime = $row->getSourceProperty('filemime');
    $usage = $row->getSourceProperty('file_usage');

    // Determine media bundle based on extension
    $media_bundle = $this->extensionBundleMap[$extension] ?? 'document';

    // Override based on usage context
    if (!empty($usage['user_picture'])) {
      $media_bundle = 'image';
    }

    // Categorize by MIME type for better accuracy
    $category = $this->categorizeByMime($filemime);
    
    // Ensure consistency between category and bundle
    if ($category === 'image' && $media_bundle !== 'image') {
      $media_bundle = 'image';
    } elseif ($category === 'audio' && !in_array($media_bundle, ['audio', 'document'])) {
      $media_bundle = 'audio';
    } elseif ($category === 'video' && $media_bundle !== 'video') {
      $media_bundle = 'video';
    }

    $row->setSourceProperty('file_category', $category);
    $row->setSourceProperty('destination_media_bundle', $media_bundle);
  }

  /**
   * Categorize file by MIME type.
   */
  protected function categorizeByMime($filemime) {
    if (strpos($filemime, 'image/') === 0) {
      return 'image';
    } elseif (strpos($filemime, 'audio/') === 0) {
      return 'audio';
    } elseif (strpos($filemime, 'video/') === 0) {
      return 'video';
    } else {
      return 'document';
    }
  }

  /**
   * Process file usage information.
   */
  protected function processFileUsage(Row $row) {
    $fid = $row->getSourceProperty('fid');
    
    // Get all usage instances for this file
    $usage_query = $this->select('file_usage', 'fu')
      ->fields('fu')
      ->condition('fu.fid', $fid);
    
    $usage_results = $usage_query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    
    $usage_info = [];
    foreach ($usage_results as $usage) {
      $usage_info[$usage['type']][] = [
        'module' => $usage['module'],
        'id' => $usage['id'],
        'count' => $usage['count'],
      ];
      
      // Special handling for user pictures
      if ($usage['type'] === 'user' && $usage['module'] === 'user') {
        $usage_info['user_picture'] = TRUE;
      }
    }
    
    $row->setSourceProperty('file_usage', $usage_info);
  }

  /**
   * Check if file exists on filesystem.
   */
  protected function checkFileExists(Row $row) {
    $filepath = $row->getSourceProperty('filepath');
    
    // Construct full path (adjust base path as needed)
    $full_path = DRUPAL_ROOT . '/sites/default/files/d6_migration' . '/' . $filepath;
    $row->setSourceProperty('file_exists', file_exists($full_path));
  }
}