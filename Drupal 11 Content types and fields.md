# Thirdwing D11 Content Types, Media Bundles en Fields - Volledige Documentatie

## 📋 **Overzicht Migratie Strategie**

**Migratie Benadering:**
- Module wordt geïnstalleerd op een schone Drupal 11 installatie
- Oude D6 site blijft actief tot nieuwe site compleet is en dient als backup voor alle data
- Reguliere syncs van oud naar nieuw met bijgewerkte content
- Altijd om bevestiging vragen voor het starten van coding
- Gebruik exacte specificaties in de documentatie voor alle velden, veldgroepen, content types en permissions
- Gebruik altijd Nederlands voor UI elementen

---

## 📊 **Migratie Overzicht**

**Content Types:** 9 content types (gemigreerd van D6)  
**Media Bundles:** 4 media bundles (vervangt afgekeurde content types)  
**User Profile Fields:** Vervangt Profile content type  
**Shared Fields:** 16 velden beschikbaar voor alle content types

---

## 🗂️ **Content Types (9 totaal)**

### 1. **Activiteit** (Activities)
**Description:** Een activiteit (uitvoering, repetitie)  
**Title Label:** Omschrijving  
**Has Body:** Yes (Berichttekst)

#### Content Type Specifieke Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_tijd_aanwezig` | string | Koor Aanwezig | 1 | max_length: 255 |
| `field_keyboard` | list_string | Toetsenist | 1 | options widget |
| `field_gitaar` | list_string | Gitarist | 1 | options widget |
| `field_basgitaar` | list_string | Basgitarist | 1 | options widget |
| `field_drums` | list_string | Drummer | 1 | options widget |
| `field_vervoer` | string | Karrijder | 1 | max_length: 255 |
| `field_sleepgroep` | list_string | Sleepgroep | 1 | options widget |
| `field_sleepgroep_aanwezig` | string | Sleepgroep Aanwezig | 1 | max_length: 255 |
| `field_kledingcode` | string | Kledingcode | 1 | max_length: 255 |
| `field_locatie` | entity_reference | Locatie | 1 | target_type: node, target_bundles: [locatie] |
| `field_l_bijzonderheden` | text_long | Bijzonderheden locatie | 1 | - |
| `field_bijzonderheden` | string | Bijzonderheden | 1 | max_length: 255 |
| `field_background` | entity_reference | Achtergrond | 1 | target_type: media, target_bundles: [image] |
| `field_sleepgroep_terug` | list_string | Sleepgroep terug | 1 | options widget |
| `field_huiswerk` | entity_reference | Huiswerk | 1 | target_type: media, target_bundles: [document] |

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
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
- **Logistiek**: field_basgitaar, field_drums, field_gitaar, field_keyboard, field_sleepgroep, field_sleepgroep_aanwezig, field_sleepgroep_terug, field_tijd_aanwezig, field_vervoer
- **Programma**: field_programma2

---

### 2. **Foto** (Photo)
**Description:** Foto-album  
**Title Label:** Titel  
**Has Body:** Yes (Omschrijving)

#### Content Type Specifieke Velden: None

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_video` | text_long | Video | 1 | embedded video |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_audio_type` | list_string | Type | 1 | options widget |
| `field_datum` | datetime | Datum | 1 | datetime with time |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |

#### Veldgroepen:
- **Activiteiten**: field_audio_type, field_ref_activiteit
- **Afbeelding**: field_afbeeldingen

---

### 3. **Locatie** (Location)
**Description:** Veelvoorkomende locaties van uitvoeringen  
**Title Label:** Titel  
**Has Body:** No

#### Content Type Specifieke Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_l_adres` | string | Adres | 1 | max_length: 255 |
| `field_l_plaats` | string | Plaats | 1 | max_length: 255 |
| `field_l_postcode` | string | Postcode | 1 | max_length: 255 |

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_l_routelink` | link | Route | 1 | - |

---

### 4. **Nieuws** (News)
**Description:** Een nieuwsbericht. Dit kan een publiek nieuwsbericht zijn, maar ook een nieuwsbericht voor op de ledenpagina.  
**Title Label:** Titel  
**Has Body:** Yes (Berichttekst)

#### Content Type Specifieke Velden: None

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |

#### Veldgroepen:
- **Bestanden**: field_afbeeldingen, field_files

---

### 5. **Pagina** (Page)
**Description:** Gebruik een 'Pagina' wanneer je een statische pagina wilt toevoegen  
**Title Label:** Titel  
**Has Body:** Yes (Berichttekst)

#### Content Type Specifieke Velden: None

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_view` | string | Extra inhoud | 1 | viewfield reference |

