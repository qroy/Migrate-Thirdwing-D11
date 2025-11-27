# Scripts Overzicht - Migration Only Versie

## 📂 Scripts Directory Structuur

```
thirdwing_migrate/scripts/
├── setup-migration.sh           ✅ NIEUW - Module setup (migration only)
├── migrate-execute.sh           ✅ BEHOUDEN - Volledige migratie uitvoeren
├── migrate-sync.sh              ✅ BEHOUDEN - Incrementele synchronisatie
└── validate-migration.php       ✅ BEHOUDEN - Data validatie na migratie
```

---

## ✅ **Behouden Scripts**

### **1. setup-migration.sh**
**Status:** NIEUW - Vervanger van setup-complete-migration.sh  
**Functie:** Module installatie en database configuratie

**Wat het doet:**
- Valideert Drupal omgeving
- Configureert D6 database connectie in settings.php
- Installeert vereiste composer packages
- Activeert benodigde modules (core + contrib)
- Activeert thirdwing_migrate module
- Valideert migratie readiness

**Wat het NIET doet:**
- Content types aanmaken
- Velden configureren
- Media bundles creëren
- User profile fields toevoegen
- View modes instellen
- Permissions configureren

**Gebruik:**
```bash
# Standaard uitvoering
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh

# Met opties
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh --skip-composer
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh --reconfigure-db
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh --debug

# Help
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh --help
```

**Opties:**
- `--skip-composer` - Sla composer installatie over
- `--skip-modules` - Sla module activering over
- `--skip-database` - Sla database configuratie over
- `--reconfigure-db` - Forceer database herconfiguratie
- `--debug` - Enable debug output
- `--help` - Toon help bericht

---

### **2. migrate-execute.sh**
**Status:** BEHOUDEN - Onveranderd  
**Functie:** Voert volledige migratie uit in correcte volgorde

**Wat het doet:**
- Voert alle migraties uit in dependency volgorde
- Fase 1: Taxonomieën, gebruikers, bestanden
- Fase 2: Media entities (image, document, audio, video)
- Fase 3: Content (alle content types)
- Fase 4: Webforms en submissions
- Fase 5: Comments
- Toont voortgang en statistieken
- Rapporteert fouten

**Gebruik:**
```bash
# Volledige migratie
bash modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# Met limiet voor testen
bash modules/custom/thirdwing_migrate/scripts/migrate-execute.sh --limit=10

# Specifieke groep
bash modules/custom/thirdwing_migrate/scripts/migrate-execute.sh --group=thirdwing_d6
```

**Migratie Volgorde:**
```bash
# Fase 1: Basis
d6_thirdwing_taxonomy_vocabulary
d6_thirdwing_taxonomy_term
d6_thirdwing_user_role
d6_thirdwing_user
d6_thirdwing_file

# Fase 2: Media
d6_thirdwing_media_image
d6_thirdwing_media_document
d6_thirdwing_media_audio
d6_thirdwing_media_video

# Fase 3: Content
d6_thirdwing_location
d6_thirdwing_repertoire
d6_thirdwing_program
d6_thirdwing_activity
d6_thirdwing_news
d6_thirdwing_page
d6_thirdwing_album
d6_thirdwing_friend

# Fase 4: Webforms
d6_thirdwing_webform_forms
d6_thirdwing_webform_submissions

# Fase 5: Comments
d6_thirdwing_comment
```

---

### **3. migrate-sync.sh**
**Status:** BEHOUDEN - Onveranderd  
**Functie:** Incrementele synchronisatie van gewijzigde content

**Wat het doet:**
- Synchroniseert alleen gewijzigde content sinds laatste sync
- Gebruikt timestamp tracking
- Ondersteunt verschillende tijdsperiodes
- Update mode voor bestaande content
- Dry-run optie voor testen

**Gebruik:**
```bash
# Sync sinds gisteren
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday

# Sync sinds specifieke datum
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=2024-01-01

# Sync sinds laatste sync
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=last

# Sync laatste week
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="-1 week"

# Dry run (test alleen, geen wijzigingen)
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday --dry-run

# Sync specifieke content types
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday --types="activity,news"
```

**Opties:**
- `--since=<date>` - Sync vanaf datum/tijd
  - `yesterday` - Vanaf gisteren
  - `last` - Vanaf laatste sync
  - `2024-01-01` - Vanaf specifieke datum
  - `"-1 week"` - Relatieve tijd
- `--dry-run` - Simuleer sync zonder wijzigingen
- `--types=<list>` - Specifieke content types (comma-separated)
- `--verbose` - Gedetailleerde output

**Sync Strategieën:**
```bash
# Dagelijkse ontwikkeling sync
0 2 * * * /path/to/migrate-sync.sh --since=yesterday

# Wekelijkse volledige check
0 3 * * 0 /path/to/migrate-sync.sh --since="-1 week"

# Na specifieke wijzigingen in D6
/path/to/migrate-sync.sh --since="2024-01-15 14:30:00"
```

---

### **4. validate-migration.php**
**Status:** BEHOUDEN - Onveranderd  
**Functie:** Valideert gemigreerde data integriteit

**Wat het doet:**
- Controleert data integriteit na migratie
- Valideert referenties tussen entities
- Controleert ontbrekende velden
- Verifieert media uploads
- Rapporteert inconsistenties

