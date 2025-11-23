# Thirdwing D6 naar D11 Migratie Module

**Versie:** 2.0 - Migratie-Only  
**Drupal Compatibiliteit:** 11.x  
**Migratierichting:** Drupal 6 → Drupal 11

---

## 📋 **Overzicht**

Deze module verzorgt de **migratie van data** van een Drupal 6 Thirdwing website naar een handmatig voorbereide Drupal 11 installatie. De module bevat GEEN functionaliteit voor het aanmaken van content types of velden - dit wordt volledig handmatig gedaan door de beheerder.

### **Wat doet deze module?**
✅ Migreert content van D6 naar D11  
✅ Ondersteunt incrementele synchronisatie  
✅ Beheert media (images, documents, audio, video)  
✅ Migreert gebruikers en profielen  
✅ Migreert taxonomieën  
✅ Migreert webforms en submissions  
✅ Valideert gemigreerde data  

### **Wat doet deze module NIET?**
❌ Content types aanmaken  
❌ Velden configureren  
❌ View modes instellen  
❌ Display configuraties aanmaken  
❌ Permissions configureren  

---

## 🔧 **Vereisten**

### **Drupal 11 Site (Handmatig Voorbereid)**
- Alle content types handmatig aangemaakt volgens documentatie
- Alle velden handmatig geconfigureerd
- Media bundles handmatig ingesteld
- User profile fields handmatig toegevoegd
- View modes en displays handmatig geconfigureerd

### **Technische Vereisten**
- Drupal 11.x installatie (volledig geconfigureerd)
- PHP 8.2+
- MySQL/MariaDB toegang tot D6 bron database
- Composer voor dependency management

### **Contrib Modules (Handmatig Geïnstalleerd)**
```bash
composer require drupal/migrate_plus:^6.0
composer require drupal/migrate_tools:^6.0
composer require drupal/webform:^6.2
composer require drupal/admin_toolbar:^3.0
composer require drupal/pathauto:^1.8
composer require drupal/token:^1.9

drush en migrate_plus migrate_tools webform admin_toolbar pathauto token -y
```

---

## 📥 **Installatie**

### **Stap 1: Module Installeren**
```bash
# Download module naar custom modules directory
cd web/modules/custom/
# [plaats thirdwing_migrate module hier]

# Activeer de migratie module
drush en thirdwing_migrate -y
```

### **Stap 2: Database Configuratie**
Voeg de Drupal 6 database connectie toe aan `settings.php`:

```php
// Thirdwing D6 Migratie Database Configuratie
$databases['migrate']['default'] = [
  'database' => 'jouw_d6_database',
  'username' => 'jouw_gebruiker',
  'password' => 'jouw_wachtwoord',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
```

### **Stap 3: Database Connectie Testen**
```bash
drush php:eval "try { 
  \$conn = \Drupal\Core\Database\Database::getConnection('default', 'migrate'); 
  echo 'Connectie succesvol: ' . \$conn->query('SELECT VERSION()')->fetchField(); 
} catch (Exception \$e) { 
  echo 'Connectie mislukt: ' . \$e->getMessage(); 
}"
```

---

## 🔄 **Migratie Uitvoeren**

### **Migratie Overzicht Bekijken**
```bash
# Alle beschikbare migraties
drush migrate:status --group=thirdwing_d6

# Specifieke migratie status
drush migrate:status d6_thirdwing_user
```

### **Volledige Migratie (Eerste Keer)**

#### **Fase 1: Basis Data**
```bash
# Taxonomieën
drush migrate:import d6_thirdwing_taxonomy_vocabulary
drush migrate:import d6_thirdwing_taxonomy_term

# Gebruikers en rollen
drush migrate:import d6_thirdwing_user_role
drush migrate:import d6_thirdwing_user

# Bestanden
drush migrate:import d6_thirdwing_file
```

#### **Fase 2: Media**
```bash
drush migrate:import d6_thirdwing_media_image
drush migrate:import d6_thirdwing_media_document
drush migrate:import d6_thirdwing_media_audio
drush migrate:import d6_thirdwing_media_video
```

#### **Fase 3: Content**
```bash
drush migrate:import d6_thirdwing_location
drush migrate:import d6_thirdwing_repertoire
drush migrate:import d6_thirdwing_program
drush migrate:import d6_thirdwing_activity
drush migrate:import d6_thirdwing_news
drush migrate:import d6_thirdwing_page
drush migrate:import d6_thirdwing_album
drush migrate:import d6_thirdwing_friend
```

#### **Fase 4: Webforms**
```bash
drush migrate:import d6_thirdwing_webform_forms
drush migrate:import d6_thirdwing_webform_submissions
```

#### **Fase 5: Reacties**
```bash
drush migrate:import d6_thirdwing_comment
```

### **Automatische Volledige Migratie**
```bash
# Voer alle migraties uit in de juiste volgorde
bash modules/custom/thirdwing_migrate/scripts/migrate-execute.sh
```

---

## 🔁 **Incrementele Synchronisatie**

