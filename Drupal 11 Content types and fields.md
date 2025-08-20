# Thirdwing D11 Content Types, Media Bundles en Velden - Volledige Nederlandse Documentatie

## 📋 **Overzicht Migratie Strategie**

**Migratie Benadering:**
- Module wordt geïnstalleerd op een schone Drupal 11 installatie
- Oude D6 site blijft actief tot nieuwe site compleet is en dient als backup voor alle data
- Reguliere syncs van oud naar nieuw met bijgewerkte content
- Altijd om bevestiging vragen voor het starten van coding
- Gebruik exacte specificaties in de documentatie voor alle velden, veldgroepen, content types en permissions
- **Gebruik altijd Nederlands voor UI elementen**

---

## 📊 **Migratie Overzicht**

**Content Types:** 8 content types (gemigreerd van D6)  
**Media Bundles:** 4 media bundles (vervangt afgekeurde content types)  
**Gebruiker Profiel Velden:** Vervangt Profiel content type  
**Gedeelde Velden:** 13 velden beschikbaar voor alle content types

---

## 🗂️ **Content Types (8 totaal)**

### 1. **Activiteit** (Activiteiten)
**Beschrijving:** Een activiteit (uitvoering, repetitie)  
**Titel Label:** Omschrijving  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_koor_aanwezig` | string | Koor Aanwezig | 1 | max_length: 255 **← D6: field_tijd_aanwezig** |
| `field_keyboard` | list_string | Toetsenist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_gitaar` | list_string | Gitarist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_basgitaar` | list_string | Basgitarist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_drums` | list_string | Drummer | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_vervoer` | string | Karrijder | 1 | max_length: 255 |
| `field_sleepgroep` | list_string | Sleepgroep | 1 | **Opties:** `I`, `II`, `III`, `IV`, `V`, `*` |
| `field_sleepgroep_aanwezig` | string | Sleepgroep Aanwezig | 1 | max_length: 255 |
| `field_kledingcode` | string | Kledingcode | 1 | max_length: 255 |
| `field_locatie` | entity_reference | Locatie | 1 | target_type: node, target_bundles: [locatie] |
| `field_l_bijzonderheden` | text_long | Bijzonderheden locatie | 1 | - |
| `field_bijzonderheden` | string | Bijzonderheden | 1 | max_length: 255 |
| `field_background` | entity_reference | Achtergrond | 1 | target_type: media, target_bundles: [image] |
| `field_sleepgroep_terug` | list_string | Sleepgroep terug | 1 | **Opties:** `I`, `II`, `III`, `IV`, `V`, `*` |
| `field_huiswerk` | entity_reference | Huiswerk | 1 | target_type: media, target_bundles: [document] |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_programma2` | entity_reference | Programma | unlimited | target_type: node, target_bundles: [programma] |
| `field_datum` | datetime | Datum en tijd | 1 | datetime with time |

#### Veldgroepen:
- **Achtergrond**: field_background
- **Bijzonderheden**: field_bijzonderheden, field_kledingcode
- **Locatie**: field_locatie, field_l_bijzonderheden
- **Bestanden**: field_files
- **Afbeeldingen**: field_afbeeldingen
- **Logistiek**: field_basgitaar, field_drums, field_gitaar, field_keyboard, field_sleepgroep, field_sleepgroep_aanwezig, field_sleepgroep_terug, field_koor_aanwezig, field_vervoer
- **Programma**: field_programma2

---

### 2. **Foto** (Foto's)
**Beschrijving:** Fotoalbums en galerijen  
**Titel Label:** Titel  
**Heeft Body:** Ja (Omschrijving)

#### Content Type Specifieke Velden: Geen

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |

---

