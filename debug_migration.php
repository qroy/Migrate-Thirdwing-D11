<?php

/**
 * Debug script to identify the source of numeric warnings.
 * 
 * Run with: drush php:script debug_migration.php
 */

use Drupal\Core\Database\Database;
use Drupal\migrate\Plugin\MigrationInterface;

echo "=== Migration Debug Analysis ===\n\n";

// Get the migration
$migration_manager = \Drupal::service('plugin.manager.migration');

try {
  $migration = $migration_manager->createInstance('d6_thirdwing_taxonomy_vocabulary');
  $source = $migration->getSourcePlugin();
  
  echo "Migration ID: " . $migration->id() . "\n";
  echo "Source plugin: " . get_class($source) . "\n\n";
  
  // Test the source query
  echo "=== Testing source data ===\n";
  $source->rewind();
  $count = 0;
  
  while ($source->valid() && $count < 5) {
    $row = $source->current();
    $source_data = $row->getSource();
    
    echo "Row " . ($count + 1) . ":\n";
    foreach ($source_data as $key => $value) {
      $type = gettype($value);
      $display_value = ($value === null) ? 'NULL' : $value;
      echo "  $key: $display_value ($type)\n";
      
      // Check for problematic values
      if ($type === 'string' && is_numeric($display_value) === false && $display_value !== '') {
        echo "    ⚠ Non-numeric string in potentially numeric field\n";
      }
      if ($type === 'string' && $display_value === '') {
        echo "    ⚠ Empty string\n";
      }
    }
    echo "\n";
    
    $source->next();
    $count++;
  }
  
  // Test the migration process pipeline
  echo "=== Testing migration process ===\n";
  $process = $migration->getProcess();
  
  foreach ($process as $destination_key => $process_config) {
    echo "Destination: $destination_key\n";
    if (is_array($process_config)) {
      if (isset($process_config['plugin'])) {
        echo "  Plugin: " . $process_config['plugin'] . "\n";
      }
      if (isset($process_config['source'])) {
        echo "  Source: " . $process_config['source'] . "\n";
      }
    } else {
      echo "  Direct mapping: $process_config\n";
    }
  }
  
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
  echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Check database values directly
echo "\n=== Direct database check ===\n";
try {
  $migrate_db = Database::getConnection('default', 'migrate');
  
  $query = $migrate_db->select('vocabulary', 'v')
    ->fields('v')
    ->range(0, 3);
  
  $results = $query->execute()->fetchAll();
  
  foreach ($results as $row) {
    echo "Vocabulary ID {$row->vid}:\n";
    foreach ($row as $field => $value) {
      $type = gettype($value);
      $display_value = ($value === null) ? 'NULL' : $value;
      echo "  $field: $display_value ($type)\n";
    }
    echo "\n";
  }
  
} catch (Exception $e) {
  echo "Database error: " . $e->getMessage() . "\n";
}

echo "=== Debug complete ===\n";