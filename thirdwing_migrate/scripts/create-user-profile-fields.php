<?php

/**
 * @file
 * Script to create user profile fields for Thirdwing migration.
 * File: thirdwing_migrate/scripts/create-user-profile-fields.php
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Main function to create user profile fields.
 */
function createUserProfileFields() {
  echo "Creating Thirdwing user profile fields...\n";
  
  try {
    // Create field storages
    createUserFieldStorages();
    
    // Create field instances
    createUserFieldInstances();
    
    // Configure field displays
    configureUserFieldDisplays();
    
    // Print summary
    printUserFieldsSummary();
    
    echo "✅ User profile fields created successfully!\n";
    
  } catch (Exception $e) {
    echo "❌ Error creating user profile fields: " . $e->getMessage() . "\n";
    throw $e;
  }
}

/**
 * Create field storages for user profile fields.
 */
function createUserFieldStorages() {
  echo "Creating user field storages...\n";
  
  $user_fields = getUserProfileFieldDefinitions();
  
  foreach ($user_fields as $field_name => $field_info) {
    // Check if field storage already exists
    if (FieldStorageConfig::loadByName('user', $field_name)) {
      echo "  Field storage '$field_name' already exists\n";
      continue;
    }
    
    // Create field storage
    $field_storage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'user',
      'type' => $field_info['type'],
      'cardinality' => $field_info['cardinality'] ?? 1,
      'settings' => $field_info['storage_settings'] ?? [],
    ]);
    
    $field_storage->save();
    echo "  ✅ Created field storage: {$field_info['label']} ($field_name)\n";
  }
}

/**
 * Create field instances for user profile fields.
 */
function createUserFieldInstances() {
  echo "Creating user field instances...\n";
  
  $user_fields = getUserProfileFieldDefinitions();
  
  foreach ($user_fields as $field_name => $field_info) {
    // Check if field instance already exists
    if (FieldConfig::loadByName('user', 'user', $field_name)) {
      echo "  Field instance '$field_name' already exists\n";
      continue;
    }
    
    // Get field storage
    $field_storage = FieldStorageConfig::loadByName('user', $field_name);
    if (!$field_storage) {
      echo "  ❌ Field storage '$field_name' not found\n";
      continue;
    }
    
    // Create field instance
    $field_config = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'user',
      'label' => $field_info['label'],
      'description' => $field_info['description'] ?? '',
      'required' => $field_info['required'] ?? FALSE,
      'settings' => $field_info['field_settings'] ?? [],
    ]);
    
    $field_config->save();
    echo "  ✅ Created field instance: {$field_info['label']} ($field_name)\n";
  }
}

/**
 * Configure field displays for user profile fields.
 */
function configureUserFieldDisplays() {
  echo "Configuring user field displays...\n";
  
  // Load or create form display
  $form_display = \Drupal::entityTypeManager()
    ->getStorage('entity_form_display')
    ->load('user.user.default');
    
  if (!$form_display) {
    $form_display = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->create([
        'targetEntityType' => 'user',
        'bundle' => 'user',
        'mode' => 'default',
      ]);
  }
  
  // Load or create view display
  $view_display = \Drupal::entityTypeManager()
    ->getStorage('entity_view_display')
    ->load('user.user.default');
    
  if (!$view_display) {
    $view_display = \Drupal::entityTypeManager()
      ->getStorage('entity_view_display')
      ->create([
        'targetEntityType' => 'user',
        'bundle' => 'user',
        'mode' => 'default',
      ]);
  }
  
  $user_fields = getUserProfileFieldDefinitions();
  $weight = 10;
  
  foreach ($user_fields as $field_name => $field_info) {
    // Configure form display
    $form_display->setComponent($field_name, [
      'type' => $field_info['form_widget'] ?? 'string_textfield',
      'weight' => $weight,
      'settings' => $field_info['widget_settings'] ?? [],
    ]);
    
    // Configure view display
    $view_display->setComponent($field_name, [
      'type' => $field_info['view_formatter'] ?? 'string',
      'weight' => $weight,
      'settings' => $field_info['formatter_settings'] ?? [],
    ]);
    
    $weight++;
  }
  
  $form_display->save();
  $view_display->save();
  
  echo "  ✅ Configured user field displays\n";
}

