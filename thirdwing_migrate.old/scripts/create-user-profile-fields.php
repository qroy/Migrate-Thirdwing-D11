<?php

/**
 * @file
 * GECORRIGEERD script om user profile velden aan te maken volgens documentatie.
 * Gebaseerd op "Drupal 11 Content types and fields.md" documentatie.
 *
 * Usage: drush php:script create-user-profile-fields.php
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Hoofduitvoeringsfunctie.
 */
function createUserProfileFields() {
  echo "🚀 Aanmaken van User Profile Velden (VOLGENS DOCUMENTATIE)...\n\n";
  
  // Krijg alle user profile velden uit documentatie
  $user_fields = getUserProfileFieldConfigurations();
  
  echo "📋 Aanmaken van " . count($user_fields) . " user profile velden...\n";
  
  foreach ($user_fields as $field_name => $field_config) {
    echo "  Verwerken van veld: {$field_name}\n";
    
    // Maak field storage aan als deze niet bestaat
    $field_storage = FieldStorageConfig::loadByName('user', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'user',
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
    
    // Maak field instance aan als deze niet bestaat
    $field_instance = FieldConfig::loadByName('user', 'user', $field_name);
    if (!$field_instance) {
      $instance_config = [
        'field_storage' => $field_storage,
        'bundle' => 'user',
        'label' => $field_config['label'],
        'required' => $field_config['required'] ?? FALSE,
      ];
      
      // Voeg instance settings toe indien aanwezig
      if (isset($field_config['instance_settings'])) {
        $instance_config['settings'] = $field_config['instance_settings'];
      }
      
      $field_instance = FieldConfig::create($instance_config);
      $field_instance->save();
      echo "    ✓ Field instance aangemaakt: {$field_config['label']}\n";
    } else {
      echo "    - Field instance '{$field_name}' bestaat al\n";
    }
  }
  
  echo "\n✅ User profile velden aanmaak voltooid!\n";
  printUserProfileFieldsSummary();
}

/**
 * Krijg user profile velden configuraties volgens documentatie.
 */
function getUserProfileFieldConfigurations() {
  return [
    'field_emailbewaking' => [
      'type' => 'string',
      'label' => 'Email origineel',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_lidsinds' => [
      'type' => 'datetime',
      'label' => 'Lid Sinds',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'datetime_type' => 'date'
      ]
    ],
    'field_koor' => [
      'type' => 'list_string',
      'label' => 'Koorfunctie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'lid' => 'Lid',
          'aspirant' => 'Aspirant-lid',
          'oud_lid' => 'Oud-lid'
        ]
      ]
    ],
    'field_sleepgroep_1' => [
      'type' => 'list_string',
      'label' => 'Sleepgroep',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          '1' => 'Sleepgroep 1',
          '2' => 'Sleepgroep 2',
          'geen' => 'Geen sleepgroep'
        ]
      ]
    ],
    'field_voornaam' => [
      'type' => 'string',
      'label' => 'Voornaam',
      'cardinality' => 1,
      'required' => TRUE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_achternaam_voorvoegsel' => [
      'type' => 'string',
      'label' => 'Achternaam voorvoegsel',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_achternaam' => [
      'type' => 'string',
      'label' => 'Achternaam',
      'cardinality' => 1,
      'required' => TRUE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_geboortedatum' => [
      'type' => 'datetime',
      'label' => 'Geboortedatum',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'datetime_type' => 'date'
      ]
    ],
    'field_geslacht' => [
      'type' => 'list_string',
      'label' => 'Geslacht',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'm' => 'Man',
          'v' => 'Vrouw',
          'x' => 'Anders'
        ]
      ]
    ],
    'field_karrijder' => [
      'type' => 'boolean',
      'label' => 'Karrijder',
      'cardinality' => 1,
      'required' => FALSE
    ],
    'field_uitkoor' => [
      'type' => 'datetime',
      'label' => 'Uit koor per',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'datetime_type' => 'date'
      ]
    ],
    'field_adres' => [
      'type' => 'string',
      'label' => 'Adres',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_postcode' => [
      'type' => 'string',
      'label' => 'Postcode',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_telefoon' => [
      'type' => 'string',
      'label' => 'Telefoon',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_mobiel' => [
      'type' => 'string',
      'label' => 'Mobiel',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_notes' => [
      'type' => 'text_long',
      'label' => 'Notities',
      'cardinality' => 1,
      'required' => FALSE
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'label' => 'Woonplaats',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'max_length' => 255
      ]
    ],
    'field_positie' => [
      'type' => 'list_string',
      'label' => 'Positie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'sopraan' => 'Sopraan',
          'alt' => 'Alt',
          'tenor' => 'Tenor',
          'bas' => 'Bas'
        ]
      ]
    ],
    
    // Commissie functies
    'field_functie_bestuur' => [
      'type' => 'list_string',
      'label' => 'Functie Bestuur',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'secretaris' => 'Secretaris',
          'penningmeester' => 'Penningmeester',
          'lid' => 'Bestuurslid'
        ]
      ]
    ],
    'field_functie_mc' => [
      'type' => 'list_string',
      'label' => 'Functie Muziekcommissie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_concert' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Concerten',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_feest' => [
      'type' => 'list_string',
      'label' => 'Functie Feestcommissie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_regie' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Koorregie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'dirigent' => 'Dirigent',
          'repetitor' => 'Repetitor',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_ir' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Interne Relaties',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_pr' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie PR',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_tec' => [
      'type' => 'list_string',
      'label' => 'Functie Technische Commissie',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'technicus' => 'Technicus',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_lw' => [
      'type' => 'list_string',
      'label' => 'Functie ledenwerf',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_fl' => [
      'type' => 'list_string',
      'label' => 'Functie Faciliteiten',
      'cardinality' => 1,
      'required' => FALSE,
      'storage_settings' => [
        'allowed_values' => [
          'beheerder' => 'Beheerder',
          'lid' => 'Lid'
        ]
      ]
    ]
  ];
}

