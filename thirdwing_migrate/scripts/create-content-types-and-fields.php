<?php

/**
 * @file
 * CORRECTED script to create content types and fields exactly matching
 * "Drupal 11 Content types and fields.md" documentation.
 *
 * Usage: drush php:script create-content-types-and-fields.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function createContentTypesAndFields() {
  echo "🚀 Creating Content Types and Fields (CORRECTED VERSION)...\n\n";
  
  // Step 1: Create shared field storages first
  echo "📋 Creating shared field storages...\n";
  createSharedFieldStorages();
  
  // Step 2: Create content types
  echo "\n📦 Creating content types...\n";
  $content_types = getContentTypeConfigurations();
  foreach ($content_types as $type_id => $config) {
    createContentType($type_id, $config);
  }
  
  // Step 3: Create content-type specific fields
  echo "\n🔧 Creating content-type specific fields...\n";
  createContentTypeSpecificFields();
  
  // Step 4: Attach shared fields to content types
  echo "\n🔗 Attaching shared fields to content types...\n";
  attachSharedFieldsToContentTypes();
  
  echo "\n✅ Content types and fields creation complete!\n";
  printSummary();
}

/**
 * Create shared field storages that will be used across multiple content types.
 */
function createSharedFieldStorages() {
  $shared_fields = getSharedFieldDefinitions();
  
  foreach ($shared_fields as $field_name => $field_config) {
    echo "  Creating shared field storage: {$field_name}\n";
    
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
      echo "    ✓ Created: {$field_config['label']}\n";
    } else {
      echo "    - Already exists: {$field_name}\n";
    }
  }
}

/**
 * Create content types.
 */
function createContentType($type_id, $config) {
  $node_type = NodeType::load($type_id);
  
  if (!$node_type) {
    $node_type = NodeType::create([
      'type' => $type_id,
      'name' => $config['name'],
      'description' => $config['description'],
    ]);
    
    $node_type->save();
    echo "  ✅ Created content type: {$config['name']} ({$type_id})\n";
  } else {
    echo "  - Content type '{$type_id}' already exists\n";
  }
}

/**
 * Create content-type specific fields (non-shared fields).
 */
function createContentTypeSpecificFields() {
  $specific_fields = getContentTypeSpecificFields();
  
  foreach ($specific_fields as $content_type => $fields) {
    echo "  Creating specific fields for: {$content_type}\n";
    
    foreach ($fields as $field_name => $field_config) {
      createFieldForContentType($content_type, $field_name, $field_config);
    }
  }
}

/**
 * Attach shared fields to content types based on documentation.
 */
function attachSharedFieldsToContentTypes() {
  $attachments = getSharedFieldAttachments();
  
  foreach ($attachments as $content_type => $field_names) {
    echo "  Attaching shared fields to: {$content_type}\n";
    
    foreach ($field_names as $field_name) {
      attachSharedFieldToContentType($content_type, $field_name);
    }
  }
}

/**
 * Create a field for a specific content type.
 */
function createFieldForContentType($content_type, $field_name, $field_config) {
  // Create field storage if it doesn't exist
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
  }
  
  // Create field instance
  $field_instance = FieldConfig::loadByName('node', $content_type, $field_name);
  if (!$field_instance) {
    $instance_config = [
      'field_storage' => $field_storage,
      'bundle' => $content_type,
      'label' => $field_config['label'],
      'required' => $field_config['required'] ?? FALSE,
    ];
    
    // Add target bundles for entity reference fields
    if (isset($field_config['target_bundles'])) {
      $instance_config['settings']['handler_settings']['target_bundles'] = $field_config['target_bundles'];
    }
    
    // Add other instance settings
    if (isset($field_config['instance_settings'])) {
      $instance_config['settings'] = array_merge(
        $instance_config['settings'] ?? [],
        $field_config['instance_settings']
      );
    }
    
    $field_instance = FieldConfig::create($instance_config);
    $field_instance->save();
    echo "    ✓ Created: {$field_config['label']}\n";
  }
}

/**
 * Attach a shared field to a content type.
 */
