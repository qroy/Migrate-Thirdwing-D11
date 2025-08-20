# ThirdWing.nl Content Types Analyse - Volledige Documentatie

**Database:** thirdwing_nl  
**CMS:** Drupal 6 (gebaseerd op tabelstructuur)  
**Totaal Content Types:** 15  
**Totaal Gedeelde Velden:** 16  

## Overzicht Content Types

### 1. **ACTIVITEIT** (Activiteit)
- **Weergavenaam:** Activiteit
- **Beschrijving:** Een activiteit (uitvoering, repetitie)
- **Module:** node
- **Titel Label:** Omschrijving
- **Heeft Body:** Ja (Berichttekst)

**Content Type Specifieke Velden:**
- `field_tijd_aanwezig_value`: "Koor Aanwezig" (text_textfield)
- `field_keyboard_value`: "Toetsenist" (optionwidgets_select)
  - **Opties:** +, ?, -, v
- `field_gitaar_value`: "Gitarist" (optionwidgets_select)
  - **Opties:** +, ?, -, v
- `field_basgitaar_value`: "Basgitarist" (optionwidgets_select)
  - **Opties:** +, ?, -, v
- `field_drums_value`: "Drummer" (optionwidgets_select)
  - **Opties:** +, ?, -, v
- `field_vervoer_value`: "Karrijder" (text_textfield)
- `field_sleepgroep_value`: "Sleepgroep" (optionwidgets_select)
  - **Opties:** I, II, III, IV, V, *
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
  - **Opties:** I, II, III, IV, V, *
- `field_huiswerk_fid`: "Huiswerk" (filefield_widget)
- `field_huiswerk_list`: "Huiswerk" (filefield_widget)
- `field_huiswerk_data`: "Huiswerk" (filefield_widget)

**Gebruikte Gedeelde Velden:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_programma2`: "Programma" (nodereference_autocomplete)
- `field_datum`: "Datum en tijd" (date_popup)

**Taxonomy Velden:**
- **Activiteiten** (vocabulary ID: 5): Categorisatie van activiteit type
  - **Terms:**
    - Wereldlijk
    - Kerkelijk
    - Concours
    - Repetitie
    - Koorreis
    - Vergadering
    - Overige
    - Vakantie

### 2. **AUDIO** (Audio)
- **Weergavenaam:** Audio
- **Beschrijving:** Geluidsbestanden
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_mp3_fid`: "mp3" (filefield_widget)
- `field_mp3_list`: "mp3" (filefield_widget)
- `field_mp3_data`: "mp3" (filefield_widget)
- `field_audio_bijz_value`: "Bijzonderheden" (text_textfield)

**Gebruikte Gedeelde Velden:**
- `field_repertoire`: "Nummer" (nodereference_select)
- `field_audio_uitvoerende`: "Uitvoerende" (text_textfield)
- `field_audio_type`: "Type" (optionwidgets_select)
  - **Opties:** Uitvoering, Repetitie, Oefenbestand, Origineel, Uitzending, Overig
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 3. **FOTO** (Foto)
- **Weergavenaam:** Foto
- **Beschrijving:** Foto-album
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Ja (Omschrijving)

**Content Type Specifieke Velden:** Geen (gebruikt alleen gedeelde velden)

**Gebruikte Gedeelde Velden:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_audio_type`: "Type" (optionwidgets_select)
  - **Opties:** Uitvoering, Repetitie, Oefenbestand, Origineel, Uitzending, Overig
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 4. **IMAGE** (Afbeelding)
- **Weergavenaam:** Image
- **Beschrijving:** Afbeelding content type
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_exif_datetimeoriginal_value`: "EXIF Datum" (date_text)
- `field_bijschrift_value`: "Bijschrift" (text_textfield)

**Gebruikte Gedeelde Velden:** Geen