/**
 * Print samenvatting van aangemaakte user profile velden.
 */
function printUserProfileFieldsSummary() {
  echo "\n📊 SAMENVATTING USER PROFILE VELDEN\n";
  echo "=" . str_repeat("=", 50) . "\n";
  
  $user_fields = getUserProfileFieldConfigurations();
  echo "✅ User Profile Velden Aangemaakt: " . count($user_fields) . "\n\n";
  
  // Groepeer velden per categorie
  $categories = [
    'Persoonlijke gegevens' => [
      'field_voornaam', 'field_achternaam_voorvoegsel', 'field_achternaam',
      'field_geboortedatum', 'field_geslacht', 'field_adres', 'field_postcode',
      'field_woonplaats', 'field_telefoon', 'field_mobiel'
    ],
    'Lidmaatschap' => [
      'field_lidsinds', 'field_uitkoor', 'field_koor', 'field_positie',
      'field_karrijder', 'field_sleepgroep_1'
    ],
    'Commissie functies' => [
      'field_functie_bestuur', 'field_functie_mc', 'field_functie_concert',
      'field_functie_feest', 'field_functie_regie', 'field_functie_ir',
      'field_functie_pr', 'field_functie_tec', 'field_functie_lw', 'field_functie_fl'
    ],
    'Overig' => [
      'field_emailbewaking', 'field_notes'
    ]
  ];
  
  foreach ($categories as $category => $fields) {
    echo "📋 {$category}:\n";
    foreach ($fields as $field_name) {
      if (isset($user_fields[$field_name])) {
        echo "   • {$user_fields[$field_name]['label']} ({$field_name})\n";
      }
    }
    echo "\n";
  }
  
  echo "🎯 VOLGENDE STAPPEN:\n";
  echo "1. Valideer user profile velden: drush php:script validate-created-fields.php\n";
  echo "2. Configureer user profile displays\n";
  echo "3. Stel permissions in voor user profile velden\n";
  echo "4. Test gebruikersregistratie en profiel bewerking\n";
  echo "5. Begin met content migratie\n";
}

// Voer het script uit
try {
  createUserProfileFields();
} catch (Exception $e) {
  echo "❌ Aanmaak gefaald: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}