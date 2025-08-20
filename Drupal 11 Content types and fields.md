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
**Media Bundles:** 4 media bundles (vervangt afgekeurde content types + verslag)  
**Gebruiker Profiel Velden:** 32 velden - Vervangt Profiel content type volledig  
**Gedeelde Velden:** 10 velden beschikbaar voor alle content types

### **🔄 D6 → D11 Content Type Transformaties:**

**Gemigreerd als Content Types (8):**
- activiteit → activiteit
- foto → fotoalbum  
- locatie → locatie
- nieuws → nieuws
- pagina → pagina
- programma → programma
- repertoire → repertoire
- vriend → vriend

**Vervangen door Media Bundles:**
- audio → audio media bundle
- video → video media bundle  
- image → image media bundle
- **verslag → document media bundle** (document_soort = "verslag")

**Vervangen door User Profile Fields:**
- profiel → 32 user profile velden

**Niet gemigreerd:**
- nieuwsbrief (vervangen door andere functionaliteit)
- webform (vervangen door Webform module)

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
**Titel Label:** Omschrijving  
**Heeft Body:** Ja (Berichttekst)

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_activiteit_soort` | list_string | Activiteit Soort | 1 | **Opties gemigreerd van D6 taxonomy "Activiteiten" (vocab ID 5):** `wereldlijk` (Wereldlijk), `kerkelijk` (Kerkelijk), `concours` (Concours), `repetitie` (Repetitie), `koorreis` (Koorreis), `vergadering` (Vergadering), `overige` (Overige), `vakantie` (Vakantie) |
| `field_koor_aanwezig` | string | Koor Aanwezig | 1 | max_length: 255 (Hernoemd van field_tijd_aanwezig) |
| `field_keyboard` | list_string | Toetsenist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_gitaar` | list_string | Gitarist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_basgitaar` | list_string | Basgitarist | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_drums` | list_string | Drummer | 1 | **Opties:** `+` (ja), `?` (misschien), `-` (nee), `v` (vervanging) |
| `field_vervoer` | string | Karrijder | 1 | max_length: 255 |
| `field_sleepgroep` | list_string | Sleepgroep | unlimited | **Opties:** `I`, `II`, `III`, `IV`, `V`, `*` |
| `field_sleepgroep_aanwezig` | string | Sleepgroep Aanwezig | 1 | max_length: 255 |
| `field_sleepgroep_terug` | list_string | Sleepgroep Terug | unlimited | **Opties:** `I`, `II`, `III`, `IV`, `V`, `*` |
| `field_kledingcode` | string | Kledingcode | 1 | max_length: 255 |
| `field_l_bijzonderheden` | text_long | Bijzonderheden locatie | 1 | formatted text |
| `field_ledeninfo` | text_long | Informatie voor leden | 1 | formatted text |
| `field_bijzonderheden` | string | Bijzonderheden | 1 | max_length: 255 |

#### Gedeelde Velden Gebruikt:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_datum` | datetime | Datum en tijd | 1 | date and time |
| `field_locatie` | entity_reference | Locatie | 1 | target_type: node, target_bundles: [locatie] |
| `field_programma` | entity_reference | Programma | unlimited | target_type: node, target_bundles: [programma] (via field_programma2) |
| `field_afbeeldingen` | entity_reference | Afbeeldingen | unlimited | target_type: media, target_bundles: [image] |
| `field_files` | entity_reference | Bestandsbijlages | unlimited | target_type: media, target_bundles: [document] |
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
| `field_l_routelink` | link | Route | unlimited | title required |

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
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_prog_type` | list_string | Type | 1 | **Opties:** `programma` (Programma onderdeel), `nummer` (Nummer) |

#### Gedeelde Velden Gebruikt: Geen

---

### 7. **Repertoire** (Repertoire)
**Beschrijving:** Muziekstukken en liedjes  
**Machine Name:** `repertoire`  
**Titel Label:** Titel  
**Heeft Body:** Nee

#### Content Type Specifieke Velden:
| Veld Naam | Veld Type | Label | Kardinaliteit | Doel/Instellingen |
|-----------|-----------|-------|---------------|-------------------|
| `field_klapper` | boolean | Actueel | 1 | Actueel repertoire indicatie |
| `field_audio_nummer` | integer | Nummer | 1 | Audio nummer |
| `field_audio_seizoen` | list_string | Seizoen | 1 | **Opties:** `regulier` (Regulier), `kerst` (Kerst) |
| `field_rep_genre` | list_string | Genre | 1 | **Opties:** `pop` (Pop), `musical` (Musical / Film), `gospel` (Geestelijk / Gospel) |
| `field_rep_componist` | string | Componist | 1 | max_length: 255 |
| `field_rep_componist_jaar` | integer | Jaar compositie | 1 | - |
| `field_rep_arr` | string | Arrangeur | 1 | max_length: 255 |
| `field_rep_arr_jaar` | integer | Jaar arrangement | 1 | - |
| `field_rep_uitv` | string | Uitvoerende | 1 | max_length: 255 |
| `field_rep_uitv_jaar` | integer | Jaar uitvoering | 1 | - |
| `field_rep_sinds` | integer | In repertoire sinds | 1 | - |

#### Gedeelde Velden Gebruikt: Geen

**⚠️ BELANGRIJKE WIJZIGING: Partituur bestanden worden NIET langer opgeslagen als velden op Repertoire, maar als Document Media entiteiten met reverse referenties via `field_gerelateerd_repertoire`.**

**Query voor partituren:**
```php
// Oud D6/D11: Query repertoire velden
$partituren = $repertoire_node->get('field_partij_band')->getValue();

