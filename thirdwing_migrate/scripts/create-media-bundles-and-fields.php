<?php

/**
 * @file
 * CORRECTED script to create media bundles and fields exactly matching
 * "Drupal 11 Content types and fields.md" documentation.
 *
 * Usage: drush php:script create-media-bundles-and-fields.php
 */

use Drupal\media\Entity\MediaType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function createMediaBundlesAndFields() {
  echo "🚀 Creating Media Bundles and Fields (CORRECTED VERSION)...\n\n";
  
  // Get media bundle configurations
  $media_bundles = getMediaBundleConfigurations();
  
  // Step 1: Create media bundles
  echo "📦 Creating media bundles...\n";
  foreach ($media_bundles as $bundle_id => $config) {
    createMediaBundle($bundle_id, $config);
  }
  
  // Step 2: Create fields for media bundles
  echo "\n📋 Creating fields for media bundles...\n";
  foreach ($media_bundles as $bundle_id => $config) {
    if (isset($config['fields'])) {
      echo "Creating fields for media bundle: {$config['name']}\n";
      createFieldsForMediaBundle($bundle_id, $config['fields']);
    }
  }
  
  echo "\n✅ Media bundles and fields creation complete!\n";
  printMediaSummary();
}

/**
 * Create a media bundle.
 */
function createMediaBundle($bundle_id, $config) {
  $media_type = MediaType::load($bundle_id);
  
  if (!$media_type) {
    $media_type = MediaType::create([
      'id' => $bundle_id,
      'label' => $config['name'],
      'description' => $config['description'],
      'source' => $config['source_plugin'],
      'source_configuration' => $config['source_configuration'] ?? [],
    ]);
    
    $media_type->save();
    echo "  ✅ Created media bundle: {$config['name']} ({$bundle_id})\n";
    
    // Set the source field
    if (isset($config['source_field'])) {
      $media_type->set('source_configuration', [
        'source_field' => $config['source_field']
      ]);
      $media_type->save();
    }
  } else {
    echo "  - Media bundle '{$bundle_id}' already exists\n";
  }
}

/**
 * Create fields for a media bundle.
 */