**Gebruik:**
```bash
# Valideer alle gemigreerde data
drush php:script scripts/validate-migration.php --type=all

# Valideer specifieke types
drush php:script scripts/validate-migration.php --type=users
drush php:script scripts/validate-migration.php --type=content
drush php:script scripts/validate-migration.php --type=media
drush php:script scripts/validate-migration.php --type=webforms

# Gedetailleerde output
drush php:script scripts/validate-migration.php --type=all --verbose

# Alleen fouten tonen
drush php:script scripts/validate-migration.php --type=all --errors-only
```

**Validatie Checks:**
- ✅ Gebruikers: Profile fields, roles, status
- ✅ Content: Field values, references, media
- ✅ Media: Files, bundles, metadata
- ✅ Webforms: Forms, submissions, user associations
- ✅ Taxonomieën: Terms, hierarchie, references
- ✅ Referenties: Entity references integriteit

**Output Formaat:**
```
=== Migration Validation Report ===

✅ Users: 150/150 validated
   • Profile fields complete: 150/150
   • Roles assigned: 150/150
   • Missing data: 0

✅ Content: 1250/1250 validated
   • Activiteit: 250/250
   • Nieuws: 180/180
   • Repertoire: 320/320
   ...

⚠️  Media: 450/500 validated
   • Missing files: 50
   • Orphaned entities: 0

=== Summary ===
Total items validated: 1850
Successful: 1800 (97.3%)
Warnings: 50 (2.7%)
Errors: 0 (0%)
```

---

## ❌ **Verwijderde Scripts**

De volgende scripts zijn **verwijderd** omdat ze content structuur aanmaakten:

### **Content Structuur (VERWIJDERD)**
- ❌ `create-content-types-and-fields.php`
- ❌ `create-media-bundles-and-fields.php`
- ❌ `create-user-profile-fields.php`
- ❌ `add-media-dependent-fields.php`

### **Configuratie (VERWIJDERD)**
- ❌ `setup-fields-display.php`
- ❌ `create-user-roles.php`
- ❌ `setup-role-permissions.php`
- ❌ `configure-image-exif-date-extraction.php`

### **Validatie Content Structuur (VERWIJDERD)**
- ❌ `validate-created-fields.php`

### **Oude Setup (VERVANGEN)**
- ❌ `setup-complete-migration.sh` → Vervangen door `setup-migration.sh`

---

## 🔄 **Workflow met Nieuwe Scripts**

### **Eerste Keer Setup:**
```bash
# 1. Handmatig: Maak content structuur aan volgens documentatie

# 2. Voer setup uit
bash modules/custom/thirdwing_migrate/scripts/setup-migration.sh

# 3. Test met kleine batch
drush migrate:import d6_thirdwing_user --limit=5

# 4. Valideer test
drush php:script scripts/validate-migration.php --type=users

# 5. Volledige migratie
bash modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# 6. Valideer alles
drush php:script scripts/validate-migration.php --type=all
```

### **Ontwikkeling Workflow:**
```bash
# Dagelijkse sync tijdens ontwikkeling
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since=yesterday

# Na wijzigingen in D6
bash modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="2024-01-15 10:00:00"

# Valideer na sync
drush php:script scripts/validate-migration.php --type=all
```

### **Troubleshooting:**
```bash
# Check migratie status
drush migrate:status --group=thirdwing_d6

# Bekijk fouten van specifieke migratie
drush migrate:messages d6_thirdwing_activity

# Reset en opnieuw proberen
drush migrate:reset d6_thirdwing_activity
drush migrate:import d6_thirdwing_activity

# Rollback als nodig
drush migrate:rollback d6_thirdwing_activity

# Valideer na fixes
drush php:script scripts/validate-migration.php --type=content
```

---

## 📋 **Script Dependencies**

### **setup-migration.sh vereist:**
- Drush (geïnstalleerd)
- Composer (geïnstalleerd)
- Drupal 11 (gebootstrapt)
- Content structuur (handmatig aangemaakt)

### **migrate-execute.sh vereist:**
- setup-migration.sh (succesvol uitgevoerd)
- D6 database (toegankelijk)
- Content structuur (compleet)

### **migrate-sync.sh vereist:**
- migrate-execute.sh (minimaal één keer uitgevoerd)
- Timestamp tracking (actief)

### **validate-migration.php vereist:**
- Gemigreerde data (aanwezig)
- Drush (voor php:script command)

---

## 🎯 **Best Practices**

### **Setup:**
1. Gebruik `--debug` voor eerste keer setup
2. Test database connectie handmatig eerst
3. Controleer module dependencies

### **Migratie:**
1. Start met kleine test batches
2. Valideer na elke fase
3. Monitor fouten en waarschuwingen
4. Gebruik dry-run voor sync testen

### **Validatie:**
1. Valideer na elke migratie fase
2. Los fouten op voordat je verder gaat
3. Document inconsistenties
4. Test specifieke probleem gebieden

### **Onderhoud:**
1. Regular incremental syncs tijdens ontwikkeling
2. Monitor disk space voor media uploads
3. Check logs regelmatig
4. Backup voor grote sync operations

---

**Laatste Update:** November 2024  
**Versie:** 2.0 - Migration Only
