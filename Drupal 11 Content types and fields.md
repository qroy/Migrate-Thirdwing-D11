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
**Beschrijving:** Koorrepeties, uitvoeringen en andere activiteiten  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_koor_aanwezig` | list_string | Koor Aanwezig | 1 | **Opties:** `alle` (Alle), `bas` (Bas), `tenor` (Tenor), `alt` (Alt), `sopraan1` (1e Sopraan), `sopraan2` (2e Sopraan), `band` (Band), `diversen` (Diversen) |
| `field_keyboard` | boolean | Keyboard | 1 | - |
| `field_gitaar` | text_long | Gitaar | 1 | - |
| `field_basgitaar` | text_long | Basgitaar | 1 | - |
| `field_drums` | text_long | Drums | 1 | - |
| `field_vervoer` | text_long | Vervoer | 1 | - |
| `field_sleepgroep` | list_string | Sleepgroep | 1 | **Opties:** `I`, `II`, `III`, `IV`, `V`, `OG`, `-` |
| `field_sleepgroep_aanwezig` | list_string | Sleepgroep Aanwezig | 1 | **Opties:** `alle` (Alle), `I`, `II`, `III`, `IV`, `V`, `OG`, `-` (Geen) |
| `field_kledingcode` | text_long | Kledingcode | 1 | - |
| `field_l_bijzonderheden` | text_long | Locatie Bijzonderheden | 1 | - |
| `field_ledeninfo` | text_long | Ledeninformatie | 1 | filtered_html format |
| `field_bijzonderheden` | text_long | Algemene Bijzonderheden | 1 | - |
| `field_sleepgroep_terug` | list_string | Sleepgroep Terug | 1 | **Opties:** `I`, `II`, `III`, `IV`, `V`, `OG`, `-` |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_datum` | datetime | Datum | 1 | date and time |
| `field_locatie` | entity_reference | Locatie | 1 | target_type: node, target_bundles: [locatie] |
| `field_programma` | entity_reference | Programma | unlimited | target_type: node, target_bundles: [programma] |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_background` | entity_reference | Achtergrond | 1 | target_type: media, target_bundles: [image] |
| `field_huiswerk` | entity_reference | Huiswerk | unlimited | target_type: media, target_bundles: [document] |

---

### 2. **Fotoalbum** (Fotoalbums)
**Beschrijving:** Fotoalbums en galerijen  
**Machine Name:** `fotoalbum`  
**Titel Label:** Titel  
**Heeft Body:** Ja (Omschrijving)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_fotoalbum_type` | list_string | Fotoalbum Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | - |

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
**Beschrijving:** Muziekprogramma's voor activiteiten  
**Titel Label:** Programma  
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_repertoire` | entity_reference | Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |

#### Gedeelde Velden Gebruikt: Geen

---

### 7. **Repertoire** (Repertoire)
**Beschrijving:** Muziekstukken en liedjes  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_componist` | string | Componist | 1 | max_length: 255 |
| `field_arrangeur` | string | Arrangeur | 1 | max_length: 255 |
| `field_uitgever` | string | Uitgever | 1 | max_length: 255 |
| `field_tekst_van` | string | Tekst van | 1 | max_length: 255 |

#### Gedeelde Velden Gebruikt: Geen

**Notitie:** Partituur bestanden worden nu beheerd via **Document Media Entities** met reverse referenties naar repertoire items. Zie Media Bundle sectie voor details.

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
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | - |

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

---

### 4. **Document Bundle** (`document`)
**Beschrijving:** PDF's, Word documenten en partituren (vervangt D6 filefields)  
**Bron Plugin:** `file`  
**Bron Veld:** `field_media_document`  
**Bestandsextensies:** pdf, doc, docx, txt

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_document` | file | Document | 1 | file_extensions: pdf doc docx txt |
| `field_document_soort` | list_string | Document Soort | 1 | **Opties:** `partituur`, `huiswerk`, `algemeen` |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] | - |

#### **🎼 Partituur Architectuur Wijziging**

**Kritieke Architecturale Verandering in D11:**

**D6 Benadering:**
- Partituur bestanden werden opgeslagen als direct gekoppelde velden op Repertoire content
- `field_partij_band_fid`, `field_partij_koor_l_fid`, `field_partij_tekst_fid`

**D11 Nieuwe Benadering:**
- **Document Media Entities** met `field_document_soort = "partituur"`
- **Reverse Referentie:** Document → Repertoire via `field_gerelateerd_repertoire`
- Maakt flexibele partituur organisatie mogelijk met metadata

**Query Implicaties:**
- **Oude D6/D11 Methode:** Query repertoire node voor bijgevoegde partituur bestanden
- **Nieuwe D11 Methode:** Query document media entities waar `field_gerelateerd_repertoire` = repertoire node ID

**Voordelen:**
- Betere metadata voor partituren (toegang, categorisatie)
- Flexibele één-op-veel relaties
- Herbruikbare partituren over meerdere repertoire items
- Consistente media behandeling door het systeem

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
| `field_positie` | list_string | Positie | 1 | **Opties:** `voorzitter`, `secretaris`, `penningmeester`, `pr`, `regie`, `webmaster`, `tec`, `dirigent`, `toets`, `gitaar`, `bas`, `drums`, `ogl`, `og2`, `og3`, `lid` |

### Communicatie Voorkeuren:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_ontvang_mail` | boolean | E-mail ontvangen | 1 | - |
| `field_ontvang_nieuwsbrief` | boolean | Nieuwsbrief ontvangen | 1 | - |
| `field_ontvang_agenda` | boolean | Agenda ontvangen | 1 | - |
| `field_toon_contactgegevens` | boolean | Contactgegevens tonen | 1 | - |
| `field_toon_fotos` | boolean | Foto's tonen | 1 | - |