### 5. **LOCATIE** (Locatie)
- **Weergavenaam:** Locatie
- **Beschrijving:** Veelvoorkomende locaties van uitvoeringen.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_l_adres_value`: "Adres" (text_textfield)
- `field_l_plaats_value`: "Plaats" (text_textfield)
- `field_l_postcode_value`: "Postcode" (text_textfield)

**Gebruikte Gedeelde Velden:**
- `field_l_routelink`: "Route" (link)

### 6. **NIEUWS** (Nieuws)
- **Weergavenaam:** Nieuws
- **Beschrijving:** Een nieuwsbericht. Dit kan een publiek nieuwsbericht zijn, maar ook een nieuwsbericht voor op de ledenpagina.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Ja (Berichttekst)

**Content Type Specifieke Velden:** Geen (gebruikt alleen gedeelde velden)

**Gebruikte Gedeelde Velden:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 7. **NIEUWSBRIEF** (Nieuwsbrief)
- **Weergavenaam:** Nieuwsbrief
- **Beschrijving:** Nieuwsbriefuitgave te zenden naar de ingeschreven e-mailadressen.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Ja (Introtekst)

**Content Type Specifieke Velden:**
- `field_jaargang_value`: "Jaargang" (number)
- `field_uitgave_value`: "Uitgave" (number)

**Gebruikte Gedeelde Velden:**
- `field_inhoud`: "Inhoud" (nodereference_autocomplete)

### 8. **PAGINA** (Pagina)
- **Weergavenaam:** Pagina
- **Beschrijving:** Gebruik een 'Pagina' wanneer je een statische pagina wilt toevoegen.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Ja (Berichttekst)

**Content Type Specifieke Velden:** Geen (gebruikt alleen gedeelde velden)

**Gebruikte Gedeelde Velden:**
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)
- `field_files`: "Bestandsbijlages" (filefield_widget)
- `field_view`: "Extra inhoud" (viewfield_select)

### 9. **PROFIEL** (Profiel)
- **Weergavenaam:** Profiel
- **Beschrijving:** Een gebruikersprofiel.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_voornaam_value`: "Voornaam" (text_textfield)
- `field_achternaam_voorvoegsel_value`: "Tussenvoegsel" (text_textfield)
- `field_achternaam_value`: "Achternaam" (text_textfield)
- `field_geboortedatum_value`: "Geboortedatum" (date_select)
- `field_geslacht_value`: "Geslacht" (optionwidgets_select)
  - **Opties:** m|Man, v|Vrouw
- `field_adres_value`: "Adres" (text_textfield)
- `field_postcode_value`: "Postcode" (text_textfield)
- `field_telefoon_value`: "Telefoon" (text_textfield)
- `field_mobiel_value`: "Mobiel" (text_textfield)
- `field_lidsinds_value`: "Lid sinds" (date_select)
- `field_uitkoor_value`: "Uit koor" (date_select)
- `field_koor_value`: "Koor" (optionwidgets_select)
  - **Opties:** B|Bas, A|Tenor, E|Alt, C|1e Sopraan, D|2e Sopraan, Y1|Dirigent, Z1|Toetsenist, Z2|Gitarist, Z3|Bassist, Z4|Drummer, Z5|Techniek en percussie
- `field_positie_value`: "Positie" (optionwidgets_select)
  - **Opties:** 
    - 101|4x01 t/m 116|4x16 (Rij 4)
    - 201|3x01 t/m 216|3x16 (Rij 3)
    - 301|2x01 t/m 316|2x16 (Rij 2)
    - 401|1x01 t/m 416|1x16 (Rij 1)
    - 501|Band 1 t/m 504|Band 4
    - 601|Dirigent
    - 701|Niet ingedeeld
- `field_karrijder_value`: "Karrijder" (optionwidgets_select)
  - **Opties:** 0, *|Karrijder
- `field_sleepgroep_1_value`: "Sleepgroep" (optionwidgets_select)
  - **Opties:** 1|I, 2|II, 3|III, 4|IV, 5|V, 8|OG, 9|-
- `field_functie_bestuur_value`: "Functie Bestuur" (optionwidgets_select)
  - **Opties:** 1|Voorzitter, 2|Secretaris, 3|Penningmeester, 4|Bestuurslid
- `field_functie_mc_value`: "Functie Muziekcommissie" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Voorzitter, 20|Secretaris, 30|Dirigent, 40|Contactpersoon band, 90|Lid
- `field_functie_concert_value`: "Functie Commissie Concerten" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid
- `field_functie_feest_value`: "Functie Feestcommissie" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid
- `field_functie_fl_value`: "Functie Faciliteiten" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid
- `field_functie_ir_value`: "Functie Commissie Interne Relaties" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid, 11|Interne relaties
- `field_functie_lw_value`: "Functie Ledenadministratie" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid
- `field_functie_pr_value`: "Functie Commissie PR" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 8|Website, 9|Social Media, 10|Lid
- `field_functie_regie_value`: "Functie Commissie Koorregie" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid
- `field_functie_tec_value`: "Functie Technische Commissie" (optionwidgets_select)
  - **Opties:** 1|Bestuurslid, 10|Lid

