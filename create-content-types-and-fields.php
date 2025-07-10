<?php
/**
 * @file
 * CORRECTED script to create Thirdwing content types with ONLY actual D6 fields.
 * Based on exact D6 database field analysis - no non-existent fields included.
 * 
 * Run with: drush php:script create-content-types-and-fields.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

// Content types with ONLY fields that actually exist in D6 database
$content_types_config = [
  'activiteit' => [
    'name' => 'Activiteit',
    'description' => 'Kooractiviteiten en evenementen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_tijd_aanwezig' => [
        'type' => 'string',
        'label' => 'Tijd Aanwezig',
        'settings' => ['max_length' => 255],
      ],
      // Instrument availability fields
      'field_keyboard' => [
        'type' => 'list_string',
        'label' => 'Keyboard',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_gitaar' => [
        'type' => 'list_string',
        'label' => 'Gitaar',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_basgitaar' => [
        'type' => 'list_string',
        'label' => 'Basgitaar',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      'field_drums' => [
        'type' => 'list_string',
        'label' => 'Drums',
        'settings' => [
          'allowed_values' => [
            '+' => 'Nodig (+)',
            '-' => 'Niet nodig (-)',
            '?' => 'Misschien (?)',
            'v' => 'Beschikbaar (v)',
          ],
        ],
      ],
      // Logistics fields
      'field_vervoer' => [
        'type' => 'text_long',
        'label' => 'Vervoer',
      ],
      'field_sleepgroep' => [
        'type' => 'text_long',
        'label' => 'Sleepgroep',
      ],
      'field_sleepgroep_aanwezig' => [
        'type' => 'text_long',
        'label' => 'Sleepgroep Aanwezig',
      ],
      'field_sleepgroep_terug' => [
        'type' => 'text_long',
        'label' => 'Sleepgroep Terug',
      ],
      'field_kledingcode' => [
        'type' => 'text_long',
        'label' => 'Kledingcode',
      ],
      // Location and details
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_l_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Locatie Bijzonderheden',
      ],
      'field_bijzonderheden' => [
        'type' => 'text_long',
        'label' => 'Bijzonderheden',
      ],
      // Media fields (from actual D6)
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'activiteit-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_background' => [
        'type' => 'image',
        'label' => 'Achtergrond Afbeelding',
        'settings' => [
          'file_directory' => 'achtergronden',
          'alt_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'activiteit-bestanden',
        ],
      ],
      // Program reference
      'field_programma2' => [
        'type' => 'entity_reference',
        'label' => 'Programma Onderdelen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire', 'programma'],
      ],
    ],
  ],
  
  'repertoire' => [
    'name' => 'Repertoire',
    'description' => 'Muzikale repertoire items',
    'fields' => [
      // Composer and arranger info (actual D6 field names)
      'field_rep_componist' => [
        'type' => 'string',
        'label' => 'Componist',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_componist_jaar' => [
        'type' => 'integer',
        'label' => 'Componist Jaar',
      ],
      'field_rep_arr' => [
        'type' => 'string',
        'label' => 'Arrangeur',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_arr_jaar' => [
        'type' => 'integer',
        'label' => 'Arrangeur Jaar',
      ],
      // Performance info
      'field_rep_uitv' => [
        'type' => 'string',
        'label' => 'Uitvoerende',
        'settings' => ['max_length' => 255],
      ],
      'field_rep_uitv_jaar' => [
        'type' => 'integer',
        'label' => 'Uitvoerende Jaar',
      ],
      'field_rep_genre' => [
        'type' => 'string',
        'label' => 'Genre',
        'settings' => ['max_length' => 100],
      ],
      'field_rep_sinds' => [
        'type' => 'datetime',
        'label' => 'In repertoire sinds',
        'settings' => ['datetime_type' => 'date'],
      ],
      // Audio info
      'field_audio_nummer' => [
        'type' => 'integer',
        'label' => 'Audio Nummer',
      ],
      'field_audio_seizoen' => [
        'type' => 'string',
        'label' => 'Audio Seizoen',
        'settings' => ['max_length' => 50],
      ],
      'field_klapper' => [
        'type' => 'string',
        'label' => 'Klapper Nummer',
        'settings' => ['max_length' => 50],
      ],
      // Sheet music files (the key D6 fields!)
      'field_partij_band' => [
        'type' => 'file',
        'label' => 'Partij Band',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx mid kar',
          'file_directory' => 'repertoire/band',
        ],
      ],
      'field_partij_koor_l' => [
        'type' => 'file',
        'label' => 'Partij Koor (Links)',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx mid kar',
          'file_directory' => 'repertoire/koor',
        ],
      ],
      'field_partij_tekst' => [
        'type' => 'file',
        'label' => 'Partij Tekst',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'repertoire/tekst',
        ],
      ],
      // MP3 files (actual D6 field!)
      'field_mp3' => [
        'type' => 'file',
        'label' => 'MP3 Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'mp3 wav ogg',
          'file_directory' => 'audio',
        ],
      ],
    ],
  ],
  
  'nieuws' => [
    'name' => 'Nieuws',
    'description' => 'Nieuwsartikelen',
    'fields' => [
      // Uses field_datum like other content types
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'nieuws-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'nieuws-bestanden',
        ],
      ],
    ],
  ],
  
  'foto' => [
    'name' => 'Foto Album',
    'description' => 'Foto albums',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_ref_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'foto-albums',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_audio_type' => [
        'type' => 'list_string',
        'label' => 'Audio Type',
        'settings' => [
          'allowed_values' => [
            'opname' => 'Opname',
            'repetitie' => 'Repetitie',
            'uitvoering' => 'Uitvoering',
            'uitzending' => 'Uitzending',
            'overig' => 'Overig',
          ],
        ],
      ],
    ],
  ],
  
  'locatie' => [
    'name' => 'Locatie',
    'description' => 'Uitvoerings- en repetitielocaties',
    'fields' => [
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      'field_postcode' => [
        'type' => 'string',
        'label' => 'Postcode',
        'settings' => ['max_length' => 20],
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
      // D6 uses field_l_routelink for website links
      'field_l_routelink' => [
        'type' => 'link',
        'label' => 'Website',
      ],
    ],
  ],
  
  'vriend' => [
    'name' => 'Vriend',
    'description' => 'Vrienden en partners van het koor',
    'fields' => [
      // D6 uses field_l_routelink for website links
      'field_l_routelink' => [
        'type' => 'link',
        'label' => 'Website',
      ],
      'field_telefoon' => [
        'type' => 'string',
        'label' => 'Telefoon',
        'settings' => ['max_length' => 50],
      ],
      'field_adres' => [
        'type' => 'text_long',
        'label' => 'Adres',
      ],
      // D6 uses field_afbeeldingen for images
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Logo/Afbeelding',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'vrienden',
          'alt_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'nieuwsbrief' => [
    'name' => 'Nieuwsbrief',
    'description' => 'Nieuwsbrieven',
    'fields' => [
      'field_uitgave_nummer' => [
        'type' => 'integer',
        'label' => 'Uitgave Nummer',
        'required' => TRUE,
      ],
      'field_uitgave_datum' => [
        'type' => 'datetime',
        'label' => 'Uitgave Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      // D6 uses field_nieuwsbrief (not field_nieuwsbrief_bestand)
      'field_nieuwsbrief' => [
        'type' => 'file',
        'label' => 'Nieuwsbrief Bestand',
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'nieuwsbrieven',
        ],
      ],
      'field_inhoud' => [
        'type' => 'entity_reference',
        'label' => 'Inhoud Referenties',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['nieuws', 'activiteit', 'repertoire'],
      ],
      // Additional D6 field I missed
      'field_jaargang' => [
        'type' => 'integer',
        'label' => 'Jaargang',
      ],
    ],
  ],
  
  'pagina' => [
    'name' => 'Pagina',
    'description' => 'Algemene pagina\'s',
    'fields' => [
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Afbeeldingen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_directory' => 'pagina-afbeeldingen',
          'alt_field' => TRUE,
          'title_field' => TRUE,
        ],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx txt',
          'file_directory' => 'pagina-bestanden',
        ],
      ],
      'field_view' => [
        'type' => 'string',
        'label' => 'View Reference',
        'settings' => ['max_length' => 255],
      ],
    ],
  ],
  
  'profiel' => [
    'name' => 'Profiel',
    'description' => 'Koorlid profielen',
    'fields' => [
      // Personal information
      'field_voornaam' => [
        'type' => 'string',
        'label' => 'Voornaam',
        'settings' => ['max_length' => 100],
      ],
      'field_achternaam' => [
        'type' => 'string',
        'label' => 'Achternaam',
        'settings' => ['max_length' => 100],
      ],
      'field_achternaam_voorvoegsel' => [
        'type' => 'string',
        'label' => 'Achternaam Voorvoegsel',
        'settings' => ['max_length' => 20],
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
        'settings' => ['max_length' => 20],
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
      // Choir information
      'field_koor' => [
        'type' => 'list_string',
        'label' => 'Koor',
        'settings' => [
          'allowed_values' => [
            'thirdwing' => 'Thirdwing',
            'anders' => 'Anders',
          ],
        ],
      ],
      'field_positie' => [
        'type' => 'list_string',
        'label' => 'Positie',
        'settings' => [
          'allowed_values' => [
            'sopraan' => 'Sopraan',
            'alt' => 'Alt',
            'tenor' => 'Tenor',
            'bas' => 'Bas',
          ],
        ],
      ],
      'field_lidsinds' => [
        'type' => 'datetime',
        'label' => 'Lid sinds',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_uitkoor' => [
        'type' => 'datetime',
        'label' => 'Uit koor',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_sleepgroep_1' => [
        'type' => 'list_string',
        'label' => 'Sleepgroep',
        'settings' => [
          'allowed_values' => [
            'I' => 'Groep I',
            'II' => 'Groep II',
            'III' => 'Groep III',
            'IV' => 'Groep IV',
            'V' => 'Groep V',
            '*' => 'Alle groepen',
          ],
        ],
      ],
      'field_karrijder' => [
        'type' => 'boolean',
        'label' => 'Karrijder',
      ],
      // Commission functions (all actual D6 fields)
      'field_functie_bestuur' => [
        'type' => 'list_string',
        'label' => 'Functie Bestuur',
        'settings' => [
          'allowed_values' => [
            '1' => 'Bestuurslid',
            '10' => 'Lid',
          ],
        ],
      ],
      'field_functie_concert' => [
        'type' => 'string',
        'label' => 'Functie Concerten',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_feest' => [
        'type' => 'string',
        'label' => 'Functie Feest',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_fl' => [
        'type' => 'string',
        'label' => 'Functie FL',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_ir' => [
        'type' => 'string',
        'label' => 'Functie IR',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_lw' => [
        'type' => 'string',
        'label' => 'Functie LW',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_mc' => [
        'type' => 'string',
        'label' => 'Functie MC',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_pr' => [
        'type' => 'string',
        'label' => 'Functie PR',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_regie' => [
        'type' => 'string',
        'label' => 'Functie Regie',
        'settings' => ['max_length' => 100],
      ],
      'field_functie_tec' => [
        'type' => 'string',
        'label' => 'Functie TEC',
        'settings' => ['max_length' => 100],
      ],
      // Administrative fields
      'field_emailbewaking' => [
        'type' => 'boolean',
        'label' => 'Email Bewaking',
      ],
      'field_notes' => [
        'type' => 'text_long',
        'label' => 'Notities',
      ],
      // D6 uses field_afbeeldingen for profile photos
      'field_afbeeldingen' => [
        'type' => 'image',
        'label' => 'Profielfoto',
        'settings' => [
          'file_directory' => 'profiel-fotos',
          'alt_field' => TRUE,
        ],
      ],
    ],
  ],
  
  'programma' => [
    'name' => 'Programma',
    'description' => 'Concert programma\'s',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_locatie' => [
        'type' => 'entity_reference',
        'label' => 'Locatie',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['locatie'],
      ],
      'field_repertoire' => [
        'type' => 'entity_reference',
        'label' => 'Repertoire',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_programma2' => [
        'type' => 'entity_reference',
        'label' => 'Programma Onderdelen',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['repertoire'],
      ],
      'field_prog_type' => [
        'type' => 'list_string',
        'label' => 'Programma Type',
        'settings' => [
          'allowed_values' => [
            'programma' => 'Programma onderdeel',
            'nummer' => 'Nummer',
          ],
        ],
      ],
    ],
  ],

  // Additional content types with actual D6 fields
  'audio' => [
    'name' => 'Audio',
    'description' => 'Audio opnames',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Opname Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_mp3' => [
        'type' => 'file',
        'label' => 'Audio Bestand',
        'settings' => [
          'file_extensions' => 'mp3 wav ogg',
          'file_directory' => 'audio',
        ],
      ],
      'field_audio_type' => [
        'type' => 'list_string',
        'label' => 'Audio Type',
        'settings' => [
          'allowed_values' => [
            'opname' => 'Opname',
            'repetitie' => 'Repetitie',
            'uitvoering' => 'Uitvoering',
            'uitzending' => 'Uitzending',
            'overig' => 'Overig',
          ],
        ],
      ],
      'field_audio_uitvoerende' => [
        'type' => 'text_long',
        'label' => 'Uitvoerende',
      ],
      // Additional D6 field I missed
      'field_audio_bijz' => [
        'type' => 'text_long',
        'label' => 'Audio Bijzonderheden',
      ],
      'field_ref_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
    ],
  ],

  'verslag' => [
    'name' => 'Verslag',
    'description' => 'Vergaderverslagen',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Verslag Datum',
        'required' => TRUE,
        'settings' => ['datetime_type' => 'date'],
      ],
      'field_files' => [
        'type' => 'file',
        'label' => 'Verslag Bestanden',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'verslagen',
        ],
      ],
      // Additional D6 field I missed
      'field_huiswerk' => [
        'type' => 'file',
        'label' => 'Huiswerk',
        'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
        'settings' => [
          'file_extensions' => 'pdf doc docx',
          'file_directory' => 'huiswerk',
        ],
      ],
    ],
  ],

  'video' => [
    'name' => 'Video',
    'description' => 'Video opnames',
    'fields' => [
      'field_datum' => [
        'type' => 'datetime',
        'label' => 'Opname Datum',
        'settings' => ['datetime_type' => 'date'],
      ],
      // D6 uses emvideo field type (we'll convert to file or string)
      'field_video' => [
        'type' => 'string',
        'label' => 'Video URL/Embed',
        'settings' => ['max_length' => 500],
      ],
      'field_ref_activiteit' => [
        'type' => 'entity_reference',
        'label' => 'Gerelateerde Activiteit',
        'settings' => ['target_type' => 'node'],
        'target_bundles' => ['activiteit'],
      ],
    ],
  ],
];

echo "=== CORRECTED THIRDWING D11 CONTENT TYPES & FIELDS CREATION ===\n";
echo "Creating content types with ONLY actual D6 fields...\n";
echo "✅ Removed 10 non-existent fields\n";
echo "✅ Added 6 missed D6 fields\n";
echo "✅ Using correct D6 field names\n\n";

foreach ($content_types_config as $type_id => $type_info) {
  echo "Processing content type: {$type_info['name']}\n";
  
  // Create content type if it doesn't exist
  if (!NodeType::load($type_id)) {
    $node_type = NodeType::create([
      'type' => $type_id,
      'name' => $type_info['name'],
      'description' => $type_info['description'],
      'help' => '',
      'new_revision' => TRUE,
      'preview_mode' => 1,
      'display_submitted' => TRUE,
    ]);
    $node_type->save();
    echo "  ✓ Content type created: {$type_info['name']}\n";
  } else {
    echo "  - Content type '{$type_id}' already exists, skipping.\n";
  }
  
  // Create fields
  if (isset($type_info['fields']) && !empty($type_info['fields'])) {
    foreach ($type_info['fields'] as $field_name => $field_config) {
      echo "    Processing field: {$field_config['label']}\n";
      
      // Check if field storage exists
      $field_storage = FieldStorageConfig::loadByName('node', $field_name);
      if (!$field_storage) {
        // Create field storage
        $storage_config = [
          'field_name' => $field_name,
          'entity_type' => 'node',
          'type' => $field_config['type'],
          'cardinality' => $field_config['cardinality'] ?? 1,
        ];
        
        // Add settings if they exist
        if (isset($field_config['settings'])) {
          $storage_config['settings'] = $field_config['settings'];
        }
        
        $field_storage = FieldStorageConfig::create($storage_config);
        $field_storage->save();
        echo "      ✓ Field storage created for: {$field_name}\n";
      }
      
      // Check if field instance exists for this content type
      $field_instance = FieldConfig::loadByName('node', $type_id, $field_name);
      if (!$field_instance) {
        // Create field instance
        $instance_config = [
          'field_storage' => $field_storage,
          'bundle' => $type_id,
          'label' => $field_config['label'],
          'required' => $field_config['required'] ?? FALSE,
        ];
        
        // Add target bundles for entity reference fields
        if (isset($field_config['target_bundles'])) {
          $instance_config['settings']['handler_settings']['target_bundles'] = $field_config['target_bundles'];
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
        echo "      ✓ Field instance created: {$field_config['label']}\n";
      } else {
        echo "      - Field '{$field_name}' already exists for {$type_id}\n";
      }
    }
  } else {
    echo "    No custom fields defined for this content type.\n";
  }
  
  echo "\n";
}

echo "=== CORRECTED CREATION COMPLETE ===\n\n";
echo "Summary of corrections made:\n";
echo "❌ REMOVED non-existent fields:\n";
echo "  - field_ledeninfo\n";
echo "  - field_nieuws_datum (uses field_datum instead)\n";
echo "  - field_samenvatting\n";
echo "  - field_land\n";
echo "  - field_website (uses field_l_routelink instead)\n";
echo "  - field_email\n";
echo "  - field_afbeelding (uses field_afbeeldingen instead)\n";
echo "  - field_nieuwsbrief_bestand (uses field_nieuwsbrief instead)\n";
echo "  - field_foto (uses field_afbeeldingen instead)\n";
echo "  - field_verslag_soort\n\n";

echo "✅ ADDED missed D6 fields:\n";
echo "  - field_audio_bijz\n";
echo "  - field_huiswerk\n";
echo "  - field_jaargang\n";
echo "  - field_l_routelink\n";
echo "  - field_nieuwsbrief\n";
echo "  - field_rep_uitv_jaar\n\n";

echo "Content types created with correct D6 fields:\n";
foreach ($content_types_config as $type_id => $type_info) {
  $field_count = isset($type_info['fields']) ? count($type_info['fields']) : 0;
  echo "  - {$type_id}: {$type_info['name']} ({$field_count} fields)\n";
}

echo "\nNext steps:\n";
echo "1. Configure form and display modes via admin UI if needed\n";
echo "2. Set up media types for file fields if Media module is used\n";
echo "3. Configure field widgets and formatters\n";
echo "4. Run the migration: drush migrate:import --group=thirdwing_d6\n";

echo "\n=== READY FOR ACCURATE MIGRATION ===\n";
echo "All content types now have ONLY the fields that actually exist in D6!\n";
echo "This ensures no 'field not found' errors during migration.\n";