#### Veldgroepen:
- **Extra inhoud**: field_view
- **Bestanden**: field_afbeeldingen, field_files

---

### 6. **Programma** (Program)
**Description:** Elementen voor in een programma voor een activiteit die niet voorkomen in de repertoire-lijst  
**Title Label:** Titel  
**Has Body:** No

#### Content Type Specifieke Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_prog_type` | list_string | Type | 1 | options widget |

#### Gedeelde Velden Gebruikt: None

---

### 7. **Repertoire** (Repertoire)
**Description:** Stuk uit het repertoire  
**Title Label:** Titel  
**Has Body:** Yes (Berichttekst)

#### Content Type Specifieke Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_rep_arr` | string | Arrangeur | 1 | max_length: 255 |
| `field_rep_arr_jaar` | integer | Arrangeur Jaar | 1 | - |
| `field_rep_componist` | string | Componist | 1 | max_length: 255 |
| `field_rep_componist_jaar` | integer | Componist Jaar | 1 | - |
| `field_rep_genre` | list_string | Genre | 1 | options widget |
| `field_rep_sinds` | integer | Sinds | 1 | - |
| `field_rep_uitv` | string | Uitvoering | 1 | max_length: 255 |
| `field_rep_uitv_jaar` | integer | Uitvoering Jaar | 1 | - |
| `field_positie` | list_string | Positie | 1 | options widget |
| `field_klapper` | boolean | Klapper | 1 | - |
| `field_audio_nummer` | string | Nummer | 1 | max_length: 255 |
| `field_audio_seizoen` | string | Seizoen | 1 | max_length: 255 |

#### Gedeelde Velden Gebruikt: None

#### Veldgroepen:
- **Arrangeur**: field_rep_arr, field_rep_arr_jaar
- **Componist**: field_rep_componist, field_rep_componist_jaar
- **Informatie**: field_audio_nummer, field_audio_seizoen, field_klapper, field_rep_genre, field_rep_sinds
- **Uitvoerende**: field_rep_uitv, field_rep_uitv_jaar

---

### 8. **Vriend** (Friend)
**Description:** Vrienden van de vereniging  
**Title Label:** Naam  
**Has Body:** No

#### Content Type Specifieke Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_vriend_website` | link | Website | 1 | - |
| `field_vriend_soort` | list_string | Soort | 1 | options widget |
| `field_vriend_benaming` | list_string | Benaming | 1 | options widget |
| `field_vriend_periode_tot` | integer | Vriend t/m | 1 | - |
| `field_vriend_periode_vanaf` | integer | Vriend vanaf | 1 | - |
| `field_vriend_duur` | list_string | Vriendlengte | 1 | options widget |

#### Gedeelde Velden Gebruikt:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |

---

### 9. **Webform** (Webform)
**Description:** Create a new form or questionnaire accessible to users  
**Title Label:** Titel  
**Has Body:** Yes (Berichttekst)

#### Content Type Specifieke Velden: None
#### Gedeelde Velden Gebruikt: None

---

## 🎬 **Media Bundles (4 totaal)**

### Ontworpen Media Bundle Architectuur

Het media bundle systeem is zorgvuldig ontworpen om alle D6 bestandstypes te behandelen met juiste metadata en relaties. Elke bundle bevat specifieke velden voor categorisatie, toegangscontrole en content relaties.

### 1. **Image Bundle** (`image`)
**Description:** Photos, graphics, and images (replaces Image content type)  
**Source Plugin:** `image`  
**Source Field:** `field_media_image`  
**File Extensions:** jpg, jpeg, png, gif, webp

| Field Name | Field Type | Label | Cardinality | Target/Settings | Auto-Population |
|------------|------------|-------|-------------|-----------------|------------------|
| `field_media_image` | image | Afbeelding | 1 | file_extensions: jpg jpeg png gif webp | - |
| `field_datum` | datetime | Datum | 1 | date only | **🔄 AUTO: EXIF → File Date** |
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
**Description:** MP3 and MIDI audio files (replaces Audio content type)  
**Source Plugin:** `audio`  
**Source Field:** `field_media_audio_file`  
**File Extensions:** mp3, midi, mid, wav

| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_media_audio_file` | file | Audio bestand | 1 | file_extensions: mp3 midi mid wav |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_audio_type` | list_string | Type | 1 | options widget |
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_audio_bijz` | string | Bijzonderheden | 1 | max_length: 255 |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

---

### 3. **Video Bundle** (`video`)
**Description:** Embedded videos from YouTube/Vimeo (replaces Video content type)  
**Source Plugin:** `video_embed_field`  
**Source Field:** `field_media_video_embed_field`

| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_media_video_embed_field` | video_embed_field | Video | 1 | - |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |
| `field_audio_type` | list_string | Type | 1 | options widget |
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

---

### 4. **Document Bundle** (`document`)
**Description:** PDFs, Word documents, MuseScore files, and Verslag reports  
**Source Plugin:** `file`  
**Source Field:** `field_media_document`  
**File Extensions:** pdf, doc, docx, txt, xls, xlsx, mscz

| Field Name | Field Type | Label | Cardinality | Target/Settings | Required When |
|------------|------------|-------|-------------|-----------------|---------------|
| `field_media_document` | file | Document | 1 | file_extensions: pdf doc docx txt xls xlsx mscz | Always |
| `field_document_soort` | list_string | Document Soort | 1 | options: verslag, partituur, huiswerk, overig | Always |
| `field_verslag_type` | list_string | Verslag Type | 1 | Select options: algemene_ledenvergadering, bestuursvergadering, combo_overleg, concertcommissie, jaarevaluatie_dirigent, jaarverslag, overige_vergadering, vergadering_muziekcommissie | field_document_soort = verslag |
| `field_datum` | datetime | Datum | 1 | date only | field_document_soort = verslag |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] | field_document_soort = partituur |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | Always |

### Conditionele Veld Vereisten

Het Document Bundle gebruikt conditionele veld vereisten gebaseerd op het document type:

#### **When `field_document_soort` = "verslag":**
- **`field_verslag_type`** - Required to specify the type of meeting report
- **`field_datum`** - Required to record the date of the meeting/report

#### **When `field_document_soort` = "partituur":**
- **`field_gerelateerd_repertoire`** - Required to link sheet music to specific repertoire pieces

#### **When `field_document_soort` = "huiswerk" or "overig":**
- No additional required fields beyond the base document and toegang fields

---

## 👤 **User Profile Fields (Instead of Profile Content Type)**

De D6 site gebruikt een **Profile** content type, maar in D11 worden deze geconverteerd naar **User Profile Fields**:

### User Profile Fields:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_emailbewaking` | string | Email origineel | 1 | max_length: 255 |
| `field_lidsinds` | datetime | Lid Sinds | 1 | date only |
| `field_koor` | list_string | Koorfunctie | 1 | options widget |
| `field_sleepgroep_1` | list_string | Sleepgroep | 1 | options widget |
| `field_voornaam` | string | Voornaam | 1 | max_length: 255 |
| `field_achternaam_voorvoegsel` | string | Achternaam voorvoegsel | 1 | max_length: 255 |
| `field_achternaam` | string | Achternaam | 1 | max_length: 255 |
| `field_geboortedatum` | datetime | Geboortedatum | 1 | date only |
| `field_geslacht` | list_string | Geslacht | 1 | options widget |
| `field_karrijder` | boolean | Karrijder | 1 | - |
| `field_uitkoor` | datetime | Uit koor per | 1 | date only |
| `field_adres` | string | Adres | 1 | max_length: 255 |
| `field_postcode` | string | Postcode | 1 | max_length: 255 |
| `field_telefoon` | string | Telefoon | 1 | max_length: 255 |
| `field_notes` | text_long | Notities | 1 | - |
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_positie` | list_string | Positie | 1 | options widget |
| `field_mobiel` | string | Mobiel | 1 | max_length: 255 |

### Commissie Functies:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_functie_bestuur` | list_string | Functie Bestuur | 1 | options widget |
| `field_functie_mc` | list_string | Functie Muziekcommissie | 1 | options widget |
| `field_functie_concert` | list_string | Functie Commissie Concerten | 1 | options widget |
| `field_functie_feest` | list_string | Functie Feestcommissie | 1 | options widget |
| `field_functie_regie` | list_string | Functie Commissie Koorregie | 1 | options widget |
| `field_functie_ir` | list_string | Functie Commissie Interne Relaties | 1 | options widget |
| `field_functie_pr` | list_string | Functie Commissie PR | 1 | options widget |
| `field_functie_tec` | list_string | Functie Technische Commissie | 1 | options widget |
| `field_functie_lw` | list_string | Functie ledenwerf | 1 | options widget |
| `field_functie_fl` | list_string | Functie Faciliteiten | 1 | options widget |

