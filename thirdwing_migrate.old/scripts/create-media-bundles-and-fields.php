<?php

/**
 * @file
 * GECORRIGEERD script om media bundles en velden aan te maken volgens documentatie.
 * Gebaseerd op "Drupal 11 Content types and fields.md" documentatie.
 *
 * Usage: drush php:script create-media-bundles-and-fields.php
 */

use Drupal\media\Entity\MediaType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Hoofduitvoeringsfunctie.
 */
function createMediaBundlesAndFields() {
  echo "🚀 Aanmaken van Media Bundles en Velden (GECORRIGEERDE VERSIE)...\n\n";
  
  // Krijg media bundle configuraties
  $media_bundles = getMediaBundleConfigurations();
  
  // Stap 1: Maak media bundles aan
  echo "📦 Aanmaken van media bundles...\n";
  foreach ($media_bundles as $bundle_id => $config) {
    createMediaBundle($bundle_id, $config);
  }
  
  // Stap 2: Maak velden aan voor media bundles
  echo "\n📋 Aanmaken van velden voor media bundles...\n";
  foreach ($media_bundles as $bundle_id => $config) {
    if (isset($config['fields'])) {
      echo "Aanmaken van velden voor media bundle: {$config['name']}\n";
      createFieldsForMediaBundle($bundle_id, $config['fields']);
    }
  }
  
  echo "\n✅ Media bundles en velden aanmaak voltooid!\n";
  printMediaSummary();
}

/**
 * Maak een media bundle aan.
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
    echo "  ✅ Aangemaakt media bundle: {$config['name']} ({$bundle_id})\n";
    
    // Stel het source veld in
    if (isset($config['source_field'])) {
      $source_config = $media_type->get('source_configuration');
      $source_config['source_field'] = $config['source_field'];
      $media_type->set('source_configuration', $source_config);
      $media_type->save();
      echo "    ✓ Source veld ingesteld: {$config['source_field']}\n";
    }
  } else {
    echo "  - Media bundle '{$bundle_id}' bestaat al\n";
  }
}

/**
 * Maak velden aan voor een media bundle.
 */
function createFieldsForMediaBundle($bundle_id, $fields) {
  foreach ($fields as $field_name => $field_config) {
    echo "    Verwerken van veld: {$field_name}\n";
    
    // Maak field storage aan als deze niet bestaat
    $field_storage = FieldStorageConfig::loadByName('media', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'media',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      // Voeg storage settings toe indien aanwezig
      if (isset($field_config['storage_settings'])) {
        $storage_config['settings'] = $field_config['storage_settings'];
      }
      
      $field_storage = FieldStorageConfig::create($storage_config);
      $field_storage->save();
      echo "      ✓ Field storage aangemaakt voor: {$field_name}\n";
    } else {
      echo "      - Field storage '{$field_name}' bestaat al\n";
    }
    
    // Maak field instance aan als deze niet bestaat
    $field_instance = FieldConfig::loadByName('media', $bundle_id, $field_name);
    if (!$field_instance) {
      $instance_config = [
        'field_storage' => $field_storage,
        'bundle' => $bundle_id,
        'label' => $field_config['label'],
        'required' => $field_config['required'] ?? FALSE,
      ];
      
      // Voeg target bundles toe voor entity reference velden
      if (isset($field_config['target_bundles'])) {
        $instance_config['settings']['handler_settings']['target_bundles'] = $field_config['target_bundles'];
      }
      
      // Voeg andere instance settings toe indien aanwezig
      if (isset($field_config['instance_settings'])) {
        $instance_config['settings'] = array_merge(
          $instance_config['settings'] ?? [],
          $field_config['instance_settings']
        );
      }
      
      $field_instance = FieldConfig::create($instance_config);
      $field_instance->save();
      echo "      ✓ Field instance aangemaakt: {$field_config['label']}\n";
    } else {
      echo "      - Veld '{$field_name}' bestaat al voor {$bundle_id}\n";
    }
  }
  
  echo "\n";
}

/**
 * Krijg media bundle configuraties volgens documentatie.
 */