Tijdens ontwikkeling kun je periodiek wijzigingen van D6 naar D11 synchroniseren:

### **Sync Script Uitvoeren**
```bash
# Sync alles sinds gisteren
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday

# Sync alles sinds specifieke datum
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=2024-01-01

# Sync alles sinds laatste sync
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=last

# Dry run (toon wat gesynchroniseerd zou worden)
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday --dry-run
```

### **Handmatige Update van Specifieke Content**
```bash
# Update specifieke migratie met --update
drush migrate:import d6_thirdwing_activity --update

# Reset en opnieuw importeren
drush migrate:reset d6_thirdwing_activity
drush migrate:import d6_thirdwing_activity
```

---

## 🧹 **Migratie Terugdraaien**

### **Enkele Migratie Terugdraaien**
```bash
drush migrate:rollback d6_thirdwing_activity
```

### **Alle Migraties Terugdraaien**
```bash
# In omgekeerde volgorde
drush migrate:rollback d6_thirdwing_comment
drush migrate:rollback d6_thirdwing_webform_submissions
drush migrate:rollback d6_thirdwing_webform_forms
drush migrate:rollback d6_thirdwing_friend
drush migrate:rollback d6_thirdwing_album
drush migrate:rollback d6_thirdwing_page
drush migrate:rollback d6_thirdwing_news
drush migrate:rollback d6_thirdwing_activity
drush migrate:rollback d6_thirdwing_program
drush migrate:rollback d6_thirdwing_repertoire
drush migrate:rollback d6_thirdwing_location
drush migrate:rollback d6_thirdwing_media_video
drush migrate:rollback d6_thirdwing_media_audio
drush migrate:rollback d6_thirdwing_media_document
drush migrate:rollback d6_thirdwing_media_image
drush migrate:rollback d6_thirdwing_file
drush migrate:rollback d6_thirdwing_user
drush migrate:rollback d6_thirdwing_user_role
drush migrate:rollback d6_thirdwing_taxonomy_term
drush migrate:rollback d6_thirdwing_taxonomy_vocabulary
```

### **Reset Zonder Data Verwijderen**
```bash
# Reset migratie status (import opnieuw mogelijk)
drush migrate:reset d6_thirdwing_activity
```

---

## 📊 **Migratie Validatie**

### **Data Integriteit Controleren**
```bash
# Valideer gemigreerde gebruikers
drush php:script scripts/validate-migration.php --type=users

# Valideer gemigreerde content
drush php:script scripts/validate-migration.php --type=content

# Valideer media entities
drush php:script scripts/validate-migration.php --type=media

# Valideer alle gemigreerde data
drush php:script scripts/validate-migration.php --type=all
```

### **Migratie Statistieken**
```bash
# Toon gedetailleerde statistieken
drush migrate:status --group=thirdwing_d6

# Toon alleen fouten
drush migrate:messages d6_thirdwing_activity
```

---

## 🛠️ **Drush Commands**

De module biedt custom Drush commands voor migratie beheer:

```bash
# Valideer migratie configuratie
drush thirdwing:validate-migration

# Toon migratie statistieken
drush thirdwing:migration-stats

# Test database connectie
drush thirdwing:test-connection

# Analyseer migratie dependencies
drush thirdwing:analyze-dependencies
```

---

## 📁 **Module Structuur**

```
thirdwing_migrate/
├── config/
│   └── install/
│       └── migrate_plus.migration_group.thirdwing_d6.yml
├── migrations/
│   ├── d6_thirdwing_taxonomy_vocabulary.yml
│   ├── d6_thirdwing_taxonomy_term.yml
│   ├── d6_thirdwing_user_role.yml
│   ├── d6_thirdwing_user.yml
│   ├── d6_thirdwing_file.yml
│   ├── d6_thirdwing_media_image.yml
│   ├── d6_thirdwing_media_document.yml
│   ├── d6_thirdwing_media_audio.yml
│   ├── d6_thirdwing_media_video.yml
│   ├── d6_thirdwing_location.yml
│   ├── d6_thirdwing_repertoire.yml
│   ├── d6_thirdwing_program.yml
│   ├── d6_thirdwing_activity.yml
│   ├── d6_thirdwing_news.yml
│   ├── d6_thirdwing_page.yml
│   ├── d6_thirdwing_album.yml
│   ├── d6_thirdwing_friend.yml
│   ├── d6_thirdwing_webform_forms.yml
│   ├── d6_thirdwing_webform_submissions.yml
│   └── d6_thirdwing_comment.yml
├── scripts/
│   ├── migrate-execute.sh
│   ├── migrate-sync.sh
│   └── validate-migration.php
├── src/
│   ├── Commands/
│   │   └── ThirdwingMigrateCommands.php
│   └── Plugin/
│       ├── migrate/
│       │   ├── source/
│       │   └── process/
│       └── [migratie plugins]
└── thirdwing_migrate.info.yml
```

---

## 🔍 **Migratie Architectuur**

