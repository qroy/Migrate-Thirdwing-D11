<?php

/**
 * @file
 * Create content types and fields for Thirdwing D6 to D11 migration.
 * 
 * FIXED VERSION: Removed obsolete 'profiel' content type since profiles
 * are migrated as user fields, not separate nodes.
 */

use Drupal\Core\Database\Database;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

echo "=== CREATING THIRDWING CONTENT TYPES AND FIELDS ===\n\n";

// CORRECTED: Content types configuration with profiel REMOVED
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
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Locatie Bijzonderheden',
      ],
      'field_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
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
        'target_bundles' => ['repertoire'],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'activiteit-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_background' => [
        'type' => 'image',
        'label' => 'Achtergrond Afbeelding',
        'settings' => [
          'file_directory' => 'activiteit-backgrounds',
          'alt_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'activiteit-bestanden',
        ],
      ],
    ],
  ],
  
  'audio' => [
    'name' => 'Audio',
    'description' => 'Audio opnames en bestanden',
    'fields' => [
      'field_files' => [
        'type' => 'file',
        'label' => 'Audio Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'mp3 wav ogg m4a aac',
          'file_directory' => 'audio',
        ],
      ],
      'field_audio_bijz' => [
        'type' => 'text_long',
        'label' => 'Audio Bijzonderheden',
      ],
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Cover Afbeelding',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'audio-covers',
          'alt_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'foto' => [
    'name' => 'Foto',
    'description' => 'Foto albums en galerijen',
    'fields' => [
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Foto\'s',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'foto-albums',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
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
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Locatie Foto\'s',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'locatie-fotos',
          'alt_field' => TRUE,
        ],
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
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Nieuws Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'nieuws-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bijlagen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'nieuws-bestanden',
        ],
      ],
      'field_huiswerk' => [
        'type' => 'text_long',
        'label' => 'Huiswerk',
      ],
      'field_nieuwsbrief' => [
        'type' => 'file',
        'label' => 'Nieuwsbrief Bestand',
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'nieuwsbrieven',
        ],
      ],
      'field_inhoud_referenties' => [
        'type' => 'entity_reference',
        'label' => 'Inhoud Referenties',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['nieuws', 'activiteit', 'repertoire'],
      ],
      'field_jaargang' => [
        'type' => 'integer',
        'label' => 'Jaargang',
      ],
    ],
  ],
  
  'pagina' => [
    'name' => 'Pagina',
    'description' => 'Algemene pagina\'s',
    'fields' => [
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'pagina-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'pagina-bestanden',
        ],
      ],
      'field_view' => [
        'type' => 'string',
        'label' => 'View Reference',
        'settings' => ['max_length' => 255],
      ],
    ],
  ],
  
  'repertoire' => [
    'name' => 'Repertoire',
    'description' => 'Muziek repertoire en nummers',
    'fields' => [
      'field_artiest' => [
        'type' => 'string',
        'label' => 'Artiest',
        'settings' => ['max_length' => 255],
      ],
      'field_genre' => [
        'type' => 'entity_reference',
        'label' => 'Genre',
        'settings' => ['target_type' => 'taxonomy_term'],
        'target_bundles' => ['genre'],
      ],
      'field_rep_uitv_jaar' => [
        'type' => 'integer',
        'label' => 'Uitvoering Jaar',
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Partituren',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt mid kar',
          'file_directory' => 'repertoire',
        ],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Repertoire Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'repertoire-afbeeldingen',
          'alt_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'verslag' => [
    'name' => 'Verslag',
    'description' => 'Vergaderverslagen en rapporten',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Vergader Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Verslag Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'verslagen',
        ],
      ],
    ],
  ],
  
  'video' => [
    'name' => 'Video',
    'description' => 'Video opnames en content',
    'fields' => [
      'field_emvideo' => [
        'type' => 'string',
        'label' => 'Embedded Video URL',
        'settings' => ['max_length' => 500],
      ],
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Video Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Video Thumbnail',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'video-thumbnails',
          'alt_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Video Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'mp4 avi mov wmv flv',
          'file_directory' => 'video',
        ],
      ],
    ],
  ],
  
  'vriend' => [
    'name' => 'Vriend',
    'description' => 'Vrienden en partners organisaties',
    'fields' => [
      'field_organisatie' => [
        'type' => 'string',
        'label' => 'Organisatie',
        'settings' => ['max_length' => 255],
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
      'field_logo' => [
        'type' => 'image',
        'label' => 'Logo',
        'settings' => [
          'file_directory' => 'vriend-logos',
          'alt_field' => TRUE,
        ],
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
echo "✅ FIXED: Removed 'profiel' content type (profiles are user fields)\n\n";

echo "Content types created:\n";
foreach ($content_types_config as $type_id => $type_info) {
  $field_count = isset($type_info['fields']) ? count($type_info['fields']) : 0;
  echo "  - {$type_id}: {$type_info['name']} ({$field_count} fields)\n";
}

echo "\nNote: Profile data is handled by user profile fields, not as separate content type.\n";
echo "User profile fields are created separately and managed by the user migration system.\n\n";

echo "Next steps:\n";
echo "1. Run user profile field creation script\n";
echo "2. Configure form and display modes via admin UI if needed\n";
echo "3. Set up media types for file fields if Media module is used\n";
echo "4. Configure access permissions for content types\n";