function getMediaBundleConfigurations() {
  return [
    'image' => [
      'name' => 'Image',
      'description' => 'Lokaal opgeslagen afbeeldingen',
      'source_plugin' => 'image',
      'source_field' => 'field_media_image',
      'fields' => [
        'field_media_image' => [
          'type' => 'image',
          'label' => 'Afbeelding',
          'cardinality' => 1,
          'required' => TRUE,
          'storage_settings' => [
            'default_image' => [
              'uuid' => NULL,
              'alt' => '',
              'title' => '',
              'width' => NULL,
              'height' => NULL,
            ],
            'target_type' => 'file',
            'display_field' => FALSE,
            'display_default' => FALSE,
            'uri_scheme' => 'public',
          ],
          'instance_settings' => [
            'file_extensions' => 'png gif jpg jpeg',
            'file_directory' => 'images/[date:custom:Y]-[date:custom:m]',
            'max_filesize' => '10 MB',
            'max_resolution' => '2000x2000',
            'min_resolution' => '',
            'alt_field' => TRUE,
            'alt_field_required' => TRUE,
            'title_field' => FALSE,
            'title_field_required' => FALSE,
          ]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'datetime_type' => 'datetime'
          ]
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => -1,
          'required' => FALSE,
          'storage_settings' => [
            'target_type' => 'user_role'
          ],
          'target_bundles' => NULL
        ]
      ]
    ],
    
    'audio' => [
      'name' => 'Audio',
      'description' => 'Lokaal opgeslagen audiobestanden',
      'source_plugin' => 'audio_file',
      'source_field' => 'field_media_audio_file',
      'fields' => [
        'field_media_audio_file' => [
          'type' => 'file',
          'label' => 'Audio File',
          'cardinality' => 1,
          'required' => TRUE,
          'storage_settings' => [
            'target_type' => 'file',
            'display_field' => FALSE,
            'display_default' => FALSE,
            'uri_scheme' => 'public',
          ],
          'instance_settings' => [
            'file_extensions' => 'mp3 wav ogg aac m4a',
            'file_directory' => 'audio/[date:custom:Y]-[date:custom:m]',
            'max_filesize' => '100 MB',
            'description_field' => TRUE,
          ]
        ],
        'field_audio_type' => [
          'type' => 'list_string',
          'label' => 'Type',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'allowed_values' => [
              'repetitie' => 'Repetitie',
              'opname' => 'Opname',
              'uitvoering' => 'Uitvoering',
              'demo' => 'Demo'
            ]
          ]
        ],
        'field_audio_uitvoerende' => [
          'type' => 'string',
          'label' => 'Uitvoerende',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'max_length' => 255
          ]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'datetime_type' => 'datetime'
          ]
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => -1,
          'required' => FALSE,
          'storage_settings' => [
            'target_type' => 'user_role'
          ],
          'target_bundles' => NULL
        ]
      ]
    ],
    
    'video' => [
      'name' => 'Video',
      'description' => 'Lokaal opgeslagen videobestanden',
      'source_plugin' => 'video_file',
      'source_field' => 'field_media_video_file',
      'fields' => [
        'field_media_video_file' => [
          'type' => 'file',
          'label' => 'Video File',
          'cardinality' => 1,
          'required' => TRUE,
          'storage_settings' => [
            'target_type' => 'file',
            'display_field' => FALSE,
            'display_default' => FALSE,
            'uri_scheme' => 'public',
          ],
          'instance_settings' => [
            'file_extensions' => 'mp4 avi mov wmv flv webm',
            'file_directory' => 'videos/[date:custom:Y]-[date:custom:m]',
            'max_filesize' => '500 MB',
            'description_field' => TRUE,
          ]
        ],
        'field_video_type' => [
          'type' => 'list_string',
          'label' => 'Type',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'allowed_values' => [
              'repetitie' => 'Repetitie',
              'opname' => 'Opname',
              'uitvoering' => 'Uitvoering',
              'promo' => 'Promotie'
            ]
          ]
        ],
        'field_video_uitvoerende' => [
          'type' => 'string',
          'label' => 'Uitvoerende',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'max_length' => 255
          ]
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'datetime_type' => 'datetime'
          ]
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => -1,
          'required' => FALSE,
          'storage_settings' => [
            'target_type' => 'user_role'
          ],
          'target_bundles' => NULL
        ]
      ]
    ]
  ];
}