### **Media Architectuur**
De module migreert D6 bestanden naar D11 media entities:

| D6 Bron | D11 Media Bundle | Veldnaam |
|---------|------------------|----------|
| Image content type | `media:image` | `field_media_image` |
| File attachments | `media:document` | `field_media_document` |
| Audio content type | `media:audio` | `field_media_audio` |
| Video content type | `media:video` | `field_media_video` |

### **Content Mapping**
| D6 Content Type | D11 Content Type | Opmerkingen |
|-----------------|------------------|-------------|
| `activiteit` | `activiteit` | Volledige field mapping |
| `foto` | `foto` | Nu met media references |
| `locatie` | `locatie` | Directe mapping |
| `nieuws` | `nieuws` | Body + custom fields |
| `pagina` | `pagina` | Statische content |
| `programma` | `programma` | Programma elementen |
| `repertoire` | `repertoire` | ZONDER partituur velden |
| `vriend` | `vriend` | Vrienden gegevens |
| `webform` | `webform` | Form + submissions |

### **Gebruiker Profile Mapping**
D6 `profiel` content type → D11 user profile fields (32 velden)

---

## ⚠️ **Belangrijke Opmerkingen**

### **Partituren Architectuur**
**D6 Structuur:**
- `field_partij_band_fid` op repertoire
- `field_partij_koor_l_fid` op repertoire
- `field_partij_tekst_fid` op repertoire

**D11 Structuur (Reverse Reference):**
- Document media met `field_gerelateerd_repertoire`
- `field_document_soort: "partituur"`
- Query via media entities naar repertoire

### **Eenrichtingsverkeer**
De migratie is **eenrichtingsverkeer** van D6 naar D11. Wijzigingen in D11 worden NIET teruggezet naar D6.

### **Backup Strategie**
De D6 site blijft actief als backup totdat D11 volledig operationeel is en gevalideerd.

---

## 🐛 **Troubleshooting**

### **Database Connectie Problemen**
```bash
# Test connectie expliciet
drush php:eval "
\$conn = \Drupal\Core\Database\Database::getConnection('default', 'migrate');
echo 'Verbonden met: ' . \$conn->query('SELECT DATABASE()')->fetchField();
"
```

### **Migratie Blijft Hangen**
```bash
# Reset migratie status
drush migrate:reset d6_thirdwing_[migration_name]

# Check voor incomplete migraties
drush migrate:status --group=thirdwing_d6 | grep -i incomplete
```

### **Ontbrekende Velden**
Als velden ontbreken in D11:
1. Controleer of content type handmatig correct is aangemaakt
2. Controleer veldnamen in D11 Content Types and Fields.md
3. Zorg dat field machine names exact overeenkomen

### **Media Upload Problemen**
```bash
# Controleer file permissions
ls -la web/sites/default/files/

# Zorg dat directory schrijfbaar is
chmod 755 web/sites/default/files/
```

---

## 📖 **Documentatie Referenties**

### **Verplichte Handmatige Voorbereidingsdocumentatie**
- `D11 Content Types and Fields.md` - Exacte velddefinities
- `D6 Content Types and Fields.md` - Bron structuur
- `D6 Permission Matrix.html` - Rechten configuratie
- `D6 Workflows.md` - Workflowlogica

### **Migratie Documentatie**
- `migrations/` directory - YAML configuraties per content type
- `src/Plugin/migrate/` - Custom source en process plugins

---

## 📞 **Support**

Voor vragen over de migratie:
1. Controleer eerst `drush migrate:messages [migration_id]`
2. Valideer D11 structuur komt overeen met documentatie
3. Test met kleine batches eerst: `drush migrate:import [migration_id] --limit=10`

---

## 🎯 **Checklist voor Productie Migratie**

### **Voorbereiding**
- [ ] D11 site volledig handmatig geconfigureerd
- [ ] Alle content types aangemaakt volgens documentatie
- [ ] Alle velden geconfigureerd met correcte machine names
- [ ] Media bundles ingesteld
- [ ] User profile fields toegevoegd
- [ ] View modes en displays geconfigureerd
- [ ] Database connectie getest
- [ ] Module geïnstalleerd en geactiveerd

### **Migratie Uitvoering**
- [ ] Test migratie op development environment
- [ ] Valideer kleine batch migraties
- [ ] Backup D6 database
- [ ] Voer volledige migratie uit
- [ ] Valideer alle gemigreerde content
- [ ] Test alle functionaliteit
- [ ] Controleer media uploads
- [ ] Verifieer gebruiker logins

### **Na Migratie**
- [ ] URL aliases gegenereerd
- [ ] Cache geleegd
- [ ] Permissions gecontroleerd
- [ ] Search index opgebouwd
- [ ] Performance getest
- [ ] Backup D11 site

---

**Laatste Update:** November 2024  
**Module Versie:** 2.0 - Migration Only  
**Drupal Compatibiliteit:** 11.x  
**Migratie Strategie:** Eenrichtingsverkeer D6 → D11
