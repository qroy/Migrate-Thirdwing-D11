# ThirdWing Migrate Module

Migratiemodule voor het migreren van content van ThirdWing Drupal 6 naar Drupal 11.

## Vereisten

- Drupal 11
- Migrate Plus module (`composer require drupal/migrate_plus`)
- Migrate Tools module (`composer require drupal/migrate_tools`)
- Migrate Upgrade module (core)
- Toegang tot de Drupal 6 database

## Installatie

1. **Database configuratie**
   
   Voeg de D6 database connectie toe aan je `settings.php`:
   
   ```php
   $databases['drupal_6']['default'] = [
     'database' => 'thirdwing_d6',
     'username' => 'your_username',
     'password' => 'your_password',
     'host' => 'localhost',
     'port' => '3306',
     'driver' => 'mysql',
     'prefix' => '',
     'collation' => 'utf8mb4_general_ci',
   ];
   ```

2. **Module installatie**
   
   ```bash
   # Kopieer de module naar je Drupal installatie
   cp -r thirdwing_migrate /path/to/drupal/modules/custom/
   
   # Installeer de benodigde modules
   drush en migrate migrate_drupal migrate_plus migrate_tools thirdwing_migrate -y
   ```

3. **Files directory**
   
   Zorg dat het D6 files directory toegankelijk is voor D11, of configureer file_copy plugin.

## Migratie volgorde

De migraties moeten in de volgende volgorde uitgevoerd worden:

```bash
# 1. User roles (core migratie)
drush migrate:import d6_user_role

# 2. Taxonomieën
drush migrate:import thirdwing_taxonomy_toegang

# 3. Users
drush migrate:import thirdwing_user

# 4. Files
drush migrate:import thirdwing_file

# 5. Media
drush migrate:import thirdwing_media_image
drush migrate:import thirdwing_media_document
# TODO: thirdwing_media_audio
# TODO: thirdwing_media_video

# 6. Content
drush migrate:import thirdwing_node_artikel
# TODO: andere content types
```

## Hermigratie (D6 blijft source of truth)

Omdat de D6 site live blijft tijdens migratie, moet je regelmatig hermigreren:

```bash
# Reset en importeer opnieuw
drush migrate:rollback thirdwing_node_artikel
drush migrate:import thirdwing_node_artikel

# Of gebruik --update om alleen gewijzigde content te updaten
drush migrate:import thirdwing_node_artikel --update
```

## Status controleren

```bash
# Bekijk status van alle migraties
drush migrate:status --group=thirdwing

# Bekijk details van een specifieke migratie
drush migrate:status thirdwing_node_artikel

# Bekijk messages/fouten
drush migrate:messages thirdwing_node_artikel
```

## Structuur

```
thirdwing_migrate/
├── config/install/
│   └── migrate_plus.migration_group.thirdwing.yml
├── migrations/
│   ├── thirdwing_taxonomy_toegang.yml
│   ├── thirdwing_user.yml
│   ├── thirdwing_file.yml
│   ├── thirdwing_media_image.yml
│   ├── thirdwing_media_document.yml
│   └── thirdwing_node_artikel.yml
├── thirdwing_migrate.info.yml
└── README.md
```

## TODO

- [ ] Media Audio migratie
- [ ] Media Video migratie
- [ ] Alle overige content types (Agenda, Document, Pagina, Podium, Werkgroep, Bijdrage, Album)
- [ ] Alle overige taxonomieën
- [ ] Custom process plugins indien nodig
- [ ] Field mapping verfijnen op basis van content audit
- [ ] File paths configureren
- [ ] URL redirects configureren

## Custom Process Plugins

Als je custom logica nodig hebt (bijvoorbeeld voor complexe field transformaties), 
kun je custom process plugins maken in `src/Plugin/migrate/process/`.

## Debugging

```bash
# Verbose mode
drush migrate:import thirdwing_node_artikel --feedback="10 items"

# Limit voor testen
drush migrate:import thirdwing_node_artikel --limit=5

# ID range voor specifieke items
drush migrate:import thirdwing_node_artikel --idlist=1,2,3
```

## Notes

- De D6 site blijft de "single source of truth"
- Elke migratie run overschrijft D11 content
- Overwrite_properties zorgt ervoor dat updates worden toegepast
- Test eerst met een kleine subset van data
