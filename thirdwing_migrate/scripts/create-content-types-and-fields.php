<?php

/**
 * @file
 * GECORRIGEERD script om content types en velden aan te maken volgens documentatie.
 * Gebaseerd op "Drupal 11 Content types and fields.md" documentatie.
 *
 * Usage: drush php:script create-content-types-and-fields.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Hoofduitvoeringsfunctie.
 */
function createContentTypesAndFields() {
  echo "🚀 Aanmaken van Content Types en Velden (GECORRIGEERDE VERSIE)...\n\n";
  
  // Stap 1: Maak content types aan
  echo "📦 Aanmaken van content types...\n";
  createContentTypes();
  
  // Stap 2: Maak gedeelde velden aan (eerst, zodat ze herbruikbaar zijn)
  echo "\n📋 Aanmaken van gedeelde velden...\n";
  createSharedFields();
  
  // Stap 3: Maak content type specifieke velden aan
  echo "\n🔧 Aanmaken van content type specifieke velden...\n";
  createContentTypeSpecificFields();
  
  // Stap 4: Koppel velden aan content types
  echo "\n🔗 Koppelen van velden aan content types...\n";
  attachFieldsToContentTypes();
  
  echo "\n✅ Content types en velden aanmaak voltooid!\n";
  printContentTypesSummary();
}

/**
 * Maak alle content types aan.
 */
function createContentTypes() {
  $content_types = getContentTypeConfigurations();
  
  foreach ($content_types as $type_id => $config) {
    $node_type = NodeType::load($type_id);
    
    if (!$node_type) {
      $node_type = NodeType::create([
        'type' => $type_id,
        'name' => $config['name'],
        'description' => $config['description'],
        'title_label' => $config['title_label'],
        'display_submitted' => FALSE,
        'new_revision' => TRUE,
      ]);
      
      $node_type->save();
      echo "  ✅ Aangemaakt: {$config['name']} ({$type_id})\n";
      
      // Configureer body veld indien nodig
      if (isset($config['has_body']) && $config['has_body']) {
        node_add_body_field($node_type, $config['body_label']);
        echo "    ✓ Body veld toegevoegd: {$config['body_label']}\n";
      } else {
        // Verwijder body veld als het niet nodig is
        $field = FieldConfig::loadByName('node', $type_id, 'body');
        if ($field) {
          $field->delete();
          echo "    ✓ Body veld verwijderd\n";
        }
      }
    } else {
      echo "  - Content type '{$type_id}' bestaat al\n";
    }
  }
}

/**
 * Maak gedeelde velden aan die door meerdere content types gebruikt worden.
 */
function createSharedFields() {
  $shared_fields = getSharedFieldConfigurations();
  
  foreach ($shared_fields as $field_name => $field_config) {
    echo "  Verwerken van gedeeld veld: {$field_name}\n";
    
    // Maak field storage aan als deze niet bestaat
    $field_storage = FieldStorageConfig::loadByName('node', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'node',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      // Voeg storage settings toe indien aanwezig
      if (isset($field_config['storage_settings'])) {
        $storage_config['settings'] = $field_config['storage_settings'];
      }
      
      $field_storage = FieldStorageConfig::create($storage_config);
      $field_storage->save();
      echo "    ✓ Field storage aangemaakt voor: {$field_name}\n";
    } else {
      echo "    - Field storage '{$field_name}' bestaat al\n";
    }
  }
}

/**
 * Maak content type specifieke velden aan.
 */
function createContentTypeSpecificFields() {
  $content_type_fields = getContentTypeSpecificFieldConfigurations();
  
  foreach ($content_type_fields as $content_type => $fields) {
    echo "  Aanmaken van specifieke velden voor: {$content_type}\n";
    
    foreach ($fields as $field_name => $field_config) {
      echo "    Verwerken van veld: {$field_name}\n";
      
      // Maak field storage aan als deze niet bestaat
      $field_storage = FieldStorageConfig::loadByName('node', $field_name);
      if (!$field_storage) {
        $storage_config = [
          'field_name' => $field_name,
          'entity_type' => 'node',
          'type' => $field_config['type'],
          'cardinality' => $field_config['cardinality'] ?? 1,
        ];
        
        // Voeg storage settings toe indien aanwezig
        if (isset($field_config['storage_settings'])) {
          $storage_config['settings'] = $field_config['storage_settings'];
        }
        
        $field_storage = FieldStorageConfig::create($storage_config);
        $field_storage->save();
        echo "      ✓ Field storage aangemaakt: {$field_name}\n";
      } else {
        echo "      - Field storage '{$field_name}' bestaat al\n";
      }
    }
  }
}

