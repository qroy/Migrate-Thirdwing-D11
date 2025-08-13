<?php

/**
 * @file
 * GECORRIGEERD script om gemaakte velden te valideren tegen documentatie.
 * Gebaseerd op "Drupal 11 Content types and fields.md" documentatie.
 *
 * Usage: drush php:script validate-created-fields.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\media\Entity\MediaType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Hoofduitvoeringsfunctie.
 */
function validateCreatedFields() {
  echo "🔍 Valideren van Gemaakte Velden Tegen Documentatie...\n\n";
  
  $errors = [];
  $warnings = [];
  
  // Stap 1: Valideer content types
  echo "📦 Valideren van content types...\n";
  validateContentTypes($errors, $warnings);
  
  // Stap 2: Valideer content type velden
  echo "\n📋 Valideren van content type velden...\n";
  validateContentTypeFields($errors, $warnings);
  
  // Stap 3: Valideer media bundles
  echo "\n🎬 Valideren van media bundles...\n";
  validateMediaBundles($errors, $warnings);
  
  // Stap 4: Valideer media bundle velden
  echo "\n🎵 Valideren van media bundle velden...\n";
  validateMediaBundleFields($errors, $warnings);
  
  // Stap 5: Valideer user profile velden
  echo "\n👤 Valideren van user profile velden...\n";
  validateUserProfileFields($errors, $warnings);
  
  // Genereer rapport
  echo "\n";
  generateValidationReport($errors, $warnings);
}

/**
 * Valideer dat content types bestaan en correct geconfigureerd zijn.
 */
function validateContentTypes(&$errors, &$warnings) {
  $expected_content_types = [
    'activiteit' => 'Activiteit',
    'foto' => 'Foto',
    'locatie' => 'Locatie', 
    'nieuws' => 'Nieuws',
    'pagina' => 'Pagina',
    'programma' => 'Programma',
    'repertoire' => 'Repertoire',
    'vriend' => 'Vriend',
    'webform' => 'Webform'
  ];
  
  foreach ($expected_content_types as $type_id => $type_name) {
    $node_type = NodeType::load($type_id);
    if (!$node_type) {
      $errors[] = "Content type '{$type_id}' ({$type_name}) ontbreekt";
    } else {
      echo "  ✓ {$type_name} ({$type_id})\n";
      
      // Controleer of naam overeenkomt
      if ($node_type->label() !== $type_name) {
        $warnings[] = "Content type '{$type_id}' naam is '{$node_type->label()}', verwacht '{$type_name}'";
      }
    }
  }
}

/**
 * Valideer content type velden.
 */
function validateContentTypeFields(&$errors, &$warnings) {
  $expected_fields = getExpectedContentTypeFields();
  
  foreach ($expected_fields as $content_type => $fields) {
    echo "  Valideren van velden voor: {$content_type}\n";
    
    foreach ($fields as $field_name => $expected_config) {
      $field_config = FieldConfig::loadByName('node', $content_type, $field_name);
      
      if (!$field_config) {
        $errors[] = "Veld '{$field_name}' ontbreekt van content type '{$content_type}'";
      } else {
        echo "    ✓ {$expected_config['label']}\n";
        
        // Valideer veld type
        $field_storage = $field_config->getFieldStorageDefinition();
        if ($field_storage->getType() !== $expected_config['type']) {
          $errors[] = "Veld '{$field_name}' in '{$content_type}' heeft type '{$field_storage->getType()}', verwacht '{$expected_config['type']}'";
        }
        
        // Valideer cardinality
        $expected_cardinality = $expected_config['cardinality'] ?? 1;
        if ($field_storage->getCardinality() !== $expected_cardinality) {
          $actual = $field_storage->getCardinality();
          $errors[] = "Veld '{$field_name}' in '{$content_type}' heeft cardinality '{$actual}', verwacht '{$expected_cardinality}'";
        }
        
        // Valideer label
        if ($field_config->getLabel() !== $expected_config['label']) {
          $warnings[] = "Veld '{$field_name}' in '{$content_type}' heeft label '{$field_config->getLabel()}', verwacht '{$expected_config['label']}'";
        }
      }
    }
  }
}

/**
 * Valideer dat media bundles bestaan en correct geconfigureerd zijn.
 */
