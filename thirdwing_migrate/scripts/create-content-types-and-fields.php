<?php

/**
 * @file
 * Create content types and fields for Thirdwing migration - CORRECTED DUTCH LABELS
 *
 * Run with: drush php:script create-content-types-and-fields.php
 *
 * This script creates all content types and fields with proper Dutch labels
 * matching the D6 documentation.
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

// CORRECTED: All content types with proper Dutch labels and complete field sets
$content_types_config = [
  'activiteit' => [
    'name' => 'Activiteit',
    'description' => 'Een activiteit (uitvoering, repetitie)',
    'fields' => [
      // CORRECTED: All missing activity fields with proper Dutch labels
      'field_tijd_aanwezig' => [
        'type' => 'string',
        'label' => 'Koor Aanwezig',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_keyboard' => [
        'type' => 'list_string',
        'label' => 'Toetsenist',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'nodig' => 'Nodig',
            'aanwezig' => 'Aanwezig',
            'niet_nodig' => 'Niet nodig',
          ],
        ],
      ],
      'field_gitaar' => [
        'type' => 'list_string',
        'label' => 'Gitarist',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'nodig' => 'Nodig',
            'aanwezig' => 'Aanwezig',
            'niet_nodig' => 'Niet nodig',
          ],
        ],
      ],
      'field_basgitaar' => [
        'type' => 'list_string',
        'label' => 'Basgitarist',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'nodig' => 'Nodig',
            'aanwezig' => 'Aanwezig',
            'niet_nodig' => 'Niet nodig',
          ],
        ],
      ],
      'field_drums' => [
        'type' => 'list_string',
        'label' => 'Drummer',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'nodig' => 'Nodig',
            'aanwezig' => 'Aanwezig',
            'niet_nodig' => 'Niet nodig',
          ],
        ],
      ],
      'field_vervoer' => [
        'type' => 'string',
        'label' => 'Karrijder',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_sleepgroep' => [
        'type' => 'list_string',
        'label' => 'Sleepgroep',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'groep_1' => 'Groep 1',
            'groep_2' => 'Groep 2',
            'groep_3' => 'Groep 3',
            'eigen_vervoer' => 'Eigen vervoer',
          ],
        ],
      ],
      'field_sleepgroep_aanwezig' => [
        'type' => 'string',
        'label' => 'Sleepgroep Aanwezig',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_sleepgroep_terug' => [
        'type' => 'list_string',
        'label' => 'Sleepgroep terug',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'groep_1' => 'Groep 1',
            'groep_2' => 'Groep 2',
            'groep_3' => 'Groep 3',
            'eigen_vervoer' => 'Eigen vervoer',
          ],
        ],
      ],
      'field_kledingcode' => [
        'type' => 'string',
        'label' => 'Kledingcode',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_ledeninfo' => [
        'type' => 'text_long',
        'label' => 'Informatie voor leden',  // CORRECTED: Dutch label
      ],
      'field_bijzonderheden' => [
        'type' => 'string',
        'label' => 'Bijzonderheden',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden locatie',  // CORRECTED: Dutch label
      ],
      
      // Date and time fields
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum en tijd',  // CORRECTED: Dutch label, not 'Nieuws Datum'
        'settings' => ['datetime_type' => 'datetime'],
      ],
      
      // References
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',  // CORRECTED: Dutch label
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_programma2' => [
        'type' => 'entity_reference',
        'label' => 'Programma',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['programma'],
      ],
      
      // Media fields - CORRECTED: Proper Dutch labels
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_background' => [
        'type' => 'entity_reference',
        'label' => 'Achtergrond',  // CORRECTED: Dutch label, not 'Achtergrond Afbeelding'
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bestandsbijlages',  // CORRECTED: Dutch label, not 'Bestanden'
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
      'field_huiswerk' => [
        'type' => 'entity_reference',
        'label' => 'Huiswerk',  // CORRECTED: Dutch label
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
    ],
  ],
  
  'nieuws' => [
    'name' => 'Nieuws',
    'description' => 'Nieuwsberichten en aankondigingen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',  // CORRECTED: Dutch label, not 'Nieuws Datum'
        'settings' => ['datetime_type' => 'datetime'],
      ],
      // Media fields - CORRECTED: Proper Dutch labels
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bestandsbijlages',  // CORRECTED: Dutch label, not 'Bijlagen'
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['document'],
      ],
    ],
  ],
  
  'foto' => [
    'name' => 'Foto',
    'description' => 'Foto-album',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',  // CORRECTED: Dutch label
        'settings' => ['datetime_type' => 'datetime'],
      ],
      'field_audio_type' => [
        'type' => 'list_string',
        'label' => 'Type',  // CORRECTED: Dutch label
        'settings' => [
          'allowed_values' => [
            'concert' => 'Concert',
            'repetitie' => 'Repetitie',
            'uitje' => 'Uitje',
            'overig' => 'Overig',
          ],
        ],
      ],
      'field_audio_uitvoerende' => [
        'type' => 'string',
        'label' => 'Uitvoerende',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_ref_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Activiteit',  // CORRECTED: Dutch label
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
      'field_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Nummer',  // CORRECTED: Dutch label
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_video' => [
        'type' => 'text_long',
        'label' => 'Video',  // CORRECTED: Dutch label
      ],
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
    ],
  ],
  
  'locatie' => [
    'name' => 'Locatie',
    'description' => 'Veelvoorkomende locaties van uitvoeringen',
    'fields' => [
      'field_l_adres' => [
        'type' => 'string',
        'label' => 'Adres',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_l_plaats' => [
        'type' => 'string',
        'label' => 'Plaats',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_l_postcode' => [
        'type' => 'string',
        'label' => 'Postcode',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 20],
      ],
      'field_l_routelink' => [
        'type' => 'link',
        'label' => 'Route',  // CORRECTED: Dutch label
      ],
    ],
  ],
  
  'pagina' => [
    'name' => 'Pagina',
    'description' => 'Algemene pagina\'s',
    'fields' => [
      'field_view' => [
        'type' => 'string',
        'label' => 'Extra inhoud',  // CORRECTED: Dutch label
        'settings' => ['max_length' => 255],
      ],
      'field_afbeeldingen' => [
        'type' => 'entity_reference',
        'label' => 'Afbeeldingen',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'media'],
        'target_bundles' => ['image'],
      ],
      'field_files' => [
        'type' => 'entity_reference',
        'label' => 'Bestandsbijlages',  // CORRECTED: Dutch label
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['