### 3. **Locatie** (Locaties)
**Beschrijving:** Locatie-informatie voor activiteiten  
**Titel Label:** Naam  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_l_adres` | string | Adres | 1 | max_length: 255 |
| `field_l_plaats` | string | Plaats | 1 | max_length: 255 |
| `field_l_postcode` | string | Postcode | 1 | max_length: 255 |
| `field_l_telefoon` | string | Telefoon | 1 | max_length: 255 |
| `field_l_contact` | string | Contactpersoon | 1 | max_length: 255 |
| `field_l_email` | email | E-mail | 1 | - |
| `field_l_website` | link | Website | 1 | - |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_l_routelink` | link | Route | 1 | - |

---

### 4. **Nieuws** (Nieuws)
**Beschrijving:** Nieuwsberichten en aankondigingen  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden: Geen

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |

---

### 5. **Pagina** (Pagina's)
**Beschrijving:** Statische pagina's  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden: Geen

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_view` | string | Extra inhoud | 1 | viewfield reference |

---

### 6. **Programma** (Programma's)
**Beschrijving:** Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst  
**Titel Label:** Titel  
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_prog_type` | list_string | Type | 1 | **Opties:** `programma` (Programma onderdeel), `nummer` (Nummer) |

#### Gedeelde Velden Gebruikt: Geen

---

### 7. **Repertoire** (Repertoire)
**Beschrijving:** Stuk uit het repertoire  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_rep_arr` | string | Arrangeur | 1 | max_length: 255 |
| `field_rep_arr_jaar` | integer | Arrangeur Jaar | 1 | - |
| `field_rep_componist` | string | Componist | 1 | max_length: 255 |
| `field_rep_componist_jaar` | integer | Componist Jaar | 1 | - |
| `field_rep_genre` | list_string | Genre | 1 | **Opties:** `pop` (Pop), `musical_film` (Musical / Film), `geestelijk_gospel` (Geestelijk / Gospel) |
| `field_rep_sinds` | integer | Sinds | 1 | - |
| `field_rep_uitv` | string | Uitvoerende | 1 | max_length: 255 |
| `field_rep_uitv_jaar` | integer | Jaar uitvoering | 1 | - |
| `field_klapper` | boolean | Actueel | 1 | - |
| `field_audio_nummer` | string | Nummer | 1 | max_length: 255 |
| `field_audio_seizoen` | list_string | Seizoen | 1 | **Opties:** `regulier` (Regulier), `kerst` (Kerst) |

#### Gedeelde Velden Gebruikt: Geen

#### Veldgroepen:
- **Arrangeur**: field_rep_arr, field_rep_arr_jaar
- **Componist**: field_rep_componist, field_rep_componist_jaar
- **Informatie**: field_audio_nummer, field_audio_seizoen, field_klapper, field_rep_genre, field_rep_sinds
- **Uitvoerende**: field_rep_uitv, field_rep_uitv_jaar

---

### 8. **Vriend** (Vrienden)
**Beschrijving:** Vrienden van de vereniging  
**Titel Label:** Naam  
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_vriend_website` | link | Website | 1 | - |
| `field_vriend_soort` | list_string | Soort | 1 | **Opties:** `financieel`, `niet-financieel`, `materieel` |
| `field_vriend_benaming` | list_string | Benaming | 1 | **Opties:** `vriend`, `vriendin`, `vrienden` |
| `field_vriend_periode_tot` | integer | Vriend t/m | 1 | **Opties:** `2008`, `2009`, `2010`, `2011`, `2012`, `2013`, `2014`, `2015`, `2016`, `2017`, `2018`, `2019`, `2020`, `2021`, `2022`, `2023`, `2024`, `2025` |
| `field_vriend_periode_vanaf` | integer | Vriend vanaf | 1 | **Opties:** `2008`, `2009`, `2010`, `2011`, `2012`, `2013`, `2014`, `2015`, `2016`, `2017`, `2018`, `2019`, `2020`, `2021`, `2022`, `2023`, `2024`, `2025` |
| `field_vriend_duur` | list_string | Vriendlengte | 1 | **Opties:** `0-1-jaar` (0 t/m 1 jaar), `2-5-jaar` (2 t/m 5 jaar), `6-plus-jaar` (6 jaar en langer) |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |

---