// Nieuw D11: Query document media met reverse referentie
$partituren = \Drupal::entityTypeManager()
  ->getStorage('media')
  ->loadByProperties([
    'bundle' => 'document',
    'field_document_soort' => 'partituur',
    'field_gerelateerd_repertoire' => $repertoire_node_id
  ]);
```

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
| `field_document_soort` | list_string | Document Soort | 1 | **Opties:** `partituur`, `huiswerk`, `verslag`, `algemeen` |
| `field_verslag_type` | list_string | Verslag Type | 1 | **Opties gemigreerd van D6 taxonomy "Verslagen" (vocab ID 9):** `bestuursvergadering` (Bestuursvergadering), `vergadering_muziekcommissie` (Vergadering Muziekcommissie), `algemene_ledenvergadering` (Algemene Ledenvergadering), `overige_vergadering` (Overige Vergadering), `combo_overleg` (Combo Overleg), `jaarevaluatie_dirigent` (Jaarevaluatie Dirigent), `jaarverslag` (Jaarverslag), `concertcommissie` (Concertcommissie) |
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

## 🔗 **Gedeelde Velden (10 totaal)**

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
| `field_l_routelink` | link | Route | locatie (unlimited, title required) |

### Gebruiker Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_woonplaats` | string | Woonplaats | vriend (+ user profiles) |

### Media/Content Relatie Velden:
| Veld Naam | Veld Type | Label | Gebruikt door Content Types |
|-----------|-----------|-------|---------------------------|
| `field_repertoire` | entity_reference | Repertoire | audio, video (via media bundles) |
| `field_audio_uitvoerende` | string | Uitvoerende | Gebruikt in media bundles |

**Opmerking:** Partituur velden (`field_partij_band`, `field_partij_koor_l`, `field_partij_tekst`) vervallen vanwege reverse reference architectuur.

---

## 🎯 **Kritieke Migratie Wijzigingen - Partituur Architectuur**

### **Veld Naam Wijzigingen:**

**D6 → D11 Veld Herbenaming:**
- `field_tijd_aanwezig` → `field_koor_aanwezig` (Activiteit content type)
  - **Reden:** Duidelijkere veldnaam die beter de functie beschrijft
  - **Label blijft:** "Koor Aanwezig"

### **Data Transformatie Vereisten:**

#### **D6 → D11 Document & Content Type Migratie:**

**1. Partituur Bestanden (blijven gekoppeld aan Repertoire):**
   - `field_partij_band_fid` → Document media met `document_soort = "partituur"`
   - `field_partij_koor_l_fid` → Document media met `document_soort = "partituur"`
   - `field_partij_tekst_fid` → Document media met `document_soort = "partituur"`

**2. VERSLAG Content Type → Document Media (VOLLEDIGE VERVANGING):**
   - **D6 Verslag nodes worden NIET gemigreerd als content**
   - **D6 Verslag data wordt getransformeerd naar Document media:**
     - Verslag titel → Document media naam
     - Verslag datum → Document media metadata
     - Verslag bestanden → `document_soort = "verslag"`
     - **D6 Taxonomy "verslag type" terms → D11 `field_verslag_type` list_string opties**
     - Verslag body tekst → Document beschrijving of bijlage

**3. Andere Document Bestanden:**
   - `field_files_fid` (huiswerk context) → `document_soort = "huiswerk"`
   - `field_files_fid` (andere context) → `document_soort = "algemeen"`

### **🔧 Uitgebreide Document Soort Migratie Logica:**

```
D6 Bron → D11 Document Soort + Type
=====================================

field_partij_band_fid     → document_soort = "partituur"
field_partij_koor_l_fid   → document_soort = "partituur"  
field_partij_tekst_fid    → document_soort = "partituur"

field_files_fid (verslag content) → document_soort = "verslag" + verslag_type detectie
field_files_fid (huiswerk context) → document_soort = "huiswerk"
field_files_fid (andere context)   → document_soort = "algemeen"
```

### **🔧 Uitgebreide Taxonomy → List Field Migraties:**

