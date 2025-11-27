# ThirdWing Migratie Configuratie Checklist

## Pre-migratie Voorbereiding

### 1. Database Setup
- [ ] D6 database toegankelijk gemaakt voor D11
- [ ] Database credentials toegevoegd aan settings.php
- [ ] Database connectie getest: `drush sqlq "SELECT COUNT(*) FROM node" --database=drupal_6`

### 2. Files Directory
- [ ] D6 files directory locatie gevonden
- [ ] Files toegankelijk gemaakt voor D11 (symlink of kopie)
- [ ] Files directory pad geconfigureerd in migraties

### 3. Drupal 11 Setup
- [ ] Content types aangemaakt in D11
- [ ] Alle custom fields aangemaakt
- [ ] Field groups geconfigureerd
- [ ] Taxonomieën aangemaakt (toegang, document_type, etc.)
- [ ] Media types met custom fields aangemaakt
- [ ] View modes geconfigureerd
- [ ] Permissions ingesteld

### 4. Module Dependencies
```bash
composer require drupal/migrate_plus
composer require drupal/migrate_tools
drush en migrate migrate_drupal migrate_plus migrate_tools -y
```

## Field Mapping per Content Type

### Artikel
D6 Field → D11 Field mapping:
- [ ] `body` → `body`
- [ ] `field_artikel_afbeelding` → `field_artikel_image` (media reference)
- [ ] `taxonomy` (toegang) → `field_toegang`
- [ ] `field_artikel_datum` → `field_artikel_date`
- [ ] Overige custom fields...

### Document
- [ ] `body` → `body`
- [ ] `field_document_file` → `field_document_media` (media reference)
- [ ] `field_document_type` → `field_document_type` (taxonomy)
- [ ] `taxonomy` (toegang) → `field_toegang`
- [ ] Overige custom fields...

### Agenda
- [ ] `body` → `body`
- [ ] `field_event_date` → `field_agenda_start_date` + `field_agenda_end_date`
- [ ] `field_event_location` → `field_agenda_location`
- [ ] `field_event_image` → `field_agenda_image` (media reference)
- [ ] `taxonomy` (toegang) → `field_toegang`
- [ ] Overige custom fields...

### Pagina
- [ ] `body` → `body`
- [ ] `parent` → `field_parent_page` (entity reference)
- [ ] `field_images` → `field_pagina_images` (media references)
- [ ] `taxonomy` (toegang) → `field_toegang`
- [ ] Overige custom fields...

### Album (was: foto)
- [ ] `title` → `title`
- [ ] `body` → `body`
- [ ] `field_foto_images` → `field_album_images` (media references)
- [ ] `taxonomy` (toegang) → `field_toegang`
- [ ] Overige custom fields...

### Podium
- [ ] Field mappings toevoegen...

### Werkgroep
- [ ] Field mappings toevoegen...

### Bijdrage
- [ ] Field mappings toevoegen...

## Media Type Mappings

### Audio (was: audio content type)
- [ ] `title` (node) → `name` (media)
- [ ] `field_audio_file` → `field_media_audio_file`
- [ ] `field_audio_artist` → `field_audio_artist`
- [ ] `field_audio_album` → `field_audio_album`
- [ ] `field_audio_duration` → `field_audio_duration`
- [ ] `body` → `field_audio_description`

### Video (was: video content type)
- [ ] `title` (node) → `name` (media)
- [ ] `field_video_file` → `field_media_video_file`
- [ ] `field_video_thumbnail` → `field_video_thumbnail`
- [ ] `field_video_duration` → `field_video_duration`
- [ ] `body` → `field_video_description`

## User Profile Fields (was: profiel content type)

Content Profile fields → User fields:
- [ ] `field_profile_first_name` → `field_first_name`
- [ ] `field_profile_last_name` → `field_last_name`
- [ ] `field_profile_photo` → `user_picture`
- [ ] `field_profile_bio` → `field_bio`
- [ ] Overige profiel fields...

## Taxonomy Mappings

### Toegang Taxonomy
D6 Terms → D11 Terms:
- [ ] Publiek → publiek
- [ ] Leden → leden
- [ ] Bestuur → bestuur
- [ ] Commissie → commissie
- [ ] Werkgroep → werkgroep

### Document Type Taxonomy (6 types)
- [ ] Type 1 mapping...
- [ ] Type 2 mapping...
- [ ] Type 3 mapping...
- [ ] Type 4 mapping...
- [ ] Type 5 mapping...
- [ ] Type 6 mapping...

## Migratie Configuratie Updates

Voor elk content type moet je de YAML bestanden aanpassen:

1. **Update source field names** - vervang placeholders door echte D6 field names
2. **Update destination field names** - gebruik je D11 field machine names
3. **Add field groups** indien gebruikt
4. **Configure date formats** voor datum velden
5. **Add entity reference mappings** voor referenties tussen content

## Test Strategie

### Fase 1: Kleine dataset test
```bash
# Test met limited aantal items
drush migrate:import thirdwing_user --limit=10
drush migrate:import thirdwing_node_artikel --limit=5
```

### Fase 2: Validatie
- [ ] Check of alle velden correct gemigreerd zijn
- [ ] Verifieer media references
- [ ] Test toegangsrechten
- [ ] Controleer taxonomy terms
- [ ] Test entity references (parent pages, etc.)

### Fase 3: Volledige migratie
```bash
# Run volledige migratie
./migrate.sh
```

### Fase 4: Hermigratie test
```bash
# Test of updates correct worden toegepast
drush migrate:import thirdwing_node_artikel --update
```

## Common Issues & Solutions

### Files niet gevonden
- Check file paths in D6 database
- Verifieer files directory permissions
- Gebruik absolute paths indien nodig

### Taxonomy terms niet gematcht
- Gebruik custom process plugin voor term mapping
- Check vocabulary machine names
- Verifieer term names (case sensitive)

### Entity references broken
- Controleer migration dependencies
- Zorg dat referenced entities eerst gemigreerd zijn
- Check migration_lookup configuratie

### Date format issues
- Gebruik format_date process plugin
- Verifieer source date format
- Check timezone settings

### Access permissions
- Configureer node access modules
- Test met verschillende user rollen
- Verifieer field level permissions

## Performance Tips

1. **Batch size**: gebruik --feedback="100 items" voor progress
2. **Limit tijdens development**: --limit=10 voor snelle tests
3. **Update mode**: --update is sneller dan rollback + import
4. **Database indices**: zorg voor goede indices op D6 database
5. **Memory**: verhoog PHP memory_limit indien nodig

## Monitoring Commands

```bash
# Status van alle migraties
drush migrate:status --group=thirdwing

# Gedetailleerde status
drush migrate:status thirdwing_node_artikel

# View error messages
drush migrate:messages thirdwing_node_artikel

# Reset stuck migrations
drush migrate:reset-status thirdwing_node_artikel
```

## Post-migratie taken

- [ ] URL redirects configureren (D6 URLs → D11 URLs)
- [ ] Search index rebuilden
- [ ] Cache clearen
- [ ] Permissions verifiëren
- [ ] Test forms en submissions
- [ ] Verifieer email notificaties
- [ ] Check broken links
- [ ] Test file downloads
- [ ] Controleer image styles
- [ ] Valideer RSS feeds indien van toepassing