### 9. **Webform** (Webformulier)
**Beschrijving:** Maak een nieuw formulier of vragenlijst toegankelijk voor gebruikers  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden: Geen
#### Gedeelde Velden Gebruikt: Geen

---

## 🎬 **Media Bundles (4 totaal)**

### Ontworpen Media Bundle Architectuur

Het media bundle systeem is zorgvuldig ontworpen om alle D6 bestandstypes te behandelen met juiste metadata en relaties. Elke bundle bevat specifieke velden voor categorisatie, toegangscontrole en content relaties.

### 1. **Image Bundle** (`image`)
**Beschrijving:** Foto's, afbeeldingen en graphics (vervangt Image content type)  
**Bron Plugin:** `image`  
**Bron Veld:** `field_media_image`  
**Bestandsextensies:** jpg, jpeg, png, gif, webp

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen | Auto-Populatie |
|-----------|-----------|-------|---------------|-------------------|------------------|
| `field_media_image` | image | Afbeelding | 1 | file_extensions: jpg jpeg png gif webp | - |
| `field_datum` | datetime | Datum | 1 | date only | **🔄 AUTO: EXIF → Bestandsdatum** |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | - |

#### **🆕 Automatische EXIF Datum Extractie**

Het `field_datum` veld wordt **automatisch gevuld** volgens de volgende prioriteit:

**Extractie Volgorde:**
1. **EXIF DateTimeOriginal** - Wanneer de foto origineel gemaakt is
2. **EXIF DateTime** - Algemene EXIF datum
3. **EXIF DateTimeDigitized** - Wanneer de foto gedigitaliseerd is  
4. **Bestandsdatum** - Upload/aanmaak datum als fallback

**Ondersteunde Formaten:**
- **JPEG** - Volledige EXIF ondersteuning
- **TIFF** - Volledige EXIF ondersteuning
- **PNG/GIF/WebP** - Alleen bestandsdatum fallback

---