/**
 * Print samenvatting van aangemaakte media bundles.
 */
function printMediaSummary() {
  echo "\n📊 SAMENVATTING MEDIA BUNDLES EN VELDEN\n";
  echo "=" . str_repeat("=", 50) . "\n";
  
  $media_bundles = getMediaBundleConfigurations();
  echo "✅ Media Bundles Aangemaakt: " . count($media_bundles) . "\n\n";
  
  foreach ($media_bundles as $bundle_id => $config) {
    echo "📦 {$config['name']} ({$bundle_id})\n";
    echo "   Beschrijving: {$config['description']}\n";
    echo "   Source plugin: {$config['source_plugin']}\n";
    echo "   Source field: {$config['source_field']}\n";
    
    if (isset($config['fields'])) {
      echo "   Velden (" . count($config['fields']) . "):\n";
      foreach ($config['fields'] as $field_name => $field_config) {
        echo "     • {$field_config['label']} ({$field_name}) - {$field_config['type']}\n";
      }
    }
    echo "\n";
  }
  
  echo "🔄 CRITIEKE MIGRATIE WIJZIGINGEN:\n";
  echo "1. **Partituur Architectuur Veranderd**:\n";
  echo "   - D6: field_partij_* velden in repertoire\n";
  echo "   - D11: Document media met field_gerelateerd_repertoire\n";
  echo "   - Vereist aparte migratie voor partituren\n\n";
  
  echo "2. **Media Entity References**:\n";
  echo "   - Alle file velden zijn nu media entity references\n";
  echo "   - Bestaande bestanden moeten gemigreerd naar media entities\n\n";
  
  echo "3. **Toegang Control**:\n";
  echo "   - field_toegang veld voor role-based toegang\n";
  echo "   - Replaceert oude permission systeem\n\n";
  
  echo "🎯 VOLGENDE STAPPEN:\n";
  echo "1. Valideer media bundles: drush php:script validate-created-fields.php\n";
  echo "2. Configureer media displays\n";
  echo "3. Test media upload functionaliteit\n";
  echo "4. Pas migratie scripts aan voor nieuwe architectuur\n";
  echo "5. Begin partituur migratie met reverse references\n";
}

// Voer het script uit
try {
  createMediaBundlesAndFields();
} catch (Exception $e) {
  echo "❌ Aanmaak gefaald: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => -1,
          'required' => FALSE,
          'storage_settings' => [
            'target_type' => 'user_role'
          ],
          'target_bundles' => NULL
        ]
      ]
    ],
    
    'document' => [
      'name' => 'Document',
      'description' => 'Lokaal opgeslagen documenten en bestanden',
      'source_plugin' => 'file',
      'source_field' => 'field_media_document',
      'fields' => [
        'field_media_document' => [
          'type' => 'file',
          'label' => 'Document',
          'cardinality' => 1,
          'required' => TRUE,
          'storage_settings' => [
            'target_type' => 'file',
            'display_field' => FALSE,
            'display_default' => FALSE,
            'uri_scheme' => 'public',
          ],
          'instance_settings' => [
            'file_extensions' => 'txt doc docx pdf xls xlsx ppt pptx',
            'file_directory' => 'documents/[date:custom:Y]-[date:custom:m]',
            'max_filesize' => '50 MB',
            'description_field' => TRUE,
          ]
        ],
        'field_document_soort' => [
          'type' => 'list_string',
          'label' => 'Document Soort',
          'cardinality' => 1,
          'required' => TRUE,
          'storage_settings' => [
            'allowed_values' => [
              'partituur' => 'Partituur',
              'huiswerk' => 'Huiswerk',
              'overig' => 'Overig document'
            ]
          ]
        ],
        'field_gerelateerd_repertoire' => [
          'type' => 'entity_reference',
          'label' => 'Gerelateerd Repertoire',
          'cardinality' => -1,
          'required' => FALSE,
          'storage_settings' => [
            'target_type' => 'node'
          ],
          'target_bundles' => ['repertoire']
        ],
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'cardinality' => 1,
          'required' => FALSE,
          'storage_settings' => [
            'datetime_type' => 'datetime'
          ]
        ],
        'field_