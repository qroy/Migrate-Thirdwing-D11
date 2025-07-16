<?php
// File: modules/custom/thirdwing_migrate/scripts/create-content-types-and-fields.php

/**
 * Create content types and fields for Thirdwing D6 to D11 migration.
 * 
 * CORRECTED VERSION: Based on actual D6 database structure and migration needs.
 * 
 * Usage: drush php:script modules/custom/thirdwing_migrate/scripts/create-content-types-and-fields.php
 */

use Drupal\Core\Database\Database;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

echo "=== CREATING THIRDWING CONTENT TYPES AND FIELDS ===\n\n";

// CORRECTED: Content types configuration based on actual D6 database
$content_types_config = [
  'activiteit' => [
    'name' => 'Activiteit',
    'description' => 'Koor activiteiten en concerten',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_tijd_aanwezig' => [
        'type' => 'string',
        'label' => 'Tijd Aanwezig',
        'settings' => ['max_length' => 100],
      ],
      // Instrument availability fields
      'field_keyboard' => [
        'type' => 'boolean',
        'label' => 'Keyboard Beschikbaar',
      ],
      'field_gitaar' => [
        'type' => 'boolean',
        'label' => 'Gitaar Beschikbaar',
      ],
      'field_basgitaar' => [
        'type' => 'boolean',
        'label' => 'Basgitaar Beschikbaar',
      ],
      'field_drums' => [
        'type' => 'boolean',
        'label' => 'Drums Beschikbaar',
      ],
      // Logistics fields
      'field_vervoer' => [
        'type' => 'string',
        'label' => 'Vervoer',
        'settings' => ['max_length' => 255],
      ],
      'field_sleepgroep' => [
        'type' => 'string',
        'label' => 'Sleepgroep',
        'settings' => ['max_length' => 100],
      ],
      'field_sleepgroep_aanwezig' => [
        'type' => 'string',
        'label' => 'Sleepgroep Aanwezig',
        'settings' => ['max_length' => 100],
      ],
      'field_sleepgroep_terug' => [
        'type' => 'string',
        'label' => 'Sleepgroep Terug',
        'settings' => ['max_length' => 100],
      ],
      'field_kledingcode' => [
        'type' => 'string',
        'label' => 'Kledingcode',
        'settings' => ['max_length' => 255],
      ],
      // Location and details
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Locatie Bijzonderheden',
      ],
      'field_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
      // References
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_programma2' => [
        'type' => 'entity_reference',
        'label' => 'Programma',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['programma'],
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_background' => [
        'type' => 'entity_reference',
        'label' => 'Achtergrond Afbeelding',
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
    ],
  ],
  
  'nieuws' => [
    'name' => 'Nieuws',
    'description' => 'Nieuwsberichten en aankondigingen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Nieuws Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_huiswerk' => [
        'type' => 'text_long',
        'label' => 'Huiswerk',
      ],
      'field_jaargang' => [
        'type' => 'integer',
        'label' => 'Jaargang',
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Nieuws Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bijlagen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
      'field_nieuwsbrief' => [
        'type' => 'entity_reference',
        'label' => 'Nieuwsbrief Bestand',
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
      // Content references
      'field_inhoud_referenties' => [
        'type' => 'entity_reference',
        'label' => 'Inhoud Referenties',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['nieuws', 'activiteit', 'programma'],
      ],
    ],
  ],
  
  'pagina' => [
    'name' => 'Pagina',
    'description' => 'Algemene pagina\'s',
    'fields' => [
      'field_view' => [
        'type' => 'string',
        'label' => 'View Reference',
        'settings' => ['max_length' => 255],
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
    ],
  ],
  
  'programma' => [
    'name' => 'Programma',
    'description' => 'Concert programma\'s en repertoire',
    'fields' => [
      'field_prog_type' => [
        'type' => 'entity_reference',
        'label' => 'Programma Type',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['programma_type'],
      ],
      'field_rep_arr' => [
        'type' => 'string',
        'label' => 'Arrangeur',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_arr_jaar' => [
        'type' => 'integer',
        'label' => 'Arrangeur Jaar',
      ],
      'field_rep_componist' => [
        'type' => 'string',
        'label' => 'Componist',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_componist_jaar' => [
        'type' => 'integer',
        'label' => 'Componist Jaar',
      ],
      'field_rep_genre' => [
        'type' => 'entity_reference',
        'label' => 'Genre',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['genre'],
      ],
      'field_rep_sinds' => [
        'type' => 'datetime',
        'label' => 'Repertoire Sinds',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_rep_uitv' => [
        'type' => 'string',
        'label' => 'Uitvoering',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_uitv_jaar' => [
        'type' => 'integer',
        'label' => 'Uitvoering Jaar',
      ],
      'field_uitgave' => [
        'type' => 'string',
        'label' => 'Uitgave',
        'settings' => ['max_length' => 255],
      ],
      // Media fields - will reference media entities
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Partituren',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document', 'audio'],
      ],
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Repertoire Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
    ],
  ],
  
  'foto' => [
    'name' => 'Foto',
    'description' => 'Foto albums en galerijen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_ref_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Foto\'s',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
    ],
  ],
  
  'locatie' => [
    'name' => 'Locatie',
    'description' => 'Concertlocaties en venues',
    'fields' => [
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      'field_woonplaats' => [
        'type' => 'string',
        'label' => 'Woonplaats',
        'settings' => ['max_length' => 100],
      ],
      'field_telefoon' => [
        'type' => 'string',
        'label' => 'Telefoon',
        'settings' => ['max_length' => 50],
      ],
      'field_l_routelink' => [
        'type' => 'link',
        'label' => 'Route Link',
      ],
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Locatie Foto\'s',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
    ],
  ],
  
  'vriend' => [
    'name' => 'Vriend',
    'description' => 'Vrienden en partners organisaties',
    'fields' => [
      'field_vriend_soort' => [
        'type' => 'entity_reference',
        'label' => 'Vriend Soort',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['vriend_soort'],
      ],
      'field_vriend_benaming' => [
        'type' => 'entity_reference',
        'label' => 'Benaming',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['vriend_benaming'],
      ],
      'field_vriend_vanaf' => [
        'type' => 'datetime',
        'label' => 'Vriend Vanaf',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_vriend_tot' => [
        'type' => 'datetime',
        'label' => 'Vriend Tot',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_vriend_lengte' => [
        'type' => 'integer',
        'label' => 'Lengte (maanden)',
      ],
      'field_contactpersoon' => [
        'type' => 'string',
        'label' => 'Contactpersoon',
        'settings' => ['max_length' => 255],
      ],
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      'field_woonplaats' => [
        'type' => 'string',
        'label' => 'Woonplaats',
        'settings' => ['max_length' => 100],
      ],
      'field_telefoon' => [
        'type' => 'string',
        'label' => 'Telefoon',
        'settings' => ['max_length' => 50],
      ],
      'field_email' => [
        'type' => 'email',
        'label' => 'Email',
      ],
      'field_website' => [
        'type' => 'link',
        'label' => 'Website',
      ],
      // Media fields - will reference media entities
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Logo',
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
    ],
  ],
];

