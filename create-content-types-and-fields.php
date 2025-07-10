<?php
/**
 * @file
 * Enhanced script to create Thirdwing content types AND fields automatically.
 * Uses original Dutch names from D6 site.
 * 
 * Run with: drush php:script create-content-types-and-fields.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

// Content types with their fields mapped from D6 database structure
// Using original Dutch names from the source site
$content_types_config = [
  'activiteit' => [
    'name' => 'Activiteit',
    'description' => 'Kooractiviteiten en evenementen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_tijd_aanwezig' => [
        'type' => 'string',
        'label' => 'Tijd Aanwezig',
        'settings' => ['max_length' => 5],
      ],
      'field_keyboard' => [
        'type' => 'list_string',
        'label' => 'Keyboard',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_gitaar' => [
        'type' => 'list_string',
        'label' => 'Gitaar',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_basgitaar' => [
        'type' => 'list_string',
        'label' => 'Basgitaar',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_drums' => [
        'type' => 'list_string',
        'label' => 'Drums',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_vervoer' => [
        'type' => 'text_long',
        'label' => 'Vervoer',
      ],
      'field_sleepgroep' => [
        'type' => 'text_long',
        'label' => 'Sleepgroep',
      ],
      'field_sleepgroep_aanwezig' => [
        'type' => 'text_long',
        'label' => 'Sleepgroep Aanwezig',
      ],
      'field_kledingcode' => [
        'type' => 'text_long',
        'label' => 'Kledingcode',
      ],
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Locatie Bijzonderheden',
      ],
      'field_ledeninfo' => [
        'type' => 'text_long',
        'label' => 'Ledeninfo',
      ],
      'field_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
    ],
  ],
  
  'repertoire' => [
    'name' => 'Repertoire',
    'description' => 'Muzikale repertoire items',
    'fields' => [
      'field_componist' => [
        'type' => 'string',
        'label' => 'Componist',
        'settings' => ['max_length' => 255],
      ],
      'field_arrangeur' => [
        'type' => 'string',
        'label' => 'Arrangeur',
        'settings' => ['max_length' => 255],
      ],
      'field_toonsoort' => [
        'type' => 'string',
        'label' => 'Toonsoort',
        'settings' => ['max_length' => 50],
      ],
      'field_tempo' => [
        'type' => 'string',
        'label' => 'Tempo',
        'settings' => ['max_length' => 100],
      ],
      'field_moeilijkheidsgraad' => [
        'type' => 'list_integer',
        'label' => 'Moeilijkheidsgraad',
        'settings' => [
          'allowed_values' => [
            1 => 'Makkelijk',
            2 => 'Gemiddeld',
            3 => 'Moeilijk',
            4 => 'Zeer moeilijk',
          ],
        ],
      ],
      'field_partituur' => [
        'type' => 'file',
        'label' => 'Partituur',
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'partituren',
        ],
      ],
      'field_audio' => [
        'type' => 'file',
        'label' => 'Audio Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'mp3 wav ogg',
          'file_directory' => 'audio',
        ],
      ],
    ],
  ],
  
  'nieuws' => [
    'name' => 'Nieuws',
    'description' => 'Nieuwsartikelen',
    'fields' => [
      'field_nieuws_datum' => [
        'type' => 'datetime',
        'label' => 'Nieuws Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_afbeelding' => [
        'type' => 'image',
        'label' => 'Afbeelding',
        'settings' => [
          'file_directory' => 'nieuws-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_samenvatting' => [
        'type' => 'text_long',
        'label' => 'Samenvatting',
      ],
    ],
  ],
  
  'foto' => [
    'name' => 'Foto Album',
    'description' => 'Foto albums',
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
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'foto-albums',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'locatie' => [
    'name' => 'Locatie',
    'description' => 'Uitvoerings- en repetitielocaties',
    'fields' => [
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      'field_postcode' => [
        'type' => 'string',
        'label' => 'Postcode',
        'settings' => ['max_length' => 20],
      ],
      'field_woonplaats' => [
        'type' => 'string',
        'label' => 'Woonplaats',
        'settings' => ['max_length' => 100],
      ],
      'field_land' => [
        'type' => 'string',
        'label' => 'Land',
        'settings' => ['max_length' => 100],
      ],
      'field_contactpersoon' => [
        'type' => 'string',
        'label' => 'Contactpersoon',
        'settings' => ['max_length' => 255],
      ],
      'field_telefoon' => [
        'type' => 'string',
        'label' => 'Telefoon',
        'settings' => ['max_length' => 50],
      ],
      'field_email' => [
        'type' => 'email',
        'label' => 'E-mail',
      ],
      'field_website' => [
        'type' => 'link',
        'label' => 'Website',
      ],
      'field_l_routelink' => [
        'type' => 'link',
        'label' => 'Route Link',
      ],
      'field_opmerkingen' => [
        'type' => 'text_long',
        'label' => 'Opmerkingen',
      ],
    ],
  ],
  
  'vriend' => [
    'name' => 'Vriend',
    'description' => 'Vrienden en sponsors van het koor',
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
        'label' => 'E-mail',
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
  
  'nieuwsbrief' => [
    'name' => 'Nieuwsbrief',
    'description' => 'Nieuwsbrief uitgaven',
    'fields' => [
      'field_uitgave_nummer' => [
        'type' => 'integer',
        'label' => 'Uitgave Nummer',
      ],
      'field_uitgave_datum' => [
        'type' => 'datetime',
        'label' => 'Uitgave Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_nieuwsbrief_bestand' => [
        'type' => 'file',
        'label' => 'Nieuwsbrief Bestand',
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'nieuwsbrieven',
        ],
      ],
    ],
  ],
  
  'pagina' => [
    'name' => 'Pagina',
    'description' => 'Algemene pagina\'s',
    'fields' => [
      // Basic page typically just needs body field which is handled by Drupal core
    ],
  ],
  
  'profiel' => [
    'name' => 'Profiel',
    'description' => 'Koorlid profielen',
    'fields' => [
      'field_voornaam' => [
        'type' => 'string',
        'label' => 'Voornaam',
        'settings' => ['max_length' => 100],
      ],
      'field_achternaam' => [
        'type' => 'string',
        'label' => 'Achternaam',
        'settings' => ['max_length' => 100],
      ],
      'field_tussenvoegsel' => [
        'type' => 'string',
        'label' => 'Tussenvoegsel',
        'settings' => ['max_length' => 20],
      ],
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      'field_postcode' => [
        'type' => 'string',
        'label' => 'Postcode',
        'settings' => ['max_length' => 20],
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
        'label' => 'E-mail',
      ],
      'field_geboortedatum' => [
        'type' => 'datetime',
        'label' => 'Geboortedatum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_stemgroep' => [
        'type' => 'list_string',
        'label' => 'Stemgroep',
        'settings' => [
          'allowed_values' => [
            'sopraan' => 'Sopraan',
            'alt' => 'Alt',
            'tenor' => 'Tenor',
            'bas' => 'Bas',
          ],
        ],
      ],
      'field_foto' => [
        'type' => 'image',
        'label' => 'Foto',
        'settings' => [
          'file_directory' => 'profiel-fotos',
          'alt_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'programma' => [
    'name' => 'Programma',
    'description' => 'Concert programma\'s',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Repertoire',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_programma2' => [
        'type' => 'entity_reference',
        'label' => 'Programma Onderdelen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
    ],
  ],
];

echo "Content types en velden aanmaken...\n\n";

foreach ($content_types_config as $type_id => $type_info) {
  echo "Verwerken content type: {$type_info['name']}\n";
  
  // Create content type if it doesn't exist
  if (!NodeType::load($type_id)) {
    $node_type = NodeType::create([
      'type' => $type_id,
      'name' => $type_info['name'],
      'description' => $type_info['description'],
      'help' => '',
      'new_revision' => TRUE,
      'preview_mode' => 1,
      'display_submitted' => TRUE,
    ]);
    $node_type->save();
    echo "  ✓ Content type aangemaakt: {$type_info['name']}\n";
  } else {
    echo "  - Content type '{$type_id}' bestaat al, overslaan.\n";
  }
  
  // Create fields
  if (isset($type_info['fields']) && !empty($type_info['fields'])) {
    foreach ($type_info['fields'] as $field_name => $field_config) {
      echo "    Verwerken veld: {$field_config['label']}\n";
      
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
        echo "      ✓ Field storage aangemaakt voor: {$field_name}\n";
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
        
        $field_instance = FieldConfig::create($instance_config);
        $field_instance->save();
        echo "      ✓ Field instance aangemaakt: {$field_config['label']}\n";
      } else {
        echo "      - Veld '{$field_name}' bestaat al voor {$type_id}\n";
      }
    }
  } else {
    echo "    Geen aangepaste velden gedefinieerd voor dit content type.\n";
  }
  
  echo "\n";
}

echo "Content types en velden succesvol aangemaakt!\n\n";
echo "Volgende stappen:\n";
echo "1. Configureer formulier en weergave modes via admin UI indien nodig\n";
echo "2. Stel media types in voor bestandsvelden indien Media module gebruikt wordt\n";
echo "3. Configureer field widgets en formatters\n";
echo "4. Voer de migratie uit: drush migrate:import --group=thirdwing_d6\n";
echo "\nAlle content types hebben nu hun velden klaar voor migratie!\n";