function attachSharedFieldToContentType($content_type, $field_name) {
  $shared_fields = getSharedFieldDefinitions();
  
  if (!isset($shared_fields[$field_name])) {
    echo "    ⚠️  Shared field '{$field_name}' not found\n";
    return;
  }
  
  $field_config = $shared_fields[$field_name];
  $field_storage = FieldStorageConfig::loadByName('node', $field_name);
  
  if (!$field_storage) {
    echo "    ⚠️  Field storage for '{$field_name}' not found\n";
    return;
  }
  
  $field_instance = FieldConfig::loadByName('node', $content_type, $field_name);
  if (!$field_instance) {
    $instance_config = [
      'field_storage' => $field_storage,
      'bundle' => $content_type,
      'label' => $field_config['label'],
      'required' => $field_config['required'] ?? FALSE,
    ];
    
    if (isset($field_config['target_bundles'])) {
      $instance_config['settings']['handler_settings']['target_bundles'] = $field_config['target_bundles'];
    }
    
    if (isset($field_config['instance_settings'])) {
      $instance_config['settings'] = array_merge(
        $instance_config['settings'] ?? [],
        $field_config['instance_settings']
      );
    }
    
    $field_instance = FieldConfig::create($instance_config);
    $field_instance->save();
    echo "    ✓ Attached: {$field_config['label']}\n";
  }
}

/**
 * Get content type configurations from documentation.
 */
function getContentTypeConfigurations() {
  return [
    'activiteit' => [
      'name' => 'Activiteit',
      'description' => 'Bedoeld voor het aanmaken van activiteiten, concerten, repetities, etc.'
    ],
    'foto' => [
      'name' => 'Foto',
      'description' => 'Foto-album'
    ],
    'locatie' => [
      'name' => 'Locatie',
      'description' => 'Veelvoorkomende locaties van uitvoeringen'
    ],
    'nieuws' => [
      'name' => 'Nieuws',
      'description' => 'Een nieuwsbericht. Dit kan een publiek nieuwsbericht zijn, maar ook een nieuwsbericht voor op de ledenpagina.'
    ],
    'pagina' => [
      'name' => 'Pagina',
      'description' => "Gebruik een 'Pagina' wanneer je een statische pagina wilt toevoegen"
    ],
    'programma' => [
      'name' => 'Programma',
      'description' => 'Programma-item'
    ],
    'repertoire' => [
      'name' => 'Repertoire',
      'description' => 'Koornummers in het repertoire'
    ],
    'vriend' => [
      'name' => 'Vriend',
      'description' => 'Vrienden van de koorstichting (sponsors etc.)'
    ],
    'webform' => [
      'name' => 'Webform',
      'description' => 'Create a new form or questionnaire accessible to users'
    ]
  ];
}

/**
 * Get shared field definitions that are available to all content types.
 */
function getSharedFieldDefinitions() {
  return [
    'field_afbeeldingen' => [
      'type' => 'entity_reference',
      'label' => 'Afbeeldingen',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      'settings' => ['target_type' => 'media'],
      'target_bundles' => ['image']
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
      'label' => 'Datum en tijd',
      'cardinality' => 1,
      'settings' => ['datetime_type' => 'datetime']
    ],
    'field_files' => [
      'type' => 'entity_reference',
      'label' => 'Bestandsbijlages',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      'settings' => ['target_type' => 'media'],
      'target_bundles' => ['document']
    ],
    'field_inhoud' => [
      'type' => 'entity_reference',
      'label' => 'Inhoud',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      'settings' => ['target_type' => 'node'],
      'target_bundles' => ['nieuws', 'activiteit', 'programma']
    ],
    'field_l_routelink' => [
      'type' => 'link',
      'label' => 'Route',
      'cardinality' => 1
    ],
    'field_partij_band' => [
      'type' => 'entity_reference',
      'label' => 'Bandpartituur',
      'cardinality' => 1,
      'settings' => ['target_type' => 'media'],
      'target_bundles' => ['document']
    ],
    'field_partij_koor_l' => [
      'type' => 'entity_reference',
      'label' => 'Koorpartituur',
      'cardinality' => 1,
      'settings' => ['target_type' => 'media'],
      'target_bundles' => ['document']
    ],
    'field_partij_tekst' => [
      'type' => 'entity_reference',
      'label' => 'Tekst / koorregie',
      'cardinality' => 1,
      'settings' => ['target_type' => 'media'],
      'target_bundles' => ['document']
    ],
    'field_programma2' => [
      'type' => 'entity_reference',
      'label' => 'Programma',
      'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
      'settings' => ['target_type' => 'node'],
      'target_bundles' => ['programma']
    ],
    'field_ref_activiteit' => [
      'type' => 'entity_reference',
      'label' => 'Activiteit',
      'cardinality' => 1,
      'settings' => ['target_type' => 'node'],
      'target_bundles' => ['activiteit']
    ],
    'field_repertoire' => [
      'type' => 'entity_reference',
      'label' => 'Nummer',
      'cardinality' => 1,
      'settings' => ['target_type' => 'node'],
      'target_bundles' => ['repertoire']
    ],
    'field_video' => [
      'type' => 'text_long',
      'label' => 'Video',
      'cardinality' => 1,
      'settings' => []
    ],
    'field_view' => [
      'type' => 'string',
      'label' => 'Extra inhoud',
      'cardinality' => 1,
      'settings' => ['max_length' => 255]
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'label' => 'Woonplaats',
      'cardinality' => 1,
      'settings' => ['max_length' => 255]
    ]
  ];
}

