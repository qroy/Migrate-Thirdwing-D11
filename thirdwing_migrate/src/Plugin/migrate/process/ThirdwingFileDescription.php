<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/process/ThirdwingFileDescription.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extract file description from D6 field_files with filename fallback.
 *
 * @MigrateProcessPlugin(
 *   id = "thirdwing_file_description"
 * )
 */
class ThirdwingFileDescription extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value) || count($value) < 2) {
      return '';
    }

    [$description, $filename] = $value;

    // Use description if available and not empty
    if (!empty(trim($description))) {
      return trim($description);
    }

    // Fallback to filename without extension
    if (!empty($filename)) {
      $name = pathinfo($filename, PATHINFO_FILENAME);
      return $name ?: $filename;
    }

    return '';
  }
}

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

    // Classify based on source content type
    if ($source_content_type === 'verslag') {
      return 'verslag';
    }

    // MuseScore files or repertoire attachments are partituren
    if ($file_extension === 'mscz' || $repertoire_attachment) {
      return 'partituur';
    }

    // Sheet music extensions
    $sheet_music_extensions = ['pdf', 'mid', 'kar'];
    if (in_array($file_extension, $sheet_music_extensions) && $source_content_type === 'repertoire') {
      return 'partituur';
    }

    // Default to general document
    return 'overig';
  }
}

// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/process/ExtractExifDate.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Extract date from EXIF data with fallbacks.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_exif_date"
 * )
 */
class ExtractExifDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value) || count($value) < 3) {
      return NULL;
    }

    [$filepath, $exif_date, $timestamp] = $value;

    // Primary: Use existing D6 EXIF date
    if (!empty($exif_date)) {
      return $this->convertExifDate($exif_date);
    }

    // Fallback 1: Extract fresh EXIF data
    if (!empty($filepath)) {
      $fresh_exif_date = $this->extractFreshExifDate($filepath);
      if ($fresh_exif_date) {
        return $fresh_exif_date;
      }
    }

    // Fallback 2: Use file timestamp
    if (!empty($timestamp)) {
      return date('Y-m-d', $timestamp);
    }

    return NULL;
  }

  /**
   * Convert EXIF date format to Drupal date format.
   */
  protected function convertExifDate($exif_date) {
    // EXIF format: "2023:12:25 14:30:15"
    if (preg_match('/^(\d{4}):(\d{2}):(\d{2})/', $exif_date, $matches)) {
      return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
    }
    return NULL;
  }

  /**
   * Extract EXIF date from image file.
   */
  protected function extractFreshExifDate($filepath) {
    $full_path = DRUPAL_ROOT . '/sites/default/files/d6_migration/' . $filepath;
    
    if (!file_exists($full_path) || !function_exists('exif_read_data')) {
      return NULL;
    }

    try {
      $exif = exif_read_data($full_path);
      
      // Try different EXIF date fields
      $date_fields = ['DateTimeOriginal', 'DateTime', 'DateTimeDigitized'];
      
      foreach ($date_fields as $field) {
        if (isset($exif[$field]) && !empty($exif[$field])) {
          return $this->convertExifDate($exif[$field]);
        }
      }
    } catch (Exception $e) {
      // Silently fail - use timestamp fallback
    }

    return NULL;
  }
}