---

## 🔗 **Gedeelde Velden Beschikbaar voor Alle Content Types**

De volgende velden zijn beschikbaar als gedeelde velden die aan elk content type gekoppeld kunnen worden:

### Core Gedeelde Velden:
| Field Name | Field Type | Label | Cardinality | Target/Settings |
|------------|------------|-------|-------------|-----------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_audio_type` | list_string | Type | 1 | options widget |
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

### Phase 1: Foundation
1. Taxonomy vocabularies and terms
2. User roles and permissions
3. Media bundles and fields
4. Content types and fields

### Phase 2: Core Content
1. Users and profiles
2. Files and media entities
3. Locations
4. Repertoire and programs (without partituur files)

### Phase 3: Activity Content
1. Activities and events
2. News articles
3. Pages and static content
4. Friends and supporters

### Phase 4: Document Media Migration
1. Document media entities with reverse references
2. Partituur files migration with repertoire linking
3. Content references validation
4. Media associations verification

---

## 🎯 **Kritieke Migratie Wijzigingen - Partituur Architectuur**

### **Data Transformatie Vereisten:**

#### **D6 → D11 Partituur Migration:**
1. **D6 Repertoire → D11 Repertoire:** 
   - `field_partij_band_fid` → Create Document media entity 
   - `field_partij_koor_l_fid` → Create Document media entity
   - `field_partij_tekst_fid` → Create Document media entity

2. **New Document Media Entity Creation:**
   - For each `field_partij_*` file in D6 repertoire
   - Set `field_document_soort` = "partituur"
   - Set `field_gerelateerd_repertoire` → back to original repertoire node

### **Migration Scripts Impact:**
1. **Repertoire migration must NOT migrate partituur files directly**
2. **Separate Document Media migration** for partituur files
3. **Reverse mapping logic:** D6 repertoire.field_partij_* → D11 document_media.field_gerelateerd_repertoire

### **Database Query Changes:**
- **Old D6/D11 approach:** "Get partituren for repertoire X" → Query repertoire fields
- **New D11 approach:** "Get partituren for repertoire X" → Query document media where gerelateerd_repertoire = X

### **Admin Interface Impact:**
- **Repertoire management:** No more partituur upload during repertoire edit
- **Document management:** New workflow - upload document → link to repertoire
- **Views/Listings:** Modified queries for partituur display

### **Migration Dependencies Order:**
1. **First:** Migrate Repertoire content (without partituren)
2. **Then:** Migrate Document media with reverse references
3. **Validation:** Verify all partituren are correctly linked
4. **Testing:** Ensure admin interface works with new relationship direction

### **Benefits of New Architecture:**
- **Logical relationship:** Document knows its repertoire (more intuitive)
- **Multiple repertoire linking:** One document can relate to multiple repertoire pieces
- **Document classification:** Via `field_document_soort` = "partituur"
- **Reduced redundancy:** No need for 3 separate partituur fields
- **Enhanced flexibility:** Easy to add new document types
- **Better admin UX:** Direct repertoire linking during document upload

---

## 📋 **Installatie Vereisten**

### Pre-Installation
- Clean Drupal 11 installation
- Database access to D6 source
- Required contrib modules installed
- File system permissions configured
- PHP EXIF extension for automatic image date extraction

### Post-Installation
- Content moderation workflow setup
- Role-based permissions configuration
- Media file directory creation
- URL alias generation
- EXIF date extraction testing

---

## ✨ **Belangrijkste Migratie Voordelen**

- **Centralized Media Management:** All media handled through proper media bundles
- **Better User Experience:** Profile fields integrated into user accounts
- **Consistent Field Structure:** Shared fields reduce duplication
- **Modern Architecture:** Leverages D11's media system capabilities
- **Streamlined Content Types:** Focus on essential content types only
- **Automatic Metadata Extraction:** EXIF date extraction for images
- **Intelligent Fallbacks:** Robust date handling with multiple fallback options
- **Enhanced Content Relationships:** Better linking between content and media