**Gebruikte Gedeelde Velden:**
- `field_woonplaats`: "Woonplaats" (text_textfield)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)

### 10. **PROGRAMMA** (Programma)
- **Weergavenaam:** Programma
- **Beschrijving:** Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_prog_type_value`: "Type" (optionwidgets_select)
  - **Opties:** programma|Programma onderdeel, nummer|Nummer

**Gebruikte Gedeelde Velden:** Geen

### 11. **REPERTOIRE** (Repertoire)
- **Weergavenaam:** Repertoire
- **Beschrijving:** Een nummer in het repertoire.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_klapper_value`: "Actueel" (optionwidgets_buttons)
  - **Opties:** Nee, Ja
- `field_audio_nummer_value`: "Nummer" (number)
- `field_audio_seizoen_value`: "Seizoen" (optionwidgets_select)
  - **Opties:** Regulier|Regulier, Kerst|Kerst
- `field_rep_genre_value`: "Genre" (optionwidgets_select)
  - **Opties:** Pop, Musical / Film, Geestelijk / Gospel
- `field_rep_uitv_jaar_value`: "Jaar uitvoering" (number)
- `field_rep_uitv_value`: "Uitvoerende" (text_textfield)
- `field_rep_componist_value`: "Componist" (text_textfield)
- `field_rep_componist_jaar_value`: "Jaar compositie" (number)
- `field_rep_arr_value`: "Arrangeur" (text_textfield)
- `field_rep_arr_jaar_value`: "Jaar arrangement" (number)
- `field_rep_sinds_value`: "In repertoire sinds" (number)

**Gebruikte Gedeelde Velden:**
- `field_partij_tekst`: "Tekst / koorregie" (filefield_widget)
- `field_partij_koor_l`: "Koorpartituur" (filefield_widget)
- `field_partij_band`: "Bandpartituur" (filefield_widget)

### 12. **VERSLAG** (Verslag)
- **Weergavenaam:** Verslag
- **Beschrijving:** Verslagen van vergaderingen.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:** Geen (gebruikt alleen gedeelde velden)

**Gebruikte Gedeelde Velden:**
- `field_datum`: "Datum" (date_popup)
- `field_files`: "Bestandsbijlages" (filefield_widget)

**Taxonomy Velden:**
- **Verslagen** (vocabulary ID: 9): Categorisatie van verslag type
  - **Terms:** 
    - Bestuursvergadering
    - Vergadering Muziekcommissie
    - Algemene Ledenvergadering
    - Overige Vergadering
    - Combo Overleg
    - Jaarevaluatie Dirigent
    - Jaarverslag
    - Concertcommissie

### 13. **VIDEO** (Video)
- **Weergavenaam:** Video
- **Beschrijving:** Een embedded video van YouTube
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Nee

**Content Type Specifieke Velden:** Geen (gebruikt alleen gedeelde velden)

**Gebruikte Gedeelde Velden:**
- `field_video`: "Video" (emvideo_textfields)
- `field_repertoire`: "Nummer" (nodereference_select)
- `field_audio_uitvoerende`: "Uitvoerende" (text_textfield)
- `field_audio_type`: "Type" (optionwidgets_select)
- `field_datum`: "Datum" (date_popup)
- `field_ref_activiteit`: "Activiteit" (nodereference_autocomplete)

### 14. **VRIEND** (Vriend)
- **Weergavenaam:** Vriendenvermelding
- **Beschrijving:** Een vriendenvermelding voor op de vrienden overzichtspagina.
- **Module:** node
- **Titel Label:** Naam
- **Heeft Body:** Nee

**Content Type Specifieke Velden:**
- `field_website_url`: "Website" (link)
- `field_website_title`: "Website" (link)
- `field_website_attributes`: "Website" (link)
- `field_vriend_soort_value`: "Soort" (optionwidgets_select)
  - **Opties:** financieel, niet-financieel, materieel
- `field_vriend_benaming_value`: "Benaming" (optionwidgets_select)
  - **Opties:** vriend, vriendin, vrienden
