# ThirdWing.nl Content Types Analysis - Complete Documentation

**Database:** thirdwing_nl  
**CMS:** Drupal 6 (based on table structure)  
**Total Content Types:** 15  
**Total Shared Fields:** 16  

## Content Types Overview

### 1. **ACTIVITEIT** (Activity)
- **Display Name:** Activiteit
- **Description:** Een activiteit (uitvoering, repetitie)
- **Module:** node
- **Title Label:** Omschrijving
- **Has Body:** Yes (Berichttekst)

**Content Type Specific Fields:**
- `field_tijd_aanwezig_value`: "Koor Aanwezig" (text_textfield)
- `field_keyboard_value`: "Toetsenist" (optionwidgets_select)
  - **Options:** +, ?, -, v
- `field_gitaar_value`: "Gitarist" (optionwidgets_select)
  - **Options:** +, ?, -, v
- `field_basgitaar_value`: "Basgitarist" (optionwidgets_select)
  - **Options:** +, ?, -, v
- `field_drums_value`: "Drummer" (optionwidgets_select)
  - **Options:** +, ?, -, v
- `field_vervoer_value`: "Karrijder" (text_textfield)
- `field_sleepgroep_value`: "Sleepgroep" (optionwidgets_select)
  - **Options:** I, II, III, IV, V, *
- `field_sleepgroep_aanwezig_value`: "Sleepgroep Aanwezig" (text_textfield)
- `field_kledingcode_value`: "Kledingcode" (text_textfield)
- `field_locatie_nid`: "Locatie" (nodereference_select)
- `field_l_bijzonderheden_value`: "Bijzonderheden locatie" (text_textarea)
- `field_ledeninfo_value`: "Informatie voor leden" (text_textarea)
- `field_ledeninfo_format`: "Informatie voor leden" (text_textarea)
- `field_bijzonderheden_value`: "Bijzonderheden" (text_textfield)
- `field_background_fid`: "Achtergrond" (imagefield_widget)
- `field_background_list`: "Achtergrond" (imagefield_widget)
- `field_background_data`: "Achtergrond" (imagefield_widget)
- `field_sleepgroep_terug_value`: "Sleepgroep terug" (optionwidgets_select)
  - **Options:** I, II, III, IV, V, *
- `field_huiswerk_fid`: "Huiswerk" (filefield_widget)
- `field_huiswerk_list`: "Huiswerk" (filefield_widget)
- `field_huiswerk_data`: "Huiswerk" (filefield_widget)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_programma2`: "Programma" (nodereference_autocomplete)
- `field_datum`: "Datum en tijd" (date_popup)

### 2. **AUDIO** (Audio)
- **Display Name:** Audio
- **Description:** Geluidsbestanden
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_mp3_fid`: "mp3" (filefield_widget)
- `field_mp3_list`: "mp3" (filefield_widget)
- `field_mp3_data`: "mp3" (filefield_widget)
- `field_audio_bijz_value`: "Bijzonderheden" (text_textfield)

**Shared Fields Used:**
- `field_repertoire`: "Nummer" (nodereference_select)
- `field_audio_uitvoerende`: "Uitvoerende" (text_textfield)
- `field_audio_type`: "Type" (optionwidgets_select)
  - **Options:** Uitvoering, Repetitie, Oefenbestand, Origineel, Uitzending, Overig
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 3. **FOTO** (Photo)
- **Display Name:** Foto
- **Description:** Foto-album
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Omschrijving)

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_view`: "Extra inhoud" (viewfield_select)

### 4. **IMAGE** (Image)
- **Display Name:** Image
- **Description:** Image content type
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_exif_datetimeoriginal_value`: "EXIF Date" (date_text)
- `field_bijschrift_value`: "Bijschrift" (text_textfield)

**Shared Fields Used:** None

