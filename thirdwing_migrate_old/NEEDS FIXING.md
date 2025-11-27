# ThirdWing Drupal 6 → 11 Migratie: Complete Aanpassingen Documentatie

## 📋 **Overzicht Migratie Strategie**

**Benadering:**
- Module wordt geïnstalleerd op een schone Drupal 11 installatie
- Oude Drupal 6 site blijft actief en dient als backup voor alle data totdat de nieuwe D11 site volledig operationeel is
- Periodieke syncs van oude naar nieuw met bijgewerkte inhoud (eenwegsync van oud naar nieuw)
- Volledige UI en documentatie in het Nederlands

---

## 🔧 **Benodigde Aanpassingen Config Files**

### **1. Database Configuratie (settings.php)**

**Bestand:** `sites/default/settings.php`
**Aanpassing:** Database verbinding voor D6 bron toevoegen

```php
// Thirdwing D6 Migration Database Configuration  
$databases['migrate']['default'] = [
  'database' => '[D6_DATABASE_NAME]',
  'username' => '[D6_DATABASE_USER]',
  'password' => '[D6_DATABASE_PASSWORD]',
  'prefix' => '[D6_TABLE_PREFIX]',
  'host' => '[D6_DATABASE_HOST]',
  'port' => '[D6_DATABASE_PORT]',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
```

### **2. Module Dependencies**

**Bestand:** `thirdwing_migrate.info.yml`
**Huidige dependencies** die gevalideerd moeten worden:

```yaml
dependencies:
  - drupal:migrate
  - drupal:migrate_drupal
  - migrate_plus:migrate_plus
  - migrate_tools:migrate_tools
  - webform:webform (^6.2)
  - drupal:media
  - drupal:field
  - drupal:user
  - drupal:node
  - drupal:taxonomy
  - drupal:file
```

---

## 🏗️ **Installatie Scripts Aanpassingen**

### **1. Complete Setup Script**

**Bestand:** `scripts/setup-complete-migration.sh`
**Benodigde aanpassingen:**

```bash
# Voeg database connectiviteit test toe
test_d6_connection() {
  echo "Testing D6 database connection..."
  # Implementeer database connectivity check
}

# Verbeter composer dependency installatie
install_composer_dependencies() {
  local packages=(
    "drupal/migrate_plus:^6.0"
    "drupal/migrate_tools:^6.0"
    "drupal/webform:^6.2"
    "drupal/admin_toolbar:^3.0"
    "drupal/pathauto:^1.8"
    "drupal/token:^1.9"
    "drupal/field_group:^3.2"
  )
  
  for package in "${packages[@]}"; do
    if ! composer show "$package" > /dev/null 2>&1; then
      composer require "$package" --no-interaction
    fi
  done
}
```

### **2. Content Types Aanmaak Script**

**Bestand:** `scripts/create-content-types-and-fields.php`
**Belangrijkste aanpassingen:**

```php
// Corrigeer content type mapping - 8 types EXACT
function getContentTypeConfigurations() {
  return [
    'activiteit' => [
      'name' => 'Activiteit',
      'description' => 'Een activiteit (uitvoering, repetitie)',
      'title_label' => 'Omschrijving',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'fotoalbum' => [ // ✅ KRITIEK: Hernoemd van D6 'foto' naar 'fotoalbum'
      'name' => 'Fotoalbum',
      'description' => 'Foto-album',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Omschrijving'
    ],
    'locatie' => [
      'name' => 'Locatie',
      'description' => 'Veelvoorkomende locaties van uitvoeringen',
      'title_label' => 'Titel',
      'has_body' => FALSE
    ],
    'nieuws' => [
      'name' => 'Nieuws',
      'description' => 'Een nieuwsbericht',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'pagina' => [
      'name' => 'Pagina',
      'description' => 'Statische pagina',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'programma' => [
      'name' => 'Programma',
      'description' => 'Concert programma',
      'title_label' => 'Titel',
      'has_body' => FALSE
    ],
    'repertoire' => [
      'name' => 'Repertoire',
      'description' => 'Stuk uit het repertoire',
      'title_label' => 'Titel',
      'has_body' => TRUE,
      'body_label' => 'Berichttekst'
    ],
    'vriend' => [
      'name' => 'Vriend',
      'description' => 'Vrienden van de vereniging',
      'title_label' => 'Naam',
      'has_body' => FALSE
    ],
    // VERWIJDER: 'webform' - wordt vervangen door Webform module
  ];
}
```

