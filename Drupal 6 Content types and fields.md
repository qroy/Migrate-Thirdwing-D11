# ThirdWing.nl Content Types Analysis

**Database:** thirdwing_nl  
**CMS:** Drupal (based on table structure)  
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
- `field_gitaar_value`: "Gitarist" (optionwidgets_select)
- `field_basgitaar_value`: "Basgitarist" (optionwidgets_select)
- `field_drums_value`: "Drummer" (optionwidgets_select)
- `field_vervoer_value`: "Karrijder" (text_textfield)
- `field_sleepgroep_value`: "Sleepgroep" (optionwidgets_select)
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
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 3. **FOTO** (Photo)
- **Display Name:** Foto
- **Description:** Foto-album
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Omschrijving)

**Content Type Specific Fields:** None (uses shared fields only)

**Shared Fields Used:** None

**Shared Fields Used:**
- `field_video`: "Video" (emvideo_textfields)
- `field_repertoire`: "Nummer" (nodereference_select)
- `field_audio_uitvoerende`: "Uitvoerende" (text_textfield)
- `field_audio_type`: "Type" (optionwidgets_select)
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

**Shared Fields Used:**
- `field_datum`: "Datum" (date_popup)
- `field_files`: "Bestandsbijlages" (filefield_widget)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_view`: "Extra inhoud" (viewfield_select)

**Shared Fields Used:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

**Shared Fields Used:**
- `field_datum`: "Datum" (date_popup)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_audio_type`: "Type" (optionwidgets_select)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

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

### 9. **PROFIEL** (Profile)
- **Display Name:** Profiel
- **Description:** Een gebruikersprofiel.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_emailbewaking_value`: "Email origineel" (text_textfield)
- `field_lidsinds_value`: "Lid Sinds" (date_select)
- `field_koor_value`: "Koorfunctie" (optionwidgets_select)
- `field_sleepgroep_1_value`: "Sleepgroep" (optionwidgets_select)
- `field_voornaam_value`: "Voornaam" (text_textfield)
- `field_achternaam_voorvoegsel_value`: "Achternaam voorvoegsel" (text_textfield)
- `field_achternaam_value`: "Achternaam" (text_textfield)
- `field_geboortedatum_value`: "Geboortedatum" (date_select)
- `field_geslacht_value`: "Geslacht" (optionwidgets_select)
- `field_karrijder_value`: "Karrijder" (optionwidgets_onoff)
- `field_uitkoor_value`: "Uit koor per" (date_select)
- `field_adres_value`: "Adres" (text_textfield)
- `field_postcode_value`: "Postcode" (text_textfield)
- `field_telefoon_value`: "Telefoon" (text_textfield)
- `field_notes_value`: "Notities" (text_textarea)
- `field_notes_format`: "Notities" (text_textarea)
- `field_mobiel_value`: "Mobiel" (text_textfield)
- `field_functie_bestuur_value`: "Functie Bestuur" (optionwidgets_select)
- `field_functie_mc_value`: "Functie Muziekcommissie" (optionwidgets_select)
- `field_functie_concert_value`: "Functie Commissie Concerten" (optionwidgets_select)
- `field_functie_feest_value`: "Functie Feestcommissie" (optionwidgets_select)
- `field_functie_regie_value`: "Functie Commissie Koorregie" (optionwidgets_select)
- `field_functie_ir_value`: "Functie Commissie Interne Relaties" (optionwidgets_select)
- `field_functie_pr_value`: "Functie Commissie PR" (optionwidgets_select)
- `field_functie_tec_value`: "Functie Technische Commissie" (optionwidgets_select)
- `field_positie_value`: "Positie" (optionwidgets_select)
- `field_functie_lw_value`: "Functie ledenwerf" (optionwidgets_select)
- `field_functie_fl_value`: "Functie Faciliteiten" (optionwidgets_select)

**Shared Fields Used:**
- `field_woonplaats`: "Woonplaats" (text_textfield)

### 10. **PROGRAMMA** (Program)
- **Display Name:** Programma
- **Description:** Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_prog_type_value`: "Type" (optionwidgets_select)

**Shared Fields Used:** None

### 11. **REPERTOIRE** (Repertoire)
- **Display Name:** Repertoire
- **Description:** Een nummer in het repertoire.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:**
- `field_klapper_value`: "Actueel" (optionwidgets_buttons)
- `field_audio_nummer_value`: "Nummer" (number)
- `field_audio_seizoen_value`: "Seizoen" (optionwidgets_select)
- `field_rep_genre_value`: "Genre" (optionwidgets_select)
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

