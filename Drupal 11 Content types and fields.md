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
**Gebruiker Profiel Velden:** 32 velden - Vervangt Profiel content type volledig  
**Gedeelde Velden:** 13 velden beschikbaar voor alle content types

---

## 👤 **Gebruiker Profiel Velden (In plaats van Profiel Content Type)**

De D6 site gebruikt een **Profiel** content type, maar in D11 worden deze geconverteerd naar **Gebruiker Profiel Velden**. **ALLE 32 D6 profielvelden** worden gemigreerd:

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
| `field_notes` | text_long | Notities | 1 | formatted text |
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_mobiel` | string | Mobiel | 1 | max_length: 255 |

### Lidmaatschap Gegevens:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_lidsinds` | datetime | Lid sinds | 1 | date only |
| `field_uitkoor` | datetime | Uit koor | 1 | date only |
| `field_karrijder` | list_string | Karrijder | 1 | **Opties:** `0` (Nee), `*` (Karrijder) |
| `field_koor` | list_string | Koorfunctie | 1 | **Opties:** `B` (Bas), `A` (Tenor), `E` (Alt), `C` (1e Sopraan), `D` (2e Sopraan), `Y1` (Dirigent), `Z1` (Toetsenist), `Z2` (Gitarist), `Z3` (Bassist), `Z4` (Drummer), `Z5` (Techniek en percussie) |
| `field_positie` | list_string | Positie | 1 | **Opties:** `101` (4x01) t/m `116` (4x16), `201` (3x01) t/m `216` (3x16), `301` (2x01) t/m `316` (2x16), `401` (1x01) t/m `416` (1x16), `501` (Band 1) t/m `504` (Band 4), `601` (Dirigent), `701` (Niet ingedeeld) |
| `field_sleepgroep_1` | list_string | Sleepgroep | 1 | **Opties:** `1` (I), `2` (II), `3` (III), `4` (IV), `5` (V), `8` (OG), `9` (-) |

### Commissie Functies - Bestuur:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_bestuur` | list_string | Functie Bestuur | 1 | **Opties:** `1` (Voorzitter), `2` (Secretaris), `3` (Penningmeester), `4` (Bestuurslid) |

### Commissie Functies - Muziek:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_mc` | list_string | Functie Muziekcommissie | 1 | **Opties:** `1` (Bestuurslid), `10` (Voorzitter), `20` (Secretaris), `30` (Dirigent), `40` (Contactpersoon band), `90` (Lid) |
| `field_functie_regie` | list_string | Functie Commissie Koorregie | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |

### Commissie Functies - Evenementen:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_concert` | list_string | Functie Commissie Concerten | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |
| `field_functie_feest` | list_string | Functie Feestcommissie | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |

### Commissie Functies - Administratie:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_lw` | list_string | Functie Ledenadministratie | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |
| `field_functie_fl` | list_string | Functie Faciliteiten | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |

### Commissie Functies - Communicatie & Techniek:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_functie_pr` | list_string | Functie Commissie PR | 1 | **Opties:** `1` (Bestuurslid), `8` (Website), `9` (Social Media), `10` (Lid) |
| `field_functie_ir` | list_string | Functie Commissie Interne Relaties | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid), `11` (Interne relaties) |
| `field_functie_tec` | list_string | Functie Technische Commissie | 1 | **Opties:** `1` (Bestuurslid), `10` (Lid) |

---

## 🗂️ **Content Types (8 totaal)**

### 1. **Activiteit** (Activiteiten)
**Beschrijving:** Activiteiten en evenementen van het koor  
**Machine Name:** `activiteit`  
**Titel Label:** Titel  
**Heeft Body:** Ja (Omschrijving)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_koor_aanwezig` | datetime | Koor Aanwezig | 1 | time only (Hernoemd van field_tijd_aanwezig) |
| `field_activiteit_soort` | list_string | Activiteit Soort | 1 | **Opties:** `repetitie`, `uitvoering`, `social`, `vergadering` |
| `field_sleepgroep` | list_string | Sleepgroep | unlimited | **Opties:** `I`, `II`, `III`, `IV`, `V`, `*` |

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
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_datum` | datetime | Datum | 1 | date only |
| `field_ref_activiteit` | entity_reference | Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |

---

### 3. **Locatie** (Locaties)
**Beschrijving:** Locatie-informatie voor activiteiten  
**Machine Name:** `locatie`  
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
**Machine Name:** `nieuws`  
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
**Beschrijving:** Statische pagina's en standaard content  
**Machine Name:** `pagina`  
**Titel Label:** Titel  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden: Geen

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
| `field_view` | string | Extra inhoud | 1 | max_length: 255 |

---

### 6. **Programma** (Programma's)
**Beschrijving:** Concertprogramma's en repertoirelijsten  
**Machine Name:** `programma`  
**Titel Label:** Titel  
**Heeft Body:** Ja (Omschrijving)

