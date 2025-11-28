# Programma Content Type VERVALT → Wordt Repertoire

## 🔄 Belangrijke Wijziging!

**Programma** is **geen apart content type** meer in D11!  
D6 Programma nodes worden **Repertoire nodes** met speciale `soort` waarde.

## 📊 Wat Gebeurt Er?

### D6 Structuur
```
Content Types:
├─ Programma (apart content type)
│  └─ field_prog_type: "programma" of "nummer"
└─ Repertoire (muzieknummers)
   └─ field_audio_seizoen: verschillende seizoenen
```

### D11 Structuur
```
Content Types:
└─ Repertoire (alle muziek-gerelateerde items)
   └─ field_repertoire_soort:
      ├─ "Regulier" (normale nummers)
      ├─ "Kerst" (kerstnummers)
      ├─ "Programma-onderdeel" (was D6 Programma type 'programma')
      └─ "Overig" (was D6 Programma type 'nummer')
```

## 🗺️ Field Mapping

### Programma → Repertoire
```yaml
D6 Content Type: programma
D11 Content Type: repertoire

D6 field_prog_type → D11 field_repertoire_soort:
  'programma' → 'Programma-onderdeel'
  'nummer'    → 'Overig'
```

### Activiteit Referenties
```yaml
D6: field_programma2 → verwijst naar Programma nodes
D11: field_programma → verwijst naar Repertoire nodes
                       (gefilterd op soort='Programma-onderdeel')
```

## 📝 Migratie Details

### Nieuwe Migratie
**Bestand:** `thirdwing_node_programma_to_repertoire.yml`

```yaml
id: thirdwing_node_programma_to_repertoire

source:
  plugin: d6_node
  node_type: programma

process:
  type:
    plugin: default_value
    default_value: repertoire  # ← Wordt Repertoire!
  
  field_repertoire_soort:
    plugin: static_map
    source: field_prog_type
    map:
      programma: 'Programma-onderdeel'
      nummer: 'Overig'

destination:
  plugin: entity:node
  default_bundle: repertoire
```

### Oude Migratie Verwijderd
~~`thirdwing_node_programma.yml`~~ → VERWIJDERD

## 🎯 D11 Setup Implicaties

### 1. Geen Programma Content Type!
Je maakt **GEEN** "Programma" content type aan in D11.

### 2. Repertoire Soort Opties
Bij het aanmaken van Repertoire, configureer `field_repertoire_soort` (List text):

**Opties:**
- `regulier` | Regulier
- `kerst` | Kerst
- `programma_onderdeel` | Programma-onderdeel ⭐ (was Programma)
- `overig` | Overig ⭐ (was Programma nummers)

### 3. Activiteit field_programma
In Activiteit content type:
- **Field:** `field_programma`
- **Type:** Entity reference (node:repertoire)
- **Filter:** Optioneel - laat alleen soort='Programma-onderdeel' zien in select

## 🔍 Views / Filters in D11

### Alle Programma Items Tonen
```
View: Programma Overzicht
Filter: Content type = Repertoire
        EN Soort = Programma-onderdeel
```

### Activiteit Programma Selectie
```
Field: field_programma (Entity reference)
Reference type: node:repertoire
View filter: Soort = Programma-onderdeel
```

## 📊 Content Aantallen

Na migratie heb je:

**Repertoire nodes bestaande uit:**
- ✅ Echte repertoire nummers (soort = Regulier, Kerst)
- ✅ Voormalige Programma nodes (soort = Programma-onderdeel, Overig)

**Geen separate Programma nodes!**

## 🚀 Migratie Volgorde

**KRITIEK:** Programma→Repertoire moet **VOOR** Activiteit!

```bash
# Correct (zoals in migrate.sh):
drush migrate:import thirdwing_node_locatie
drush migrate:import thirdwing_node_programma_to_repertoire  # EERST!
drush migrate:import thirdwing_node_repertoire
drush migrate:import thirdwing_node_activiteit  # Dan pas Activiteit

# Anders:
# Activiteit field_programma verwijzingen zullen falen!
```

## ✅ Validatie

Na migratie, controleer:

### 1. Programma Nodes zijn Repertoire
```sql
SELECT nid, title, type 
FROM node_field_data 
WHERE nid IN (
  SELECT entity_id 
  FROM node__field_repertoire_soort 
  WHERE field_repertoire_soort_value IN ('Programma-onderdeel', 'Overig')
)
```

### 2. Activiteit Verwijzingen Kloppen
```sql
SELECT a.nid, a.title, r.title as programma_title, rs.field_repertoire_soort_value
FROM node_field_data a
JOIN node__field_programma p ON a.nid = p.entity_id
JOIN node_field_data r ON p.field_programma_target_id = r.nid
JOIN node__field_repertoire_soort rs ON r.nid = rs.entity_id
WHERE a.type = 'activiteit'
```

### 3. Soort Distributie
```sql
SELECT field_repertoire_soort_value as soort, COUNT(*) as aantal
FROM node__field_repertoire_soort
JOIN node_field_data ON entity_id = nid
WHERE type = 'repertoire'
GROUP BY field_repertoire_soort_value
```

Verwacht resultaat:
```
soort                | aantal
---------------------+--------
Regulier            | XXX
Kerst               | XX
Programma-onderdeel | XX  ← Voormalige Programma nodes
Overig              | XX  ← Voormalige Programma nummers
```

## 📋 Content Overzicht

### Voor Migratie (D6)
- Programma: ~20-50 nodes
- Repertoire: ~100-300 nodes

### Na Migratie (D11)
- Programma: 0 nodes (content type bestaat niet!)
- Repertoire: ~120-350 nodes (inclusief voormalige Programma)

## 🎉 Voordelen van Deze Aanpak

**Vereenvoudiging:**
- ✅ 1 minder content type om te beheren
- ✅ Consistent voor alle muziek-gerelateerde items
- ✅ Eenvoudiger Views en filters
- ✅ Unified workflows voor repertoire

**Flexibiliteit:**
- ✅ Kun je alsnog filteren op "type" via soort veld
- ✅ Verschillende permissions per soort mogelijk
- ✅ Gemakkelijker om nieuwe soorten toe te voegen

## ⚠️ Let Op!

1. **Geen Programma content type aanmaken in D11!**
2. **Repertoire moet field_repertoire_soort hebben**
3. **Migreer Programma VOOR Activiteit**
4. **Update Views/filters om op soort te filteren**
