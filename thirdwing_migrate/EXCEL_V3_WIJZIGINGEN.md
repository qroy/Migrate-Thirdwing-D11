# Excel Wijzigingen Doorgevoerd - Versie 3

## ✅ Wijzigingen uit Excel Versie 3

### 1. Body Field - ✅ GECORRIGEERD
**Status:** Body blijft standaard `body` field (niet field_body)

Alle content types gebruiken nu correct de standaard body:
- ✅ Activiteit: body
- ✅ Album: body  
- ✅ Nieuws: body
- ✅ Pagina: body
- ✅ Locatie: body

**Geen wijzigingen nodig** - migraties waren al correct!

### 2. Repertoire Veld Wijzigingen - ✅ DOORGEVOERD

#### A. Seizoen → Soort
```yaml
# OUD:
field_repertoire_seizoen: field_audio_seizoen

# NIEUW:
field_repertoire_soort: field_audio_seizoen
```

**Reden:** Het veld heet blijkbaar "Soort" (niet "Seizoen")  
**Type:** List (text)  
**Status:** ✅ Toegepast in thirdwing_node_repertoire.yml

#### B. Actueel Type
```yaml
field_repertoire_actueel: field_klapper
```

**Type wijziging:** List (text) → Boolean  
**Betekenis:** Checkbox voor "Is dit nummer actueel?" (Ja/Nee)  
**Status:** ✅ Geen YAML wijziging nodig (simpele value mapping)

#### C. Uitvoerende Jaar Field
```yaml
# OUD:
field_repertoire_uitvoering_jaar: field_rep_uitv_jaar

# NIEUW:
field_repertoire_uitvoerende_jaa: field_rep_uitv_jaar
```

**Excel naam:** `field_repertoire_uitvoerende_jaa` (32 chars - exact op limiet!)  
**Opgelet:** Dit is de **maximale** lengte voor Drupal machine names  
**Status:** ✅ Toegepast in thirdwing_node_repertoire.yml

## 📊 Impact Overzicht

### Gewijzigde Migraties: 1
- ✅ thirdwing_node_repertoire.yml (3 veld namen gewijzigd)

### Ongewijzigde Migraties: 17
Alle andere migraties blijven hetzelfde:
- Content types (6): Activiteit, Nieuws, Pagina, Album, Locatie, Programma
- Media (6): Image, Audio, Video, Documents
- Partituren (3): Band, Koor, Koorregie  
- Users, Files, Taxonomy

## 🎯 D11 Field Setup Vereist

Bij het aanmaken van **Repertoire** content type in D11:

### Hernoemde/Gewijzigde Velden:
```
field_repertoire_soort (List text) - NIET field_repertoire_seizoen!
  └─ Was vroeger "Seizoen", is nu "Soort"

field_repertoire_actueel (Boolean) - NIET List!
  └─ Simpele checkbox: Ja/Nee

field_repertoire_uitvoerende_jaa (Integer) - LET OP: 32 chars!
  └─ Machine name is op de limiet!
```

### Alle Repertoire Velden:
```
field_repertoire_nummer (Integer)
field_repertoire_soort (List text) ⭐ hernoemd
field_repertoire_genre (List text)
field_repertoire_actueel (Boolean) ⭐ type gewijzigd
field_repertoire_sinds (Date)
field_repertoire_uitvoerende (Text)
field_repertoire_uitvoerende_jaa (Integer) ⭐ hernoemd
field_repertoire_componist (Text)
field_repertoire_compositie_jaar (Integer)
field_repertoire_arrangeur (Text)
field_repertoire_arrangement_jaar (Integer)
```

## ⚠️ Aandachtspunten

### 1. Machine Name Limiet
`field_repertoire_uitvoerende_jaa` heeft **exact 32 karakters**.

Als je tijdens setup in D11 problemen krijgt, overweeg dan:
- `field_repertoire_uitv_jaar` (26 chars) - meer ruimte
- Maar blijf consistent met wat in Excel staat!

### 2. Boolean vs List
`field_repertoire_actueel` is nu een **Boolean** (checkbox).

Als het D6 veld meerdere waarden had, wordt dit nu:
- Waarschijnlijk value 0 of 1
- Of eerste waarde uit de list wordt TRUE

## 🚀 Klaar voor Migratie

Alle wijzigingen uit Excel versie 3 zijn doorgevoerd!

**Test eerst:**
```bash
drush migrate:import thirdwing_node_repertoire --limit=5
```

**Check specifiek:**
- field_repertoire_soort heeft waarden
- field_repertoire_actueel is 0 of 1 (Boolean)
- field_repertoire_uitvoerende_jaa heeft jaartallen

```bash
drush sqlq "SELECT nid, title, 
            field_repertoire_soort_value,
            field_repertoire_actueel_value,
            field_repertoire_uitvoerende_jaa_value
            FROM node__field_repertoire_soort 
            JOIN node__field_repertoire_actueel USING (entity_id)
            JOIN node__field_repertoire_uitvoerende_jaa USING (entity_id)
            LIMIT 5"
```