function validateMediaBundles(&$errors, &$warnings) {
  $expected_media_bundles = [
    'image' => 'Image',
    'document' => 'Document', 
    'audio' => 'Audio',
    'video' => 'Video'
  ];
  
  foreach ($expected_media_bundles as $bundle_id => $bundle_name) {
    $media_type = MediaType::load($bundle_id);
    if (!$media_type) {
      $errors[] = "Media bundle '{$bundle_id}' ({$bundle_name}) ontbreekt";
    } else {
      echo "  ✓ {$bundle_name} ({$bundle_id})\n";
      
      // Controleer of naam overeenkomt
      if ($media_type->label() !== $bundle_name) {
        $warnings[] = "Media bundle '{$bundle_id}' naam is '{$media_type->label()}', verwacht '{$bundle_name}'";
      }
    }
  }
}

/**
 * Valideer media bundle velden.
 */
function validateMediaBundleFields(&$errors, &$warnings) {
  $expected_fields = getExpectedMediaBundleFields();
  
  foreach ($expected_fields as $bundle_id => $fields) {
    echo "  Valideren van velden voor media bundle: {$bundle_id}\n";
    
    foreach ($fields as $field_name => $expected_config) {
      $field_config = FieldConfig::loadByName('media', $bundle_id, $field_name);
      
      if (!$field_config) {
        $errors[] = "Veld '{$field_name}' ontbreekt van media bundle '{$bundle_id}'";
      } else {
        echo "    ✓ {$expected_config['label']}\n";
        
        // Valideer veld type
        $field_storage = $field_config->getFieldStorageDefinition();
        if ($field_storage->getType() !== $expected_config['type']) {
          $errors[] = "Media veld '{$field_name}' in '{$bundle_id}' heeft type '{$field_storage->getType()}', verwacht '{$expected_config['type']}'";
        }
        
        // Valideer cardinality
        $expected_cardinality = $expected_config['cardinality'] ?? 1;
        if ($field_storage->getCardinality() !== $expected_cardinality) {
          $actual = $field_storage->getCardinality();
          $errors[] = "Media veld '{$field_name}' in '{$bundle_id}' heeft cardinality '{$actual}', verwacht '{$expected_cardinality}'";
        }
      }
    }
  }
}

/**
 * Valideer user profile velden.
 */
function validateUserProfileFields(&$errors, &$warnings) {
  $expected_fields = getExpectedUserProfileFields();
  
  foreach ($expected_fields as $field_name => $expected_config) {
    $field_config = FieldConfig::loadByName('user', 'user', $field_name);
    
    if (!$field_config) {
      $errors[] = "User profile veld '{$field_name}' ontbreekt";
    } else {
      echo "  ✓ {$expected_config['label']}\n";
      
      // Valideer veld type
      $field_storage = $field_config->getFieldStorageDefinition();
      if ($field_storage->getType() !== $expected_config['type']) {
        $errors[] = "User veld '{$field_name}' heeft type '{$field_storage->getType()}', verwacht '{$expected_config['type']}'";
      }
      
      // Valideer cardinality
      $expected_cardinality = $expected_config['cardinality'] ?? 1;
      if ($field_storage->getCardinality() !== $expected_cardinality) {
        $actual = $field_storage->getCardinality();
        $errors[] = "User veld '{$field_name}' heeft cardinality '{$actual}', verwacht '{$expected_cardinality}'";
      }
    }
  }
}

/**
 * Genereer validatierapport.
 */
function generateValidationReport($errors, $warnings) {
  echo "📊 VALIDATIERAPPORT\n";
  echo "=" . str_repeat("=", 50) . "\n";
  
  if (empty($errors) && empty($warnings)) {
    echo "🎉 SUCCES: Alle velden komen exact overeen met de documentatie!\n\n";
    echo "✅ Alle content types zijn aanwezig\n";
    echo "✅ Alle velden hebben correcte types en cardinality\n";
    echo "✅ Alle labels zijn correct in het Nederlands\n";
    echo "✅ Media bundles zijn compleet\n";
    echo "✅ User profile velden zijn geconfigureerd\n";
  } else {
    if (!empty($errors)) {
      echo "❌ FOUTEN GEVONDEN ({count($errors)}):\n";
      foreach ($errors as $error) {
        echo "   • {$error}\n";
      }
      echo "\n";
    }
    
    if (!empty($warnings)) {
      echo "⚠️ WAARSCHUWINGEN ({count($warnings)}):\n";
      foreach ($warnings as $warning) {
        echo "   • {$warning}\n";
      }
      echo "\n";
    }
    
    if (!empty($errors)) {
      echo "🔧 ACTIES VEREIST:\n";
      echo "  1. Los de fouten hierboven op\n";
      echo "  2. Controleer ontbrekende dependencies\n";
      echo "  3. Verifieer module installaties\n";
      echo "  4. Voer deze validatie opnieuw uit\n\n";
    }
  }
  
  echo "📋 Volgende Stappen:\n";
  if (empty($errors)) {
    echo "  1. Configureer veld displays\n";
    echo "  2. Stel permissions in\n";
    echo "  3. Test content aanmaken\n";
    echo "  4. Begin migratie uitvoering\n";
  } else {
    echo "  1. Los fouten hierboven op\n";
    echo "  2. Voer validatie opnieuw uit\n";
    echo "  3. Ga verder wanneer alle velden correct zijn\n";
  }
}

