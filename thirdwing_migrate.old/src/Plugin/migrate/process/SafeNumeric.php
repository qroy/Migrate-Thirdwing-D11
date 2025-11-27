<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin for safe numeric conversion.
 *
 * @MigrateProcessPlugin(
 *   id = "safe_numeric"
 * )
 */
class SafeNumeric extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Handle null values
    if ($value === null || $value === '') {
      return 0;
    }
    
    // If already numeric, convert to int
    if (is_numeric($value)) {
      return (int) $value;
    }
    
    // If string, try to extract numbers
    if (is_string($value)) {
      $numeric_value = preg_replace('/[^0-9.-]/', '', $value);
      if (is_numeric($numeric_value) && $numeric_value !== '') {
        return (int) $numeric_value;
      }
    }
    
    // Log problematic values
    \Drupal::logger('thirdwing_migrate')->warning('Could not convert value to numeric: @value (type: @type)', [
      '@value' => print_r($value, TRUE),
      '@type' => gettype($value),
    ]);
    
    return 0;
  }
}