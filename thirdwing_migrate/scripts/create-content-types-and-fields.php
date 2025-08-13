<?php

/**
 * @file
 * Script to create content types and fields for Thirdwing migration.
 * File: thirdwing_migrate/scripts/create-content-types-and-fields.php
 */

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;

/**
 * Main function to create content types and fields.
 */
function createContentTypesAndFields() {
  echo "Creating Thirdwing content types and fields...\n";
  
  try {
    // Create content types
    createContentTypes();
    
    // Create shared fields
    createSharedFields();
    
    // Attach fields to content types
    attachFieldsToContentTypes();
    
    // Configure field displays
    configureFieldDisplays();
    
    // Print summary
    printSummary();
    
    echo "✅ Content types and fields created successfully!\n";
    
  } catch (Exception $e) {
    echo "❌ Error creating content types and fields: " . $e->getMessage() . "\n";
    throw $e;
  }
}

/**
 * Create all content types.
 */
function createContentTypes() {
  echo "Creating content types...\n";
  
  $content_types = [
    'activiteit' => [
      'label' => 'Activiteit',
      'description' => 'Events, concerts, and activities.',
    ],
    'foto' => [
      'label' => 'Foto',
      'description' => 'Photo galleries and image collections.',
    ],
    'locatie' => [
      'label' => 'Locatie', 
      'description' => 'Venue and location information.',
    ],
    'nieuws' => [
      'label' => 'Nieuws',
      'description' => 'News articles and announcements.',
    ],
    'pagina' => [
      'label' => 'Pagina',
      'description' => 'Static pages and general content.',
    ],
    'programma' => [
      'label' => 'Programma',
      'description' => 'Concert programs and performance content.',
    ],
    'repertoire' => [
      'label' => 'Repertoire', 
      'description' => 'Musical pieces and repertoire catalog.',
    ],
    'vriend' => [
      'label' => 'Vriend',
      'description' => 'Friends and supporters of the choir.',
    ],
    'webform' => [
      'label' => 'Webform',
      'description' => 'Contact forms and questionnaires.',
    ],
  ];
  
  foreach ($content_types as $machine_name => $info) {
    // Check if content type already exists
    if (NodeType::load($machine_name)) {
      echo "  Content type '$machine_name' already exists\n";
      continue;
    }
    
    // Create content type
    $node_type = NodeType::create([
      'type' => $machine_name,
      'label' => $info['label'],
      'description' => $info['description'],
      'new_revision' => TRUE,
      'preview_mode' => DRUPAL_OPTIONAL,
      'display_submitted' => TRUE,
    ]);
    
    $node_type->save();
    echo "  ✅ Created content type: {$info['label']} ($machine_name)\n";
  }
}

/**
 * Create shared fields that will be used across content types.
 */