/**
 * Krijg verwachte content type velden voor validatie - VOLLEDIG GECORRIGEERD.
 */
function getExpectedContentTypeFields() {
  return [
    'activiteit' => [
      // Content-type specifieke velden volgens documentatie
      'field_tijd_aanwezig' => ['type' => 'string', 'label' => 'Koor Aanwezig', 'cardinality' => 1],
      'field_keyboard' => ['type' => 'list_string', 'label' => 'Toetsenist', 'cardinality' => 1],
      'field_gitaar' => ['type' => 'list_string', 'label' => 'Gitarist', 'cardinality' => 1],
      'field_basgitaar' => ['type' => 'list_string', 'label' => 'Basgitarist', 'cardinality' => 1],
      'field_drums' => ['type' => 'list_string', 'label' => 'Drummer', 'cardinality' => 1],
      'field_vervoer' => ['type' => 'string', 'label' => 'Karrijder', 'cardinality' => 1],
      'field_sleepgroep' => ['type' => 'list_string', 'label' => 'Sleepgroep', 'cardinality' => 1],
      'field_sleepgroep_aanwezig' => ['type' => 'string', 'label' => 'Sleepgroep Aanwezig', 'cardinality' => 1],
      'field_kledingcode' => ['type' => 'string', 'label' => 'Kledingcode', 'cardinality' => 1],
      'field_locatie' => ['type' => 'entity_reference', 'label' => 'Locatie', 'cardinality' => 1],
      'field_l_bijzonderheden' => ['type' => 'text_long', 'label' => 'Bijzonderheden locatie', 'cardinality' => 1],
      'field_bijzonderheden' => ['type' => 'string', 'label' => 'Bijzonderheden', 'cardinality' => 1],
      'field_background' => ['type' => 'entity_reference', 'label' => 'Achtergrond', 'cardinality' => 1],
      'field_sleepgroep_terug' => ['type' => 'list_string', 'label' => 'Sleepgroep terug', 'cardinality' => 1],
      'field_huiswerk' => ['type' => 'entity_reference', 'label' => 'Huiswerk', 'cardinality' => 1],
      // Gedeelde velden
      'field_afbeeldingen' => ['type' => 'entity_reference', 'label' => 'Afbeeldingen', 'cardinality' => -1],
      'field_files' => ['type' => 'entity_reference', 'label' => 'Bestandsbijlages', 'cardinality' => -1],
      'field_programma2' => ['type' => 'entity_reference', 'label' => 'Programma', 'cardinality' => -1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum en tijd', 'cardinality' => 1]
    ],
    
    'foto' => [
      'field_video' => ['type' => 'text_long', 'label' => 'Video', 'cardinality' => 1],
      'field_gerelateerd_repertoire' => ['type' => 'entity_reference', 'label' => 'Gerelateerd Repertoire', 'cardinality' => -1],
      'field_audio_uitvoerende' => ['type' => 'string', 'label' => 'Uitvoerende', 'cardinality' => 1],
      'field_audio_type' => ['type' => 'list_string', 'label' => 'Type', 'cardinality' => 1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum', 'cardinality' => 1],
      'field_ref_activiteit' => ['type' => 'entity_reference', 'label' => 'Activiteit', 'cardinality' => 1]
    ],
    
    'locatie' => [
      'field_l_adres' => ['type' => 'string', 'label' => 'Adres', 'cardinality' => 1],
      'field_l_plaats' => ['type' => 'string', 'label' => 'Plaats', 'cardinality' => 1],
      'field_l_postcode' => ['type' => 'string', 'label' => 'Postcode', 'cardinality' => 1],
      'field_l_routelink' => ['type' => 'link', 'label' => 'Route', 'cardinality' => 1]
    ],
    
    'nieuws' => [
      'field_afbeeldingen' => ['type' => 'entity_reference', 'label' => 'Afbeeldingen', 'cardinality' => -1],
      'field_files' => ['type' => 'entity_reference', 'label' => 'Bestandsbijlages', 'cardinality' => -1]
    ],
    
    'pagina' => [
      'field_afbeeldingen' => ['type' => 'entity_reference', 'label' => 'Afbeeldingen', 'cardinality' => -1],
      'field_files' => ['type' => 'entity_reference', 'label' => 'Bestandsbijlages', 'cardinality' => -1],
      'field_view' => ['type' => 'string', 'label' => 'Extra inhoud', 'cardinality' => 1]
    ],
    
    'programma' => [
      'field_prog_type' => ['type' => 'list_string', 'label' => 'Type', 'cardinality' => 1]
    ],
    
    'repertoire' => [
      'field_rep_arr' => ['type' => 'string', 'label' => 'Arrangeur', 'cardinality' => 1],
      'field_rep_arr_jaar' => ['type' => 'integer', 'label' => 'Arrangeur Jaar', 'cardinality' => 1],
      'field_rep_componist' => ['type' => 'string', 'label' => 'Componist', 'cardinality' => 1],
      'field_rep_componist_jaar' => ['type' => 'integer', 'label' => 'Componist Jaar', 'cardinality' => 1],
      'field_rep_genre' => ['type' => 'list_string', 'label' => 'Genre', 'cardinality' => 1],
      'field_rep_sinds' => ['type' => 'integer', 'label' => 'Sinds', 'cardinality' => 1],
      'field_rep_uitv' => ['type' => 'string', 'label' => 'Uitvoering', 'cardinality' => 1],
      'field_rep_uitv_jaar' => ['type' => 'integer', 'label' => 'Uitvoering Jaar', 'cardinality' => 1],
      'field_positie' => ['type' => 'list_string', 'label' => 'Positie', 'cardinality' => 1],
      'field_klapper' => ['type' => 'boolean', 'label' => 'Klapper', 'cardinality' => 1],
      'field_audio_nummer' => ['type' => 'string', 'label' => 'Nummer', 'cardinality' => 1],
      'field_audio_seizoen' => ['type' => 'string', 'label' => 'Seizoen', 'cardinality' => 1]
    ],
    
    'vriend' => [
      'field_vriend_website' => ['type' => 'link', 'label' => 'Website', 'cardinality' => 1],
      'field_vriend_soort' => ['type' => 'list_string', 'label' => 'Soort', 'cardinality' => 1],
      'field_vriend_benaming' => ['type' => 'list_string', 'label' => 'Benaming', 'cardinality' => 1],
      'field_vriend_periode_tot' => ['type' => 'integer', 'label' => 'Vriend t/m', 'cardinality' => 1],
      'field_vriend_periode_vanaf' => ['type' => 'integer', 'label' => 'Vriend vanaf', 'cardinality' => 1],
      'field_vriend_duur' => ['type' => 'list_string', 'label' => 'Vriendlengte', 'cardinality' => 1],
      'field_woonplaats' => ['type' => 'string', 'label' => 'Woonplaats', 'cardinality' => 1],
      'field_afbeeldingen' => ['type' => 'entity_reference', 'label' => 'Afbeeldingen', 'cardinality' => -1]
    ],
    
    'webform' => [
      // Webform heeft geen specifieke velden - alleen gedeelde velden
    ]
  ];
}

/**
 * Krijg verwachte media bundle velden voor validatie.
 */
function getExpectedMediaBundleFields() {
  return [
    'image' => [
      'field_media_image' => ['type' => 'image', 'label' => 'Afbeelding', 'cardinality' => 1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum', 'cardinality' => 1],
      'field_toegang' => ['type' => 'entity_reference', 'label' => 'Toegang', 'cardinality' => -1]
    ],
    'document' => [
      'field_media_document' => ['type' => 'file', 'label' => 'Document', 'cardinality' => 1],
      'field_document_soort' => ['type' => 'list_string', 'label' => 'Document Soort', 'cardinality' => 1],
      'field_gerelateerd_repertoire' => ['type' => 'entity_reference', 'label' => 'Gerelateerd Repertoire', 'cardinality' => -1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum', 'cardinality' => 1],
      'field_toegang' => ['type' => 'entity_reference', 'label' => 'Toegang', 'cardinality' => -1]
    ],
    'audio' => [
      'field_media_audio_file' => ['type' => 'file', 'label' => 'Audio File', 'cardinality' => 1],
      'field_audio_type' => ['type' => 'list_string', 'label' => 'Type', 'cardinality' => 1],
      'field_audio_uitvoerende' => ['type' => 'string', 'label' => 'Uitvoerende', 'cardinality' => 1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum', 'cardinality' => 1],
      'field_toegang' => ['type' => 'entity_reference', 'label' => 'Toegang', 'cardinality' => -1]
    ],
    'video' => [
      'field_media_video_file' => ['type' => 'file', 'label' => 'Video File', 'cardinality' => 1],
      'field_video_type' => ['type' => 'list_string', 'label' => 'Type', 'cardinality' => 1],
      'field_video_uitvoerende' => ['type' => 'string', 'label' => 'Uitvoerende', 'cardinality' => 1],
      'field_datum' => ['type' => 'datetime', 'label' => 'Datum', 'cardinality' => 1],
      'field_toegang' => ['type' => 'entity_reference', 'label' => 'Toegang', 'cardinality' => -1]
    ]
  ];
}

/**
 * Krijg verwachte user profile velden voor validatie - VOLLEDIG VOLGENS DOCUMENTATIE.
 */
function getExpectedUserProfileFields() {
  return [
    'field_emailbewaking' => ['type' => 'string', 'label' => 'Email origineel', 'cardinality' => 1],
    'field_lidsinds' => ['type' => 'datetime', 'label' => 'Lid Sinds', 'cardinality' => 1],
    'field_koor' => ['type' => 'list_string', 'label' => 'Koorfunctie', 'cardinality' => 1],
    'field_sleepgroep_1' => ['type' => 'list_string', 'label' => 'Sleepgroep', 'cardinality' => 1],
    'field_voornaam' => ['type' => 'string', 'label' => 'Voornaam', 'cardinality' => 1],
    'field_achternaam_voorvoegsel' => ['type' => 'string', 'label' => 'Achternaam voorvoegsel', 'cardinality' => 1],
    'field_achternaam' => ['type' => 'string', 'label' => 'Achternaam', 'cardinality' => 1],
    'field_geboortedatum' => ['type' => 'datetime', 'label' => 'Geboortedatum', 'cardinality' => 1],
    'field_geslacht' => ['type' => 'list_string', 'label' => 'Geslacht', 'cardinality' => 1],
    'field_karrijder' => ['type' => 'boolean', 'label' => 'Karrijder', 'cardinality' => 1],
    'field_uitkoor' => ['type' => 'datetime', 'label' => 'Uit koor per', 'cardinality' => 1],
    'field_adres' => ['type' => 'string', 'label' => 'Adres', 'cardinality' => 1],
    'field_postcode' => ['type' => 'string', 'label' => 'Postcode', 'cardinality' => 1],
    'field_telefoon' => ['type' => 'string', 'label' => 'Telefoon', 'cardinality' => 1],
    'field_notes' => ['type' => 'text_long', 'label' => 'Notities', 'cardinality' => 1],
    'field_woonplaats' => ['type' => 'string', 'label' => 'Woonplaats', 'cardinality' => 1],
    
    // Commissie functies
    'field_functie_bestuur' => ['type' => 'list_string', 'label' => 'Functie Bestuur', 'cardinality' => 1],
    'field_functie_mc' => ['type' => 'list_string', 'label' => 'Functie Muziekcommissie', 'cardinality' => 1],
    'field_functie_concert' => ['type' => 'list_string', 'label' => 'Functie Commissie Concerten', 'cardinality' => 1],
    'field_functie_feest' => ['type' => 'list_string', 'label' => 'Functie Feestcommissie', 'cardinality' => 1],
    'field_functie_regie' => ['type' => 'list_string', 'label' => 'Functie Commissie Koorregie', 'cardinality' => 1],
    'field_functie_ir' => ['type' => 'list_string', 'label' => 'Functie Commissie Interne Relaties', 'cardinality' => 1],
    'field_functie_pr' => ['type' => 'list_string', 'label' => 'Functie Commissie PR', 'cardinality' => 1],
    'field_functie_tec' => ['type' => 'list_string', 'label' => 'Functie Technische Commissie', 'cardinality' => 1],
    'field_functie_lw' => ['type' => 'list_string', 'label' => 'Functie ledenwerf', 'cardinality' => 1],
    'field_functie_fl' => ['type' => 'list_string', 'label' => 'Functie Faciliteiten', 'cardinality' => 1],
    
    // Extra velden
    'field_positie' => ['type' => 'list_string', 'label' => 'Positie', 'cardinality' => 1],
    'field_mobiel' => ['type' => 'string', 'label' => 'Mobiel', 'cardinality' => 1]
  ];
}

// Voer het script uit
try {
  validateCreatedFields();
} catch (Exception $e) {
  echo "❌ Validatie gefaald: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}