### Technische/Website Gegevens:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_website` | link | Website | 1 | - |
| `field_twitter` | string | Twitter | 1 | max_length: 255 |
| `field_facebook` | string | Facebook | 1 | max_length: 255 |
| `field_last_login` | datetime | Laatste login | 1 | timestamp |
| `field_user_picture` | entity_reference | Profielfoto | 1 | target_type: media, target_bundles: [image] |

---

## 📋 **Toegangscontrole & Taxonomie**

### Toegang Taxonomie:
Alle content en media kunnen worden gecategoriseerd met toegangsniveaus:

- **Publiek**: Iedereen kan bekijken
- **Leden**: Alleen koorleden
- **Bestuur**: Alleen bestuursleden
- **Privé**: Beperkte toegang

### Media Toegangscontrole:
Alle media bundles hebben `field_toegang` velden voor granulaire toegangscontrole per bestand.

---

## 🔗 **Gedeelde Velden (13 totaal)**

Deze velden zijn beschikbaar voor gebruik door meerdere content types:

### Basis Content Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | activiteit, fotoalbum, nieuws, pagina, vriend |
| `field_files` | entity_reference | Bestandsbijlages | nieuws, pagina |
| `field_datum` | datetime | Datum | activiteit, fotoalbum |
| `field_view` | string | Extra inhoud | pagina |

### Activiteit Gerelateerde Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_ref_activiteit` | entity_reference | Activiteit | fotoalbum, nieuws |
| `field_locatie` | entity_reference | Locatie | activiteit |
| `field_programma` | entity_reference | Programma | activiteit |
| `field_background` | entity_reference | Achtergrond | activiteit |
| `field_huiswerk` | entity_reference | Huiswerk | activiteit |

### Locatie Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_l_routelink` | link | Route | locatie |

### Gebruiker Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_woonplaats` | string | Woonplaats | vriend (+ user profiles) |

### Media/Content Relatie Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_repertoire` | entity_reference | Repertoire | programma |
| `field_audio_uitvoerende` | string | Uitvoerende | Gebruikt in media bundles |

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
4. **Admin Training:** Nieuwe workflow voor partituur beheer

---

## 📊 **Content Relaties Schema**

```
Activiteit ←→ Locatie (many-to-one)
Activiteit ←→ Programma (many-to-many)
Programma ←→ Repertoire (many-to-many)
Fotoalbum ←→ Activiteit (many-to-one)
Nieuws ←→ Activiteit (many-to-one)
Document Media ←→ Repertoire (many-to-many)
Audio Media ←→ Repertoire (many-to-many)
Audio Media ←→ Activiteit (many-to-one)
Video Media ←→ Repertoire (many-to-many)
Video Media ←→ Activiteit (many-to-one)
All Content ←→ Image Media (many-to-many)
```

---

## 📋 **Wijzigingslog**

### Versie 1.1 (Augustus 2025):
- ✅ **WIJZIGING**: Content type "Foto" hernoemd naar "Fotoalbum"
- ✅ **WIJZIGING**: Machine name `foto` gewijzigd naar `fotoalbum`
- ✅ **NIEUW**: Toegevoegd `field_fotoalbum_type` veld
- ✅ **NIEUW**: Toegevoegd `field_datum` veld aan Fotoalbum
- ✅ **NIEUW**: Toegevoegd `field_ref_activiteit` veld aan Fotoalbum
- ✅ **BREAKING**: Alle migratie scripts moeten worden bijgewerkt
- ✅ **BREAKING**: URL structuur wijzigt van `/node/add/foto` naar `/node/add/fotoalbum`

### Versie 1.0 (Juli 2025):
- Initiële release met volledige D6 naar D11 migratie ondersteuning
- Alle 8 content types geïmplementeerd
- 4 media bundles geconfigureerd
- Gebruiker profiel velden systeem
- Automatische EXIF datum extractie
- Partituur architectuur herstructurering

---

*Laatst Bijgewerkt: Augustus 2025*  
*Module Versie: 1.1 - Volledig Nederlandse Documentatie met Fotoalbum Updates*  
*Drupal Compatibiliteit: 11.x*  
*Alle UI Elementen: Nederlands*