#### Content Type Specifieke Velden: Geen

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_repertoire` | entity_reference | Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |

---

### 7. **Repertoire** (Repertoire)
**Beschrijving:** Muziekstukken en liedjes  
**Machine Name:** `repertoire`  
**Titel Label:** Titel  
**Heeft Body:** Ja (Omschrijving)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_componist` | string | Componist | 1 | max_length: 255 |
| `field_genre` | list_string | Genre | 1 | **Opties:** `pop`, `jazz`, `klassiek`, `gospels`, `modern`, `volkslied` |
| `field_klapper` | boolean | Klapper | 1 | Actueel repertoire indicatie |
| `field_stemverdeling` | string | Stemverdeling | 1 | max_length: 255 |
| `field_toonsoort` | string | Toonsoort | 1 | max_length: 255 |

**⚠️ BELANGRIJKE WIJZIGING: Partituren worden niet langer opgeslagen als directe velden op Repertoire, maar als aparte Document Media entiteiten met een reverse referentie.**

---

### 8. **Vriend** (Vrienden)
**Beschrijving:** Vrienden en sponsors van het koor  
**Machine Name:** `vriend`  
**Titel Label:** Naam  
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_v_website` | link | Website | 1 | - |
| `field_vriend_soort` | list_string | Soort | 1 | **Opties:** `financieel`, `niet-financieel`, `materieel` |
| `field_vriend_benaming` | list_string | Benaming | 1 | **Opties:** `vriend`, `vriendin`, `vrienden` |
| `field_vriend_vanaf` | list_string | Vriend vanaf | 1 | **Opties:** 2008-2025 |
| `field_vriend_tot` | list_string | Vriend t/m | 1 | **Opties:** 2008-2025 |
| `field_vriend_lengte` | list_string | Vriendlengte | 1 | **Opties:** `1` (0 t/m 1 jaar), `2` (2 t/m 5 jaar), `3` (6 jaar en langer) |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_woonplaats` | string | Woonplaats | 1 | max_length: 255 |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |

---

## 📁 **Media Bundles (4 totaal)**

### 1. **Image Bundle** (`image`)
**Beschrijving:** Afbeeldingen en foto's (vervangt D6 imagefield)  
**Bron Plugin:** `image`  
**Bron Veld:** `field_media_image`  
**Bestandsextensies:** jpg, jpeg, png, gif

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_image` | image | Afbeelding | 1 | file_extensions: jpg jpeg png gif |
| `field_datum_exif` | datetime | EXIF Datum | 1 | Automatisch geëxtraheerd |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

---

### 2. **Audio Bundle** (`audio`)
**Beschrijving:** Audio bestanden en opnames (vervangt D6 Audio content type)  
**Bron Plugin:** `file`  
**Bron Veld:** `field_media_audio_file`  
**Bestandsextensies:** mp3, wav, ogg

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_audio_file` | file | Audio Bestand | 1 | file_extensions: mp3 wav ogg |
| `field_audio_type` | list_string | Audio Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_gerelateerde_activiteit` | entity_reference | Gerelateerde Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |

---

### 3. **Video Bundle** (`video`)
**Beschrijving:** Video bestanden en opnames (vervangt D6 Video content type)  
**Bron Plugin:** `file`  
**Bron Veld:** `field_media_video_file`  
**Bestandsextensies:** mp4, avi, mov, wmv

| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_media_video_file` | file | Video Bestand | 1 | file_extensions: mp4 avi mov wmv |
| `field_video_type` | list_string | Video Type | 1 | **Opties:** `uitvoering`, `repetitie`, `oefenbestand`, `origineel`, `uitzending`, `overig` |
| `field_gerelateerd_repertoire` | entity_reference | Gerelateerd Repertoire | unlimited | target_type: node, target_bundles: [repertoire] |
| `field_gerelateerde_activiteit` | entity_reference | Gerelateerde Activiteit | 1 | target_type: node, target_bundles: [activiteit] |
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_audio_uitvoerende` | string | Uitvoerende | 1 | max_length: 255 |

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
| `field_toegang` | entity_reference | Toegang | unlimited | target_type: taxonomy_term, target_bundles: [toegang] |

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
User Profiles ←→ All Profile Fields (one-to-one)
```

---

## 📋 **Wijzigingslog**

### **Versie 1.2 (Augustus 2025) - VOLLEDIGE PROFIEL VELDEN:**
- ✅ **KRITIEK**: Toegevoegd ALLE 22 ontbrekende D6 profielvelden
- ✅ **NIEUW**: Volledige commissie functie velden (10 commissies)
- ✅ **NIEUW**: `field_positie` met volledige koor/band posities (64+ opties)
- ✅ **NIEUW**: `field_sleepgroep_1` met sleepgroep opties
- ✅ **NIEUW**: Uitgebreide documentatie van optie lijsten
- ✅ **BREAKING**: Alle migratie scripts moeten bijgewerkt worden voor 32 gebruiker profiel velden
- ✅ **BREAKING**: Permissie systeem moet uitgebreid worden voor commissie velden
- ✅ **KRITIEK**: Nu 100% compatibel met D6 profiel content type

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
- Basis gebruiker profiel velden systeem
- Automatische EXIF datum extractie
- Partituur architectuur herstructurering

---

*Laatst Bijgewerkt: Augustus 2025*  
*Module Versie: 1.2 - Volledig Nederlandse Documentatie met ALLE D6 Profielvelden*  
*Drupal Compatibiliteit: 11.x*  
*Alle UI Elementen: Nederlands*  
*Profiel Velden: 32 velden - 100% D6 compatibel*