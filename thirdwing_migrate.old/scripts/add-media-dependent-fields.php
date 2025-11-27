<?php

/**
 * @file
 * GECORRIGEERD script om media-afhankelijke velden toe te voegen aan content types.
 * Voer dit uit NA het aanmaken van media bundles.
 * Gebaseerd op "Drupal 11 Content types and fields.md" documentatie.
 *
 * Usage: drush php:script add-media-dependent-fields.php
 */

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Hoofduitvoeringsfunctie.
 */
function addMediaDependentFields() {
  echo "🚀 Toevoegen van Media-Afhankelijke Velden aan Content Types...\n\n";
  
  // Controleer of media entity type bestaat
  if (!checkMediaEntityExists()) {
    echo "❌ Media entity type niet gevonden. Voer eerst create-media-bundles-and-fields.php uit.\n";
    exit(1);
  }
  
  // Controleer of vereiste media bundles bestaan
  if (!checkMediaBundlesExist()) {
    echo "❌ Vereiste media bundles niet gevonden. Voer eerst create-media-bundles-and-fields.php uit.\n";
    exit(1);
  }
  
  // Maak media-afhankelijke gedeelde field storages aan
  echo "📋 Aanmaken van media-afhankelijke gedeelde field storages...\n";
  createMediaDependentSharedFields();
  
  // Koppel media-afhankelijke velden aan content types
  echo "\n🔗 Koppelen van media-afhankelijke velden aan content types...\n";
  attachMediaDependentFieldsToContentTypes();
  
  echo "\n✅ Media-afhankelijke velden succesvol toegevoegd!\n";
  printMediaFieldsSummary();
}

/**
 * Controleer of media entity type bestaat.
 */
function checkMediaEntityExists() {
  try {
    $entity_type_manager = \Drupal::entityTypeManager();
    $media_definition = $entity_type_manager->getDefinition('media');
    return $media_definition !== null;
  } catch (Exception $e) {
    return false;
  }
}

/**
 * Controleer of vereiste media bundles bestaan.
 */
function checkMediaBundlesExist() {
  $required_bundles = ['image', 'document', 'audio', 'video'];
  $missing_bundles = [];
  
  foreach ($required_bundles as $bundle) {
    if (!\Drupal\media\Entity\MediaType::load($bundle)) {
      $missing_bundles[] = $bundle;
    }
  }
  
  if (!empty($missing_bundles)) {
    echo "❌ Ontbrekende media bundles: " . implode(', ', $missing_bundles) . "\n";
    return false;
  }
  
  echo "✅ Vereiste media bundles gevonden: " . implode(', ', $required_bundles) . "\n";
  return true;
}

/**
 * Maak media-afhankelijke gedeelde velden aan.
 */
function createMediaDependentSharedFields() {
  $media_fields = getMediaDependentSharedFields();
  
  foreach ($media_fields as $field_name => $field_config) {
    echo "  Aanmaken van media-afhankelijke field storage: {$field_name}\n";
    
    $field_storage = FieldStorageConfig::loadByName('node', $field_name);
    if (!$field_storage) {
      $storage_config = [
        'field_name' => $field_name,
        'entity_type' => 'node',
        'type' => $field_config['type'],
        'cardinality' => $field_config['cardinality'] ?? 1,
      ];
      
      if (isset($field_config['storage_settings'])) {
        $storage_config['settings'] = $field_config['storage_settings'];
      }
      
      $field_storage = FieldStorageConfig::create($storage_config);
      $field_storage->save();
      echo "    ✓ Field storage aangemaakt: {$field_name}\n";
    } else {
      echo "    - Field storage '{$field_name}' bestaat al\n";
    }
  }
}

/**
 * Koppel media-afhankelijke velden aan content types.
 */
function attachMediaDependentFieldsToContentTypes() {
  $content_type_mappings = getContentTypeMediaFieldMappings();
  
  foreach ($content_type_mappings as $content_type => $fields) {
    echo "  Koppelen van media velden aan: {$content_type}\n";
    
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
 * Krijg media-afhankelijke gedeelde velden volgens documentatie.
 */
function getMediaDependentSharedFields() {
  return [
    'field_afbeeldingen' => [
      'type' => 'entity_reference',
      'cardinality' => -1, // unlimited
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ],
    'field_files' => [
      'type' => 'entity_reference',
      'cardinality' => -1, // unlimited
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ],
    'field_background' => [
      'type' => 'entity_reference',
      'cardinality' => 1,
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ],
    'field_huiswerk' => [
      'type' => 'entity_reference',
      'cardinality' => 1,
      'storage_settings' => [
        'target_type' => 'media'
      ]
    ]
  ];
}

/**
 * Krijg content type naar media veld mappings volgens documentatie.
 */
function getContentTypeMediaFieldMappings() {
  return [
    'activiteit' => [
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
      'field_background' => [
        'label' => 'Achtergrond',
        'required' => FALSE,
        'target_bundles' => ['image']
      ],
      'field_huiswerk' => [
        'label' => 'Huiswerk',
        'required' => FALSE,
        'target_bundles' => ['document']
      ]
    ],
    
    'foto' => [
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ]
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
      ]
    ],
    
    'vriend' => [
      'field_afbeeldingen' => [
        'label' => 'Afbeeldingen',
        'required' => FALSE,
        'target_bundles' => ['image']
      ]
    ]
  ];
}

/**
 * Print samenvatting van toegevoegde media velden.
 */
function printMediaFieldsSummary() {
  echo "\n📊 SAMENVATTING MEDIA-AFHANKELIJKE VELDEN\n";
  echo "=" . str_repeat("=", 50) . "\n";
  
  $media_fields = getMediaDependentSharedFields();
  echo "✅ Media-Afhankelijke Shared Fields: " . count($media_fields) . "\n";
  foreach ($media_fields as $field_name => $config) {
    $cardinality = $config['cardinality'] == -1 ? 'unlimited' : $config['cardinality'];
    echo "   • {$field_name} (cardinality: {$cardinality})\n";
  }
  
  echo "\n✅ Content Types met Media Velden:\n";
  $content_type_mappings = getContentTypeMediaFieldMappings();
  foreach ($content_type_mappings as $content_type => $fields) {
    echo "   • {$content_type}: " . count($fields) . " media velden\n";
    foreach ($fields as $field_name => $field_config) {
      $bundles = implode(', ', $field_config['target_bundles']);
      echo "     - {$field_config['label']} → {$bundles}\n";
    }
  }
  
  echo "\n🔄 ARCHITECTUUR WIJZIGINGEN:\n";
  echo "1. **field_afbeeldingen**: D6 imagefield → D11 entity_reference naar media:image\n";
  echo "2. **field_files**: D6 filefield → D11 entity_reference naar media:document\n";
  echo "3. **field_background**: Nieuw in D11 voor activiteit achtergronden\n";
  echo "4. **field_huiswerk**: Nieuw in D11 voor activiteit huiswerk documenten\n\n";
  
  echo "🎯 VOLGENDE STAPPEN:\n";
  echo "1. Valideer alle velden: drush php:script validate-created-fields.php\n";
  echo "2. Configureer field displays\n";
  echo "3. Test content aanmaken met media velden\n";
  echo "4. Begin bestandsmigratie naar media entities\n";
  echo "5. Update migratie scripts voor nieuwe media architectuur\n";
}

// Voer het script uit
try {
  addMediaDependentFields();
} catch (Exception $e) {
  echo "❌ Script gefaald: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}