### 13. **VIDEO** (Video)
- **Display Name:** Video
- **Description:** Een embedded video van YouTube
- **Module:** node
- **Title Label:** Titel
- **Has Body:** No

**Content Type Specific Fields:** None (uses shared fields only)

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
- `field_vriend_benaming_value`: "Benaming" (optionwidgets_select)
- `field_vriend_tot_value`: "Vriend t/m" (optionwidgets_select)
- `field_vriend_vanaf_value`: "Vriend vanaf" (optionwidgets_select)
- `field_vriend_lengte_value`: "Vriendlengte" (optionwidgets_select)

**Shared Fields Used:**
- `field_woonplaats`: "Woonplaats" (text_textfield)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)

### 15. **WEBFORM** (Webform)
- **Display Name:** Webformulier
- **Description:** Create a new form or questionnaire accessible to users. Submission results and statistics are recorded and accessible to privileged users.
- **Module:** node
- **Title Label:** Titel
- **Has Body:** Yes (Berichttekst)

**Content Type Specific Fields:** None (uses shared fields only)

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

### 11. **field_programma2** (Program 2)
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
  - `field_video_value` (varchar(255)) - Video value
  - `field_video_provider` (varchar(255)) - Video provider
  - `field_video_data` (longtext) - Video data
  - `field_video_version` (int(10)) - Video version
  - `field_video_duration` (int(10)) - Video duration
  - `field_video_status` (int(10)) - Video status
  - `field_video_title` (varchar(255)) - Video title
  - `field_video_description` (varchar(255)) - Video description

### 15. **field_view** (View)
- **Label:** "Extra inhoud" (viewfield_select)
- **Database Fields:**
  - `field_view_vname` (varchar(128)) - View name
  - `field_view_vargs` (varchar(255)) - View arguments

### 16. **field_woonplaats** (Residence)
- **Label:** "Woonplaats" (text_textfield)
- **Database Fields:**
  - `field_woonplaats_value` (longtext) - Residence value

## Summary

This Drupal-based website for "ThirdWing.nl" appears to be a comprehensive content management system for a musical organization (likely a choir or band) with the following key features:

**Core Content Types:**
- **Member Management**: Profile content type with extensive personal and role information
- **Event Management**: Activity content type for performances and rehearsals
- **Content Management**: News, newsletters, pages for communication
- **Media Management**: Audio, video, photo content types
- **Musical Content**: Repertoire and program content types for managing musical pieces
- **Administrative**: Reports (verslag) and webforms for organizational needs
- **Community**: Friends/sponsors (vriend) content type
- **Location Management**: Dedicated location content type for venues

**Field Architecture:**
- **Content-Specific Fields**: Each content type has specialized fields relevant to its purpose
- **Shared Fields**: 16 reusable fields available across content types for consistency
- **File Management**: Extensive file attachment capabilities for documents, images, audio, and video
- **Reference Fields**: Node references for creating relationships between content

**Widget Types Used:**
- **text_textfield**: Single-line text input
- **text_textarea**: Multi-line text input
- **optionwidgets_select**: Dropdown selection
- **optionwidgets_buttons**: Radio buttons
- **optionwidgets_onoff**: Checkbox/toggle
- **date_select**: Date picker
- **date_popup**: Date/time popup
- **date_text**: Date text field
- **number**: Numeric input
- **filefield_widget**: File upload
- **imagefield_widget**: Image upload
- **link**: URL link field
- **nodereference_select**: Node reference dropdown
- **nodereference_autocomplete**: Node reference with autocomplete
- **viewfield_select**: View selection
- **emvideo_textfields**: Embedded video fields

**Database Storage Pattern:**
- **Primary Tables**: `content_type_[typename]` for content-specific fields
- **Shared Tables**: `content_field_[fieldname]` for reusable fields
- **Instance Configuration**: `content_node_field_instance` stores field labels and widget settings
- **Field Definitions**: `content_node_field` stores field type definitions

**Technical Details:**
- Built on Drupal CMS with CCK (Content Construction Kit)
- Uses CCK field system for flexible content modeling
- Supports multiple file types and media embedding
- Includes comprehensive user profiling system
- Node reference system for content relationships
- Dutch language interface throughout

**Content Type Purposes:**
- **activiteit**: Event/performance management with detailed logistics
- **profiel**: Comprehensive member profiles with roles and contact info
- **repertoire**: Musical piece catalog with composer and arrangement details
- **audio/video**: Media management for performances
- **nieuws/nieuwsbrief**: Communication and newsletter system
- **locatie**: Venue management for performances
- **vriend**: Sponsor/friend acknowledgment system
- **programma**: Program elements not in main repertoire
- **pagina**: Static page content
- **foto**: Photo album management
- **verslag**: Meeting minutes and reports
- **webform**: Form builder for user interaction

