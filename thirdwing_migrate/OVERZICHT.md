# ThirdWing Migrate Module - Overzicht

## 📦 Wat is er gemaakt

Een complete Drupal migratiemodule voor het migreren van ThirdWing van Drupal 6 naar Drupal 11.

## 📁 Structuur

```
thirdwing_migrate/
├── config/install/
│   └── migrate_plus.migration_group.thirdwing.yml    # Migration group configuratie
│
├── migrations/                                        # Alle migratie YAML bestanden
│   ├── thirdwing_taxonomy_toegang.yml                # Toegang taxonomy
│   ├── thirdwing_user.yml                            # Users met profiel velden
│   ├── thirdwing_file.yml                            # Files migratie
│   ├── thirdwing_media_image.yml                     # Media - afbeeldingen
│   ├── thirdwing_media_document.yml                  # Media - documenten
│   ├── thirdwing_media_audio.yml                     # Media - audio (van node)
│   ├── thirdwing_media_video.yml                     # Media - video (van node)
│   ├── thirdwing_node_artikel.yml                    # Content - Artikel
│   ├── thirdwing_node_document.yml                   # Content - Document
│   ├── thirdwing_node_agenda.yml                     # Content - Agenda
│   └── thirdwing_node_pagina.yml                     # Content - Pagina
│
├── src/
│   └── Plugin/
│       ├── migrate/
│       │   ├── process/
│       │   │   └── ToegangMapper.php                 # Custom process plugin
│       │   └── source/
│       │       └── ThirdWingNode.php                 # Custom source plugin
│
├── thirdwing_migrate.info.yml                        # Module definitie
├── settings.example.php                              # Database configuratie voorbeeld
├── migrate.sh                                        # Migratie uitvoer script
├── rollback.sh                                       # Rollback script
├── README.md                                         # Installatie & gebruik instructies
└── CONFIGURATION_CHECKLIST.md                        # Uitgebreide configuratie checklist
```

## ✅ Wat is gedaan

### 1. Basis Module Setup
- Module info bestand met alle dependencies
- Migration group configuratie
- Database connectie voorbeeld

### 2. Migratie Bestanden
**Taxonomieën:**
- Toegang taxonomy (met hierarchie support)

**Users:**
- User migratie met profiel velden (Content Profile → user fields)
- Role mapping

**Files & Media:**
- File migratie met file_copy
- Media Image (van D6 files)
- Media Document (van D6 files)
- Media Audio (van D6 audio content type → media)
- Media Video (van D6 video content type → media)

**Content Types:**
- Artikel (met afbeeldingen, taxonomy, datum)
- Document (met document type, toegang)
- Agenda (met datum/tijd, locatie, afbeelding)
- Pagina (met hiërarchie, multiple images)

### 3. Custom Plugins
- **ThirdWingNode**: Custom source plugin voor complexere queries en CCK field handling
- **ToegangMapper**: Custom process plugin voor taxonomy term mapping

### 4. Utility Scripts
- **migrate.sh**: Voert alle migraties uit in correcte volgorde met feedback
- **rollback.sh**: Draait alle migraties terug (met confirmatie)

### 5. Documentatie
- **README.md**: Installatie, volgorde, commands, debugging tips
- **CONFIGURATION_CHECKLIST.md**: Uitgebreide checklist met:
  - Field mappings per content type
  - Taxonomy mappings
  - Test strategie
  - Common issues & solutions
  - Performance tips
  - Post-migratie taken

## 🔧 Wat moet je nog doen

### 1. Field Mapping Aanpassen
Elk migratie YAML bestand heeft placeholder comments voor fields. Je moet deze vervangen met de **echte field names** uit je D6 site:

```yaml
# Voorbeeld in thirdwing_node_artikel.yml:
# field_artikel_afbeelding:              # ← Vervang met echte D6 field name
#   plugin: sub_process
#   source: field_artikel_afbeelding     # ← Vervang met echte D6 field name
#   process:
#     target_id:
#       plugin: migration_lookup
#       migration: thirdwing_media_image
#       source: fid
```

### 2. Ontbrekende Content Types
Je hebt nog 4 content types die toegevoegd moeten worden:
- **Podium** (template maken zoals artikel/document)
- **Werkgroep** (template maken)
- **Bijdrage** (template maken)
- **Album** (was 'foto', template maken met media gallery)

### 3. Extra Taxonomieën
Volgens je documentatie heb je nog meer taxonomieën:
- Document Type taxonomy (6 types)
- Eventuele andere taxonomieën

### 4. D11 Content Types Aanmaken
Voor de migratie moet je alle content types in D11 aanmaken met:
- Alle custom fields
- Juiste field types
- Field groups
- View modes
- Permissions

### 5. Test & Verfijn
1. Installeer de module
2. Test met kleine dataset (--limit=5)
3. Controleer field mappings
4. Verfijn waar nodig
5. Test hermigratie (--update)

## 🚀 Snel Starten

```bash
# 1. Kopieer module naar Drupal
cp -r thirdwing_migrate /pad/naar/drupal/modules/custom/

# 2. Voeg database config toe aan settings.php
# (zie settings.example.php)

# 3. Installeer module
drush en thirdwing_migrate -y

# 4. Test met één content type
drush migrate:import thirdwing_user --limit=5
drush migrate:import thirdwing_node_artikel --limit=5

# 5. Controleer resultaat in D11

# 6. Run volledige migratie
cd modules/custom/thirdwing_migrate
./migrate.sh
```

## 💡 Belangrijke Principes

1. **D6 blijft source of truth** - hermigraties overschrijven D11 content
2. **Overwrite_properties** - zorgt dat updates worden toegepast
3. **Migration dependencies** - volgorde is cruciaal (users voor content, files voor media, etc.)
4. **Test met kleine dataset** - gebruik --limit tijdens development
5. **Monitor errors** - gebruik drush migrate:messages

## 📋 Next Steps Priority

1. ✅ Vul alle echte field names in (gebruik je Excel spreadsheet)
2. ✅ Maak D11 content types aan met alle fields
3. ✅ Voeg ontbrekende content types toe (Podium, Werkgroep, Bijdrage, Album)
4. ✅ Test met kleine dataset
5. ✅ Verfijn mappings op basis van test resultaten
6. ✅ Run volledige migratie
7. ✅ Setup regelmatige hermigratie (bijv. nightly cron)

## 📚 Handige Resources

- Migrate API: https://www.drupal.org/docs/drupal-apis/migrate-api
- Process plugins: https://www.drupal.org/docs/drupal-apis/migrate-api/migrate-process-plugins
- Migrate Tools: https://www.drupal.org/project/migrate_tools

## ⚠️ Let op

- Backup je D11 database voor je start
- Test eerst in development omgeving
- Monitor disk space (files worden gekopieerd)
- Check PHP memory_limit (verhoog indien nodig)
- Verwacht dat eerste run tijd kost (veel content)