### 2. **Audio Bundle** (`audio`)
**Beschrijving:** MP3 en MIDI audiobestanden (vervangt Audio content type)  
**Bron Plugin:** `audio`  
**Bron Veld:** `field_media_audio_file`  
**Bestandsextensies:** mp3, midi, mid, wav

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_audio_file` | file | Audio bestand | 1 | file_extensions: mp3 midi mid wav |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_audio_type` | list_string | Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_audio_bijz` | string | Bijzonderheden | 1 | max_length: 255 |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

---

### 3. **Video Bundle** (`video`)
**Beschrijving:** Embedded video's van YouTube/Vimeo (vervangt Video content type)  
**Bron Plugin:** `video_embed_field`  
**Bron Veld:** `field_media_video_embed_field`

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_video_embed_field` | video_embed_field | Video | 1 | - |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_audio_type` | list_string | Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

---

### 4. **Document Bundle** (`document`)
**Beschrijving:** PDF's, Word documenten, MuseScore bestanden, en Verslag rapporten  
**Bron Plugin:** `file`  
**Bron Veld:** `field_media_document`  
**Bestandsextensies:** pdf, doc, docx, txt, xls, xlsx, mscz

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen | Vereist Wanneer |
|-----------|-----------|-------|---------------|-------------------|------------------|
| `field_media_document` | file | Document | 1 | file_extensions: pdf doc docx txt xls xlsx mscz | Altijd |
| `field_document_soort` | list_string | Document Soort | 1 | **Opties:** `verslag`, `partituur`, `huiswerk`, `overig` | Altijd |
| `field_verslag_type` | list_string | Verslag Type | 1 | **Opties:** `algemene_ledenvergadering`, `bestuursvergadering`, `combo_overleg`, `concertcommissie`, `jaarevaluatie_dirigent`, `jaarverslag`, `overige_vergadering`, `vergadering_muziekcommissie` | field_document_soort = verslag |
| `field_datum` | datetime | Datum | 1 | date only | field_document_soort = verslag |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] | field_document_soort = partituur |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | Altijd |

### Conditionele Veld Vereisten

Het Document Bundle gebruikt conditionele veld vereisten gebaseerd op het document type:

#### **Wanneer `field_document_soort` = "verslag":**
- **`field_verslag_type`** - Vereist om het type vergaderverslag te specificeren
- **`field_datum`** - Vereist om de datum van de vergadering/verslag vast te leggen

#### **Wanneer `field_document_soort` = "partituur":**
- **`field_gerelateerd_repertoire`** - Vereist om bladmuziek te koppelen aan specifieke repertoire stukken

#### **Wanneer `field_document_soort` = "huiswerk" of "overig":**
- Geen aanvullende vereiste velden naast de basis document en toegang velden

---

## 👤 **Gebruiker Profiel Velden (In plaats van Profiel Content Type)**

De D6 site gebruikt een **Profiel** content type, maar in D11 worden deze geconverteerd naar **Gebruiker Profiel Velden**:

### Persoonlijke Gegevens:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_emailbewaking` | string | Email origineel | 1 | max_length: 255 |
| `field_voornaam` | string | Voornaam | 1 | max_length: 255 |
| `field_achternaam_voorvoegsel` | string | Tussenvoegsel | 1 | max_length: 255 |
| `field_achternaam` | string | Achternaam | 1 | max_length: 255 |
| `field_geboortedatum` | datetime | Geboortedatum | 1 | date only |
| `field_geslacht` | list_string | Geslacht | 1 | **Opties:** `m` (Man), `v` (Vrouw) |
| `field_adres` | string | Adres | 1 | max_length: 255 |
| `field_postcode` | string | Postcode | 1 | max_length: 255 |
| `field_telefoon` | string | Telefoon | 1 | max_length: 255 |
| `field_notes` | text_long | Notities | 1 | - |
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_mobiel` | string | Mobiel | 1 | max_length: 255 |

### Lidmaatschap Gegevens:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_lidsinds` | datetime | Lid sinds | 1 | date only |
| `field_uitkoor` | datetime | Uit koor | 1 | date only |
| `field_karrijder` | boolean | Karrijder | 1 | - |
| `field_koor` | list_string | Koorfunctie | 1 | **Opties:** `B` (Bas), `A` (Tenor), `E` (Alt), `C` (1e Sopraan), `D` (2e Sopraan), `Y1` (Dirigent), `Z1` (Toetsenist), `Z2` (Gitarist), `Z3` (Bassist), `Z4` (Drummer), `Z5` (Techniek en percussie) |
| `field_sleepgroep_1` | list_string | Sleepgroep | 1 | **Opties:** `I`, `II`, `III`, `IV`, `V`, `OG`, `-` |
| `field_positie` | list_string | Positie | 1 | **Zie volledige koorpositie mapping hieronder** |

### Koorpositie Mapping (field_positie):
**Rij 4 (101-116):** `4x01`, `4x02`, `4x03`, `4x04`, `4x05`, `4x06`, `4x07`, `4x08`, `4x09`, `4x10`, `4x11`, `4x12`, `4x13`, `4x14`, `4x15`, `4x16`

**Rij 3 (201-216):** `3x01`, `3x02`, `3x03`, `3x04`, `3x05`, `3x06`, `3x07`, `3x08`, `3x09`, `3x10`, `3x11`, `3x12`, `3x13`, `3x14`, `3x15`, `3x16`

**Rij 2 (301-316):** `2x01`, `2x02`, `2x03`, `2x04`, `2x05`, `2x06`, `2x07`, `2x08`, `2x09`, `2x10`, `2x11`, `2x12`, `2x13`, `2x14`, `2x15`, `2x16`

**Rij 1 (401-416):** `1x01`, `1x02`, `1x03`, `1x04`, `1x05`, `1x06`, `1x07`, `1x08`, `1x09`, `1x10`, `1x11`, `1x12`, `1x13`, `1x14`, `1x15`, `1x16`

**Band (501-504):** `Band 1`, `Band 2`, `Band 3`, `Band 4`