/**
 * Koppel velden aan content types.
 */
function attachFieldsToContentTypes() {
  $content_type_field_mappings = getContentTypeFieldMappings();
  
  foreach ($content_type_field_mappings as $content_type => $fields) {
    echo "  Koppelen van velden aan: {$content_type}\n";
    
    foreach ($fields as $field_name => $field_config) {
      $field_instance = FieldConfig::loadByName('node', $content_type, $field_name);
      
      if (!$field_instance) {
        $field_storage = FieldStorageConfig::loadByName('node', $field_name);
        
        if ($field_storage) {
          $instance_config = [
            'field_storage' => $field_storage,
            'bundle' => $content_type,
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
          echo "    ✓ Veld gekoppeld: {$field_config['label']}\n";
        } else {
          echo "    ❌ Field storage niet gevonden voor: {$field_name}\n";
        }
      } else {
        echo "    - Veld '{$field_name}' al gekoppeld aan {$content_type}\n";
      }
    }
  }
}

/**
 * Krijg content type configuraties volgens documentatie.
 */
function getContentTypeConfigurations() {
  return [
    'activiteit' => [
      'name' => 'Activiteit',
      'description' => 'Een activiteit (uitvoering, repetitie)',
      'title_label' => 'Omschrijving',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'foto' => [
      'name' => 'Foto',
      'description' => 'Foto-album',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Omschrijving'
    ],
    'locatie' => [
      'name' => 'Locatie',
      'description' => 'Veelvoorkomende locaties van uitvoeringen',
      'title_label' => 'Titel',
      'has_body' => FALSE
    ],
    'nieuws' => [
      'name' => 'Nieuws',
      'description' => 'Een nieuwsbericht. Dit kan een publiek nieuwsbericht zijn, maar ook een nieuwsbericht voor op de ledenpagina.',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'pagina' => [
      'name' => 'Pagina',
      'description' => 'Gebruik een \'Pagina\' wanneer je een statische pagina wilt toevoegen',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'programma' => [
      'name' => 'Programma',
      'description' => 'Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst',
      'title_label' => 'Titel',
      'has_body' => FALSE
    ],
    'repertoire' => [
      'name' => 'Repertoire',
      'description' => 'Stuk uit het repertoire',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'vriend' => [
      'name' => 'Vriend',
      'description' => 'Vrienden van de vereniging',
      'title_label' => 'Naam',
      'has_body' => FALSE
    ],
    'webform' => [
      'name' => 'Webform',
      'description' => 'Webformulier voor gebruikersinvoer',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ]
  ];
}

/**
 * Krijg gedeelde velden configuraties.
 */
function getSharedFieldConfigurations() {
  return [
    'field_afbeeldingen' => [
      'type' => 'entity_reference',
      'cardinality' => -1,
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ],
    'field_audio_type' => [
      'type' => 'list_string',
      'cardinality' => 1,
      'storage_settings' => [
        'allowed_values' => [
          'repetitie' => 'Repetitie',
          'opname' => 'Opname',
          'uitvoering' => 'Uitvoering'
        ]
      ]
    ],
    'field_audio_uitvoerende' => [
      'type' => 'string',
      'cardinality' => 1,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_datum' => [
      'type' => 'datetime',
      'cardinality' => 1,
      'storage_settings' => [
        'datetime_type' => 'datetime'
      ]
    ],
    'field_files' => [
      'type' => 'entity_reference',
      'cardinality' => -1,
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ],
    'field_inhoud' => [
      'type' => 'entity_reference',
      'cardinality' => -1,
      'storage_settings' => [
        'target_type' => 'node'
      ]
    ],
    'field_l_routelink' => [
      'type' => 'link',
      'cardinality' => 1
    ],
    'field_programma2' => [
      'type' => 'entity_reference',
      'cardinality' => -1,
      'storage_settings' => [
        'target_type' => 'node'
      ]
    ],
    'field_ref_activiteit' => [
      'type' => 'entity_reference',
      'cardinality' => 1,
      'storage_settings' => [
        'target_type' => 'node'
      ]
    ],
    'field_gerelateerd_repertoire' => [
      'type' => 'entity_reference',
      'cardinality' => -1,
      'storage_settings' => [
        'target_type' => 'node'
      ]
    ],
    'field_video' => [
      'type' => 'text_long',
      'cardinality' => 1
    ],
    'field_view' => [
      'type' => 'string',
      'cardinality' => 1,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'cardinality' => 1,
      'storage_settings' => [
        'max_length' => 255
      ]
    ]
  ];
}

/**
 * Krijg content type specifieke velden configuraties.
 */
function getContentTypeSpecificFieldConfigurations() {
  return [
    'activiteit' => [
      'field_tijd_aanwezig' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_keyboard' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'ja' => 'Ja',
            'nee' => 'Nee',
            'misschien' => 'Misschien'
          ]
        ]
      ],
      'field_gitaar' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'ja' => 'Ja',
            'nee' => 'Nee',
            'misschien' => 'Misschien'
          ]
        ]
      ],
      'field_basgitaar' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'ja' => 'Ja',
            'nee' => 'Nee',
            'misschien' => 'Misschien'
          ]
        ]
      ],
      'field_drums' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'ja' => 'Ja',
            'nee' => 'Nee',
            'misschien' => 'Misschien'
          ]
        ]
      ],
      'field_vervoer' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_sleepgroep' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            '1' => 'Sleepgroep 1',
            '2' => 'Sleepgroep 2',
            'geen' => 'Geen'
          ]
        ]
      ],
      'field_sleepgroep_aanwezig' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_kledingcode' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_locatie' => [
        'type' => 'entity_reference',
        'cardinality' => 1,
        'storage_settings' => ['target_type' => 'node']
      ],
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'cardinality' => 1
      ],
      'field_bijzonderheden' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_background' => [
        'type' => 'entity_reference',
        'cardinality' => 1,
        'storage_settings' => ['target_type' => 'media']
      ],
      'field_sleepgroep_terug' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            '1' => 'Sleepgroep 1',
            '2' => 'Sleepgroep 2',
            'geen' => 'Geen'
          ]
        ]
      ],
      'field_huiswerk' => [
        'type' => 'entity_reference',
        'cardinality' => 1,
        'storage_settings' => ['target_type' => 'media']
      ]
    ],
    
    'locatie' => [
      'field_l_adres' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_l_plaats' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_l_postcode' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ]
    ],
    
    'programma' => [
      'field_prog_type' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'opening' => 'Opening',
            'pauze' => 'Pauze',
            'slot' => 'Slot',
            'toegift' => 'Toegift'
          ]
        ]
      ]
    ],
    
    'repertoire' => [
      'field_rep_arr' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_rep_arr_jaar' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_rep_componist' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_rep_componist_jaar' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_rep_genre' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'pop' => 'Pop',
            'rock' => 'Rock',
            'klassiek' => 'Klassiek',
            'musical' => 'Musical',
            'film' => 'Film',
            'overig' => 'Overig'
          ]
        ]
      ],
      'field_rep_sinds' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_rep_uitv' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_rep_uitv_jaar' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_positie' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'actueel' => 'Actueel',
            'archief' => 'Archief'
          ]
        ]
      ],
      'field_klapper' => [
        'type' => 'boolean',
        'cardinality' => 1
      ],
      'field_audio_nummer' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ],
      'field_audio_seizoen' => [
        'type' => 'string',
        'cardinality' => 1,
        'storage_settings' => ['max_length' => 255]
      ]
    ],
    
    'vriend' => [
      'field_vriend_website' => [
        'type' => 'link',
        'cardinality' => 1
      ],
      'field_vriend_soort' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'sponsor' => 'Sponsor',
            'donateur' => 'Donateur',
            'supporter' => 'Supporter'
          ]
        ]
      ],
      'field_vriend_benaming' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            'particulier' => 'Particulier',
            'bedrijf' => 'Bedrijf',
            'organisatie' => 'Organisatie'
          ]
        ]
      ],
      'field_vriend_periode_tot' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_vriend_periode_vanaf' => [
        'type' => 'integer',
        'cardinality' => 1
      ],
      'field_vriend_duur' => [
        'type' => 'list_string',
        'cardinality' => 1,
        'storage_settings' => [
          'allowed_values' => [
            '1jaar' => '1 jaar',
            '2jaar' => '2 jaar',
            'doorlopend' => 'Doorlopend'
          ]
        ]
      ]
    ]
  ];
}

