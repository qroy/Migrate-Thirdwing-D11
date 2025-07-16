<?php
// File: modules/custom/thirdwing_migrate/scripts/create-media-bundles-and-fields.php

/**
 * Script to create media bundles and fields for Thirdwing migration.
 * 
 * This script creates the 4-bundle media architecture:
 * - image: For image files with metadata
 * - document: For PDFs, docs, and MuseScore files
 * - audio: For audio files including MIDI/karaoke
 * - video: For video files and embedded content
 * 
 * Usage: drush php:script modules/custom/thirdwing_migrate/scripts/create-media-bundles-and-fields.php
 */

use Drupal\media\Entity\MediaType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function createMediaBundlesAndFields() {
  echo "🚀 Creating Media Bundles and Fields for Thirdwing Migration...\n\n";
  
  // Create media bundles
  $bundles = createMediaBundles();
  
  // Create fields for each bundle
  foreach ($bundles as $bundle_id => $bundle_info) {
    echo "Creating fields for bundle: {$bundle_info['label']}\n";
    createBundleFields($bundle_id, $bundle_info);
  }
  
  // Create file directories
  createFileDirectories();
  
  echo "\n✅ Media bundles and fields creation complete!\n";
  echo "📋 Run verification: drush php:script modules/custom/thirdwing_migrate/scripts/verify-media-bundle-setup.php\n";
}

/**
 * Create media bundles.
 */
function createMediaBundles() {
  echo "📦 Creating media bundles...\n";
  
  $bundles = [
    'image' => [
      'label' => 'Afbeelding',
      'description' => 'Afbeeldingen en foto\'s',
      'source' => 'image',
      'source_field' => 'field_media_image',
    ],
    'document' => [
      'label' => 'Document',
      'description' => 'PDF, Word documenten en MuseScore bestanden',
      'source' => 'file',
      'source_field' => 'field_media_document',
    ],
    'audio' => [
      'label' => 'Audio',
      'description' => 'Audio bestanden inclusief MIDI en karaoke',
      'source' => 'audio_file',
      'source_field' => 'field_media_audio_file',
    ],
    'video' => [
      'label' => 'Video',
      'description' => 'Video bestanden en embedded content',
      'source' => 'oembed:video',
      'source_field' => 'field_media_oembed_video',
    ],
  ];
  
  foreach ($bundles as $bundle_id => $bundle_info) {
    $media_type = MediaType::load($bundle_id);
    
    if (!$media_type) {
      $media_type = MediaType::create([
        'id' => $bundle_id,
        'label' => $bundle_info['label'],
        'description' => $bundle_info['description'],
        'source' => $bundle_info['source'],
        'source_configuration' => [
          'source_field' => $bundle_info['source_field'],
        ],
      ]);
      
      $media_type->save();
      echo "   ✅ Created bundle: {$bundle_info['label']} ($bundle_id)\n";
    } else {
      echo "   - Bundle '$bundle_id' already exists\n";
    }
  }
  
  echo "\n";
  return $bundles;
}

/**
 * Create fields for media bundles.
 */
