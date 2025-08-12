<?php

/**
 * @file
 * NEW script to create user profile fields to replace the Profile content type.
 * Based on "Drupal 11 Content types and fields.md" documentation.
 *
 * Usage: drush php:script create-user-profile-fields.php
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Main execution function.
 */
function createUserProfileFields() {
  echo "🚀 Creating User Profile Fields (Replaces Profile Content Type)...\n\n";
  
  // Get user profile field configurations
  $profile_fields = getUserProfileFieldDefinitions();
  
  // Step 1: Create field storages
  echo "📋 Creating user profile field storages...\n";
  foreach ($profile_fields as $field_name => $field_config) {
    createUserFieldStorage($field_name, $field_config);
  }
  
  // Step 2: Create field instances
  echo "\n🔧 Creating user profile field instances...\n";
  foreach ($profile_fields as $field_name => $field_config) {
    createUserFieldInstance($field_name, $field_config);
  }
  
  echo "\n✅ User profile fields creation complete!\n";
  printUserProfileSummary();
}

/**
 * Create field storage for user entity.
 */
function createUserFieldStorage($field_name, $field_config) {
  echo "  Creating field storage: {$field_name}\n";
  
  $field_storage = FieldStorageConfig::loadByName('user', $field_name);
  if (!$field_storage) {
    $storage_config = [
      'field_name' => $field_name,
      'entity_type' => 'user',
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

/**
 * Create field instance for user entity.
 */
function createUserFieldInstance($field_name, $field_config) {
  echo "  Creating field instance: {$field_name}\n";
  
  $field_storage = FieldStorageConfig::loadByName('user', $field_name);
  if (!$field_storage) {
    echo "    ⚠️  Field storage for '{$field_name}' not found\n";
    return;
  }
  
  $field_instance = FieldConfig::loadByName('user', 'user', $field_name);
  if (!$field_instance) {
    $instance_config = [
      'field_storage' => $field_storage,
      'bundle' => 'user',
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
  } else {
    echo "    - Already exists: {$field_name}\n";
  }
}

/**
 * Get user profile field definitions from documentation.
 */
function getUserProfileFieldDefinitions() {
  return [
    // Persoonlijk group
    'field_voornaam' => [
      'type' => 'string',
      'label' => 'Voornaam',
      'cardinality' => 1,
      'required' => TRUE,
      'settings' => ['max_length' => 255]
    ],
    'field_achternaam_voorvoegsel' => [
      'type' => 'string',
      'label' => 'Achternaam voorvoegsel',
      'cardinality' => 1,
      'settings' => ['max_length' => 50]
    ],
    'field_achternaam' => [
      'type' => 'string',
      'label' => 'Achternaam',
      'cardinality' => 1,
      'required' => TRUE,
      'settings' => ['max_length' => 255]
    ],
    'field_geslacht' => [
      'type' => 'list_string',
      'label' => 'Geslacht',
      'cardinality' => 1,
      'settings' => [
        'allowed_values' => [
          'm' => 'Man',
          'v' => 'Vrouw',
          'x' => 'Anders/Onbekend'
        ]
      ]
    ],
    'field_geboortedatum' => [
      'type' => 'datetime',
      'label' => 'Geboortedatum',
      'cardinality' => 1,
      'settings' => ['datetime_type' => 'date']
    ],
    'field_adres' => [
      'type' => 'string',
      'label' => 'Adres',
      'cardinality' => 1,
      'settings' => ['max_length' => 255]
    ],
    'field_postcode' => [
      'type' => 'string',
      'label' => 'Postcode',
      'cardinality' => 1,
      'settings' => ['max_length' => 10]
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'label' => 'Woonplaats',
      'cardinality' => 1,
      'settings' => ['max_length' => 255]
    ],
    'field_telefoon' => [
      'type' => 'telephone',
      'label' => 'Telefoon',
      'cardinality' => 1
    ],
    'field_mobiel' => [
      'type' => 'telephone',
      'label' => 'Mobiel',
      'cardinality' => 1
    ],
    
    // Koor group
    'field_lidsinds' => [
      'type' => 'datetime',
      'label' => 'Lid sinds',
      'cardinality' => 1,
      'settings' => ['datetime_type' => 'date']
    ],
    'field_uitkoor' => [
      'type' => 'datetime',
      'label' => 'Uit koor',
      'cardinality' => 1,
      'settings' => ['datetime_type' => 'date']
    ],
    'field_koor' => [
      'type' => 'list_string',
      'label' => 'Koor',
      'cardinality' => 1,
      'settings' => [
        'allowed_values' => [
          'hoofdkoor' => 'Hoofdkoor',
          'jongerenkoor' => 'Jongerenkoor',
          'kinderkoor' => 'Kinderkoor',
          'oud_lid' => 'Oud lid',
          'erelid' => 'Erelid'
        ]
      ]
    ],
    'field_positie' => [
      'type' => 'list_string',
      'label' => 'Positie',
      'cardinality' => 1,
      'settings' => [
        'allowed_values' => [
          'sopraan' => 'Sopraan',
          'alt' => 'Alt',
          'tenor' => 'Tenor',
          'bas' => 'Bas'
        ]
      ]
    ],
    'field_karrijder' => [
      'type' => 'boolean',
      'label' => 'Karrijder',
      'cardinality' => 1
    ],
    'field_sleepgroep_1' => [
      'type' => 'boolean',
      'label' => 'Sleepgroep 1',
      'cardinality' => 1
    ],
    
    // Commissies group
    'field_functie_bestuur' => [
      'type' => 'list_string',
      'label' => 'Functie Bestuur',
      'cardinality' => 1,
      'settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'secretaris' => 'Secretaris',
          'penningmeester' => 'Penningmeester',
          'artistiek_leider' => 'Artistiek leider',
          'lid' => 'Bestuurslid'
        ]
      ]
    ],
    'field_functie_mc' => [
      'type' => 'list_string',
      'label' => 'Functie Muziekcommissie',
      'cardinality' => 1,
      'settings' => [
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
      'settings' => [
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
      'settings' => [
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
      'settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_ir' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Interne Relaties',
      'cardinality' => 1,
      'settings' => [
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
      'settings' => [
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
      'settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    'field_functie_lw' => [
      'type' => 'list_string',
      'label' => 'Functie ledenwerf',
      'cardinality' => 1,
      'settings' => [
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
      'settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid'
        ]
      ]
    ],
    
    // Beheer group
    'field_emailbewaking' => [
      'type' => 'boolean',
      'label' => 'Email bewaking',
      'cardinality' => 1
    ],
    'field_notes' => [
      'type' => 'text_long',
      'label' => 'Notes',
      'cardinality' => 1
    ]
  ];
}

/**
 * Get field group definitions for organizing fields in the UI.
 */
function getUserProfileFieldGroups() {
  return [
    'Persoonlijk' => [
      'field_voornaam',
      'field_achternaam_voorvoegsel', 
      'field_achternaam',
      'field_geslacht',
      'field_geboortedatum',
      'field_adres',
      'field_postcode',
      'field_woonplaats',
      'field_telefoon',
      'field_mobiel'
    ],
    'Koor' => [
      'field_lidsinds',
      'field_uitkoor',
      'field_koor',
      'field_positie',
      'field_karrijder',
      'field_sleepgroep_1'
    ],
    'Commissies' => [
      'field_functie_bestuur',
      'field_functie_mc',
      'field_functie_concert',
      'field_functie_feest',
      'field_functie_regie',
      'field_functie_ir',
      'field_functie_pr',
      'field_functie_tec',
      'field_functie_lw',
      'field_functie_fl'
    ],
    'Beheer' => [
      'field_emailbewaking',
      'field_notes'
    ]
  ];
}

/**
 * Print summary of created user profile fields.
 */
function printUserProfileSummary() {
  $profile_fields = getUserProfileFieldDefinitions();
  $field_groups = getUserProfileFieldGroups();
  
  echo "\n📊 User Profile Fields Summary:\n";
  echo "  • Total Profile Fields: " . count($profile_fields) . "\n";
  echo "  • Field Groups: " . count($field_groups) . "\n\n";
  
  echo "📋 Profile Fields by Group:\n";
  foreach ($field_groups as $group_name => $field_names) {
    echo "  🏷️  {$group_name}:\n";
    foreach ($field_names as $field_name) {
      if (isset($profile_fields[$field_name])) {
        $field_config = $profile_fields[$field_name];
        $required = isset($field_config['required']) && $field_config['required'] ? ' (required)' : '';
        echo "    • {$field_config['label']}{$required}\n";
      }
    }
    echo "\n";
  }
  
  echo "🔄 Migration Benefits:\n";
  echo "  • Replaces D6 Profile content type with D11 user fields\n";
  echo "  • Integrated with user accounts (no separate content)\n";
  echo "  • Consistent with modern Drupal architecture\n";
  echo "  • Better performance and user experience\n";
  echo "  • Simplified permission management\n\n";
  
  echo "📋 Next Steps:\n";
  echo "  1. Configure user account form display\n";
  echo "  2. Set up field group layout (install field_group module)\n";
  echo "  3. Configure user permissions for profile editing\n";
  echo "  4. Test user registration and profile editing\n";
  echo "  5. Run migration to transfer Profile content type data\n\n";
  
  echo "📋 Verification Commands:\n";
  echo "  drush entity:info user\n";
  echo "  drush config:export\n";
  echo "  Visit: /admin/config/people/accounts/fields\n";
}

// Execute the script
try {
  createUserProfileFields();
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}