**Speciale Posities:** `Dirigent` (601), `Niet ingedeeld` (701)

### Commissie Functies:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_bestuur` | list_string | Functie Bestuur | 1 | **Opties:** `voorzitter`, `secretaris`, `penningmeester`, `bestuurslid` |
| `field_functie_mc` | list_string | Functie Muziekcommissie | 1 | **Opties:** `bestuurslid`, `voorzitter`, `secretaris`, `dirigent`, `contactpersoon_band`, `lid` |
| `field_functie_concert` | list_string | Functie Commissie Concerten | 1 | **Opties:** `bestuurslid`, `lid` |
| `field_functie_feest` | list_string | Functie Feestcommissie | 1 | **Opties:** `bestuurslid`, `lid` |
| `field_functie_regie` | list_string | Functie Commissie Koorregie | 1 | **Opties:** `bestuurslid`, `lid` |
| `field_functie_ir` | list_string | Functie Commissie Interne Relaties | 1 | **Opties:** `bestuurslid`, `lid`, `interne_relaties` |
| `field_functie_pr` | list_string | Functie Commissie PR | 1 | **Opties:** `bestuurslid`, `website`, `social_media`, `lid` |
| `field_functie_tec` | list_string | Functie Technische Commissie | 1 | **Opties:** `bestuurslid`, `lid` |
| `field_functie_lw` | list_string | Functie ledenwerf | 1 | **Opties:** `bestuurslid`, `lid` |
| `field_functie_fl` | list_string | Functie Faciliteiten | 1 | **Opties:** `bestuurslid`, `lid` |

---

## 🔗 **Gedeelde Velden Beschikbaar voor Alle Content Types**

De volgende velden zijn beschikbaar als gedeelde velden die aan elk content type gekoppeld kunnen worden:

### Kern Gedeelde Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_audio_type` | list_string | Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_datum` | datetime | Datum en tijd | 1 | datetime with time |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_inhoud` | entity_reference | Inhoud | unlimited | target_type: node, target_bundles: [nieuws, activiteit, programma] |
| `field_l_routelink` | link | Route | 1 | - |
| `field_programma2` | entity_reference | Programma | unlimited | target_type: node, target_bundles: [programma] |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_video` | text_long | Video | 1 | embedded video |
| `field_view` | string | Extra inhoud | 1 | viewfield reference |
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |

---

## 🔄 **Migratie Dependencies**

### Fase 1: Fundering
1. Taxonomie vocabulaires en termen
2. Gebruikersrollen en rechten
3. Media bundles en velden
4. Content types en velden

### Fase 2: Kern Content
1. Gebruikers en profielen
2. Bestanden en media entiteiten
3. Locaties
4. Repertoire en programma's (zonder partituur bestanden)

### Fase 3: Activiteit Content
1. Activiteiten en evenementen
2. Nieuwsartikelen
3. Pagina's en statische content
4. Vrienden en supporters

### Fase 4: Document Media Migratie
1. Document media entiteiten met reverse referenties
2. Partituur bestanden migratie met repertoire koppeling
3. Content referenties validatie
4. Media associaties verificatie

---

## 🎯 **Kritieke Migratie Wijzigingen - Partituur Architectuur**

### **Veld Naam Wijzigingen:**

**D6 → D11 Veld Herbenaming:**
- `field_tijd_aanwezig` → `field_koor_aanwezig` (Activiteit content type)
  - **Reden:** Duidelijkere veldnaam die beter de functie beschrijft
  - **Label blijft:** "Koor Aanwezig"

### **Data Transformatie Vereisten:**

#### **D6 → D11 Partituur Migratie:**
1. **D6 Repertoire → D11 Repertoire:** 
   - `field_partij_band_fid` → Maak Document media entiteit aan
   - `field_partij_koor_l_fid` → Maak Document media entiteit aan
   - `field_partij_tekst_fid` → Maak Document media entiteit aan

2. **Nieuwe Document Media Entiteit Aanmaak:**
   - Voor elke `field_partij_*` bestand in D6 repertoire
   - Stel `field_document_soort` = "partituur" in
   - Stel `field_gerelateerd_repertoire` → terug naar originele repertoire node

### **Migratie Scripts Impact:**
1. **Repertoire migratie mag NIET direct partituur bestanden migreren**
2. **Aparte Document Media migratie** voor partituur bestanden
3. **Reverse mapping logica:** D6 repertoire.field_partij_* → D11 document_media.field_gerelateerd_repertoire

### **Database Query Wijzigingen:**
- **Oude D6/D11 benadering:** "Haal partituren op voor repertoire X" → Query repertoire velden
- **Nieuwe D11 benadering:** "Haal partituren op voor repertoire X" → Query document media waar gerelateerd_repertoire = X

### **Admin Interface Impact:**
- **Repertoire beheer:** Geen partituur upload meer tijdens repertoire bewerking
- **Document beheer:** Nieuwe workflow - upload document → koppel aan repertoire
- **Views/Lijsten:** Aangepaste queries voor partituur weergave

### **Migratie Dependencies Volgorde:**
1. **Eerst:** Migreer Repertoire content (zonder partituren)
2. **Daarna:** Migreer Document media met reverse referenties
3. **Validatie:** Verifieer dat alle partituren correct gekoppeld zijn
4. **Testen:** Zorg ervoor dat admin interface werkt met nieuwe relatie richting

### **Voordelen van Nieuwe Architectuur:**
- **Logische relatie:** Document kent zijn repertoire (meer intuïtief)
- **Multiple repertoire koppeling:** Eén document kan aan meerdere repertoire stukken gekoppeld worden
- **Document classificatie:** Via `field_document_soort` = "partituur"
- **Verminderde redundantie:** Geen 3 aparte partituur velden nodig
- **Verhoogde flexibiliteit:** Eenvoudig nieuwe document types toevoegen
- **Betere admin UX:** Directe repertoire koppeling tijdens document upload

---

## 📋 **Installatie Vereisten**

### Pre-Installatie
- Schone Drupal 11 installatie
- Database toegang tot D6 bron
- Vereiste contrib modules geïnstalleerd
- Bestandssysteem rechten geconfigureerd
- PHP EXIF extensie voor automatische afbeelding datum extractie

### Post-Installatie
- Content moderatie workflow setup
- Rol-gebaseerde rechten configuratie
- Media bestand directory aanmaak
- URL alias generatie
- EXIF datum extractie testen

---

## ✨ **Belangrijkste Migratie Voordelen**

- **Gecentraliseerd Media Beheer:** Alle media behandeld via juiste media bundles
- **Betere Gebruikerservaring:** Profiel velden geïntegreerd in gebruikersaccounts
- **Consistente Veld Structuur:** Gedeelde velden verminderen duplicatie
- **Moderne Architectuur:** Maakt gebruik van D11's media systeem mogelijkheden
- **Gestroomlijnde Content Types:** Focus op essentiële content types alleen
- **Automatische Metadata Extractie:** EXIF datum extractie voor afbeeldingen
- **Intelligente Fallbacks:** Robuuste datum behandeling met meerdere fallback opties
- **Verbeterde Content Relaties:** Betere koppeling tussen content en media

---

## 🏷️ **Taxonomie Vocabulaires**

### Toegang Vocabulaire (`toegang`)
**Beschrijving:** Toegangscontrole voor content en media
**Termen:**
- `publiek` - Toegankelijk voor iedereen
- `leden` - Alleen toegankelijk voor leden
- `bestuur` - Alleen toegankelijk voor bestuur
- `commissies` - Toegankelijk voor commissieleden
- `band` - Alleen toegankelijk voor bandleden
- `dirigent` - Alleen toegankelijk voor dirigent
- `aspiranten` - Toegankelijk voor aspirant-leden

---

## 🔐 **Gebruikersrollen en Rechten**

### Rol Hiërarchie:
1. **Anonieme Gebruiker** - Basis publieke toegang
2. **Geauthenticeerde Gebruiker** - Ingelogde gebruiker basis toegang
3. **Aspirant-lid** - Beperkte lidtoegang
4. **Lid** - Volledige lidtoegang
5. **Band** - Bandlid specifieke toegang
6. **Dirigent** - Dirigent specifieke toegang
7. **Commissie rollen:**
   - **Muziekcommissie** - Repertoire en muziek beheer
   - **Bestuur** - Administratieve toegang
   - **Feestcommissie** - Evenement organisatie
   - **Commissie IR** - Interne relaties
   - **Commissie Concerten** - Concert organisatie
   - **Commissie Koorregie** - Koor management
8. **Auteur** - Content creatie rechten
9. **Redacteur** - Content bewerking rechten
10. **Webmaster** - Technische beheer
11. **Admin** - Volledige systeem toegang

### Rol-specifieke Content Toegang:
- **Publiek:** Basis website content
- **Leden:** Persoonlijke gegevens, activiteiten, repertoire
- **Band:** Bandpartituren, instrumentale content
- **Dirigent:** Alle partituren, koor management
- **Bestuur:** Administratieve content, ledengegevens
- **Commissies:** Commissie-specifieke documenten en toegang

---

## 📊 **Migratie Statistieken**

### Content Volume Verwachting:
- **Gebruikers:** ~150-200 gebruikers
- **Activiteiten:** ~50-100 per jaar
- **Repertoire:** ~200-300 stukken
- **Media Bestanden:** ~500-1000 bestanden
- **Nieuws Items:** ~50-100 per jaar
- **Locaties:** ~20-30 locaties

### Migratie Prestatie Doelen:
- **Volledige migratie:** < 2 uur
- **Gebruiker migratie:** < 30 minuten
- **Media migratie:** < 1 uur
- **Content migratie:** < 30 minuten
- **Validatie:** < 15 minuten

---

## 🧪 **Kwaliteitsborging**

### Validatie Checklist:
- [ ] Alle content types aangemaakt
- [ ] Alle velden geconfigureerd met juiste opties
- [ ] Media bundles werkend
- [ ] Gebruiker profiel velden actief
- [ ] Taxonomie termen geïmporteerd
- [ ] Rol rechten geconfigureerd
- [ ] Migratie scripts getest
- [ ] EXIF datum extractie werkend
- [ ] Display configuraties ingesteld
- [ ] URL aliassen gegenereerd

### Test Scenario's:
1. **Gebruiker Registratie:** Nieuwe gebruiker kan registreren en profiel invullen
2. **Content Creatie:** Gebruikers kunnen content aanmaken met media uploads
3. **Toegangscontrole:** Rol-gebaseerde toegang wordt correct afgedwongen
4. **Media Upload:** Alle media types kunnen worden geüpload en weergegeven
5. **Datum Extractie:** EXIF datums worden automatisch ingevuld
6. **Partituur Systeem:** Document-repertoire koppelingen werken correct

---

## 📞 **Ondersteuning & Contact**

Voor problemen, vragen, of bijdragen:

1. **Controleer troubleshooting sectie** in de hoofddocumentatie
2. **Bekijk log bestanden** voor gedetailleerde foutinformatie  
3. **Voer validatie scripts uit** voor configuratie details
4. **Raadpleeg documentatie** voor configuratie instructies

### Belangrijke Drush Commando's:
```bash
# Valideer volledige setup
drush thirdwing:validate-all

# Migreer specifieke content type
drush migrate:import d6_thirdwing_[content_type]

# Reset migratie
drush migrate:reset d6_thirdwing_[migration_name]

# Status controle
drush migrate:status --group=thirdwing_d6
```

---

*Laatst Bijgewerkt: Augustus 2025*  
*Module Versie: 1.1 - Volledig Nederlandse Documentatie*  
*Drupal Compatibiliteit: 11.x*  
*Alle UI Elementen: Nederlands*