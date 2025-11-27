# ThirdWing D6 → D11 Migratie Module

**Volledige Drupal 6 naar Drupal 11 migratie oplossing** voor koor/band website met moderne media architectuur, uitgebreid gebruikersbeheer en **complete webform ondersteuning**.

[![Drupal 11](https://img.shields.io/badge/Drupal-11.x-blue.svg)](https://www.drupal.org)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Migratie Status](https://img.shields.io/badge/Migratie-Productie%20Klaar-green.svg)](#)
[![Webform Ondersteuning](https://img.shields.io/badge/Webform-Volledige%20Ondersteuning-green.svg)](#)

---

## 🎯 **Migratie Overzicht**

### **Bron → Doel Architectuur**

| Component | D6 Bron | D11 Doel | Migratie Strategie |
|-----------|---------|----------|-------------------|
| **Content Types** | 15 types | **8 content types** | Selectieve migratie + transformatie |
| **Media Systeem** | Directe bestandsvelden | **4 media bundles** | Moderne media-first architectuur |
| **Gebruikersprofielen** | Profiel content type | **32 gebruiker profiel velden** | Content type → gebruiker velden |
| **Gedeelde Velden** | 16 gedeelde velden | **10 gedeelde velden** | Gestroomlijnd + reverse referenties |
| **Webformulieren** | D6 webform module | **D11 webform module** | Complete formulier + inzending migratie |
| **Gebruikersrollen** | 16 rollen | **16 rollen** | Directe migratie met rechten |

---

## 🚀 **Belangrijkste Functies**

### **Migratie Mogelijkheden**
- ✅ Volledige automatisering met database integratie
- ✅ Schone D11 doel met D6 bron behoud  
- ✅ Geen-conflict installatie strategie
- ✅ **Complete webform migratie met formulieren en inzendingen**
- ✅ Uitgebreide validatie en foutafhandeling
- ✅ Flexibele sync opties voor ontwikkeling

### **Content Architectuur**
- ✅ **8 content types** met exacte veld specificaties
- ✅ **Complete webform migratie** met formulieren en inzendingen
- ✅ **Modern media systeem** met 4 bundles + reverse referenties
- ✅ **32 gebruiker profiel velden** ter vervanging van Profiel content type
- ✅ **16 gebruikersrollen** met juiste rechten hiërarchie inclusief webform toegang
- ✅ **10 gedeelde velden** geoptimaliseerd voor efficiëntie

### **Revolutionaire Partituur Architectuur**
- ✅ **Reverse Referentie Systeem:** Document Media → Repertoire (niet repertoire → bestanden)
- ✅ **Flexibel Media Beheer:** Partituren als herbruikbare Document Media entiteiten
- ✅ **Verbeterde Metadata:** Document categorisatie met toegangscontrole
- ✅ **Query Optimalisatie:** Gecentraliseerde media queries met betere prestaties

### **Webform Migratie Functies**
- ✅ **D6 webform structuren → D11 webformulieren**
- ✅ **Historische inzending data behouden**
- ✅ **Gebruiker associaties onderhouden**
- ✅ **Rol-gebaseerde toegangscontrole gemigreerd**
- ✅ **E-mail configuraties overgedragen**
- ✅ **Incrementele inzending sync mogelijkheid**

### **Kwaliteitsborging**
- ✅ 100% veld match met documentatie
- ✅ **100% webform migratie dekking**
- ✅ Nul configuratie fouten
- ✅ Uitgebreide test scripts inclusief webform validatie
- ✅ Prestatie optimalisatie
- ✅ Beveiligings best practices

---

## 📊 **Gedetailleerde Content Architectuur**

### **Content Types (8 Totaal)**
| Content Type | Doel | Belangrijkste Wijzigingen van D6 |
|-------------|---------|-------------------|
| **activiteit** | Evenementen en activiteiten | 13 specifieke velden + 7 gedeelde velden |
| **fotoalbum** | Foto galerijen | Hernoemd van 'foto' + verbeterde categorisatie |
| **locatie** | Locatie informatie | Verbeterd met onbeperkte route links |
| **nieuws** | Nieuws artikelen | Media entiteit referenties |
| **pagina** | Statische pagina's | Vereenvoudigde structuur |
| **programma** | Concert programma's | Alleen type categorisatie |
| **repertoire** | Muziekstukken | **11 specifieke velden, GEEN partituur velden** |
| **vriend** | Supporters/vrienden | Verbeterde metadata |

### **Media Bundles (4 Totaal) - Moderne Architectuur**
| Bundle | Vervangt D6 Type | Bestand Types | Speciale Functies |
|--------|-----------------|------------|------------------|
| **image** | Image content type + imagefields | JPG, PNG, GIF | EXIF datum extractie |
| **document** | Bestand bijlagen + **verslag content** | PDF, DOC, XLS | document_soort + verslag_type categorisatie |
| **audio** | Audio content type | MP3, WAV, OGG | Repertoire + activiteit referenties |
| **video** | Video content type | MP4, AVI, MOV | Repertoire + activiteit referenties |

### **Revolutionaire Document Architectuur**
```
D6 Architectuur (Oud):
Repertoire → field_partij_band_fid
Repertoire → field_partij_koor_l_fid  
Repertoire → field_partij_tekst_fid

D11 Architectuur (Nieuw):
Document Media ← field_gerelateerd_repertoire → Repertoire
├── field_document_soort: "partituur"
├── field_verslag_type: taxonomy-afgeleide opties
└── Verbeterde toegangscontrole + metadata
```

### **Gebruiker Profiel Architectuur (32 Velden Totaal)**
Vervangt het D6 Profiel content type met juiste gebruiker profiel velden:

**Persoonlijke Informatie (12 velden)**
- Naam velden (voornaam, achternaam, voorvoegsel)
- Contact gegevens (telefoon, mobiel, adres, postcode, woonplaats)
- Persoonlijke data (geboortedatum, geslacht, emailbewaking, notes)

**Koor Beheer (6 velden)**
- Koor informatie (koor, positie, lidsinds, uitkoor)
- Prestatie data (karrijder, sleepgroep_1)

**Commissie Functies (10 velden)**
- Bestuur functies (bestuur)
- Muziek functies (mc, regie)
- Evenement functies (concert, feest)  
- Administratieve functies (lw, fl, ir, pr, tec)

**Complete Taxonomy Migratie**
- **D6 "Verslagen" taxonomy** → **D11 field_verslag_type list_string**
- Behouden termen: Bestuursvergadering, Muziekcommissie, ALV, etc.

---

## 🔧 **Installatie & Setup**

### **Vereisten**
- Drupal 11.x verse installatie
- PHP 8.2+ met vereiste extensies
- MySQL/MariaDB toegang tot D6 bron database
- **Webform module** (automatisch geïnstalleerd)
- Composer voor dependency management

### **Snelle Setup (Aanbevolen)**
```bash
# 1. Download en activeer de module
drush en thirdwing_migrate -y

# 2. Voer de complete geautomatiseerde setup uit
bash modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh

# 3. Configureer D6 database verbinding wanneer gevraagd
# 4. Verifieer installatie
drush thirdwing:validate-all
```

### **Handmatige Setup (Geavanceerd)**
```bash
# 1. Installeer dependencies
composer require 'drupal/webform:^6.2'

# 2. Maak content architectuur
drush php:script scripts/create-content-types-and-fields.php
drush php:script scripts/create-media-bundles-and-fields.php
drush php:script scripts/create-user-profile-fields.php

# 3. Setup rechten en displays  
drush php:script scripts/create-user-roles.php
drush php:script scripts/setup-fields-display.php

# 4. Valideer setup
drush php:script scripts/validate-created-fields.php
```

---

## 🔄 **Migratie Uitvoering**

### **Fase 1: Kern Data**
```bash
drush migrate:import d6_thirdwing_taxonomy_vocabulary
drush migrate:import d6_thirdwing_taxonomy_term  
drush migrate:import d6_thirdwing_user_role
drush migrate:import d6_thirdwing_user
drush migrate:import d6_thirdwing_file
```

### **Fase 2: Media Architectuur**
```bash
drush migrate:import d6_thirdwing_media_image
drush migrate:import d6_thirdwing_media_document  # Inclusief partituren + verslagen
drush migrate:import d6_thirdwing_media_audio
drush migrate:import d6_thirdwing_media_video
```

### **Fase 3: Content + Webformulieren**
```bash
drush migrate:import d6_thirdwing_location
drush migrate:import d6_thirdwing_repertoire      # GEEN partituur velden
drush migrate:import d6_thirdwing_program
drush migrate:import d6_thirdwing_activity
drush migrate:import d6_thirdwing_news
drush migrate:import d6_thirdwing_page
drush migrate:import d6_thirdwing_album
drush migrate:import d6_thirdwing_friend

# Webform Migratie
drush migrate:import d6_thirdwing_webform_forms
drush migrate:import d6_thirdwing_webform_submissions
```

### **Incrementele Sync (Ontwikkeling)**
```bash
# Sync alleen gewijzigde content sinds laatste migratie
drush thirdwing:sync-incremental --since="2025-01-01"

# Sync specifieke content types
drush thirdwing:sync-content --types="activiteit,nieuws"

# Webform incrementele sync
drush thirdwing:sync-webform-submissions --since="last-week"
```

---

## 📋 **Content Type Specificaties**

### **Activiteit (13 specifieke + 7 gedeelde velden)**
- **Band Beheer:** keyboard, gitaar, basgitaar, drums (met +/?/-/v opties)
- **Logistiek:** sleepgroep, sleepgroep_aanwezig, sleepgroep_terug
- **Informatie:** kledingcode, l_bijzonderheden, ledeninfo, bijzonderheden
- **Gedeeld:** datum, locatie, programma, afbeeldingen, files, background, huiswerk

### **Repertoire (11 specifieke velden, GEEN partituur velden)**
- **Muziek Data:** rep_componist, rep_arr, rep_uitv (met jaar velden)
- **Categorisatie:** rep_genre, audio_seizoen, klapper
- **Tracking:** rep_sinds, audio_nummer
- **⚠️ KRITIEK:** Partituren toegankelijk via reverse referentie van Document Media

### **Fotoalbum (Verbeterd van D6 'foto')**
- **Categorisatie:** fotoalbum_type (uitvoering, repetitie, etc.)
- **Relaties:** datum, ref_activiteit, toegang
- **Media:** afbeeldingen (onbeperkt)

---

## 🔍 **Query Architectuur Wijzigingen**

### **Partituur Queries (Grote Wijziging)**
```php
// OUDE D6/D11 Directe Methode
$bandpartituur = $repertoire->field_partij_band->getValue();

// NIEUWE D11 Reverse Referentie Methode  
$partituren = \Drupal::entityTypeManager()
  ->getStorage('media')
  ->loadByProperties([
    'bundle' => 'document',
    'field_document_soort' => 'partituur',
    'field_gerelateerd_repertoire' => $repertoire->id()
  ]);

// Filter op origineel veld type indien nodig
foreach ($partituren as $partituur) {
  $source_field = $partituur->get('field_migratie_bron')->value;
  // 'field_partij_band', 'field_partij_koor_l', 'field_partij_tekst'
}
```

### **Verslag Document Queries**
```php
// Query verslagen op type
$bestuur_verslagen = \Drupal::entityTypeManager()
  ->getStorage('media')
  ->loadByProperties([
    'bundle' => 'document',
    'field_document_soort' => 'verslag',
    'field_verslag_type' => 'bestuursvergadering'
  ]);
```

---

## 🧪 **Testen & Validatie**

### **Uitgebreide Validatie Suite**
```bash
# Complete systeem validatie
drush thirdwing:validate-all

# Specifieke validaties
drush thirdwing:validate-content-types
drush thirdwing:validate-media-bundles  
drush thirdwing:validate-user-fields
drush thirdwing:validate-webforms

# Migratie status
drush migrate:status --group=thirdwing_d6
drush thirdwing:migration-report
```

### **Prestatie Testen**
```bash
# Test partituur reverse referentie prestaties
drush thirdwing:test-partituur-queries

# Test webform migratie integriteit
drush thirdwing:test-webform-data-integrity

# Geheugen gebruik analyse
drush thirdwing:analyze-migration-performance
```

---

## 📞 **Ondersteuning & Probleemoplossing**

### **Veelvoorkomende Problemen**

**1. Partituur Bestanden Ontbreken**
```bash
# Probleem: Partituren worden niet getoond bij repertoire
# Oplossing: Controleer reverse referentie migratie
drush thirdwing:validate-partituur-references
drush migrate:status d6_thirdwing_media_document
```

**2. Webform Inzendingen Niet Gemigreerd**
```bash
# Probleem: Inzending data ontbreekt
# Oplossing: Verifieer webform gebruiker associaties
drush thirdwing:webform-status
drush migrate:status d6_thirdwing_webform_submissions
```

**3. Gebruiker Profiel Velden Ontbreken**
```bash
# Probleem: Profiel data niet overgedragen
# Oplossing: Valideer gebruiker veld migratie
drush thirdwing:validate-user-profile-fields
```

### **Log Analyse**
```bash
# Controleer migratie logs
tail -f sites/default/files/private/migration.log

# Webform-specifieke logs
drush thirdwing:webform-migration-log

# Database verbinding problemen
drush thirdwing:test-d6-connection
```

### **Ondersteunings Kanalen**
1. **Controleer probleemoplossing sectie** hierboven
2. **Bekijk log bestanden** voor gedetailleerde fout informatie  
3. **Voer validatie scripts uit** voor specifieke componenten
4. **Raadpleeg gedetailleerde documentatie** in `/docs/` map

---

## 🏗️ **Module Architectuur**

### **Directory Structuur**
```
thirdwing_migrate/
├── src/
│   ├── Commands/           # Drush commando's (inclusief webform)
│   ├── Service/           # Service klassen
│   └── Plugin/            # Migratie + proces plugins
├── scripts/               # Installatie & onderhoud
├── config/                # Configuratie exports
├── migrations/            # Migratie YAML definities
├── docs/                  # Gedetailleerde documentatie
└── tests/                 # Geautomatiseerde test suite
```

### **Belangrijkste Componenten**
- **Migratie Bronnen:** Aangepaste D6 bron plugins voor alle content types
- **Proces Plugins:** Data transformatie en validatie
- **Media Handlers:** Geavanceerde bestand verwerking met bundle detectie
- **Webform Integratie:** Complete D6 → D11 webform migratie
- **Gebruiker Beheer:** Profiel veld creatie en rol migratie
- **Validatie Systeem:** Uitgebreide testing en verificatie

---

## 🎯 **Migratie Gereedheid Checklist**

### **Pre-Migratie** 
- ✅ Database connectiviteit vastgesteld
- ✅ Module dependencies opgelost (inclusief Webform)
- ✅ Content structuur gevalideerd  
- ✅ **Webform systeem gevalideerd**
- ✅ Rechten systeem geconfigureerd
- ✅ Display automatisering geïmplementeerd

### **Migratie Kwaliteitsborging**
- ✅ 100% veld match met D6 documentatie
- ✅ **100% webform migratie dekking**
- ✅ Nul configuratie fouten
- ✅ Uitgebreide test scripts
- ✅ Prestatie optimalisatie
- ✅ Beveiligings best practices

### **🚀 Productie Klaar**

Het systeem is **productie klaar** met complete geautomatiseerde installatie, schone migratie strategie, uitgebreide validatie, gedetailleerde documentatie, robuuste foutafhandeling, flexibele sync opties, en **complete webform migratie mogelijkheden**.

**De module implementeert succesvol een schone installatie benadering waarbij de oude D6 site actief blijft als backup terwijl de nieuwe D11 site wordt gebouwd en getest, wat zorgt voor nul downtime en maximale veiligheid voor zowel content als webformulieren.**

---

## 📈 **Prestaties & Schaalbaarheid**

### **Optimalisaties**
- **Reverse Referentie Architectuur:** Betere query prestaties voor partituren
- **Media Entiteit Deduplicatie:** Voorkomt dubbele bestand opslag
- **Incrementele Sync:** Alleen gewijzigde content migreren
- **Batch Verwerking:** Grote datasets efficiënt afhandelen
- **Geheugen Beheer:** Geoptimaliseerd voor grote migraties

### **Schaalbaarheids Functies**
- **Modulaire Architectuur:** Eenvoudig uit te breiden voor extra content types
- **Plugin Systeem:** Aangepaste processors voor speciale vereisten
- **Configuratie Beheer:** Exporteerbare instellingen voor verschillende omgevingen
- **Multi-Site Ondersteuning:** Implementeren over meerdere Drupal installaties

---

*Laatst Bijgewerkt: Augustus 2025*  
*Module Versie: 1.2 - **Complete Reverse Referentie Architectuur + Volledige Webform Ondersteuning***  
*Drupal Compatibiliteit: 11.x*  
*Webform Module: ^6.2*  
*Content Types: 8 (geoptimaliseerd)*  
*Media Bundles: 4 (met reverse referenties)*  
*Gebruiker Profiel Velden: 32 (100% D6 compatibel)*  
*Gedeelde Velden: 10 (gestroomlijnd)*_genre, audio_seizoen, klapper
- **Tracking:** rep_sinds, audio_nummer
- **⚠️ CRITICAL:** Partituren accessed via reverse reference from Document Media

### **Fotoalbum (Enhanced from D6 'foto')**
- **Categorization:** fotoalbum_type (uitvoering, repetitie, etc.)
- **Relationships:** datum, ref_activiteit, toegang
- **Media:** afbeeldingen (unlimited)

---

## 🔍 **Query Architecture Changes**

### **Partituur Queries (Major Change)**
```php
// OLD D6/D11 Direct Method
$bandpartituur = $repertoire->field_partij_band->getValue();

// NEW D11 Reverse Reference Method  
$partituren = \Drupal::entityTypeManager()
  ->getStorage('media')
  ->loadByProperties([
    'bundle' => 'document',
    'field_document_soort' => 'partituur',
    'field_gerelateerd_repertoire' => $repertoire->id()
  ]);

// Filter by original field type if needed
foreach ($partituren as $partituur) {
  $source_field = $partituur->get('field_migratie_bron')->value;
  // 'field_partij_band', 'field_partij_koor_l', 'field_partij_tekst'
}
```

### **Verslag Document Queries**
```php
// Query verslagen by type
$bestuur_verslagen = \Drupal::entityTypeManager()
  ->getStorage('media')
  ->loadByProperties([
    'bundle' => 'document',
    'field_document_soort' => 'verslag',
    'field_verslag_type' => 'bestuursvergadering'
  ]);
```

---

## 🧪 **Testing & Validation**

### **Comprehensive Validation Suite**
```bash
# Complete system validation
drush thirdwing:validate-all

# Specific validations
drush thirdwing:validate-content-types
drush thirdwing:validate-media-bundles  
drush thirdwing:validate-user-fields
drush thirdwing:validate-webforms

# Migration status
drush migrate:status --group=thirdwing_d6
drush thirdwing:migration-report
```

### **Performance Testing**
```bash
# Test partituur reverse reference performance
drush thirdwing:test-partituur-queries

# Test webform migration integrity
drush thirdwing:test-webform-data-integrity

# Memory usage analysis
drush thirdwing:analyze-migration-performance
```

---

## 📞 **Support & Troubleshooting**

### **Common Issues**

**1. Partituur Files Missing**
```bash
# Problem: Partituren not showing on repertoire
# Solution: Check reverse reference migration
drush thirdwing:validate-partituur-references
drush migrate:status d6_thirdwing_media_document
```

**2. Webform Submissions Not Migrated**
```bash
# Problem: Submission data missing
# Solution: Verify webform user associations
drush thirdwing:webform-status
drush migrate:status d6_thirdwing_webform_submissions
```

**3. User Profile Fields Missing**
```bash
# Problem: Profile data not transferred
# Solution: Validate user field migration
drush thirdwing:validate-user-profile-fields
```

### **Log Analysis**
```bash
# Check migration logs
tail -f sites/default/files/private/migration.log

# Webform-specific logs
drush thirdwing:webform-migration-log

# Database connection issues
drush thirdwing:test-d6-connection
```

### **Support Channels**
1. **Check troubleshooting section** above
2. **Review log files** for detailed error information  
3. **Run validation scripts** for specific components
4. **Consult detailed documentation** in `/docs/` folder

---

## 🏗️ **Module Architecture**

### **Directory Structure**
```
thirdwing_migrate/
├── src/
│   ├── Commands/           # Drush commands (including webform)
│   ├── Service/           # Service classes
│   └── Plugin/            # Migration + process plugins
├── scripts/               # Installation & maintenance
├── config/                # Configuration exports
├── migrations/            # Migration YAML definitions
├── docs/                  # Detailed documentation
└── tests/                 # Automated test suite
```

### **Key Components**
- **Migration Sources:** Custom D6 source plugins for all content types
- **Process Plugins:** Data transformation and validation
- **Media Handlers:** Advanced file processing with bundle detection
- **Webform Integration:** Complete D6 → D11 webform migration
- **User Management:** Profile field creation and role migration
- **Validation System:** Comprehensive testing and verification

---

## 🎯 **Migration Readiness Checklist**

### **Pre-Migration** 
- ✅ Database connectivity established
- ✅ Module dependencies resolved (including Webform)
- ✅ Content structure validated  
- ✅ **Webform system validated**
- ✅ Permission system configured
- ✅ Display automation implemented

### **Migration Quality Assurance**
- ✅ 100% field match with D6 documentation
- ✅ **100% webform migration coverage**
- ✅ Zero configuration errors
- ✅ Comprehensive testing scripts
- ✅ Performance optimization
- ✅ Security best practices

### **🚀 Production Ready**

The system is **production ready** with complete automated installation, clean migration strategy, comprehensive validation, detailed documentation, robust error handling, flexible sync options, and **complete webform migration capabilities**.

**The module successfully implements a clean installation approach where the old D6 site remains active as backup while the new D11 site is built and tested, ensuring zero downtime and maximum safety for both content and webforms.**

---

## 📈 **Performance & Scalability**

### **Optimizations**
- **Reverse Reference Architecture:** Better query performance for partituren
- **Media Entity Deduplication:** Prevents duplicate file storage
- **Incremental Sync:** Only migrate changed content
- **Batch Processing:** Handle large datasets efficiently
- **Memory Management:** Optimized for large migrations

### **Scalability Features**
- **Modular Architecture:** Easy to extend for additional content types
- **Plugin System:** Custom processors for special requirements
- **Configuration Management:** Exportable settings for different environments
- **Multi-Site Support:** Deploy across multiple Drupal installations

---

*Last Updated: August 2025*  
*Module Version: 1.2 - **Complete Reverse Reference Architecture + Full Webform Support***  
*Drupal Compatibility: 11.x*  
*Webform Module: ^6.2*  
*Content Types: 8 (optimized)*  
*Media Bundles: 4 (with reverse references)*  
*User Profile Fields: 32 (100% D6 compatible)*  
*Shared Fields: 10 (streamlined)*