// Create content types and fields
foreach ($content_types_config as $type_id => $type_info) {
  echo "Processing content type: {$type_info['name']} ({$type_id})\n";
  
  // Check if content type exists
  $node_type = NodeType::load($type_id);
  if (!$node_type) {
    // Create content type
    $node_type = NodeType::create([
      'type' => $type_id,
      'name' => $type_info['name'],
      'description' => $type_info['description'],
    ]);
    $node_type->save();
    echo "  ✓ Content type created: {$type_info['name']}\n";
  } else {
    echo "  - Content type '{$type_id}' already exists\n";
  }
  
  // Create fields if they exist
  if (isset($type_info['fields']) && !empty($type_info['fields'])) {
    echo "  Creating fields...\n";
    
    foreach ($type_info['fields'] as $field_name => $field_config) {
      echo "    Processing field: $field_name\n";
      
      // Check if field storage exists
      $field_storage = FieldStorageConfig::loadByName('node', $field_name);
      if (!$field_storage) {
        // Create field storage
        $storage_config = [
          'field_name' => $field_name,
          'entity_type' => 'node',
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
      
      // Check if field instance exists for this content type
      $field_instance = FieldConfig::loadByName('node', $type_id, $field_name);
      if (!$field_instance) {
        // Create field instance
        $instance_config = [
          'field_storage' => $field_storage,
          'bundle' => $type_id,
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
        echo "      - Field '{$field_name}' already exists for {$type_id}\n";
      }
    }
  } else {
    echo "    No custom fields defined for this content type.\n";
  }
  
  echo "\n";
}

echo "=== CONTENT TYPE CREATION COMPLETE ===\n\n";

echo "✅ Content types created:\n";
foreach ($content_types_config as $type_id => $type_info) {
  $field_count = isset($type_info['fields']) ? count($type_info['fields']) : 0;
  echo "  - {$type_id}: {$type_info['name']} ({$field_count} fields)\n";
}

echo "\n🔧 Key corrections made:\n";
echo "  - Added missing 'programma' content type (was missing)\n";
echo "  - Fixed field references to use media entities instead of files\n";
echo "  - Added proper entity reference fields for taxonomy terms\n";
echo "  - Included all instrument availability fields for activities\n";
echo "  - Added proper logistics fields for activities\n";
echo "  - Fixed field naming to match D6 source structure\n";

echo "\n📋 Next steps:\n";
echo "1. Run media bundle creation script\n";
echo "2. Create taxonomy vocabularies and terms\n";
echo "3. Configure content moderation workflow\n";
echo "4. Set up user roles and permissions\n";
echo "5. Run migration validation script\n";

echo "\n⚠️  Note: All media fields now reference media entities instead of files.\n";
echo "This requires the media bundles to be created first using the media script.\n";