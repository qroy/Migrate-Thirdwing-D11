# ThirdWing Migrate - Quick Reference

## 🚀 Snelstart Commando's

```bash
# Installatie
drush en migrate migrate_drupal migrate_plus migrate_tools thirdwing_migrate -y

# Overzicht van alle migraties
drush thirdwing:overview
# of:
drush tw-overview

# Volledige migratie uitvoeren
./migrate.sh
# of:
drush thirdwing:migrate
drush tw-migrate

# Rollback alles
./rollback.sh
```

## 📊 Status & Monitoring

```bash
# Status van alle ThirdWing migraties
drush migrate:status --group=thirdwing

# Status van specifieke migratie
drush migrate:status thirdwing_node_artikel

# Zie foutmeldingen
drush migrate:messages thirdwing_node_artikel

# Reset stuck migration
drush migrate:reset-status thirdwing_node_artikel
```

## ▶️ Individuele Migraties

```bash
# Importeer met feedback
drush migrate:import thirdwing_node_artikel --feedback="100 items"

# Importeer met update mode (hermigratie)
drush migrate:import thirdwing_node_artikel --update

# Test met limited aantal
drush migrate:import thirdwing_node_artikel --limit=10

# Specifieke items
drush migrate:import thirdwing_node_artikel --idlist=1,2,3

# Skip naar specifiek ID
drush migrate:import thirdwing_node_artikel --skip-progress-bar --feedback=1 --idlist=100:200
```

## ⏮️ Rollback

```bash
# Rollback specifieke migratie
drush migrate:rollback thirdwing_node_artikel

# Rollback alles
./rollback.sh
```

## 🔍 Debugging

```bash
# Uitgebreide info tijdens import
drush migrate:import thirdwing_node_artikel --feedback=1 -vvv

# Check source data
drush sqlq "SELECT nid, title FROM node WHERE type='artikel' LIMIT 5" --database=drupal_6

# Check destination data
drush sqlq "SELECT nid, title FROM node_field_data WHERE type='artikel' LIMIT 5"

# Clear cache
drush cr
```

## 🔧 Handige Queries

```bash
# Hoeveel nodes van elk type in D6?
drush sqlq "SELECT type, COUNT(*) as count FROM node GROUP BY type" --database=drupal_6

# Hoeveel users in D6?
drush sqlq "SELECT COUNT(*) FROM users WHERE uid > 0" --database=drupal_6

# Check taxonomy terms
drush sqlq "SELECT tid, name FROM term_data WHERE vid=1" --database=drupal_6

# Check files
drush sqlq "SELECT COUNT(*) FROM files" --database=drupal_6
```

## 📝 Configuratie Wijzigen

```bash
# Edit migratie configuratie
vi modules/custom/thirdwing_migrate/migrations/thirdwing_node_artikel.yml

# Herlaad configuratie
drush cr

# Of re-import config
drush config:import --partial --source=modules/custom/thirdwing_migrate/config/install
```

## 🎯 Test Workflow

```bash
# 1. Test met kleine dataset
drush migrate:import thirdwing_user --limit=5
drush migrate:import thirdwing_node_artikel --limit=3

# 2. Controleer in UI
# Browse naar je D11 site en check content

# 3. Bekijk errors
drush migrate:messages thirdwing_node_artikel

# 4. Rollback als nodig
drush migrate:rollback thirdwing_node_artikel

# 5. Fix configuratie en probeer opnieuw
vi migrations/thirdwing_node_artikel.yml
drush cr
drush migrate:import thirdwing_node_artikel --limit=3
```

## 🔄 Regelmatige Hermigratie Setup

```bash
# Maak cron script: /etc/cron.daily/thirdwing-migrate
#!/bin/bash
cd /path/to/drupal
drush migrate:import thirdwing_node_artikel --update
drush migrate:import thirdwing_node_document --update
# ... andere content types
```

## 📈 Performance

```bash
# Verhoog PHP memory
php -d memory_limit=512M vendor/bin/drush migrate:import thirdwing_node_artikel

# Parallel uitvoeren (voorzichtig!)
drush migrate:import thirdwing_node_artikel &
drush migrate:import thirdwing_node_document &
wait
```

## 🚨 Troubleshooting

```bash
# Database connectie check
drush sqlq "SELECT 1" --database=drupal_6

# Module enabled?
drush pm:list --filter=thirdwing

# Configuration imported?
drush config:status | grep migrate

# Clear all caches
drush cr

# Rebuild cache en registry
drush cache:rebuild
```

## 💾 Backup voor Migratie

```bash
# Backup D11 database
drush sql:dump > backup-before-migration-$(date +%Y%m%d-%H%M%S).sql

# Backup files
tar -czf files-backup-$(date +%Y%m%d).tar.gz sites/default/files/
```

## 📋 Volgorde van Migraties

**Altijd deze volgorde aanhouden:**

1. User Roles (`d6_user_role`)
2. Taxonomieën (`thirdwing_taxonomy_*`)
3. Users (`thirdwing_user`)
4. Files (`thirdwing_file`)
5. Media (`thirdwing_media_*`)
6. Content (`thirdwing_node_*`)

## 🔗 Nuttige Links

- Drupal Migrate API: https://www.drupal.org/docs/drupal-apis/migrate-api
- Process Plugins: https://www.drupal.org/docs/drupal-apis/migrate-api/migrate-process-plugins
- Migrate Tools: https://www.drupal.org/project/migrate_tools
- Migrate Plus: https://www.drupal.org/project/migrate_plus

## 🆘 Hulp Nodig?

```bash
# Drush help voor migrate commands
drush help migrate:import
drush help migrate:status
drush help migrate:rollback

# Custom command help
drush help thirdwing:overview
```
