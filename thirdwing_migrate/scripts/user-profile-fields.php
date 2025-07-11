<?php

/**
 * @file
 * Create user profile fields for Thirdwing D6 to D11 migration.
 * 
 * Creates user profile fields that correspond to the D6 Content Profile
 * 'profiel' content type fields.
 */

use Drupal\Core\Database\Database;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

echo "=== CREATING USER PROFILE FIELDS ===\n\n";

// User profile fields configuration matching D6 Content Profile fields
$user_profile_fields = [
  // Personal information
  'field_voornaam' => [
    'type' => 'string',
    'label' => 'Voornaam',
    'settings' => ['max_length' => 100],
    'required' => TRUE,
  ],
  'field_achternaam' => [
    'type' => 'string',
    'label' => 'Achternaam',
    'settings' => ['max_length' => 100],
    'required' => TRUE,
  ],
  'field_tussenvoegsel' => [
    'type' => 'string',
    'label' => 'Tussenvoegsel',
    'settings' => ['max_length' => 20],
    'description' => 'Voorvoegsel achternaam (van, de, der, etc.)',
  ],
  'field_geslacht' => [
    'type' => 'list_string',
    'label' => 'Geslacht',
    'settings' => [
      'allowed_values' => [
        'm' => 'Man',
        'v' => 'Vrouw',
      ],
    ],
  ],
  'field_geboortedatum' => [
    'type' => 'datetime',
    'label' => 'Geboortedatum',
    'settings' => ['datetime_type' => 'date'],
  ],
  
  // Contact information
  'field_adres' => [
    'type' => 'text_long',
    'label' => 'Adres',
  ],
  'field_postcode' => [
    'type' => 'string',
    'label' => 'Postcode',
    'settings' => ['max_length' => 10],
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
  'field_mobiel' => [
    'type' => 'string',
    'label' => 'Mobiel',
    'settings' => ['max_length' => 50],
  ],
  
  // Choir membership information
  'field_lidsinds' => [
    'type' => 'datetime',
    'label' => 'Lid Sinds',
    'settings' => ['datetime_type' => 'date'],
    'description' => 'Datum waarop lid is geworden van Thirdwing',
  ],
  'field_uitkoor' => [
    'type' => 'datetime',
    'label' => 'Uit Koor Per',
    'settings' => ['datetime_type' => 'date'],
    'description' => 'Datum waarop lid uit koor is gegaan',
  ],
  'field_koor' => [
    'type' => 'string',
    'label' => 'Koor Functie',
    'settings' => ['max_length' => 100],
  ],
  'field_positie' => [
    'type' => 'string',
    'label' => 'Positie',
    'settings' => ['max_length' => 100],
    'description' => 'Zangpositie (sopraan, alt, tenor, bas)',
  ],
  
  // Transport and logistics
  'field_karrijder' => [
    'type' => 'boolean',
    'label' => 'Autorijder',
    'description' => 'Kan auto rijden voor vervoer',
  ],
  'field_sleepgroep' => [
    'type' => 'string',
    'label' => 'Sleepgroep',
    'settings' => ['max_length' => 100],
    'description' => 'Vervoersgroep voor concerten',
  ],
  
  // Committee and function fields
  'field_functie_bestuur' => [
    'type' => 'string',
    'label' => 'Functie Bestuur',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_mc' => [
    'type' => 'string',
    'label' => 'Functie Muziekcommissie',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_concert' => [
    'type' => 'string',
    'label' => 'Functie Commissie Concerten',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_feest' => [
    'type' => 'string',
    'label' => 'Functie Feestcommissie',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_regie' => [
    'type' => 'string',
    'label' => 'Functie Commissie Koorregie',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_ir' => [
    'type' => 'string',
    'label' => 'Functie Commissie Interne Relaties',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_pr' => [
    'type' => 'string',
    'label' => 'Functie Commissie Publieke Relaties',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_tec' => [
    'type' => 'string',
    'label' => 'Functie Technische Commissie',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_lw' => [
    'type' => 'string',
    'label' => 'Functie Commissie Ledenwerving',
    'settings' => ['max_length' => 100],
  ],
  'field_functie_fl' => [
    'type' => 'string',
    'label' => 'Functie Commissie Faciliteiten',
    'settings' => ['max_length' => 100],
  ],
  
  // Administrative fields
  'field_emailbewaking' => [
    'type' => 'boolean',
    'label' => 'Email Bewaking',
    'description' => 'Email monitoring ingeschakeld',
  ],
  'field_notes' => [
    'type' => 'text_long',
    'label' => 'Notities',
    'description' => 'Administratieve notities over dit lid',
  ],
];

echo "Creating user profile fields for Content Profile migration...\n\n";

foreach ($user_profile_fields as $field_name => $field_config) {
  echo "Processing field: {$field_config['label']} ({$field_name})\n";
  
  // Check if field storage exists
  $field_storage = FieldStorageConfig::loadByName('user', $field_name);
  if (!$field_storage) {
    // Create field storage
    $storage_config = [
      'field_name' => $field_name,
      'entity_type' => 'user',
      'type' => $field_config['type'],
      'cardinality' => $field_config['cardinality'] ?? 1,
    ];
    
    // Add settings if they exist
    if (isset($field_config['settings'])) {
      $storage_config['settings'] = $field_config['settings'];
    }
    
    $field_storage = FieldStorageConfig::create($storage_config);
    $field_storage->save();
    echo "  ✓ Field storage created for: {$field_name}\n";
  } else {
    echo "  - Field storage '{$field_name}' already exists\n";
  }
  
  // Check if field instance exists for user entity
  $field_instance = FieldConfig::loadByName('user', 'user', $field_name);
  if (!$field_instance) {
    // Create field instance
    $instance_config = [
      'field_storage' => $field_storage,
      'bundle' => 'user',
      'label' => $field_config['label'],
      'required' => $field_config['required'] ?? FALSE,
    ];
    
    // Add description if it exists
    if (isset($field_config['description'])) {
      $instance_config['description'] = $field_config['description'];
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
    echo "  ✓ Field instance created: {$field_config['label']}\n";
  } else {
    echo "  - Field '{$field_name}' already exists for user\n";
  }
  
  echo "\n";
}

echo "=== USER PROFILE FIELDS CREATION COMPLETE ===\n\n";

echo "✅ Created user profile fields for Content Profile migration:\n";
$personal_fields = ['field_voornaam', 'field_achternaam', 'field_tussenvoegsel', 'field_geslacht', 'field_geboortedatum'];
$contact_fields = ['field_adres', 'field_postcode', 'field_woonplaats', 'field_telefoon', 'field_mobiel'];
$choir_fields = ['field_lidsinds', 'field_uitkoor', 'field_koor', 'field_positie', 'field_karrijder', 'field_sleepgroep'];
$function_fields = array_filter(array_keys($user_profile_fields), function($field) {
  return strpos($field, 'field_functie_') === 0;
});
$admin_fields = ['field_emailbewaking', 'field_notes'];

echo "\n📝 Personal Information Fields (" . count($personal_fields) . "):\n";
foreach ($personal_fields as $field) {
  echo "  - {$field}: {$user_profile_fields[$field]['label']}\n";
}

echo "\n📞 Contact Information Fields (" . count($contact_fields) . "):\n";
foreach ($contact_fields as $field) {
  echo "  - {$field}: {$user_profile_fields[$field]['label']}\n";
}

echo "\n🎵 Choir Membership Fields (" . count($choir_fields) . "):\n";
foreach ($choir_fields as $field) {
  echo "  - {$field}: {$user_profile_fields[$field]['label']}\n";
}

echo "\n👥 Committee Function Fields (" . count($function_fields) . "):\n";
foreach ($function_fields as $field) {
  echo "  - {$field}: {$user_profile_fields[$field]['label']}\n";
}

echo "\n⚙️ Administrative Fields (" . count($admin_fields) . "):\n";
foreach ($admin_fields as $field) {
  echo "  - {$field}: {$user_profile_fields[$field]['label']}\n";
}

echo "\nTotal user profile fields created: " . count($user_profile_fields) . "\n\n";

echo "🔗 Migration Integration:\n";
echo "- These fields match the D6 Content Profile 'profiel' content type fields\n";
echo "- User migration will populate these fields from content_type_profiel table\n";
echo "- Profile data is migrated as user fields, not separate content nodes\n";
echo "- All Dutch field names are preserved for consistency\n\n";

echo "Next steps:\n";
echo "1. Configure user profile form display modes\n";
echo "2. Set up field permissions and visibility\n";
echo "3. Test user migration to verify field population\n";
echo "4. Configure field validation and requirements\n";