### **3. User Profile Fields Script**

**Bestand:** `scripts/create-user-profile-fields.php`
**Aanpassingen voor 32 gebruiker profiel velden:**

```php
function getUserProfileFieldConfigurations() {
  return [
    // Persoonlijke gegevens (12 velden)
    'field_voornaam' => [
      'type' => 'string',
      'label' => 'Voornaam',
      'required' => TRUE,
      'storage_settings' => ['max_length' => 255]
    ],
    'field_achternaam' => [
      'type' => 'string', 
      'label' => 'Achternaam',
      'required' => TRUE,
      'storage_settings' => ['max_length' => 255]
    ],
    // ... (30 andere profiel velden volgens documentatie)
  ];
}
```

---

## 📊 **Media Bundle Configuratie Aanpassingen**

### **Bestand:** `scripts/create-media-bundles-and-fields.php`

**Kritieke wijziging - Document Bundle voor Partituren:**

```php
'document' => [
  'name' => 'Document',
  'description' => 'Lokaal opgeslagen documenten en bestanden',
  'source_plugin' => 'file',
  'source_field' => 'field_media_document',
  'fields' => [
    'field_document_soort' => [
      'type' => 'list_string',
      'label' => 'Document Soort',
      'required' => TRUE,
      'storage_settings' => [
        'allowed_values' => [
          'partituur' => 'Partituur',
          'verslag' => 'Verslag', // ✅ NIEUW: Voor D6 verslag content type
          'huiswerk' => 'Huiswerk',
          'algemeen' => 'Algemeen document' // ✅ WIJZIGING: 'overig' → 'algemeen'
        ]
      ]
    ],
    'field_gerelateerd_repertoire' => [
      'type' => 'entity_reference',
      'label' => 'Gerelateerd Repertoire',
      'cardinality' => -1, // Onbeperkt
      'storage_settings' => ['target_type' => 'node'],
      'target_bundles' => ['repertoire']
    ],
    'field_verslag_type' => [
      'type' => 'list_string', 
      'label' => 'Verslag Type',
      'storage_settings' => [
        'allowed_values' => [
          'bestuursvergadering' => 'Bestuursvergadering',
          'vergadering_muziekcommissie' => 'Vergadering Muziekcommissie',
          'algemene_ledenvergadering' => 'Algemene Ledenvergadering',
          'overige_vergadering' => 'Overige Vergadering',
          'combo_overleg' => 'Combo Overleg',
          'jaarevaluatie_dirigent' => 'Jaarevaluatie Dirigent',
          'jaarverslag' => 'Jaarverslag',
          'concertcommissie' => 'Concertcommissie'
        ]
      ]
    ]
  ]
]
```

---

## 🔄 **Migratie YAML Files Aanpassingen**

### **1. Repertoire Migratie - KRITIEKE WIJZIGING**

**Bestand:** `config/install/migrate_plus.migration.d6_thirdwing_repertoire.yml`

```yaml
# VERWIJDER alle partituur veld mappings:
# field_partij_band: VERWIJDERD
# field_partij_koor_l: VERWIJDERD  
# field_partij_tekst: VERWIJDERD

# BEHOUD alleen:
process:
  title: title
  body/value: body
  body/format: constants/body_format
  
  # Repertoire specifieke velden (11 velden)
  field_rep_componist: field_componist_value
  field_rep_arr: field_arrangeur_value
  # ... (andere repertoire velden)
  
  # Media references (GEEN partituur velden)
  field_media_images:
    plugin: sub_process
    source: images
    # ... (alleen images, audio, video - GEEN documenten)
```

### **2. Nieuwe Document Media Migratie**

**Bestand:** `config/install/migrate_plus.migration.d6_thirdwing_media_document.yml`