### 5. **LOCATIE** (Location)
- **Display Name:** Locatie
- **Description:** Veelvoorkomende locaties van uitvoeringen.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_l_adres_value`: "Adres" (text_textfield)
- `field_l_plaats_value`: "Plaats" (text_textfield)
- `field_l_postcode_value`: "Postcode" (text_textfield)

**Shared Fields Used:**
- `field_l_routelink`: "Route" (link)

### 6. **NIEUWS** (News)
- **Display Name:** Nieuws
- **Description:** Een nieuwsbericht. Dit kan een publiek nieuwsbericht zijn, maar ook een nieuwsbericht voor op de ledenpagina.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Berichttekst)

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 7. **NIEUWSBRIEF** (Newsletter)
- **Display Name:** Nieuwsbrief
- **Description:** Nieuwsbriefuitgave te zenden naar de ingeschreven e-mailadressen.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Introtekst)

**Content Type Specific Fields:**
- `field_jaargang_value`: "Jaargang" (number)
- `field_uitgave_value`: "Uitgave" (number)

**Shared Fields Used:**
- `field_inhoud`: "Inhoud" (nodereference_autocomplete)

### 8. **PAGINA** (Page)
- **Display Name:** Pagina
- **Description:** Gebruik een 'Pagina' wanneer je een statische pagina wilt toevoegen.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Berichttekst)

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_view`: "Extra inhoud" (viewfield_select)

### 9. **PROFIEL** (Profile)
- **Display Name:** Profiel
- **Description:** Een gebruikersprofiel.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_voornaam_value`: "Voornaam" (text_textfield)
- `field_achternaam_voorvoegsel_value`: "Tussenvoegsel" (text_textfield)
- `field_achternaam_value`: "Achternaam" (text_textfield)
- `field_geboortedatum_value`: "Geboortedatum" (date_select)
- `field_geslacht_value`: "Geslacht" (optionwidgets_select)
  - **Options:** m|Man, v|Vrouw
- `field_adres_value`: "Adres" (text_textfield)
- `field_postcode_value`: "Postcode" (text_textfield)
- `field_telefoon_value`: "Telefoon" (text_textfield)
- `field_mobiel_value`: "Mobiel" (text_textfield)
- `field_lidsinds_value`: "Lid sinds" (date_select)
- `field_uitkoor_value`: "Uit koor" (date_select)
- `field_koor_value`: "Koor" (optionwidgets_select)
  - **Options:** B|Bas, A|Tenor, E|Alt, C|1e Sopraan, D|2e Sopraan, Y1|Dirigent, Z1|Toetsenist, Z2|Gitarist, Z3|Bassist, Z4|Drummer, Z5|Techniek en percussie
- `field_positie_value`: "Positie" (optionwidgets_select)
  - **Options:** 
    - 101|4x01 through 116|4x16 (Row 4)
    - 201|3x01 through 216|3x16 (Row 3)
    - 301|2x01 through 316|2x16 (Row 2)
    - 401|1x01 through 416|1x16 (Row 1)
    - 501|Band 1 through 504|Band 4
    - 601|Dirigent
    - 701|Niet ingedeeld
- `field_karrijder_value`: "Karrijder" (optionwidgets_select)
  - **Options:** 0, *|Karrijder
- `field_sleepgroep_1_value`: "Sleepgroep" (optionwidgets_select)
  - **Options:** 1|I, 2|II, 3|III, 4|IV, 5|V, 8|OG, 9|-
- `field_functie_bestuur_value`: "Functie Bestuur" (optionwidgets_select)
  - **Options:** 1|Voorzitter, 2|Secretaris, 3|Penningmeester, 4|Bestuurslid
- `field_functie_mc_value`: "Functie Muziekcommissie" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Voorzitter, 20|Secretaris, 30|Dirigent, 40|Contactpersoon band, 90|Lid
- `field_functie_concert_value`: "Functie Commissie Concerten" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid
- `field_functie_feest_value`: "Functie Feestcommissie" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid
- `field_functie_fl_value`: "Functie Faciliteiten" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid
- `field_functie_ir_value`: "Functie Commissie Interne Relaties" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid, 11|Interne relaties
- `field_functie_lw_value`: "Functie Ledenadministratie" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid
- `field_functie_pr_value`: "Functie Commissie PR" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 8|Website, 9|Social Media, 10|Lid
- `field_functie_regie_value`: "Functie Commissie Koorregie" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid
- `field_functie_tec_value`: "Functie Technische Commissie" (optionwidgets_select)
  - **Options:** 1|Bestuurslid, 10|Lid

**Shared Fields Used:**
- `field_woonplaats`: "Woonplaats" (text_textfield)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)

### 10. **PROGRAMMA** (Program)
- **Display Name:** Programma
- **Description:** Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_prog_type_value`: "Type" (optionwidgets_select)
  - **Options:** programma|Programma onderdeel, nummer|Nummer