/**
 * Get user profile field definitions.
 */
function getUserProfileFieldDefinitions() {
  return [
    // Personal Information
    'field_voornaam' => [
      'type' => 'string',
      'label' => 'Voornaam',
      'description' => 'Voornaam van het lid',
      'storage_settings' => ['max_length' => 255],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_achternaam' => [
      'type' => 'string',
      'label' => 'Achternaam',
      'description' => 'Achternaam van het lid',
      'storage_settings' => ['max_length' => 255],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_achternaam_voorvoegsel' => [
      'type' => 'string',
      'label' => 'Achternaam voorvoegsel',
      'description' => 'Voorvoegsel van de achternaam (van, de, etc.)',
      'storage_settings' => ['max_length' => 50],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_geboortedatum' => [
      'type' => 'datetime',
      'label' => 'Geboortedatum',
      'description' => 'Geboortedatum van het lid',
      'storage_settings' => ['datetime_type' => 'date'],
      'form_widget' => 'datetime_default',
      'view_formatter' => 'datetime_default',
    ],
    'field_geslacht' => [
      'type' => 'list_string',
      'label' => 'Geslacht',
      'description' => 'Geslacht van het lid',
      'storage_settings' => [
        'allowed_values' => [
          'm' => 'Man',
          'v' => 'Vrouw',
          'x' => 'Anders',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    
    // Contact Information
    'field_adres' => [
      'type' => 'string',
      'label' => 'Adres',
      'description' => 'Straatnaam en huisnummer',
      'storage_settings' => ['max_length' => 255],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_postcode' => [
      'type' => 'string',
      'label' => 'Postcode',
      'description' => 'Postcode',
      'storage_settings' => ['max_length' => 10],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'label' => 'Woonplaats',
      'description' => 'Woonplaats',
      'storage_settings' => ['max_length' => 255],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_telefoon' => [
      'type' => 'string',
      'label' => 'Telefoon',
      'description' => 'Telefoonnummer',
      'storage_settings' => ['max_length' => 20],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_mobiel' => [
      'type' => 'string',
      'label' => 'Mobiel',
      'description' => 'Mobiel telefoonnummer',
      'storage_settings' => ['max_length' => 20],
      'form_widget' => 'string_textfield',
      'view_formatter' => 'string',
    ],
    'field_emailbewaking' => [
      'type' => 'string',
      'label' => 'Email origineel',
      'description' => 'Origineel email adres voor bewaking',
      'storage_settings' => ['max_length' => 255],
      'form_widget' => 'email_default',
      'view_formatter' => 'basic_string',
    ],
    
    // Choir Information
    'field_lidsinds' => [
      'type' => 'datetime',
      'label' => 'Lid sinds',
      'description' => 'Datum vanaf wanneer lid van het koor',
      'storage_settings' => ['datetime_type' => 'date'],
      'form_widget' => 'datetime_default',
      'view_formatter' => 'datetime_default',
    ],
    'field_uitkoor' => [
      'type' => 'datetime',
      'label' => 'Uit koor per',
      'description' => 'Datum uitgetreden uit het koor',
      'storage_settings' => ['datetime_type' => 'date'],
      'form_widget' => 'datetime_default',
      'view_formatter' => 'datetime_default',
    ],
    'field_koor' => [
      'type' => 'list_string',
      'label' => 'Koorfunctie',
      'description' => 'Functie binnen het koor',
      'storage_settings' => [
        'allowed_values' => [
          'sopraan' => 'Sopraan',
          'alt' => 'Alt',
          'tenor' => 'Tenor',
          'bas' => 'Bas',
          'dirigent' => 'Dirigent',
          'pianist' => 'Pianist',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_positie' => [
      'type' => 'list_string',
      'label' => 'Positie',
      'description' => 'Positie binnen de stemgroep',
      'storage_settings' => [
        'allowed_values' => [
          'links' => 'Links',
          'midden' => 'Midden',
          'rechts' => 'Rechts',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_sleepgroep' => [
      'type' => 'list_string',
      'label' => 'Sleepgroep',
      'description' => 'Sleepgroep indeling',
      'storage_settings' => [
        'allowed_values' => [
          'a' => 'Groep A',
          'b' => 'Groep B',
          'c' => 'Groep C',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_karrijder' => [
      'type' => 'boolean',
      'label' => 'Karrijder',
      'description' => 'Heeft auto beschikbaar voor vervoer',
      'form_widget' => 'boolean_checkbox',
      'view_formatter' => 'boolean',
    ],
    
    // Committee Functions
    'field_functie_bestuur' => [
      'type' => 'list_string',
      'label' => 'Functie Bestuur',
      'description' => 'Functie binnen het bestuur',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'secretaris' => 'Secretaris',
          'penningmeester' => 'Penningmeester',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_mc' => [
      'type' => 'list_string',
      'label' => 'Functie Muziekcommissie',
      'description' => 'Functie binnen de muziekcommissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_concert' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Concerten',
      'description' => 'Functie binnen de concertcommissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_feest' => [
      'type' => 'list_string',
      'label' => 'Functie Feestcommissie',
      'description' => 'Functie binnen de feestcommissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_regie' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Koorregie',
      'description' => 'Functie binnen de regiecommissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_ir' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie Interne Relaties',
      'description' => 'Functie binnen de IR commissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_pr' => [
      'type' => 'list_string',
      'label' => 'Functie Commissie PR',
      'description' => 'Functie binnen de PR commissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_tec' => [
      'type' => 'list_string',
      'label' => 'Functie Technische Commissie',
      'description' => 'Functie binnen de technische commissie',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_lw' => [
      'type' => 'list_string',
      'label' => 'Functie ledenwerf',
      'description' => 'Functie bij de ledenwerving',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    'field_functie_fl' => [
      'type' => 'list_string',
      'label' => 'Functie Faciliteiten',
      'description' => 'Functie binnen faciliteiten',
      'storage_settings' => [
        'allowed_values' => [
          'voorzitter' => 'Voorzitter',
          'lid' => 'Lid',
        ],
      ],
      'form_widget' => 'options_select',
      'view_formatter' => 'list_default',
    ],
    
    // Additional Information
    'field_notes' => [
      'type' => 'text_long',
      'label' => 'Notities',
      'description' => 'Aanvullende notities over het lid',
      'form_widget' => 'text_textarea',
      'view_formatter' => 'text_default',
    ],
  ];
}

/**
 * Print summary of created user profile fields.
 */
function printUserFieldsSummary() {
  echo "\n=== USER PROFILE FIELDS SUMMARY ===\n";
  
  $user_fields = getUserProfileFieldDefinitions();
  $total_fields = count($user_fields);
  
  echo "User Profile Fields Created: $total_fields\n";
  
  $categories = [
    'Personal Information' => ['field_voornaam', 'field_achternaam', 'field_achternaam_voorvoegsel', 'field_geboortedatum', 'field_geslacht'],
    'Contact Information' => ['field_adres', 'field_postcode', 'field_woonplaats', 'field_telefoon', 'field_mobiel', 'field_emailbewaking'],
    'Choir Information' => ['field_lidsinds', 'field_uitkoor', 'field_koor', 'field_positie', 'field_sleepgroep', 'field_karrijder'],
    'Committee Functions' => ['field_functie_bestuur', 'field_functie_mc', 'field_functie_concert', 'field_functie_feest', 'field_functie_regie', 'field_functie_ir', 'field_functie_pr', 'field_functie_tec', 'field_functie_lw', 'field_functie_fl'],
    'Additional Information' => ['field_notes'],
  ];
  
  foreach ($categories as $category => $fields) {
    echo "\n$category:\n";
    foreach ($fields as $field_name) {
      if (isset($user_fields[$field_name])) {
        echo "  - {$user_fields[$field_name]['label']} ($field_name)\n";
      }
    }
  }
  
  echo "\n✅ User profile system ready for migration!\n";
}

// Execute the main function
createUserProfileFields();