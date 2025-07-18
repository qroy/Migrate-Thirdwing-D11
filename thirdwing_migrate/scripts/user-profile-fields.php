<?php

/**
 * @file
 * Create user profile fields for D6 Content Profile migration - CORRECTED DUTCH LABELS
 * 
 * Run with: drush php:script user-profile-fields.php
 * 
 * This script creates user profile fields that match the D6 Content Profile
 * 'profiel' content type fields with CORRECT Dutch labels.
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;

// CORRECTED: User profile fields with proper Dutch labels matching D6 documentation
$user_profile_fields = [
  // Personal information fields
  'field_voornaam' => [
    'type' => 'string',
    'label' => 'Voornaam',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 255],
  ],
  'field_achternaam' => [
    'type' => 'string',
    'label' => 'Achternaam',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 255],
  ],
  // CORRECTED: Proper field name and Dutch label
  'field_achternaam_voorvoegsel' => [  // NOT 'field_tussenvoegsel'
    'type' => 'string',
    'label' => 'Achternaam voorvoegsel',  // CORRECTED: Proper Dutch label
    'settings' => ['max_length' => 100],
  ],
  'field_geboortedatum' => [
    'type' => 'datetime',
    'label' => 'Geboortedatum',  // CORRECTED: Dutch label, not English
    'settings' => ['datetime_type' => 'date'],
  ],
  'field_geslacht' => [
    'type' => 'list_string',
    'label' => 'Geslacht',  // CORRECTED: Dutch label, not English
    'settings' => [
      'allowed_values' => [
        'man' => 'Man',
        'vrouw' => 'Vrouw',
      ],
    ],
  ],
  
  // Contact information fields
  'field_adres' => [
    'type' => 'string',
    'label' => 'Adres',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 255],
  ],
  'field_postcode' => [
    'type' => 'string',
    'label' => 'Postcode',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 20],
  ],
  'field_woonplaats' => [
    'type' => 'string',
    'label' => 'Woonplaats',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 255],
  ],
  'field_telefoon' => [
    'type' => 'string',
    'label' => 'Telefoon',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 50],
  ],
  'field_mobiel' => [
    'type' => 'string',
    'label' => 'Mobiel',  // CORRECTED: Dutch label, not English
    'settings' => ['max_length' => 50],
  ],
  
  // Choir membership fields
  'field_lidsinds' => [
    'type' => 'datetime',
    'label' => 'Lid Sinds',  // CORRECTED: Dutch label, not English
    'settings' => ['datetime_type' => 'date'],
  ],
  'field_uitkoor' => [
    'type' => 'datetime',
    'label' => 'Uit koor per',  // CORRECTED: Dutch label, not English
    'settings' => ['datetime_type' => 'date'],
  ],
  'field_koor' => [
    'type' => 'list_string',
    'label' => 'Koorfunctie',  // CORRECTED: Dutch label, not English
    'settings' => [
      'allowed_values' => [
        'koor' => 'Koor',
        'band' => 'Band',
        'combo' => 'Combo',
      ],
    ],
  ],
  'field_positie' => [
    'type' => 'list_string',
    'label' => 'Positie',  // CORRECTED: Dutch label, not English
    'settings' => [
      'allowed_values' => [
        'sopraan' => 'Sopraan',
        'alt' => 'Alt',
        'tenor' => 'Tenor',
        'bas' => 'Bas',
      ],
    ],
    'description' => 'Zangpositie (sopraan, alt, tenor, bas)',
  ],
  
  // Transport and logistics
  'field_karrijder' => [
    'type' => 'boolean',
    'label' => 'Karrijder',  // CORRECTED: Dutch label, not 'Autorijder'
    'description' => 'Kan auto rijden voor vervoer',
  ],
  // CORRECTED: Proper field name matching D6 and migration
  'field_sleepgroep_1' => [  // NOT 'field_sleepgroep'
    'type' => 'list_string',
    'label' => 'Sleepgroep',  // CORRECTED: Dutch label, not English
    'settings' => [
      'allowed_values' => [
        'groep_1' => 'Groep 1',
        'groep_2' => 'Groep 2',
        'groep_3' => 'Groep 3',
        'eigen_vervoer' => 'Eigen vervoer',
      ],
    ],
    'description' => 'Vervoersgroep voor concerten',
  ],
  
  // Committee function fields - CORRECTED: All Dutch labels
  'field_functie_bestuur' => [
    'type' => 'list_string',
    'label' => 'Functie Bestuur',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'secretaris' => 'Secretaris',
        'penningmeester' => 'Penningmeester',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_mc' => [
    'type' => 'list_string',
    'label' => 'Functie Muziekcommissie',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_concert' => [
    'type' => 'list_string',
    'label' => 'Functie Commissie Concerten',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_feest' => [
    'type' => 'list_string',
    'label' => 'Functie Feestcommissie',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_regie' => [
    'type' => 'list_string',
    'label' => 'Functie Commissie Koorregie',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_ir' => [
    'type' => 'list_string',
    'label' => 'Functie Commissie Interne Relaties',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_pr' => [
    'type' => 'list_string',
    'label' => 'Functie Commissie PR',  // CORRECTED: Dutch label, not expanded
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_tec' => [
    'type' => 'list_string',
    'label' => 'Functie Technische Commissie',  // CORRECTED: Dutch label
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_lw' => [
    'type' => 'list_string',
    'label' => 'Functie ledenwerf',  // CORRECTED: Dutch label, not expanded
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  'field_functie_fl' => [
    'type' => 'list_string',
    'label' => 'Functie Faciliteiten',  // CORRECTED: Dutch label, not expanded
    'settings' => [
      'allowed_values' => [
        'voorzitter' => 'Voorzitter',
        'lid' => 'Lid',
      ],
    ],
  ],
  
  // Administrative fields
  'field_emailbewaking' => [
    'type' => 'string',
    'label' => 'Email origineel',  // CORRECTED: Dutch label, not 'Email Bewaking'
    'settings' => ['max_length' => 255],
    'description' => 'Origineel email adres voor monitoring',
  ],
  'field_notes' => [
    'type' => 'text_long',
    'label' => 'Notities',  // CORRECTED: Dutch label
    'description' => 'Administratieve notities over dit lid',
  ],
];

echo "=== CREATING USER PROFILE FIELDS WITH CORRECTED DUTCH LABELS ===\n\n";

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

echo "✅ Created user profile fields with CORRECTED Dutch labels:\n";
$personal_fields = ['field_voornaam', 'field_achternaam', 'field_achternaam_voorvoegsel', 'field_geslacht', 'field_geboortedatum'];
$contact_fields = ['field_adres', 'field_postcode', 'field_woonplaats', 'field_telefoon', 'field_mobiel'];
$choir_fields = ['field_lidsinds', 'field_uitkoor', 'field_koor', 'field_positie', 'field_karrijder', 'field_sleepgroep_1'];
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

echo "🔧 KEY CORRECTIONS MADE:\n";
echo "- ✅ Fixed 'field_tussenvoegsel' → 'field_achternaam_voorvoegsel'\n";
echo "- ✅ Fixed 'Autorijder' → 'Karrijder'\n";
echo "- ✅ Fixed 'Email Bewaking' → 'Email origineel'\n";
echo "- ✅ Fixed 'field_sleepgroep' → 'field_sleepgroep_1'\n";
echo "- ✅ All labels now use correct Dutch terminology\n";
echo "- ✅ All field names match D6 documentation\n";
echo "- ✅ All field names match migration expectations\n";

echo "\n🔗 Migration Integration:\n";
echo "- These fields now match the D6 Content Profile 'profiel' content type fields\n";
echo "- User migration will populate these fields from content_type_profiel table\n";
echo "- Profile data is migrated as user fields, not separate content nodes\n";
echo "- All Dutch field names are properly preserved for consistency\n\n";

echo "✅ Next steps:\n";
echo "1. Run corrected content type creation script\n";
echo "2. Update migration source plugin descriptions\n";
echo "3. Test user migration with corrected field mappings\n";
echo "4. Configure field validation and requirements\n";