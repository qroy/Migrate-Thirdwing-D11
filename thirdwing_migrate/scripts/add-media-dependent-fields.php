<?php

/**
 * @file
 * Script to add media-dependent fields to content types.
 * Run this AFTER media bundles have been created.
 *
 * Usage: drush php:script add-media-dependent-fields.php
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function addMediaDependentFields() {
  echo "🚀 Adding Media-Dependent Fields to Content Types...\n\n";
  
  // Check if media entity type exists
  if (!checkMediaEntityExists()) {
    echo "❌ Media entity type not found. Please run create-media-bundles-and-fields.php first.\n";
    exit(1);
  }
  
  // Check if required media bundles exist
  if (!checkMediaBundlesExist()) {
    echo "❌ Required media bundles not found. Please run create-media-bundles-and-fields.php first.\n";
    exit(1);
  }
  
  // Create media-dependent shared field storages
  echo "📋 Creating media-dependent shared field storages...\n";
  createMediaDependentSharedFields();
  
  // Attach media-dependent fields to content types
  echo "\n🔗 Attaching media-dependent fields to content types...\n";
  attachMediaDependentFieldsToContentTypes();
  
  echo "\n✅ Media-dependent fields added successfully!\n";
  printMediaFieldsSummary();
}

/**
 * Check if media entity type exists.
 */
function checkMediaEntityExists() {
  try {
    $entity_type_manager = \Drupal::entityTypeManager();
    $media_definition = $entity_type_manager->getDefinition('media');
    return $media_definition !== null;
  } catch (Exception $e) {
    return false;
  }
}

/**
 * Check if required media bundles exist.
 */
function checkMediaBundlesExist() {
  $required_bundles = ['image', 'document'];
  $missing_bundles = [];
  
  foreach ($required_bundles as $bundle) {
    if (!\Drupal\media\Entity\MediaType::load($bundle)) {
      $missing_bundles[] = $bundle;
    }
  }
  
  if (!empty($missing_bundles)) {
    echo "❌ Missing media bundles: " . implode(', ', $missing_bundles) . "\n";
    return false;
  }
  
  echo "✅ Required media bundles found: " . implode(', ', $required_bundles) . "\n";
  return true;
}

/**
 * Create media-dependent shared fields.
 */
function createMediaDependentSharedFields() {
  $media_fields = getMediaDependentSharedFields();
  
  foreach ($media_fields as $field_name => $field_config) {
    echo "  Creating media-dependent field storage: {$field_name}\n";
    
    $field_storage = FieldStorageConfig::loadByName('node', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'node',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      if (isset($field_config['settings'])) {
        $storage_config['settings'] = $field_config['settings'];
      }
      
      $field_storage = FieldStorageConfig::create($storage_config);
      $field_storage->save();
      echo "