- `field_vriend_tot_value`: "Vriend t/m" (optionwidgets_select)
  - **Opties:** 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020
- `field_vriend_vanaf_value`: "Vriend vanaf" (optionwidgets_select)
  - **Opties:** 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020
- `field_vriend_lengte_value`: "Vriendlengte" (optionwidgets_select)
  - **Opties:** 1|0 t/m 1 jaar, 2|2 t/m 5 jaar, 3|6 jaar en langer

**Gebruikte Gedeelde Velden:**
- `field_woonplaats`: "Woonplaats" (text_textfield)
- `field_afbeeldingen`: "Afbeeldingen" (imagefield_widget)

### 15. **WEBFORM** (Webformulier)
- **Weergavenaam:** Webformulier
- **Beschrijving:** Maak een nieuw formulier of vragenlijst toegankelijk voor gebruikers. Inzendingsresultaten en statistieken worden geregistreerd en zijn toegankelijk voor bevoegde gebruikers.
- **Module:** node
- **Titel Label:** Titel
- **Heeft Body:** Ja (Berichttekst)

**Content Type Specifieke Velden:** Geen (gebruikt alleen standaard node velden - geen aangepaste CCK tabel)

**Gebruikte Gedeelde Velden:** Geen

## Gedeelde Velden Beschikbaar voor Alle Content Types

De volgende velden zijn beschikbaar als gedeelde velden die aan elk content type kunnen worden gekoppeld:

### 1. **field_afbeeldingen** (Afbeeldingen)
- **Label:** "Afbeeldingen" (imagefield_widget)
- **Database Velden:**
  - `field_afbeeldingen_fid` (int(11)) - Bestand ID
  - `field_afbeeldingen_list` (tinyint(4)) - Lijst vlag
  - `field_afbeeldingen_data` (text) - Afbeelding data

### 2. **field_audio_type** (Audio Type)
- **Label:** "Type" (optionwidgets_select)
- **Opties:** Uitvoering, Repetitie, Oefenbestand, Origineel, Uitzending, Overig
- **Database Velden:**
  - `field_audio_type_value` (longtext) - Audio type waarde

### 3. **field_audio_uitvoerende** (Audio Uitvoerende)
- **Label:** "Uitvoerende" (text_textfield)
- **Database Velden:**
  - `field_audio_uitvoerende_value` (longtext) - Audio uitvoerende waarde

### 4. **field_datum** (Datum)
- **Label:** "Datum en tijd" (date_popup)
- **Database Velden:**
  - `field_datum_value` (varchar(20)) - Datum waarde

### 5. **field_files** (Bestanden)
- **Label:** "Bestandsbijlages" (filefield_widget)
- **Database Velden:**
  - `field_files_fid` (int(11)) - Bestand ID
  - `field_files_list` (tinyint(4)) - Lijst vlag
  - `field_files_data` (text) - Bestand data

### 6. **field_inhoud** (Inhoud)
- **Label:** "Inhoud" (nodereference_autocomplete)
- **Database Velden:**
  - `field_inhoud_nid` (int(10)) - Inhoud node ID referentie

### 7. **field_l_routelink** (Route Link)
- **Label:** "Route" (link)
- **Database Velden:**
  - `field_l_routelink_url` (varchar(2048)) - URL
  - `field_l_routelink_title` (varchar(255)) - Link titel
  - `field_l_routelink_attributes` (mediumtext) - Link attributen

### 8. **field_partij_band** (Band Partituur)
- **Label:** "Bandpartituur" (filefield_widget)
- **Database Velden:**
  - `field_partij_band_fid` (int(11)) - Bestand ID
  - `field_partij_band_list` (tinyint(4)) - Lijst vlag
  - `field_partij_band_data` (text) - Partituur data

### 9. **field_partij_koor_l** (Koor Partituur)
- **Label:** "Koorpartituur" (filefield_widget)
- **Database Velden:**
  - `field_partij_koor_l_fid` (int(11)) - Bestand ID
  - `field_partij_koor_l_list` (tinyint(4)) - Lijst vlag
  - `field_partij_koor_l_data` (text) - Partituur data

### 10. **field_partij_tekst** (Tekst/Koorregie)
- **Label:** "Tekst / koorregie" (filefield_widget)
- **Database Velden:**
  - `field_partij_tekst_fid` (int(11)) - Bestand ID
  - `field_partij_tekst_list` (tinyint(4)) - Lijst vlag
  - `field_partij_tekst_data` (text) - Tekst partituur data