function createSharedFields() {
  echo "Creating shared fields...\n";
  
  $shared_fields = [
    'field_afbeeldingen' => [
      'type' => 'entity_reference',
      'label' => 'Afbeeldingen',
      'settings' => [
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['image'],
        ],
      ],
      'cardinality' => -1,
    ],
    'field_audio_type' => [
      'type' => 'list_string',
      'label' => 'Audio Type',
      'settings' => [
        'allowed_values' => [
          'live' => 'Live opname',
          'studio' => 'Studio opname',
          'rehearsal' => 'Repetitie',
        ],
      ],
      'cardinality' => 1,
    ],
    'field_audio_uitvoerende' => [
      'type' => 'string',
      'label' => 'Uitvoerende',
      'settings' => [
        'max_length' => 255,
      ],
      'cardinality' => 1,
    ],
    'field_datum' => [
      'type' => 'datetime',
      'label' => 'Datum en tijd',
      'settings' => [
        'datetime_type' => 'datetime',
      ],
      'cardinality' => 1,
    ],
    'field_files' => [
      'type' => 'entity_reference',
      'label' => 'Bestandsbijlages',
      'settings' => [
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['document'],
        ],
      ],
      'cardinality' => -1,
    ],
    'field_inhoud' => [
      'type' => 'entity_reference',
      'label' => 'Inhoud',
      'settings' => [
        'target_type' => 'node',
        'handler_settings' => [
          'target_bundles' => ['nieuws', 'activiteit', 'programma'],
        ],
      ],
      'cardinality' => -1,
    ],
    'field_l_routelink' => [
      'type' => 'link',
      'label' => 'Route',
      'settings' => [],
      'cardinality' => 1,
    ],
    'field_partij_band' => [
      'type' => 'entity_reference',
      'label' => 'Bandpartituur',
      'settings' => [
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['document'],
        ],
      ],
      'cardinality' => 1,
    ],
    'field_partij_koor_l' => [
      'type' => 'entity_reference',
      'label' => 'Koorpartituur',
      'settings' => [
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['document'],
        ],
      ],
      'cardinality' => 1,
    ],
    'field_partij_tekst' => [
      'type' => 'entity_reference',
      'label' => 'Tekst / koorregie',
      'settings' => [
        'target_type' => 'media',
        'handler_settings' => [
          'target_bundles' => ['document'],
        ],
      ],
      'cardinality' => 1,
    ],
    'field_programma2' => [
      'type' => 'entity_reference',
      'label' => 'Programma',
      'settings' => [
        'target_type' => 'node',
        'handler_settings' => [
          'target_bundles' => ['programma'],
        ],
      ],
      'cardinality' => -1,
    ],
    'field_ref_activiteit' => [
      'type' => 'entity_reference',
      'label' => 'Activiteit',
      'settings' => [
        'target_type' => 'node',
        'handler_settings' => [
          'target_bundles' => ['activiteit'],
        ],
      ],
      'cardinality' => 1,
    ],
    'field_repertoire' => [
      'type' => 'entity_reference',
      'label' => 'Nummer',
      'settings' => [
        'target_type' => 'node',
        'handler_settings' => [
          'target_bundles' => ['repertoire'],
        ],
      ],
      'cardinality' => 1,
    ],
    'field_video' => [
      'type' => 'text_long',
      'label' => 'Video',
      'settings' => [],
      'cardinality' => 1,
    ],
    'field_view' => [
      'type' => 'string',
      'label' => 'Extra inhoud',
      'settings' => [
        'max_length' => 255,
      ],
      'cardinality' => 1,
    ],
    'field_woonplaats' => [
      'type' => 'string',
      'label' => 'Woonplaats',
      'settings' => [
        'max_length' => 255,
      ],
      'cardinality' => 1,
    ],
  ];
  
  foreach ($shared_fields as $field_name => $field_info) {
    // Check if field storage already exists
    if (FieldStorageConfig::loadByName('node', $field_name)) {
      echo "  Field storage '$field_name' already exists\n";
      continue;
    }
    
    // Create field storage
    $field_storage = FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => $field_info['type'],
      'cardinality' => $field_info['cardinality'],
      'settings' => $field_info['settings'],
    ]);
    
    $field_storage->save();
    echo "  ✅ Created field storage: {$field_info['label']} ($field_name)\n";
  }
}

/**
 * Attach fields to content types based on the documentation.
 */
function attachFieldsToContentTypes() {
  echo "Attaching fields to content types...\n";
  
  $field_attachments = getSharedFieldAttachments();
  
  foreach ($field_attachments as $content_type => $fields) {
    echo "  Attaching fields to '$content_type':\n";
    
    foreach ($fields as $field_name) {
      // Check if field instance already exists
      if (FieldConfig::loadByName('node', $content_type, $field_name)) {
        echo "    Field '$field_name' already attached to '$content_type'\n";
        continue;
      }
      
      // Get field storage
      $field_storage = FieldStorageConfig::loadByName('node', $field_name);
      if (!$field_storage) {
        echo "    ❌ Field storage '$field_name' not found\n";
        continue;
      }
      
      // Create field instance
      $field_config = FieldConfig::create([
        'field_storage' => $field_storage,
        'bundle' => $content_type,
        'label' => $field_storage->getLabel(),
        'required' => FALSE,
      ]);
      
      $field_config->save();
      echo "    ✅ Attached field: $field_name\n";
    }
  }
}