/**
 * Krijg content type veld mappings.
 */
function getContentTypeFieldMappings() {
  return [
    'activiteit' => [
      // Content type specifieke velden
      'field_tijd_aanwezig' => ['label' => 'Koor Aanwezig', 'required' => FALSE],
      'field_keyboard' => ['label' => 'Toetsenist', 'required' => FALSE],
      'field_gitaar' => ['label' => 'Gitarist', 'required' => FALSE],
      'field_basgitaar' => ['label' => 'Basgitarist', 'required' => FALSE],
      'field_drums' => ['label' => 'Drummer', 'required' => FALSE],
      'field_vervoer' => ['label' => 'Karrijder', 'required' => FALSE],
      'field_sleepgroep' => ['label' => 'Sleepgroep', 'required' => FALSE],
      'field_sleepgroep_aanwezig' => ['label' => 'Sleepgroep Aanwezig', 'required' => FALSE],
      'field_kledingcode' => ['label' => 'Kledingcode', 'required' => FALSE],
      'field_locatie' => [
        'label' => 'Locatie',
        'required' => FALSE,
        'target_bundles' => ['locatie']
      ],
      'field_l_bijzonderheden' => ['label' => 'Bijzonderheden locatie', 'required' => FALSE],
      'field_bijzonderheden' => ['label' => 'Bijzonderheden', 'required' => FALSE],
      'field_background' => [
        'label' => 'Achtergrond',
        'required' => FALSE,
        'target_bundles' => ['image']
      ],
      'field_sleepgroep_terug' => ['label' => 'Sleepgroep terug', 'required' => FALSE],
      'field_huiswerk' => [
        'label' => 'Huiswerk',
        'required' => FALSE,
        'target_bundles' => ['document']
      ],
      // Gedeelde velden
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ],
      'field_files' => [
        'label' => 'Bestandsbijlages',
        'required' => FALSE,
        'target_bundles' => ['document']
      ],
      'field_programma2' => [
        'label' => 'Programma',
        'required' => FALSE,
        'target_bundles' => ['programma']
      ],
      'field_datum' => ['label' => 'Datum en tijd', 'required' => TRUE]
    ],
    
    'foto' => [
      'field_video' => ['label' => 'Video', 'required' => FALSE],
      'field_gerelateerd_repertoire' => [
        'label' => 'Gerelateerd Repertoire',
        'required' => FALSE,
        'target_bundles' => ['repertoire']
      ],
      'field_audio_uitvoerende' => ['label' => 'Uitvoerende', 'required' => FALSE],
      'field_audio_type' => ['label' => 'Type', 'required' => FALSE],
      'field_datum' => ['label' => 'Datum', 'required' => FALSE],
      'field_ref_activiteit' => [
        'label' => 'Activiteit',
        'required' => FALSE,
        'target_bundles' => ['activiteit']
      ]
    ],
    
    'locatie' => [
      'field_l_adres' => ['label' => 'Adres', 'required' => FALSE],
      'field_l_plaats' => ['label' => 'Plaats', 'required' => FALSE],
      'field_l_postcode' => ['label' => 'Postcode', 'required' => FALSE],
      'field_l_routelink' => ['label' => 'Route', 'required' => FALSE]
    ],
    
    'nieuws' => [
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ],
      'field_files' => [
        'label' => 'Bestandsbijlages',
        'required' => FALSE,
        'target_bundles' => ['document']
      ]
    ],
    
    'pagina' => [
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ],
      'field_files' => [
        'label' => 'Bestandsbijlages',
        'required' => FALSE,
        'target_bundles' => ['document']
      ],
      'field_view' => ['label' => 'Extra inhoud', 'required' => FALSE]
    ],
    
    'programma' => [
      'field_prog_type' => ['label' => 'Type', 'required' => FALSE]
    ],
    
    'repertoire' => [
      'field_rep_arr' => ['label' => 'Arrangeur', 'required' => FALSE],
      'field_rep_arr_jaar' => ['label' => 'Arrangeur Jaar', 'required' => FALSE],
      'field_rep_componist' => ['label' => 'Componist', 'required' => FALSE],
      'field_rep_componist_jaar' => ['label' => 'Componist Jaar', 'required' => FALSE],
      'field_rep_genre' => ['label' => 'Genre', 'required' => FALSE],
      'field_rep_sinds' => ['label' => 'Sinds', 'required' => FALSE],
      'field_rep_uitv' => ['label' => 'Uitvoering', 'required' => FALSE],
      'field_rep_uitv_jaar' => ['label' => 'Uitvoering Jaar', 'required' => FALSE],
      'field_positie' => ['label' => 'Positie', 'required' => FALSE],
      'field_klapper' => ['label' => 'Klapper', 'required' => FALSE],
      'field_audio_nummer' => ['label' => 'Nummer', 'required' => FALSE],
      'field_audio_seizoen' => ['label' => 'Seizoen', 'required' => FALSE]
    ],
    
    'vriend' => [
      'field_vriend_website' => ['label' => 'Website', 'required' => FALSE],
      'field_vriend_soort' => ['label' => 'Soort', 'required' => FALSE],
      'field_vriend_benaming' => ['label' => 'Benaming', 'required' => FALSE],
      'field_vriend_periode_tot' => ['label' => 'Vriend t/m', 'required' => FALSE],
      'field_vriend_periode_vanaf' => ['label' => 'Vriend vanaf', 'required' => FALSE],
      'field_vriend_duur' => ['label' => 'Vriendlengte', 'required' => FALSE],
      'field_woonplaats' => ['label' => 'Woonplaats', 'required' => FALSE],
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ]
    ],
    
    'webform' => [
      // Webform heeft geen specifieke velden - alleen standaard body
    ]
  ];
}

