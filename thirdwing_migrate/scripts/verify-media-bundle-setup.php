<?php
// File: modules/custom/thirdwing_migrate/scripts/verify-media-bundle-setup.php

/**
 * Verification script for media bundle implementation.
 * 
 * Usage: drush php:script modules/custom/thirdwing_migrate/scripts/verify-media-bundle-setup.php
 */

use Drupal\media\Entity\MediaType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Main verification function.
 */
function verifyMediaBundleSetup() {
  echo "🔍 Verifying Media Bundle Implementation...\n\n";
  
  $results = [
    'bundles' => verifyMediaBundles(),
    'fields' => verifyBundleFields(),
    'directories' => verifyFileDirectories(),
    'migrations' => verifyMigrationConfigs(),
  ];
  
  displayResults($results);
}

/**
 * Verify media bundles exist.
 */
function verifyMediaBundles() {
  echo "📦 Checking media bundles...\n";
  
  $expected_bundles = [
    'image' => 'Afbeelding',
    'document' => 'Document',
    'audio' => 'Audio',
    'video' => 'Video',
  ];
  
  $results = [];
  
  foreach ($expected_bundles as $bundle_id => $expected_label) {
    $media_type = MediaType::load($bundle_id);
    
    if ($media_type) {
      $label = $media_type->label();
      $source = $media_type->getSource()->getPluginId();
      
      echo "   ✅ Bundle '$bundle_id': $label (source: $source)\n";
      $results[$bundle_id] = [
        'exists' => TRUE,
        'label' => $label,
        'source' => $source,
      ];
    } else {
      echo "   ❌ Bundle '$bundle_id': Missing\n";
      $results[$bundle_id] = ['exists' => FALSE];
    }
  }
  
  return $results;
}

/**
 * Verify bundle-specific fields.
 */
function verifyBundleFields() {
  echo "\n🏷️  Checking bundle fields...\n";
  
  $expected_fields = [
    'image' => [
      'field_media_image' => 'source',
      'field_datum' => 'custom',
      'field_toegang' => 'custom',
    ],
    'document' => [
      'field_media_document' => 'source',
      'field_document_soort' => 'custom',
      'field_verslag_type' => 'custom',
      'field_datum' => 'custom',
      'field_gerelateerd_repertoire' => 'custom',
      'field_toegang' => 'custom',
    ],
    'audio' => [
      'field_media_audio_file' => 'source',
      'field_datum' => 'custom',
      'field_audio_type' => 'custom',
      'field_audio_uitvoerende' => 'custom',
      'field_audio_bijz' => 'custom',
      'field_gerelateerd_activiteit' => 'custom',
      'field_gerelateerd_repertoire' => 'custom',
      'field_toegang' => 'custom',
    ],
    'video' => [
      'field_media_oembed_video' => 'source',
      'field_media_video_file' => 'custom',
      'field_datum' => 'custom',
      'field_audio_type' => 'custom',
      'field_audio_uitvoerende' => 'custom',
      'field_gerelateerd_activiteit' => 'custom',
      'field_gerelateerd_repertoire' => 'custom',
      'field_toegang' => 'custom',
    ],
  ];
  
  $results = [];
  
  foreach ($expected_fields as $bundle => $fields) {
    echo "   📁 Bundle: $bundle\n";
    $results[$bundle] = [];
    
    foreach ($fields as $field_name => $field_type) {
      $field = FieldConfig::loadByName('media', $bundle, $field_name);
      
      if ($field) {
        $label = $field->getLabel();
        $type = $field->getType();
        
        echo "      ✅ $field_name: $label ($type)\n";
        $results[$bundle][$field_name] = [
          'exists' => TRUE,
          'label' => $label,
          'type' => $type,
        ];
      } else {
        echo "      ❌ $field_name: Missing\n";
        $results[$bundle][$field_name] = ['exists' => FALSE];
      }
    }
  }
  
  return $results;
}

/**
 * Verify file directories exist.
 */
