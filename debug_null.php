<?php

/**
 * Debug script to find null values causing Html::escape() errors.
 * 
 * Run with: drush php:script debug_null.php
 */

echo "=== Debugging Null Values in Taxonomy Vocabulary ===\n\n";

$migration_manager = \Drupal::service('plugin.manager.migration');

try {
  $migration = $migration_manager->createInstance('d6_thirdwing_taxonomy_vocabulary');
  $source = $migration->getSourcePlugin();
  
  echo "Source: " . get_class($source) . "\n\n";
  
  $source->rewind();
  
  while ($source->valid()) {
    $row = $source->current();
    $source_data = $row->getSource();
    
    $vid = $source_data['vid'] ?? 'unknown';
    echo "=== Vocabulary $vid ===\n";
    
    $has_nulls = false;
    foreach ($source_data as $key => $value) {
      if ($value === null) {
        echo "  ⚠ NULL: $key\n";
        $has_nulls = true;
      } elseif ($value === '') {
        echo "  ⚠ EMPTY: $key\n";
        $has_nulls = true;
      } elseif (!is_string($value) && !is_numeric($value) && $value !== null) {
        echo "  ⚠ WEIRD TYPE: $key = " . gettype($value) . "\n";
        $has_nulls = true;
      }
    }
    
    if (!$has_nulls) {
      echo "  ✓ No problematic values found\n";
    }
    
    echo "\n";
    $source->next();
  }
  
  echo "=== Testing Migration Process ===\n";
  
  // Test the actual migration executable
  $executable = new \Drupal\migrate\MigrateExecutable($migration);
  
  // Hook into the process to see what happens
  $source->rewind();
  if ($source->valid()) {
    $row = $source->current();
    
    echo "Before processing:\n";
    foreach ($row->getSource() as $key => $value) {
      $type = gettype($value);
      $display = ($value === null) ? 'NULL' : $value;
      echo "  $key: '$display' ($type)\n";
    }
    
    // This is where the error likely occurs
    try {
      echo "\nAttempting to process row...\n";
      $executable->processRow($row);
      echo "Row processed successfully!\n";
    } catch (\Exception $e) {
      echo "Error during processing: " . $e->getMessage() . "\n";
      echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
  }
  
} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
  echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Debug Complete ===\n";