```yaml
id: d6_thirdwing_media_document
source:
  plugin: d6_thirdwing_partituren
  # Custom source plugin voor partituren + verslag content

process:
  bundle: 
    plugin: static_map
    source: source_type
    map:
      partituur_band: document
      partituur_koor: document  
      partituur_tekst: document
      verslag_doc: document

  field_document_soort:
    plugin: static_map
    source: source_type
    map:
      partituur_band: partituur
      partituur_koor: partituur
      partituur_tekst: partituur
      verslag_doc: verslag

  # Reverse referentie - kritieke wijziging!
  field_gerelateerd_repertoire:
    plugin: migration_lookup
    migration: d6_thirdwing_repertoire
    source: parent_node_id
```

---

## 👥 **Gebruikersrollen Configuratie**

### **Bestand:** `scripts/create-user-roles.php`

**EXACTE Drupal 6 gebruikersrollen volgens Permission Matrix:**

```php
function getUserRoleConfigurations() {
  return [
    // ✅ RID 1 & 2 zijn standaard Drupal rollen (niet migreren)
    // 'anonymous' => RID 1
    // 'authenticated' => RID 2
    
    // ✅ EXACTE D6 ROLLEN volgens Permission Matrix:
    'lid' => ['name' => 'Lid', 'rid' => 3],
    'vriend' => ['name' => 'Vriend', 'rid' => 4], // ✅ Ontbrak in vorige versie
    // RID 5 ontbreekt in Permission Matrix
    'beheerder' => ['name' => 'Beheerder', 'rid' => 6], // ✅ NIET "Admin"
    'bestuur' => ['name' => 'Bestuur', 'rid' => 7],
    'muziekcommissie' => ['name' => 'Muziekcommissie', 'rid' => 8],
    // RID 9 ontbreekt in Permission Matrix  
    'feestcommissie' => ['name' => 'Feestcommissie', 'rid' => 10],
    'commissie_ir' => ['name' => 'Commissie IR', 'rid' => 11],
    'auteur' => ['name' => 'Auteur', 'rid' => 12],
    'commissie_concerten' => ['name' => 'Commissie Concerten', 'rid' => 13],
    'admin' => ['name' => 'Admin', 'rid' => 14], // ✅ VERSCHIL: Admin ≠ Beheerder
    'commissie_koorregie' => ['name' => 'Commissie Koorregie', 'rid' => 15],
    'band' => ['name' => 'Band', 'rid' => 16],
    // RID 17-20 ontbreken in Permission Matrix
    'aspirant_lid' => ['name' => 'Aspirant-lid', 'rid' => 21],
    'dirigent' => ['name' => 'Dirigent', 'rid' => 22], // ✅ RID 22, niet RID 4!
  ];
}
```

**✅ KRITIEKE CORRECTIES:**
1. **Vriend rol (RID 4)** - Volledig ontbrak in vorige configuratie
2. **Dirigent rol (RID 22)** - Was verkeerd als RID 4, juist is RID 22
3. **Beheerder ≠ Admin** - Dit zijn 2 verschillende rollen (RID 6 vs RID 14)
4. **Totaal 13 rollen** (niet 16) - Sommige RID's ontbreken in Permission Matrix
```

### **Rechten Configuratie**

**Bestand:** `scripts/setup-role-permissions.php`

**Aanpassingen voor veld-niveau toegang:**

```php
$role_permissions = [
  'lid' => [
    // Basis content toegang
    'access content',
    'post comments',
    
    // Specifieke veld viewing rechten
    'view field_afbeeldingen',
    'view field_datum', 
    'view field_programma2',
    'view field_repertoire',
    'view field_partij_koor_l', // Koor partituren
    'view field_partij_tekst',  // Tekst partituren
    
    // Profiel velden (beperkt)
    'view own field_voornaam',
    'view own field_achternaam',
    'edit own field_telefoon',
    'edit own field_mobiel',
  ],
  
  'band' => [
    // Inherit lid permissions plus:
    'view field_partij_band', // Band partituren
    'view field_basgitaar',
    'view field_drums',
    'view field_gitaar', 
    'view field_keyboard',
    'edit field_basgitaar', // Band leden kunnen hun status bewerken
    'edit field_drums',
    'edit field_gitaar',
    'edit field_keyboard',
  ],
  
  'dirigent' => [
    // Enhanced activity permissions
    'edit any activiteit content',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'edit field_programma2',
    'edit field_datum',
  ],
  
  'bestuur' => [
    // Administrative access
    'create activiteit content',
    'edit any activiteit content',
    'view all user profile fields',
    'access user profiles',
  ],
];
```

---

## 🌐 **Webform Migratie Configuratie**

### **1. Webform Formulieren Migratie**

**Bestand:** `config/install/migrate_plus.migration.webform_forms.yml`

```yaml
source:
  plugin: d6_webform_forms
  