function verifyFileDirectories() {
  echo "\n📁 Checking file directories...\n";
  
  $expected_directories = [
    'media/image',
    'media/document',
    'media/audio',
    'media/video',
  ];
  
  $results = [];
  $files_path = \Drupal::service('file_system')->realpath('public://');
  
  foreach ($expected_directories as $directory) {
    $full_path = $files_path . '/' . $directory;
    
    if (is_dir($full_path)) {
      $permissions = substr(sprintf('%o', fileperms($full_path)), -4);
      echo "   ✅ Directory: $directory (permissions: $permissions)\n";
      $results[$directory] = [
        'exists' => TRUE,
        'path' => $full_path,
        'permissions' => $permissions,
      ];
    } else {
      echo "   ❌ Directory: $directory (missing)\n";
      $results[$directory] = ['exists' => FALSE];
      
      // Attempt to create directory
      if (mkdir($full_path, 0755, TRUE)) {
        echo "   ✅ Created directory: $directory\n";
        $results[$directory]['created'] = TRUE;
      } else {
        echo "   ❌ Failed to create directory: $directory\n";
        $results[$directory]['created'] = FALSE;
      }
    }
  }
  
  return $results;
}

/**
 * Verify migration configurations exist.
 */
function verifyMigrationConfigs() {
  echo "\n🔄 Checking migration configurations...\n";
  
  $expected_migrations = [
    'd6_thirdwing_media_image',
    'd6_thirdwing_media_document',
    'd6_thirdwing_media_audio',
    'd6_thirdwing_media_video',
  ];
  
  $results = [];
  $migration_manager = \Drupal::service('plugin.manager.migration');
  
  foreach ($expected_migrations as $migration_id) {
    try {
      $migration = $migration_manager->createInstance($migration_id);
      
      if ($migration) {
        $label = $migration->label();
        $source_plugin = $migration->getSourcePlugin()->getPluginId();
        
        echo "   ✅ Migration: $migration_id ($label)\n";
        echo "      Source: $source_plugin\n";
        
        $results[$migration_id] = [
          'exists' => TRUE,
          'label' => $label,
          'source' => $source_plugin,
        ];
      }
    } catch (Exception $e) {
      echo "   ❌ Migration: $migration_id (error: " . $e->getMessage() . ")\n";
      $results[$migration_id] = [
        'exists' => FALSE,
        'error' => $e->getMessage(),
      ];
    }
  }
  
  return $results;
}

/**
 * Display verification results summary.
 */
function displayResults($results) {
  echo "\n" . str_repeat("=", 60) . "\n";
  echo "📊 VERIFICATION SUMMARY\n";
  echo str_repeat("=", 60) . "\n\n";
  
  // Bundle summary
  $bundle_count = count(array_filter($results['bundles'], function($b) { 
    return $b['exists'] ?? FALSE; 
  }));
  echo "📦 Media Bundles: $bundle_count/4 created\n";
  
  // Field summary
  $field_counts = [];
  foreach ($results['fields'] as $bundle => $fields) {
    $created = count(array_filter($fields, function($f) { 
      return $f['exists'] ?? FALSE; 
    }));
    $total = count($fields);
    $field_counts[] = "$created/$total";
    echo "🏷️  Fields ($bundle): $created/$total created\n";
  }
  
  // Directory summary
  $dir_count = count(array_filter($results['directories'], function($d) { 
    return $d['exists'] ?? FALSE; 
  }));
  echo "📁 Directories: $dir_count/4 exist\n";
  
  // Migration summary
  $migration_count = count(array_filter($results['migrations'], function($m) { 
    return $m['exists'] ?? FALSE; 
  }));
  echo "🔄 Migrations: $migration_count/4 configured\n";
  
  // Overall status
  $total_success = $bundle_count + $dir_count + $migration_count;
  $total_possible = 12; // 4 bundles + 4 directories + 4 migrations
  
  echo "\n📈 Overall Status: $total_success/$total_possible components ready\n";
  
  if ($total_success === $total_possible) {
    echo "\n🎉 SUCCESS: Media bundle implementation is complete!\n";
    echo "✅ Ready to run migrations\n\n";
    
    echo "🚀 Next steps:\n";
    echo "   1. drush migrate:import d6_thirdwing_media_image\n";
    echo "   2. drush migrate:import d6_thirdwing_media_document\n";
    echo "   3. drush migrate:import d6_thirdwing_media_audio\n";
    echo "   4. drush migrate:import d6_thirdwing_media_video\n";
  } else {
    echo "\n⚠️  WARNING: Some components are missing\n";
    echo "❗ Please review the errors above and re-run setup scripts\n";
  }
  
  echo "\n" . str_repeat("-", 60) . "\n";
}

// Execute verification
try {
  verifyMediaBundleSetup();
} catch (Exception $e) {
  echo "❌ Verification failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}