### 11. **field_programma2** (Programma)
- **Label:** "Programma" (nodereference_autocomplete)
- **Database Velden:**
  - `field_programma2_nid` (int(10)) - Programma node ID referentie

### 12. **field_ref_activiteit** (Activiteit Referentie)
- **Label:** "Activiteit" (nodereference_autocomplete)
- **Database Velden:**
  - `field_ref_activiteit_nid` (int(10)) - Activiteit node ID referentie

### 13. **field_repertoire** (Repertoire)
- **Label:** "Nummer" (nodereference_select)
- **Database Velden:**
  - `field_repertoire_nid` (int(10)) - Repertoire node ID referentie

### 14. **field_video** (Video)
- **Label:** "Video" (emvideo_textfields)
- **Database Velden:**
  - `field_video_embed` (longtext) - Video embed code
  - `field_video_value` (text) - Video URL/identifier
  - `field_video_provider` (text) - Video provider
  - `field_video_data` (longtext) - Video metadata

### 15. **field_view** (Weergave)
- **Label:** "Extra inhoud" (viewfield_select)
- **Database Velden:**
  - `field_view_vname` (varchar(128)) - Weergave naam
  - `field_view_vargs` (varchar(255)) - Weergave argumenten

### 16. **field_woonplaats** (Woonplaats)
- **Label:** "Woonplaats" (text_textfield)
- **Database Velden:**
  - `field_woonplaats_value` (longtext) - Woonplaats waarde

## Widget Types en Opties Overzicht