process:
  id: 
    plugin: machine_name
    source: nid
    prefix: 'webform_'
    
  title: title
  description: body
  
  # Convert D6 webform components to D11 elements
  elements:
    plugin: webform_elements
    source: components
    
  # Access permissions mapping
  access:
    plugin: webform_access_roles
    source: nid
```

### **2. Webform Inzendingen Migratie**

**Bestand:** `config/install/migrate_plus.migration.webform_submissions.yml`

```yaml
source:
  plugin: d6_webform_submissions
  
process:
  webform_id:
    plugin: migration_lookup
    migration: webform_forms
    source: nid
    
  uid:
    plugin: migration_lookup  
    migration: d6_thirdwing_user
    source: uid
    
  created: submitted
  completed: submitted
  data: 
    plugin: webform_submission_data
    source: submission_data
```

---

## 🔍 **Source Plugins Aanpassingen**

### **Nieuwe Source Plugin voor Partituren**

**Bestand:** `src/Plugin/migrate/source/D6ThirdwingPartituren.php`

```php
class D6ThirdwingPartituren extends DrupalSqlBase {
  
  public function query() {
    // Query om partituren uit D6 repertoire velden te halen
    $query = $this->select('content_type_repertoire', 'r')
      ->fields('r', ['nid', 'vid']);
      
    // Union queries voor verschillende partituur types
    $band_query = $this->select('content_field_partij_band', 'pb')
      ->fields('pb', ['nid', 'vid', 'field_partij_band_fid'])
      ->condition('pb.field_partij_band_fid', '', '!=');
    
    // Combine en retourneer resultaat voor media migratie
    return $query->union($band_query);
  }
  
  public function prepareRow(Row $row) {
    // Bepaal document soort en gerelateerd repertoire
    $row->setSourceProperty('source_type', 'partituur_band');
    $row->setSourceProperty('parent_node_id', $row->getSourceProperty('nid'));
    return parent::prepareRow($row);
  }
}
```

---

## 🧪 **Validatie Scripts Aanpassingen**

### **Uitgebreide Validatie**

**Bestand:** `scripts/validate-migration.php`

```php
function validateMigrationIntegrity() {
  echo "🔍 Validating Migration Integrity...\n";
  
  // 1. Content types validatie (8 types)
  validateContentTypes();
  
  // 2. Media bundles validatie (4 bundles)
  validateMediaBundles();
  
  // 3. User profile velden (32 velden)
  validateUserProfileFields();
  
  // 4. Reverse referenties validatie (KRITIEK)
  validatePartituurReferences();
  
  // 5. Webform integriteit
  validateWebformMigration();
}

