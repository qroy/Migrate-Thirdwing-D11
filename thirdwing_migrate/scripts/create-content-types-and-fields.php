<?php

/**
 * @file
 * Complete script to create content types and fields for Thirdwing migration.
 *
 * Usage: drush php:script create-content-types-and-fields.php
 *
 * This script creates all content types and fields with proper Dutch labels
 * matching the D6 documentation.
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function createContentTypesAndFields() {
  echo "🚀 Creating Content Types and Fields for Thirdwing Migration...\n\n";
  
  // Get content type configurations
  $content_types_config = getContentTypeConfigurations();
  
  // Create content types first
  echo "📦 Creating content types...\n";
  foreach ($content_types_config as $type_id => $config) {
    createContentType($type_id, $config);
  }
  
  echo "\n📋 Creating fields...\n";
  // Create fields second (after content types exist)
  foreach ($content_types_config as $type_id => $config) {
    if (isset($config['fields'])) {
      echo "Creating fields for content type: {$config['name']}\n";
      createFieldsForContentType($type_id, $config['fields']);
    }
  }
  
  echo "\n✅ Content types and fields creation complete!\n";
  echo "📊 Summary:\n";
  
  // Display summary
  foreach ($content_types_config as $type_id => $config) {
    $field_count = isset($config['fields']) ? count($config['fields']) : 0;
    echo "  • {$config['name']} ({$type_id}): {$field_count} fields\n";
  }
  
  echo "\n📋 Run verification with: drush entity:info node\n";
}

/**
 * Create a content type.
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
 * Create fields for a content type.
 */
function createFieldsForContentType($content_type, $fields) {
  foreach ($fields as $field_name => $field_config) {
    echo "    Processing field: {$field_name}\n";
    
    // Create field storage if it doesn't exist
    $field_storage = FieldStorageConfig::loadByName('node', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'node',
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
      echo "      - Field '{$field_name}' already exists for {$content_type}\n";
    }
  }
  
  echo "\n";
}

/**
 * Get content type configurations.
 */