function createFieldsForMediaBundle($bundle_id, $fields) {
  foreach ($fields as $field_name => $field_config) {
    echo "    Processing field: {$field_name}\n";
    
    // Create field storage if it doesn't exist
    $field_storage = FieldStorageConfig::loadByName('media', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'media',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      // Add storage settings if they exist
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
 * Get media bundle configurations from documentation.
 */
function getMediaBundleConfigurations() {
  return [
    'image' => [
      'name' => 'Image',
      'description' => 'Photos, graphics, and images (replaces Image content type)',
      'source_plugin' => 'image',
      'source_field' => 'field_media_image',
      'fields' => [
        'field_media_image' => [
          'type' => 'image',
          'label' => 'Afbeelding',
          'cardinality' => 1,
          'required' => TRUE,
          'settings' => [
            'file_extensions' => 'jpg jpeg png gif webp',
            'max_filesize' => '10 MB',
            'max_resolution' => '3000x3000',
            'min_resolution' => '100x100'
          ]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'settings' => ['datetime_type' => 'date']
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang']
        ]
      ]
    ],
    
    'document' => [
      'name' => 'Document',
      'description' => 'PDFs, sheet music, and document files',
      'source_plugin' => 'file',
      'source_field' => 'field_media_document',
      'fields' => [
        'field_media_document' => [
          'type' => 'file',
          'label' => 'Document',
          'cardinality' => 1,
          'required' => TRUE,
          'settings' => [
            'file_extensions' => 'pdf doc docx txt rtf odt ods xls xlsx csv mscz mscx xml ly',
            'max_filesize' => '50 MB'
          ]
        ],
        'field_doc_categorie' => [
          'type' => 'list_string',
          'label' => 'Categorie',
          'cardinality' => 1,
          'settings' => [
            'allowed_values' => [
              'partituur' => 'Partituur',
              'tekst' => 'Tekst/Koorregie',
              'band' => 'Bandpartituur',
              'document' => 'Document',
              'notulen' => 'Notulen',
              'financieel' => 'Financieel',
              'overig' => 'Overig'
            ]
          ]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'settings' => ['datetime_type' => 'date']
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang']
        ]
      ]
    ],
    
    'audio' => [
      'name' => 'Audio',
      'description' => 'Audio recordings, rehearsals, and concerts',
      'source_plugin' => 'audio_file',
      'source_field' => 'field_media_audio_file',
      'fields' => [
        'field_media_audio_file' => [
          'type' => 'file',
          'label' => 'Audio File',
          'cardinality' => 1,
          'required' => TRUE,
          'settings' => [
            'file_extensions' => 'mp3 wav ogg m4a aac flac',
            'max_filesize' => '100 MB'
          ]
        ],
        'field_audio_type' => [
          'type' => 'list_string',
          'label' => 'Type',
          'cardinality' => 1,
          'settings' => [
            'allowed_values' => [
              'rehearsal' => 'Repetitie',
              'concert' => 'Concert',
              'interview' => 'Interview',
              'demo' => 'Demo',
              'other' => 'Overig'
            ]
          ]
        ],
        'field_audio_uitvoerende' => [
          'type' => 'string',
          'label' => 'Uitvoerende',
          'cardinality' => 1,
          'settings' => ['max_length' => 255]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'settings' => ['datetime_type' => 'datetime']
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang']
        ]
      ]
    ],
    
    'video' => [
      'name' => 'Video',
      'description' => 'Video recordings and multimedia content',
      'source_plugin' => 'video_file',
      'source_field' => 'field_media_video_file',
      'fields' => [
        'field_media_video_file' => [
          'type' => 'file',
          'label' => 'Video File',
          'cardinality' => 1,
          'required' => TRUE,
          'settings' => [
            'file_extensions' => 'mp4 avi mov wmv flv webm mkv',
            'max_filesize' => '500 MB'
          ]
        ],
        'field_video_type' => [
          'type' => 'list_string',
          'label' => 'Type',
          'cardinality' => 1,
          'settings' => [
            'allowed_values' => [
              'concert' => 'Concert',
              'rehearsal' => 'Repetitie',
              'interview' => 'Interview',
              'promotional' => 'Promotie',
              'documentary' => 'Documentaire',
              'other' => 'Overig'
            ]
          ]
        ],
        'field_video_uitvoerende' => [
          'type' => 'string',
          'label' => 'Uitvoerende',
          'cardinality' => 1,
          'settings' => ['max_length' => 255]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'settings' => ['datetime_type' => 'datetime']
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang']
        ]
      ]
    ]
  ];
}

/**
 * Print summary of created media bundles.
 */
function printMediaSummary() {
  $media_bundles = getMediaBundleConfigurations();
  
  echo "\n📊 Media Bundle Summary:\n";
  echo "  • Total Media Bundles: " . count($media_bundles) . "\n\n";
  
  echo "📋 Media Bundles Created:\n";
  foreach ($media_bundles as $bundle_id => $config) {
    $field_count = isset($config['fields']) ? count($config['fields']) : 0;
    echo "  • {$config['name']} ({$bundle_id}): {$field_count} fields\n";
    echo "    - Source: {$config['source_plugin']}\n";
    echo "    - Source Field: {$config['source_field']}\n";
    
    if (isset($config['fields'])) {
      foreach ($config['fields'] as $field_name => $field_config) {
        if ($field_name === $config['source_field']) {
          echo "    - ⭐ {$field_config['label']} (source field)\n";
        } else {
          echo "    - {$field_config['label']}\n";
        }
      }
    }
    echo "\n";
  }
  
  echo "📋 File Extensions Supported:\n";
  echo "  • Images: jpg, jpeg, png, gif, webp\n";
  echo "  • Documents: pdf, doc, docx, txt, rtf, odt, ods, xls, xlsx, csv, mscz, mscx, xml, ly\n";
  echo "  • Audio: mp3, wav, ogg, m4a, aac, flac\n";
  echo "  • Video: mp4, avi, mov, wmv, flv, webm, mkv\n\n";
  
  echo "📋 Next Steps:\n";
  echo "  1. Run: drush php:script create-user-profile-fields.php\n";
  echo "  2. Verify: drush entity:info media\n";
  echo "  3. Check: /admin/structure/media\n";
  echo "  4. Configure display settings if needed\n";
}

// Execute the script
try {
  createMediaBundlesAndFields();
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}