function validatePartituurReferences() {
  echo "  Checking partituur reverse references...\n";
  
  // Check dat alle partituren gekoppeld zijn aan repertoire
  $orphaned_partituren = \Drupal::entityQuery('media')
    ->condition('bundle', 'document')
    ->condition('field_document_soort', 'partituur') 
    ->notExists('field_gerelateerd_repertoire')
    ->execute();
    
  if (!empty($orphaned_partituren)) {
    echo "  ❌ Found " . count($orphaned_partituren) . " orphaned partituren\n";
  } else {
    echo "  ✅ All partituren properly referenced\n";
  }
}
```

---

## 📈 **Performance Optimalisaties**

### **1. Database Query Optimalisaties**

```php
// Gebruik indexes voor reverse referentie queries
$database->schema()->addIndex(
  'media__field_gerelateerd_repertoire', 
  'repertoire_lookup_idx',
  ['field_gerelateerd_repertoire_target_id']
);
```

### **2. Batch Processing**

**Bestand:** `src/Commands/ThirdwingMigrationCommands.php`

```php
public function migrateIncrementally($options = ['batch-size' => 50]) {
  $batch_size = $options['batch-size'];
  
  // Process migrations in smaller batches
  $migrations = [
    'd6_thirdwing_user',
    'd6_thirdwing_taxonomy_term', 
    'd6_thirdwing_repertoire',
    'd6_thirdwing_media_document', // Partituren
    'd6_thirdwing_webform_forms',
    'd6_thirdwing_webform_submissions'
  ];
  
  foreach ($migrations as $migration_id) {
    $this->output()->writeln("Processing {$migration_id} in batches of {$batch_size}...");
    // Implementeer batch processing
  }
}
```

---

## 🎯 **Deployment Checklist**

### **Pre-Deployment Validatie**

1. **✅ Database Verbinding**
   - [ ] D6 database toegankelijk
   - [ ] Juiste credentials in settings.php
   - [ ] Netwerk connectiviteit

2. **✅ Content Architectuur** 
   - [ ] 8 content types aangemaakt
   - [ ] 4 media bundles geconfigureerd  
   - [ ] 32 user profile velden
   - [ ] 16 gebruikersrollen

3. **✅ Migratie Gereedheid**
   - [ ] Alle YAML configuraties correct
   - [ ] Source plugins geïmplementeerd
   - [ ] Process plugins geconfigureerd
   - [ ] Webform module geïnstalleerd

4. **✅ Reverse Referentie Setup**
   - [ ] Document media bundle voor partituren
   - [ ] field_gerelateerd_repertoire geconfigureerd
   - [ ] Partituur migratie logic geïmplementeerd

### **Post-Migration Validatie**

1. **✅ Data Integriteit**
   - [ ] Alle content gemigreerd
   - [ ] Partituren correct gekoppeld
   - [ ] User profiles compleet
   - [ ] Webform inzendingen behouden

2. **✅ Functionaliteit**
   - [ ] Partituur queries werken
   - [ ] Webformulieren functioneel
   - [ ] Gebruikersrechten correct
   - [ ] Media uploads werkend

---

## 🚨 **Kritieke Wijzigingen Samenvatting**

### **1. Partituur Architectuur (GROOTSTE WIJZIGING)**
- **Oud:** Directe bestandsvelden in repertoire
- **Nieuw:** Document media entities met reverse referenties
- **Impact:** Alle partituur queries moeten aangepast worden

### **2. Content Type Reductie**
- **Van:** 15 content types (D6)
- **Naar:** 8 content types (D11)
- **Vervangen:** audio, video, image → media bundles
- **Vervangen:** profiel → user profile fields

### **3. Webform Integratie**
- **D6:** Webform module met custom tabellen
- **D11:** Moderne Webform module (^6.2)
- **Migratie:** Formulieren + inzendingen + gebruiker associaties

### **4. Database Schema**
- **Nieuwe tabellen:** Media entity tabellen
- **Gewijzigde tabellen:** User profile data
- **Verwijderde afhankelijkheid:** Content type tabellen voor media

---

## 📞 **Ondersteuning & Troubleshooting**

Voor implementatie van deze aanpassingen:

1. **Valideer alle configuraties** voordat je begint met installatie
2. **Test database connectiviteit** naar D6 bron systeem
3. **Backup Drupal 11 site** voordat je migratie start
4. **Gebruik incremental migratie** voor grote datasets
5. **Monitor performance** tijdens partituur reverse referentie queries

**Belangrijke commando's:**
```bash
# Valideer complete setup
drush thirdwing:validate-all

# Test partituur reverse references
drush thirdwing:test-partituur-queries  

# Monitor migratie status
drush migrate:status --group=thirdwing_d6

# Rollback indien nodig
drush migrate:rollback d6_thirdwing_media_document
```

---

*Deze documentatie is leidend voor alle implementatie beslissingen. Alle config files, installatiescripts en migratiecode moeten worden aangepast volgens deze specificaties.*