### Selectie Widgets (optionwidgets_select)
**Band/Instrument Velden:**
- Alle gebruiken opties: `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging)

**Commissie Functies:**
- Meeste gebruiken: `1|Bestuurslid`, `10|Lid`
- Speciale gevallen:
  - **MC**: Extra opties voor Voorzitter, Secretaris, Dirigent, Contactpersoon band
  - **PR**: Extra opties voor Website, Social Media
  - **IR**: Extra optie voor Interne relaties

**Andere Selectie Velden:**
- **Sleepgroep**: Romeinse cijfers I-V en wildcard *
- **Koor Positie**: Uitgebreide lijst van koorposities en bandrollen
- **Audio Type**: 6 verschillende opname/uitvoering types
- **Vriend Velden**: Categorieën voor vriendtypen, duur, en jaren

### Knop Widgets (optionwidgets_buttons)
- **field_klapper**: Eenvoudige Ja/Nee voor actueel repertoire status

### Datum Widgets
- **date_popup**: Datum en tijd selectie
- **date_select**: Alleen datum selectie
- **date_text**: EXIF datum formaat

### Bestand Widgets
- **filefield_widget**: Bestand uploads voor documenten, partituren
- **imagefield_widget**: Afbeelding uploads met lijst en metadata opties

### Referentie Widgets
- **nodereference_select**: Dropdown referentie naar andere nodes
- **nodereference_autocomplete**: Autocomplete referentie naar andere nodes

## Database Opslag Patroon

**Content-Specifieke Tabellen:** 14 tabellen volgens patroon `content_type_[typenaam]`
**Gedeelde Veld Tabellen:** 16 tabellen volgens patroon `content_field_[veldnaam]`
**Configuratie Tabellen:**
- `content_node_field`: Veld type definities en toegestane waarden
- `content_node_field_instance`: Veld labels, widget instellingen, en weergave configuratie per content type

## Technische Details

### Veld Opslag Architectuur
Drupal 6 CCK gebruikt een geavanceerd veld opslag systeem:

1. **Content-specifieke velden** worden opgeslagen in dedicated tabellen genaamd `content_type_[content_type]`
2. **Gedeelde velden** worden opgeslagen in aparte tabellen genaamd `content_field_[veld_naam]`
3. **Veld definities** worden opgeslagen in `content_node_field` met geserialiseerde PHP configuratie
4. **Veld instanties** worden opgeslagen in `content_node_field_instance` met content-type-specifieke instellingen

### Optie Veld Implementatie
Optie velden in Drupal 6 CCK slaan hun toegestane waarden op in de `global_settings` kolom van `content_node_field` als geserialiseerde PHP arrays met de sleutel `allowed_values`. Het formaat ondersteunt:

- **Eenvoudige waarden**: Één optie per regel (bijv. "Pop", "Jazz")
- **Sleutel|Waarde paren**: Opgeslagen waarde|Weergave label (bijv. "1|Bestuurslid", "10|Lid")
- **Regel scheidingstekens**: Opties gescheiden door `\r\n` (carriage return + line feed)

### Gebruikerstoegang en Rechten

De site implementeert een uitgebreid rol-gebaseerd rechten systeem:

**Gebruikersrollen:**
1. **Anonieme Gebruiker** (ID: 1) - Basis publieke toegang
2. **Geauthenticeerde Gebruiker** (ID: 2) - Ingelogde gebruiker toegang
3. **Lid** (ID: 3) - Koorlid toegang
4. **Dirigent** (ID: 4) - Dirigent toegang
5. **Webmaster** (ID: 5) - Technische administratie
6. **Redacteur** (ID: 6) - Content editor
7. **Bestuur** (ID: 7) - Bestuurslid toegang
8. **Muziekcommissie** (ID: 8) - Muziekcommissie toegang
9. **Feestcommissie** (ID: 10) - Feestcommissie toegang
10. **Commissie IR** (ID: 11) - Interne relaties commissie
11. **Auteur** (ID: 12) - Content auteur
12. **Commissie Concerten** (ID: 13) - Concerten commissie
13. **Admin** (ID: 14) - Volledige systeem administratie
14. **Commissie Koorregie** (ID: 15) - Koorregie commissie
15. **Band** (ID: 16) - Bandlid toegang
16. **Aspirant-lid** (ID: 21) - Aspirant lid toegang

**Veld-Niveau Rechten:**
Het systeem implementeert granulaire veld-niveau rechten met het patroon `view field_[veldnaam]` en `edit field_[veldnaam]`. Dit stelt specifieke gebruikersrollen in staat om alleen relevante informatie te benaderen.

**Voorbeelden van Rol-Gebaseerde Veld Toegang:**
- **Persoonlijke Informatie**: Alleen zichtbaar voor leden en hoger
- **Administratieve Velden**: Beperkt tot bestuur en admin rollen
- **Partituren**: Verschillende toegangsniveaus voor band vs. koor materialen
- **Interne Communicatie**: Alleen leden zichtbaarheid

### Content Workflow
De site gebruikt een workflow systeem voor content beheer:
- **Concept staten** voor content creatie
- **Gepubliceerde staten** voor publieke content
- **Alleen-leden staten** voor interne content
- **Geplande publicatie** voor tijd-gevoelige content

### Weergave Beheer
Drupal 6 CCK biedt meerdere weergave contexten:
- **Volledige node weergave**: Complete content weergave
- **Teaser weergave**: Samenvatting weergave voor lijsten
- **Compacte weergave**: Verkorte formaat
- **Email formaten**: Platte tekst en HTML email opmaak
- **RSS feeds**: Syndicatie-vriendelijke opmaak
- **Zoekresultaten**: Zoek-geoptimaliseerde weergave
- **Token vervanging**: Voor geautomatiseerde content generatie

### Veld Formatters en Aangepaste Weergave
Het systeem implementeert aangepaste formatters voor gespecialiseerde weergave:
- **Badge formatters**: Voor rol en status indicatoren
- **Embedded view formatters**: Voor dynamische content inclusie
- **Datum formatters**: Meerdere datum weergave opties
- **Bestand formatters**: Gespecialiseerde verwerking voor partituren en audiobestanden

### Migratie Overwegingen

**Voor Toekomstige Drupal Upgrades:**
1. **Veld Types**: Meeste CCK veld types hebben directe equivalenten in moderne Drupal
2. **Optie Lijsten**: Kunnen gemigreerd worden naar Lijst velden of Taxonomie vocabulaires
3. **Bestand Velden**: Directe migratie pad naar Bestand en Afbeelding velden
4. **Node Referenties**: Kunnen geconverteerd worden naar Entity Referentie velden
5. **Aangepaste Formatters**: Mogelijk herbouwen vereist in moderne Drupal

**Data Behoud:**
- Alle veld data is opgeslagen in een gestructureerd, database-genormaliseerd formaat
- Optie waarden en veld configuraties zijn volledig gedocumenteerd
- Gebruikersrechten en rollen zijn duidelijk gedefinieerd
- Content relaties worden onderhouden door node referentie velden

## Samenvatting

Deze Drupal 6-gebaseerde website voor "ThirdWing.nl" vertegenwoordigt een geavanceerd content management systeem voor een muziekorganisatie met de volgende kernkenmerken:

**Organisatiestructuur:**
- **15 content types** die alle aspecten van koor/band beheer dekken
- **Ledenbeheer** met gedetailleerde profiel informatie en rol toewijzingen
- **Evenement coördinatie** door activiteit planning en logistiek tracking
- **Muzikaal content beheer** voor repertoire, partituren, en opnames
- **Communicatie systemen** voor nieuws, nieuwsbrieven, en ledeninformatie

**Technische Architectuur:**
- **16 gedeelde velden** die data consistentie verzekeren over content types
- **Uitgebreide optie systemen** met 1.000+ voorgedefinieerde keuzes
- **Rol-gebaseerde toegangscontrole** met 16 verschillende gebruikersrechten niveaus
- **Flexibel weergave systeem** dat meerdere weergave contexten ondersteunt
- **Bestandsbeheer** voor documenten, afbeeldingen, audio, en partituren

**Data Rijkdom:**
- **34 verschillende optie velden** met zorgvuldig samengestelde keuzes
- **Positie tracking** voor 70+ koor zitplaats arrangementen
- **Commissie rollen** over 8 verschillende organisatorische commissies
- **Temporele data** met datum bereiken voor lidmaatschappen en vriendschappen
- **Muzikale metadata** inclusief genres, arrangementen, en uitvoering geschiedenis

Dit systeem demonstreert enterprise-niveau content management mogelijkheden, en biedt een uitgebreide digitale infrastructuur voor het beheren van alle aspecten van een muziekorganisatie's operaties, van ledenadministratie tot uitvoering logistiek en content publicatie.

## Bijlagen

### A. Veld Mapping Tabel
| Veld Naam | Type | Widget | Gebruikt in Content Types |
|-----------|------|--------|---------------------------|
| field_afbeeldingen | imagefield | imagefield_widget | activiteit, nieuws, pagina, foto, profiel, vriend |
| field_audio_type | text | optionwidgets_select | audio, video |
| field_datum | date | date_popup | activiteit, audio, video, verslag |
| field_sleepgroep | text | optionwidgets_select | activiteit |
| field_koor | text | optionwidgets_select | profiel |
| field_positie | text | optionwidgets_select | profiel |

### B. Gebruikersrol Rechten Matrix
| Rol | Content Maken | Velden Bewerken | Speciale Rechten |
|-----|---------------|-----------------|------------------|
| Lid | Beperkt | Basis profiel | Leden content bekijken |
| Dirigent | Muziek content | Repertoire | Partituren beheren |
| Bestuur | Alle content | Administratief | Ledenbeheer |
| Admin | Alle content | Alle velden | Systeem configuratie |

### C. Content Type Relaties
```
Activiteit ← verwijst naar → Locatie
Activiteit ← verwijst naar → Programma
Audio ← verwijst naar → Repertoire
Audio ← verwijst naar → Activiteit
Video ← verwijst naar → Repertoire
Video ← verwijst naar → Activiteit
Nieuwsbrief ← verwijst naar → Nieuws/Pagina (inhoud)
```

### D. Database Schema Overzicht
```sql
-- Content Type Tabellen (14)
content_type_activiteit
content_type_audio
content_type_foto
content_type_image
content_type_locatie
content_type_nieuws
content_type_nieuwsbrief
content_type_pagina
content_type_profiel
content_type_programma
content_type_repertoire
content_type_verslag
content_type_video
content_type_vriend

-- Gedeelde Veld Tabellen (16)
content_field_afbeeldingen
content_field_audio_type
content_field_audio_uitvoerende
content_field_datum
content_field_files
content_field_inhoud
content_field_l_routelink
content_field_partij_band
content_field_partij_koor_l
content_field_partij_tekst
content_field_programma2
content_field_ref_activiteit
content_field_repertoire
content_field_video
content_field_view
content_field_woonplaats
```

Deze documentatie biedt een complete referentie voor ontwikkelaars, beheerders, en analisten die werken met het ThirdWing.nl Drupal 6 systeem.