function createBundleFields($bundle_id, $bundle_info) {
  $field_configs = getFieldConfigurations();
  
  if (!isset($field_configs[$bundle_id])) {
    echo "   No fields defined for bundle: $bundle_id\n";
    return;
  }
  
  $fields = $field_configs[$bundle_id];
  
  foreach ($fields as $field_name => $field_config) {
    echo "   Processing field: $field_name\n";
    
    // Create field storage if it doesn't exist
    $field_storage = FieldStorageConfig::loadByName('media', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'media',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      // Add settings if they exist
      if (isset($field_config['settings'])) {
        $storage_config['settings'] = $field_config['settings'];
      }
      
      $field_storage = FieldStorageConfig::create($storage_config);
      $field_storage->save();
      echo "      ✓ Field storage created for: {$field_name}\n";
    }
    
    // Create field instance if it doesn't exist
    $field_instance = FieldConfig::loadByName('media', $bundle_id, $field_name);
    if (!$field_instance) {
      $instance_config = [
        'field_storage' => $field_storage,
        'bundle' => $bundle_id,
        'label' => $field_config['label'],
        'required' => $field_config['required'] ?? FALSE,
      ];
      
      // Add target bundles for entity reference fields
      if (isset($field_config['target_bundles'])) {
        $instance_config['settings']['handler_settings']['target_bundles'] = $field_config['target_bundles'];
      }
      
      // Add other instance settings if they exist
      if (isset($field_config['instance_settings'])) {
        $instance_config['settings'] = array_merge(
          $instance_config['settings'] ?? [],
          $field_config['instance_settings']
        );
      }
      
      $field_instance = FieldConfig::create($instance_config);
      $field_instance->save();
      echo "      ✓ Field instance created: {$field_config['label']}\n";
    } else {
      echo "      - Field '{$field_name}' already exists for {$bundle_id}\n";
    }
  }
  
  echo "\n";
}

/**
 * Get field configurations for all media bundles.
 */
function getFieldConfigurations() {
  return [
    'image' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_toegang' => [
        'type' => 'entity_reference',
        'label' => 'Toegang',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['toegang'],
      ],
    ],
    
    'document' => [
      'field_document_soort' => [
        'type' => 'entity_reference',
        'label' => 'Document Soort',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['document_soort'],
      ],
      'field_verslag_type' => [
        'type' => 'entity_reference',
        'label' => 'Verslag Type',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['verslag_type'],
      ],
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_gerelateerd_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerd Repertoire',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_toegang' => [
        'type' => 'entity_reference',
        'label' => 'Toegang',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['toegang'],
      ],
    ],
    
    'audio' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_audio_type' => [
        'type' => 'entity_reference',
        'label' => 'Audio Type',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['audio_type'],
      ],
      'field_audio_uitvoerende' => [
        'type' => 'string',
        'label' => 'Uitvoerende',
        'settings' => ['max_length' => 255],
      ],
      'field_audio_bijz' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
      'field_gerelateerd_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
      'field_gerelateerd_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerd Repertoire',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_toegang' => [
        'type' => 'entity_reference',
        'label' => 'Toegang',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['toegang'],
      ],
    ],
    
    'video' => [
      'field_media_video_file' => [
        'type' => 'file',
        'label' => 'Video Bestand',
        'settings' => [
          'file_extensions' => 'mp4 avi mov wmv flv',
          'file_directory' => 'media/video',
        ],
      ],
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_audio_type' => [
        'type' => 'entity_reference',
        'label' => 'Video Type',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['audio_type'],
      ],
      'field_audio_uitvoerende' => [
        'type' => 'string',
        'label' => 'Uitvoerende',
        'settings' => ['max_length' => 255],
      ],
      'field_gerelateerd_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
      'field_gerelateerd_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerd Repertoire',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_toegang' => [
        'type' => 'entity_reference',
        'label' => 'Toegang',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['toegang'],
      ],
    ],
  ];
}

/**
 * Create file directories for media bundles.
 */
function createFileDirectories() {
  echo "📁 Creating file directories...\n";
  
  $directories = [
    'media/image',
    'media/document',
    'media/audio',
    'media/video',
  ];
  
  $file_system = \Drupal::service('file_system');
  
  foreach ($directories as $directory) {
    $full_path = $file_system->realpath('public://') . '/' . $directory;
    
    if (!is_dir($full_path)) {
      if (mkdir($full_path, 0755, TRUE)) {
        echo "   ✅ Created directory: $directory\n";
      } else {
        echo "   ❌ Failed to create directory: $directory\n";
      }
    } else {
      echo "   - Directory already exists: $directory\n";
    }
  }
  
  echo "\n";
}

// Execute the script
try {
  createMediaBundlesAndFields();
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}