**Shared Fields Used:** None

### 11. **REPERTOIRE** (Repertoire)
- **Display Name:** Repertoire
- **Description:** Een nummer in het repertoire.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_klapper_value`: "Actueel" (optionwidgets_buttons)
  - **Options:** Nee, Ja
- `field_audio_nummer_value`: "Nummer" (number)
- `field_audio_seizoen_value`: "Seizoen" (optionwidgets_select)
  - **Options:** Regulier|Regulier, Kerst|Kerst
- `field_rep_genre_value`: "Genre" (optionwidgets_select)
  - **Options:** Pop, Musical / Film, Geestelijk / Gospel
- `field_rep_uitv_jaar_value`: "Jaar uitvoering" (number)
- `field_rep_uitv_value`: "Uitvoerende" (text_textfield)
- `field_rep_componist_value`: "Componist" (text_textfield)
- `field_rep_componist_jaar_value`: "Jaar compositie" (number)
- `field_rep_arr_value`: "Arrangeur" (text_textfield)
- `field_rep_arr_jaar_value`: "Jaar arrangement" (number)
- `field_rep_sinds_value`: "In repertoire sinds" (number)

**Shared Fields Used:**
- `field_partij_tekst`: "Tekst / koorregie" (filefield_widget)
- `field_partij_koor_l`: "Koorpartituur" (filefield_widget)
- `field_partij_band`: "Bandpartituur" (filefield_widget)

### 12. **VERSLAG** (Report)
- **Display Name:** Verslag
- **Description:** Verslagen van vergaderingen.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:**
- `field_datum`: "Datum" (date_popup)
- `field_files`: "Bestandsbijlages" (filefield_widget)

### 13. **VIDEO** (Video)
- **Display Name:** Video
- **Description:** Een embedded video van YouTube
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:**
- `field_video`: "Video" (emvideo_textfields)
- `field_repertoire`: "Nummer" (nodereference_select)
- `field_audio_uitvoerende`: "Uitvoerende" (text_textfield)
- `field_audio_type`: "Type" (optionwidgets_select)
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 14. **VRIEND** (Friend)
- **Display Name:** Vriendenvermelding
- **Description:** Een vriendenvermelding voor op de vrienden overzichtspagina.
- **Module:** node
- **Title Label:** Naam
- **Has Body:** No

**Content Type Specific Fields:**
- `field_website_url`: "Website" (link)
- `field_website_title`: "Website" (link)
- `field_website_attributes`: "Website" (link)
- `field_vriend_soort_value`: "Soort" (optionwidgets_select)
  - **Options:** financieel, niet-financieel, materieel
- `field_vriend_benaming_value`: "Benaming" (optionwidgets_select)
  - **Options:** vriend, vriendin, vrienden
- `field_vriend_tot_value`: "Vriend t/m" (optionwidgets_select)
  - **Options:** 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020
- `field_vriend_vanaf_value`: "Vriend vanaf" (optionwidgets_select)
  - **Options:** 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020
- `field_vriend_lengte_value`: "Vriendlengte" (optionwidgets_select)
  - **Options:** 1|0 t/m 1 jaar, 2|2 t/m 5 jaar, 3|6 jaar en langer

**Shared Fields Used:**
- `field_woonplaats`: "Woonplaats" (text_textfield)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)

### 15. **WEBFORM** (Webform)
- **Display Name:** Webformulier
- **Description:** Create a new form or questionnaire accessible to users. Submission results and statistics are recorded and accessible to privileged users.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Berichttekst)

**Content Type Specific Fields:** None (uses only standard node fields - no custom CCK table)

**Shared Fields Used:** None

## Shared Fields Available to All Content Types

The following fields are available as shared fields that can be attached to any content type:

### 1. **field_afbeeldingen** (Images)
- **Label:** "Afbeeldingen" (imagefield_widget)
- **Database Fields:**
  - `field_afbeeldingen_fid` (int(11)) - File ID
  - `field_afbeeldingen_list` (tinyint(4)) - List flag
  - `field_afbeeldingen_data` (text) - Image data

### 2. **field_audio_type** (Audio Type)
- **Label:** "Type" (optionwidgets_select)
- **Options:** Uitvoering, Repetitie, Oefenbestand, Origineel, Uitzending, Overig
- **Database Fields:**
  - `field_audio_type_value` (longtext) - Audio type value

### 3. **field_audio_uitvoerende** (Audio Performer)
- **Label:** "Uitvoerende" (text_textfield)
- **Database Fields:**
  - `field_audio_uitvoerende_value` (longtext) - Audio performer value

### 4. **field_datum** (Date)
- **Label:** "Datum en tijd" (date_popup)
- **Database Fields:**
  - `field_datum_value` (varchar(20)) - Date value

### 5. **field_files** (Files)
- **Label:** "Bestandsbijlages" (filefield_widget)
- **Database Fields:**
  - `field_files_fid` (int(11)) - File ID
  - `field_files_list` (tinyint(4)) - List flag
  - `field_files_data` (text) - File data

### 6. **field_inhoud** (Content)
- **Label:** "Inhoud" (nodereference_autocomplete)
- **Database Fields:**
  - `field_inhoud_nid` (int(10)) - Content node ID reference

### 7. **field_l_routelink** (Route Link)
- **Label:** "Route" (link)
- **Database Fields:**
  - `field_l_routelink_url` (varchar(2048)) - URL
  - `field_l_routelink_title` (varchar(255)) - Link title
  - `field_l_routelink_attributes` (mediumtext) - Link attributes

### 8. **field_partij_band** (Band Sheet Music)
- **Label:** "Bandpartituur" (filefield_widget)
- **Database Fields:**
  - `field_partij_band_fid` (int(11)) - File ID
  - `field_partij_band_list` (tinyint(4)) - List flag
  - `field_partij_band_data` (text) - Sheet music data

### 9. **field_partij_koor_l** (Choir Sheet Music)
- **Label:** "Koorpartituur" (filefield_widget)
- **Database Fields:**
  - `field_partij_koor_l_fid` (int(11)) - File ID
  - `field_partij_koor_l_list` (tinyint(4)) - List flag
  - `field_partij_koor_l_data` (text) - Sheet music data

### 10. **field_partij_tekst** (Text/Choir Direction)
- **Label:** "Tekst / koorregie" (filefield_widget)
- **Database Fields:**
  - `field_partij_tekst_fid` (int(11)) - File ID
  - `field_partij_tekst_list` (tinyint(4)) - List flag
  - `field_partij_tekst_data` (text) - Text sheet music data

### 11. **field_programma2** (Program)
- **Label:** "Programma" (nodereference_autocomplete)
- **Database Fields:**
  - `field_programma2_nid` (int(10)) - Program node ID reference

### 12. **field_ref_activiteit** (Activity Reference)
- **Label:** "Activiteit" (nodereference_autocomplete)
- **Database Fields:**
  - `field_ref_activiteit_nid` (int(10)) - Activity node ID reference

### 13. **field_repertoire** (Repertoire)
- **Label:** "Nummer" (nodereference_select)
- **Database Fields:**
  - `field_repertoire_nid` (int(10)) - Repertoire node ID reference

### 14. **field_video** (Video)
- **Label:** "Video" (emvideo_textfields)
- **Database Fields:**
  - `field_video_embed` (longtext) - Video embed code
  - `field_video_value` (text) - Video URL/identifier
  - `field_video_provider` (text) - Video provider
  - `field_video_data` (longtext) - Video metadata

### 15. **field_view** (View)
- **Label:** "Extra inhoud" (viewfield_select)
- **Database Fields:**
  - `field_view_vname` (varchar(128)) - View name
  - `field_view_vargs` (varchar(255)) - View arguments

### 16. **field_woonplaats** (Residence)
- **Label:** "Woonplaats" (text_textfield)
- **Database Fields:**
  - `field_woonplaats_value` (longtext) - Residence value

## Widget Types and Options Summary

### Select Widgets (optionwidgets_select)
**Band/Instrument Fields:**
- All use options: `+` (yes), `?` (maybe), `-` (no), `v` (substitute)

**Commissie Functions:**
- Most use: `1|Bestuurslid`, `10|Lid`
- Special cases:
  - **MC**: Additional options for Voorzitter, Secretaris, Dirigent, Contactpersoon band
  - **PR**: Additional options for Website, Social Media
  - **IR**: Additional option for Interne relaties

**Other Select Fields:**
- **Sleepgroep**: Roman numerals I-V and wildcard *
- **Koor Position**: Extensive list of choir positions and band roles
- **Audio Type**: 6 different recording/performance types
- **Vriend Fields**: Categories for friend types, duration, and years

### Button Widgets (optionwidgets_buttons)
- **field_klapper**: Simple Yes/No for current repertoire status

### Date Widgets
- **date_popup**: Date and time selection
- **date_select**: Date selection only
- **date_text**: EXIF date format

### File Widgets
- **filefield_widget**: File uploads for documents, sheet music
- **imagefield_widget**: Image uploads with list and metadata options

### Reference Widgets
- **nodereference_select**: Dropdown reference to other nodes
- **nodereference_autocomplete**: Autocomplete reference to other nodes

## Database Storage Pattern

**Content-Specific Tables:** 14 tables following pattern `content_type_[typename]`
**Shared Field Tables:** 16 tables following pattern `content_field_[fieldname]`
**Configuration Tables:**
- `content_node_field`: Field type definitions and allowed values
- `content_node_field_instance`: Field labels, widget settings, and display configuration per content type

## Technical Details

### Field Storage Architecture
Drupal 6 CCK uses a sophisticated field storage system:

1. **Content-specific fields** are stored in dedicated tables named `content_type_[content_type]`
2. **Shared fields** are stored in separate tables named `content_field_[field_name]`
3. **Field definitions** are stored in `content_node_field` with serialized PHP configuration
4. **Field instances** are stored in `content_node_field_instance` with content-type-specific settings

### Option Field Implementation
Option fields in Drupal 6 CCK store their allowed values in the `global_settings` column of `content_node_field` as serialized PHP arrays with the key `allowed_values`. The format supports:

- **Simple values**: One option per line (e.g., "Pop", "Jazz")
- **Key|Value pairs**: Stored value|Display label (e.g., "1|Bestuurslid", "10|Lid")
- **Line separators**: Options separated by `\r\n` (carriage return + line feed)

### User Access and Permissions

The site implements a comprehensive role-based permission system:

**User Roles:**
1. **Anonymous User** (ID: 1) - Basic public access
2. **Authenticated User** (ID: 2) - Logged-in user access
3. **Lid** (ID: 3) - Choir member access
4. **Dirigent** (ID: 4) - Conductor access
5. **Webmaster** (ID: 5) - Technical administration
6. **Redacteur** (ID: 6) - Content editor
7. **Bestuur** (ID: 7) - Board member access
8. **Muziekcommissie** (ID: 8) - Music committee access
9. **Feestcommissie** (ID: 10) - Event committee access
10. **Commissie IR** (ID: 11) - Internal relations committee
11. **Auteur** (ID: 12) - Content author
12. **Commissie Concerten** (ID: 13) - Concert committee
13. **Admin** (ID: 14) - Full system administration
14. **Commissie Koorregie** (ID: 15) - Choir direction committee
15. **Band** (ID: 16) - Band member access
16. **Aspirant-lid** (ID: 21) - Prospective member access

**Field-Level Permissions:**
The system implements granular field-level permissions using the pattern `view field_[fieldname]` and `edit field_[fieldname]`. This allows specific user roles to access only relevant information.

**Examples of Role-Based Field Access:**
- **Personal Information**: Only visible to members and above
- **Administrative Fields**: Restricted to board and admin roles
- **Sheet Music**: Different access levels for band vs. choir materials
- **Internal Communications**: Member-only visibility

### Content Workflow
The site uses a workflow system for content management:
- **Draft states** for content creation
- **Published states** for public content
- **Member-only states** for internal content
- **Scheduled publishing** for time-sensitive content

### Display Management
Drupal 6 CCK provides multiple display contexts:
- **Full node view**: Complete content display
- **Teaser view**: Summary display for listings
- **Compact view**: Condensed format
- **Email formats**: Plain text and HTML email formatting
- **RSS feeds**: Syndication-friendly formatting
- **Search results**: Search-optimized display
- **Token replacement**: For automated content generation

### Field Formatters and Custom Display
The system implements custom formatters for specialized display:
- **Badge formatters**: For role and status indicators
- **Embedded view formatters**: For dynamic content inclusion
- **Date formatters**: Multiple date display options
- **File formatters**: Specialized handling for sheet music and audio files

### Migration Considerations

**For Future Drupal Upgrades:**
1. **Field Types**: Most CCK field types have direct equivalents in modern Drupal
2. **Option Lists**: Can be migrated to List fields or Taxonomy vocabularies
3. **File Fields**: Direct migration path to File and Image fields
4. **Node References**: Can be converted to Entity Reference fields
5. **Custom Formatters**: May require rebuilding in modern Drupal

**Data Preservation:**
- All field data is stored in a structured, database-normalized format
- Option values and field configurations are fully documented
- User permissions and roles are clearly defined
- Content relationships are maintained through node reference fields

## Summary

This Drupal 6-based website for "ThirdWing.nl" represents a sophisticated content management system for a musical organization with the following key characteristics:

**Organizational Structure:**
- **15 content types** covering all aspects of choir/band management
- **Member management** with detailed profile information and role assignments
- **Event coordination** through activity planning and logistics tracking
- **Musical content management** for repertoire, sheet music, and recordings
- **Communication systems** for news, newsletters, and member information

**Technical Architecture:**
- **16 shared fields** ensuring data consistency across content types
- **Comprehensive option systems** with 1,000+ predefined choices
- **Role-based access control** with 16 different user permission levels
- **Flexible display system** supporting multiple viewing contexts
- **File management** for documents, images, audio, and sheet music

**Data Richness:**
- **34 different option fields** with carefully curated choices
- **Position tracking** for 70+ choir seating arrangements
- **Commission roles** across 8 different organizational committees
- **Temporal data** with date ranges for memberships and friendships
- **Musical metadata** including genres, arrangements, and performance history

This system demonstrates enterprise-level content management capabilities, providing a comprehensive digital infrastructure for managing all aspects of a musical organization's operations, from member administration to performance logistics and content publishing.