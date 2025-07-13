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
  echo "\n🏷️  Checking bu