#### **Verslag Type Migratie (D6 Taxonomy → D11 List Field):**
```php
// D6 Taxonomy "Verslagen" (vocab ID 9) → D11 list_string veld opties
function migrateVerslagTypeTaxonomyToListOptions() {
  
  // D6 taxonomy termen uit vocabulary "Verslagen" (ID: 9)
  $d6_verslag_terms = [
    'Bestuursvergadering' => 'bestuursvergadering',
    'Vergadering Muziekcommissie' => 'vergadering_muziekcommissie',
    'Algemene Ledenvergadering' => 'algemene_ledenvergadering',
    'Overige Vergadering' => 'overige_vergadering',
    'Combo Overleg' => 'combo_overleg',
    'Jaarevaluatie Dirigent' => 'jaarevaluatie_dirigent',
    'Jaarverslag' => 'jaarverslag',
    'Concertcommissie' => 'concertcommissie'
  ];
  
  // Stel allowed_values in voor field_verslag_type
  setFieldAllowedValues('field_verslag_type', $d6_verslag_terms);
  
  return $d6_verslag_terms;
}
```

#### **Activiteit Soort Migratie (D6 Taxonomy → D11 List Field):**
```php
// D6 Taxonomy "Activiteiten" (vocab ID 5) → D11 list_string veld opties
function migrateActiviteitSoortTaxonomyToListOptions() {
  
  // D6 taxonomy termen uit vocabulary "Activiteiten" (ID: 5)
  $d6_activiteit_terms = [
    'Wereldlijk' => 'wereldlijk',
    'Kerkelijk' => 'kerkelijk',
    'Concours' => 'concours',
    'Repetitie' => 'repetitie',
    'Koorreis' => 'koorreis',
    'Vergadering' => 'vergadering',
    'Overige' => 'overige',
    'Vakantie' => 'vakantie'
  ];
  
  // Stel allowed_values in voor field_activiteit_soort
  setFieldAllowedValues('field_activiteit_soort', $d6_activiteit_terms);
  
  return $d6_activiteit_terms;
}

// Migratie van activiteit node met taxonomy term
function migrateActiviteitWithSoort($d6_activiteit_node) {
  // Haal taxonomy term ID op uit vocabulary 5 (Activiteiten)
  $activiteit_soort_tid = getActiviteitTaxonomyTerm($d6_activiteit_node['nid'], 5);
  
  // Converteer TID naar machine name voor list_string veld
  $term_name = getTermName($activiteit_soort_tid);
  $machine_name = convertTermToMachineName($term_name);
  
  return $machine_name; // Voor field_activiteit_soort waarde
}
```
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

## 🔧 **Migratie Vereisten - Gebruiker Profiel Velden**

### **Kritieke D6 → D11 Transformaties:**

#### **Veld Type Conversies:**
| D6 Veld Type | D6 Widget | D11 Veld Type | Opmerkingen |
|--------------|-----------|---------------|-------------|
| `text` (longtext) | `text_textfield` | `string` | max_length: 255 voor korte teksten |
| `text` (longtext) | `optionwidgets_select` | `list_string` | Opties naar allowed_values |
| `date` (varchar) | `date_select` | `datetime` | date only, geen tijd |
| `text` (longtext) | `text_textarea` | `text_long` | Voor notities met formatting |

#### **Optie Lijsten Mapping:**

**Geslacht Opties:**
- D6: `m|Man`, `v|Vrouw` → D11: `m` (Man), `v` (Vrouw)

**Koor Functies:**
- D6: `B|Bas`, `A|Tenor`, etc. → D11: `B` (Bas), `A` (Tenor), etc.

**Positie Mapping (Uitgebreid):**
- **Rij 4:** `101` (4x01) tot `116` (4x16)
- **Rij 3:** `201` (3x01) tot `216` (3x16)  
- **Rij 2:** `301` (2x01) tot `316` (2x16)
- **Rij 1:** `401` (1x01) tot `416` (1x16)
- **Band:** `501` (Band 1) tot `504` (Band 4)
- **Speciaal:** `601` (Dirigent), `701` (Niet ingedeeld)

**Commissie Functies (Alle):**
- **Bestuur:** `1` (Voorzitter), `2` (Secretaris), `3` (Penningmeester), `4` (Bestuurslid)
- **Muziekcommissie:** `1` (Bestuurslid), `10` (Voorzitter), `20` (Secretaris), `30` (Dirigent), `40` (Contactpersoon band), `90` (Lid)
- **Overige Commissies:** `1` (Bestuurslid), `10` (Lid) + specifieke opties

### **Database Migratie Volgorde:**
1. **Eerst:** Migreer basis gebruiker accounts (uid, name, mail, etc.)
2. **Daarna:** Voeg user profile velden toe vanuit `content_type_profiel` tabel
3. **Daarna:** Voeg gedeelde veld data toe (bijv. `field_woonplaats` vanuit `content_field_woonplaats`)
4. **Validatie:** Controleer of alle 32 profielvelden correct gemigreerd zijn

### **Permissies Impact:**
Alle D6 veld-niveau permissies (`view field_[veldnaam]`, `edit field_[veldnaam]`) moeten worden geconverteerd naar D11 gebruiker profiel veld permissies.

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