/**
 * Get content-type specific fields (not shared).
 */
function getContentTypeSpecificFields() {
  return [
    'activiteit' => [
      'field_a_locatie' => [
        'type' => 'string',
        'label' => 'Locatie vrije invoer',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_a_planner' => [
        'type' => 'entity_reference',
        'label' => 'Planner',
        'cardinality' => 1,
        'settings' => ['target_type' => 'user']
      ],
      'field_a_tijd_begin' => [
        'type' => 'string',
        'label' => 'Tijd begin',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_a_tijd_einde' => [
        'type' => 'string',
        'label' => 'Tijd einde',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_a_wijzigingen' => [
        'type' => 'text_long',
        'label' => 'Last-minute wijzigingen',
        'cardinality' => 1
      ],
      'field_l_ref_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie uit lijst',
        'cardinality' => 1,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie']
      ]
    ],
    'locatie' => [
      'field_l_adres' => [
        'type' => 'string',
        'label' => 'Adres',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_l_plaats' => [
        'type' => 'string',
        'label' => 'Plaats',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_l_postcode' => [
        'type' => 'string',
        'label' => 'Postcode',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ]
    ],
    'repertoire' => [
      'field_componist' => [
        'type' => 'string',
        'label' => 'Componist',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_arrangeur' => [
        'type' => 'string',
        'label' => 'Arrangeur',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_genre' => [
        'type' => 'entity_reference',
        'label' => 'Genre',
        'cardinality' => 1,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['genre']
      ],
      'field_uitgave' => [
        'type' => 'string',
        'label' => 'Uitgave',
        'cardinality' => 1,
        'settings' => ['max_length' => 255]
      ],
      'field_toegang' => [
        'type' => 'entity_reference',
        'label' => 'Toegang',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['toegang']
      ]
    ],
    'vriend' => [
      'field_v_categorie' => [
        'type' => 'entity_reference',
        'label' => 'Categorie',
        'cardinality' => 1,
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['vrienden_categorie']
      ],
      'field_v_website' => [
        'type' => 'link',
        'label' => 'Website',
        'cardinality' => 1
      ]
    ]
  ];
}

/**
 * Define which shared fields attach to which content types.
 */
function getSharedFieldAttachments() {
  return [
    'activiteit' => [
      'field_datum', 'field_afbeeldingen', 'field_files', 'field_programma2'
    ],
    'foto' => [
      'field_video', 'field_repertoire', 'field_audio_uitvoerende', 
      'field_audio_type', 'field_datum', 'field_ref_activiteit'
    ],
    'nieuws' => [
      'field_datum', 'field_afbeeldingen', 'field_files'
    ],
    'pagina' => [
      'field_afbeeldingen', 'field_files', 'field_view'
    ],
    'programma' => [
      'field_afbeeldingen', 'field_files', 'field_ref_activiteit'
    ],
    'repertoire' => [
      'field_partij_band', 'field_partij_koor_l', 'field_partij_tekst'
    ],
    'vriend' => [
      'field_afbeeldingen', 'field_woonplaats'
    ]
  ];
}

/**
 * Print summary of created content.
 */
function printSummary() {
  $content_types = getContentTypeConfigurations();
  $shared_fields = getSharedFieldDefinitions();
  $specific_fields = getContentTypeSpecificFields();
  
  echo "\n📊 Summary:\n";
  echo "  • Content Types: " . count($content_types) . "\n";
  echo "  • Shared Fields: " . count($shared_fields) . "\n";
  
  $total_specific = 0;
  foreach ($specific_fields as $fields) {
    $total_specific += count($fields);
  }
  echo "  • Content-Type Specific Fields: {$total_specific}\n";
  
  echo "\n📋 Content Types Created:\n";
  foreach ($content_types as $type_id => $config) {
    $specific_count = isset($specific_fields[$type_id]) ? count($specific_fields[$type_id]) : 0;
    $shared_count = count(getSharedFieldAttachments()[$type_id] ?? []);
    $total_fields = $specific_count + $shared_count;
    echo "  • {$config['name']} ({$type_id}): {$total_fields} fields\n";
  }
  
  echo "\n📋 Next Steps:\n";
  echo "  1. Run: drush php:script create-media-bundles-and-fields.php\n";
  echo "  2. Run: drush php:script create-user-profile-fields.php\n";
  echo "  3. Verify: drush entity:info node\n";
  echo "  4. Verify: drush entity:info media\n";
}

// Execute the script
try {
  createContentTypesAndFields();
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}