/**
 * Get field attachments mapping for each content type.
 */
function getSharedFieldAttachments() {
  return [
    'activiteit' => [
      'field_afbeeldingen',
      'field_audio_type',
      'field_audio_uitvoerende', 
      'field_datum',
      'field_files',
      'field_inhoud',
      'field_l_routelink',
      'field_partij_band',
      'field_partij_koor_l',
      'field_partij_tekst',
      'field_programma2',
      'field_ref_activiteit',
      'field_repertoire',
      'field_video',
      'field_view',
      'field_woonplaats',
    ],
    'foto' => [
      'field_afbeeldingen',
      'field_datum',
      'field_files',
    ],
    'locatie' => [
      'field_afbeeldingen',
      'field_l_routelink',
      'field_woonplaats',
    ],
    'nieuws' => [
      'field_afbeeldingen', 
      'field_datum',
      'field_files',
      'field_inhoud',
    ],
    'pagina' => [
      'field_afbeeldingen',
      'field_files',
      'field_view',
    ],
    'programma' => [
      'field_afbeeldingen',
      'field_datum',
      'field_files',
      'field_inhoud',
      'field_repertoire',
      'field_video',
    ],
    'repertoire' => [
      'field_afbeeldingen',
      'field_audio_type',
      'field_audio_uitvoerende',
      'field_files',
      'field_partij_band',
      'field_partij_koor_l', 
      'field_partij_tekst',
      'field_video',
    ],
    'vriend' => [
      'field_afbeeldingen',
      'field_woonplaats',
    ],
    'webform' => [
      // Webforms use their own field system
    ],
  ];
}

/**
 * Configure field displays for better UX.
 */
function configureFieldDisplays() {
  echo "Configuring field displays...\n";
  
  $content_types = ['activiteit', 'foto', 'locatie', 'nieuws', 'pagina', 'programma', 'repertoire', 'vriend', 'webform'];
  
  foreach ($content_types as $content_type) {
    // Configure form display
    $form_display = EntityFormDisplay::load("node.{$content_type}.default");
    if (!$form_display) {
      $form_display = EntityFormDisplay::create([
        'targetEntityType' => 'node',
        'bundle' => $content_type,
        'mode' => 'default',
      ]);
    }
    
    // Configure view display
    $view_display = EntityViewDisplay::load("node.{$content_type}.default");
    if (!$view_display) {
      $view_display = EntityViewDisplay::create([
        'targetEntityType' => 'node',
        'bundle' => $content_type,
        'mode' => 'default',
      ]);
    }
    
    // Save displays
    $form_display->save();
    $view_display->save();
    
    echo "  ✅ Configured displays for: $content_type\n";
  }
}

/**
 * Print summary of created content.
 */
function printSummary() {
  echo "\n=== CONTENT TYPES AND FIELDS SUMMARY ===\n";
  
  // Count content types
  $content_types = \Drupal::entityTypeManager()
    ->getStorage('node_type')
    ->loadMultiple();
    
  $thirdwing_types = array_filter($content_types, function($type) {
    return in_array($type->id(), ['activiteit', 'foto', 'locatie', 'nieuws', 'pagina', 'programma', 'repertoire', 'vriend', 'webform']);
  });
  
  echo "Content Types Created: " . count($thirdwing_types) . "\n";
  foreach ($thirdwing_types as $type) {
    echo "  - {$type->label()} ({$type->id()})\n";
  }
  
  // Count shared fields
  $field_attachments = getSharedFieldAttachments();
  $total_attachments = 0;
  
  echo "\nField Attachments:\n";
  foreach ($field_attachments as $content_type => $fields) {
    $count = count($fields);
    $total_attachments += $count;
    echo "  - $content_type: $count fields\n";
  }
  
  echo "\nTotal field attachments: $total_attachments\n";
  echo "✅ Content structure ready for migration!\n";
}

// Execute the main function
createContentTypesAndFields();