/**
 * Print samenvatting van aangemaakte content types.
 */
function printContentTypesSummary() {
  echo "\n📊 SAMENVATTING CONTENT TYPES EN VELDEN\n";
  echo "=" . str_repeat("=", 50) . "\n";
  
  $content_types = getContentTypeConfigurations();
  echo "✅ Content Types Aangemaakt: " . count($content_types) . "\n";
  foreach ($content_types as $type_id => $config) {
    echo "   • {$config['name']} ({$type_id})\n";
  }
  
  $shared_fields = getSharedFieldConfigurations();
  echo "\n✅ Gedeelde Velden Aangemaakt: " . count($shared_fields) . "\n";
  foreach ($shared_fields as $field_name => $config) {
    echo "   • {$field_name} ({$config['type']})\n";
  }
  
  $specific_fields = getContentTypeSpecificFieldConfigurations();
  $total_specific = 0;
  foreach ($specific_fields as $content_type => $fields) {
    $total_specific += count($fields);
  }
  echo "\n✅ Content Type Specifieke Velden: {$total_specific}\n";
  foreach ($specific_fields as $content_type => $fields) {
    echo "   • {$content_type}: " . count($fields) . " velden\n";
  }
  
  echo "\n🎯 VOLGENDE STAPPEN:\n";
  echo "1. Voer validatie uit: drush php:script validate-created-fields.php\n";
  echo "2. Maak media bundles aan: drush php:script create-media-bundles-and-fields.php\n";
  echo "3. Maak user profile velden aan: drush php:script create-user-profile-fields.php\n";
  echo "4. Configureer field displays: drush thirdwing:setup-displays\n";
  echo "5. Test content aanmaak in admin interface\n";
}

// Voer het script uit
try {
  createContentTypesAndFields();
} catch (Exception $e) {
  echo "❌ Aanmaak gefaald: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}