This structure indicates a sophisticated system designed specifically for managing a musical organization's comprehensive digital operations, from member management to performance logistics to community engagement.

## Field Groupings

The system uses field groupings to organize related fields together in the admin interface. Here are the field groupings for each content type:

### ACTIVITEIT (Activity) Field Groups

**Group: "Achtergrond" (Background)**
- `field_background`

**Group: "Bijzonderheden" (Special Notes)**
- `field_bijzonderheden`
- `field_kledingcode`

**Group: "Locatie" (Location)**
- `field_locatie`
- `field_l_bijzonderheden`

**Group: "Bestanden" (Files)**
- `field_files`

**Group: "Afbeeldingen" (Images)**
- `field_afbeeldingen`

**Group: "Logistiek" (Logistics)**
- `field_basgitaar`
- `field_drums`
- `field_gitaar`
- `field_keyboard`
- `field_sleepgroep`
- `field_sleepgroep_aanwezig`
- `field_sleepgroep_terug`
- `field_tijd_aanwezig`
- `field_vervoer`

**Group: "Programma" (Program)**
- `field_programma2`

### FOTO (Photo) Field Groups

**Group: "Activiteiten" (Activities)**
- `field_audio_type`
- `field_ref_activiteit`

**Group: "Afbeelding" (Image)**
- `field_afbeeldingen`

### NIEUWS (News) Field Groups

**Group: "Bestanden" (Files)**
- `field_afbeeldingen`
- `field_files`

### PAGINA (Page) Field Groups

**Group: "Extra inhoud" (Extra Content)**
- `field_view`

**Group: "Bestanden" (Files)**
- `field_afbeeldingen`
- `field_files`

### PROFIEL (Profile) Field Groups

**Group: "Beheer" (Management)**
- `field_emailbewaking`
- `field_notes`

**Group: "Commissies" (Committees)**
- `field_functie_bestuur`
- `field_functie_concert`
- `field_functie_feest`
- `field_functie_fl`
- `field_functie_ir`
- `field_functie_lw`
- `field_functie_mc`
- `field_functie_pr`
- `field_functie_regie`
- `field_functie_tec`

**Group: "Koor" (Choir)**
- `field_karrijder`
- `field_koor`
- `field_lidsinds`
- `field_positie`
- `field_sleepgroep_1`
- `field_uitkoor`

**Group: "Persoonlijk" (Personal)**
- `field_achternaam`
- `field_achternaam_voorvoegsel`
- `field_adres`
- `field_geboortedatum`
- `field_geslacht`
- `field_mobiel`
- `field_postcode`
- `field_telefoon`
- `field_voornaam`
- `field_woonplaats`

### REPERTOIRE (Repertoire) Field Groups

**Group: "Arrangeur" (Arranger)**
- `field_rep_arr`
- `field_rep_arr_jaar`

**Group: "Bandpartituur" (Band Score)**
- `field_partij_band`

**Group: "Componist" (Composer)**
- `field_rep_componist`
- `field_rep_componist_jaar`

**Group: "Koorpartituur" (Choir Score)**
- `field_partij_koor_l`

**Group: "Informatie" (Information)**
- `field_audio_nummer`
- `field_audio_seizoen`
- `field_klapper`
- `field_rep_genre`
- `field_rep_sinds`

**Group: "Tekst en koorregie" (Text and Choir Direction)**
- `field_partij_tekst`

**Group: "Uitvoerende" (Performer)**
- `field_rep_uitv`
- `field_rep_uitv_jaar`

## Field Grouping Analysis

**Logical Organization**: Field groups provide logical organization of related fields, making the admin interface more user-friendly and intuitive.

**Content-Specific Groupings**: Each content type has groups tailored to its specific purpose:
- **Activities**: Organized by logistics, location, files, and program elements
- **Profiles**: Separated into personal info, choir roles, committee functions, and management
- **Repertoire**: Grouped by musical elements (composer, arranger, performer) and file types

**Consistent Naming**: Groups use clear Dutch labels that reflect their purpose, making the interface accessible to Dutch-speaking users.

**File Management**: Most content types with file capabilities have dedicated "Bestanden" (Files) groups for organizing attachments.

This grouping system enhances the user experience by organizing complex forms into logical sections, making data entry and editing more efficient for the musical organization's administrators.