function getContentTypeConfigurations() {
  return [
    'activiteit' => [
      'name' => 'Activiteit',
      'description' => 'Een activiteit (uitvoering, repetitie)',
      'fields' => [
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'settings' => ['datetime_type' => 'datetime'],
        ],
        'field_tijd_aanwezig' => [
          'type' => 'string',
          'label' => 'Koor Aanwezig',
          'settings' => ['max_length' => 255],
        ],
        'field_tijd_soundcheck' => [
          'type' => 'string',
          'label' => 'Soundcheck',
          'settings' => ['max_length' => 255],
        ],
        'field_tijd_start' => [
          'type' => 'string',
          'label' => 'Start',
          'settings' => ['max_length' => 255],
        ],
        'field_tijd_einde' => [
          'type' => 'string',
          'label' => 'Einde',
          'settings' => ['max_length' => 255],
        ],
        'field_keyboard' => [
          'type' => 'list_string',
          'label' => 'Toetsenist',
          'settings' => [
            'allowed_values' => [
              '0' => 'Onbekend',
              '1' => 'Aanwezig',
              '2' => 'Vrijwilliger nodig',
              '3' => 'Niet beschikbaar',
            ],
          ],
        ],
        'field_gitaar' => [
          'type' => 'list_string',
          'label' => 'Gitarist',
          'settings' => [
            'allowed_values' => [
              '0' => 'Onbekend',
              '1' => 'Aanwezig',
              '2' => 'Vrijwilliger nodig',
              '3' => 'Niet beschikbaar',
            ],
          ],
        ],
        'field_basgitaar' => [
          'type' => 'list_string',
          'label' => 'Basgitarist',
          'settings' => [
            'allowed_values' => [
              '0' => 'Onbekend',
              '1' => 'Aanwezig',
              '2' => 'Vrijwilliger nodig',
              '3' => 'Niet beschikbaar',
            ],
          ],
        ],
        'field_drums' => [
          'type' => 'list_string',
          'label' => 'Drummer',
          'settings' => [
            'allowed_values' => [
              '0' => 'Onbekend',
              '1' => 'Aanwezig',
              '2' => 'Vrijwilliger nodig',
              '3' => 'Niet beschikbaar',
            ],
          ],
        ],
        'field_locatie' => [
          'type' => 'entity_reference',
          'label' => 'Locatie',
          'settings' => ['target_type' => 'node'],
          'target_bundles' => ['locatie'],
        ],
        'field_media_images' => [
          'type' => 'entity_reference',
          'label' => 'Afbeeldingen',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['image'],
        ],
        'field_media_documents' => [
          'type' => 'entity_reference',
          'label' => 'Documenten',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['document'],
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang'],
        ],
      ],
    ],
    
    'nieuws' => [
      'name' => 'Nieuws',
      'description' => 'Nieuwsberichten en aankondigingen',
      'fields' => [
        'field_datum' => [
          'type' => 'datetime',
          'label' => 'Datum',
          'settings' => ['datetime_type' => 'datetime'],
        ],
        'field_media_images' => [
          'type' => 'entity_reference',
          'label' => 'Afbeeldingen',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['image'],
        ],
        'field_media_documents' => [
          'type' => 'entity_reference',
          'label' => 'Documenten',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['document'],
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang'],
        ],
      ],
    ],
    
    'pagina' => [
      'name' => 'Pagina',
      'description' => 'Algemene pagina\'s',
      'fields' => [
        'field_view' => [
          'type' => 'string',
          'label' => 'Extra inhoud',
          'settings' => ['max_length' => 255],
        ],
        'field_media_images' => [
          'type' => 'entity_reference',
          'label' => 'Afbeeldingen',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['image'],
        ],
        'field_media_documents' => [
          'type' => 'entity_reference',
          'label' => 'Documenten',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['document'],
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang'],
        ],
      ],
    ],
    
    'locatie' => [
      'name' => 'Locatie',
      'description' => 'Veelvoorkomende locaties van uitvoeringen',
      'fields' => [
        'field_l_adres' => [
          'type' => 'string',
          'label' => 'Adres',
          'settings' => ['max_length' => 255],
        ],
        'field_l_plaats' => [
          'type' => 'string',
          'label' => 'Plaats',
          'settings' => ['max_length' => 255],
        ],
        'field_l_postcode' => [
          'type' => 'string',
          'label' => 'Postcode',
          'settings' => ['max_length' => 20],
        ],
        'field_l_routelink' => [
          'type' => 'link',
          'label' => 'Route',
        ],
      ],
    ],
    
    'vriend' => [
      'name' => 'Vriend',
      'description' => 'Vrienden en sponsors van het koor',
      'fields' => [
        'field_vriend_website' => [
          'type' => 'link',
          'label' => 'Website',
        ],
        'field_vriend_telefoon' => [
          'type' => 'string',
          'label' => 'Telefoon',
          'settings' => ['max_length' => 255],
        ],
        'field_vriend_email' => [
          'type' => 'email',
          'label' => 'E-mail',
        ],
        'field_vriend_adres' => [
          'type' => 'string',
          'label' => 'Adres',
          'settings' => ['max_length' => 255],
        ],
        'field_vriend_woonplaats' => [
          'type' => 'string',
          'label' => 'Woonplaats',
          'settings' => ['max_length' => 255],
        ],
        'field_vriend_benaming' => [
          'type' => 'list_string',
          'label' => 'Benaming',
          'settings' => [
            'allowed_values' => [
              '1' => 'Vriend',
              '2' => 'Vriendin',
              '3' => 'Vrienden',
            ],
          ],
        ],
        'field_media_images' => [
          'type' => 'entity_reference',
          'label' => 'Afbeeldingen',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['image'],
        ],
      ],
    ],
    
    'repertoire' => [
      'name' => 'Repertoire',
      'description' => 'Nummers in het repertoire',
      'fields' => [
        'field_rep_componist' => [
          'type' => 'string',
          'label' => 'Componist',
          'settings' => ['max_length' => 255],
        ],
        'field_rep_arr' => [
          'type' => 'string',
          'label' => 'Arrangeur',
          'settings' => ['max_length' => 255],
        ],
        'field_rep_genre' => [
          'type' => 'entity_reference',
          'label' => 'Genre',
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['genre'],
        ],
        'field_rep_sinds' => [
          'type' => 'datetime',
          'label' => 'In repertoire sinds',
          'settings' => ['datetime_type' => 'date'],
        ],
        'field_uitgave' => [
          'type' => 'string',
          'label' => 'Uitgave',
          'settings' => ['max_length' => 255],
        ],
        'field_media_documents' => [
          'type' => 'entity_reference',
          'label' => 'Partituren',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'media'],
          'target_bundles' => ['document'],
        ],
        'field_toegang' => [
          'type' => 'entity_reference',
          'label' => 'Toegang',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'taxonomy_term'],
          'target_bundles' => ['toegang'],
        ],
      ],
    ],
    
    'programma' => [
      'name' => 'Programma',
      'description' => 'Programma-onderdelen',
      'fields' => [
        'field_inhoud' => [
          'type' => 'entity_reference',
          'label' => 'Inhoud',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'settings' => ['target_type' => 'node'],
          'target_bundles' => ['repertoire', 'activiteit'],
        ],
        'field_ref_activiteit' => [
          'type' => 'entity_reference',
          'label' => 'Activiteit',
          'settings' => ['target_type' => 'node'],
          'target_bundles' => ['activiteit'],
        ],
        'field_repertoire' => [
          'type' => 'entity_reference',
          'label' => 'Nummer',
          'settings' => ['target_type' => 'node'],
          'target_bundles' => ['repertoire'],
        ],
      ],
    ],
  ];
}

// Execute the script
try {
  createContentTypesAndFields();
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}