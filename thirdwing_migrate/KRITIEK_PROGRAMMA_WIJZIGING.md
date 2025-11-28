# KRITIEKE WIJZIGING: Programma Vervalt! 🚨

## ⚠️ Belangrijke Ontdekking

Het **Programma content type VERVALT** en wordt **Repertoire**!  
Dit stond in het Excel maar was nog niet verwerkt in de migraties.

## ✅ Nu Doorgevoerd

### 1. Migratie Verwijderd
~~`thirdwing_node_programma.yml`~~ → VERWIJDERD

### 2. Nieuwe Migratie Toegevoegd
✅ `thirdwing_node_programma_to_repertoire.yml`

**Functie:**
- Leest D6 Programma nodes
- Schrijft naar D11 als Repertoire nodes
- Maps field_prog_type → field_repertoire_soort:
  - 'programma' → 'Programma-onderdeel'
  - 'nummer' → 'Overig'

### 3. Activiteit Updated
✅ `thirdwing_node_activiteit.yml`

- field_programma verwijst nu naar `thirdwing_node_programma_to_repertoire`
- Dependency aangepast

### 4. Scripts Updated
✅ `migrate.sh` - Correcte volgorde
✅ `rollback.sh` - Reverse volgorde

## 📊 Repertoire Soort Opties

`field_repertoire_soort` heeft nu **4 opties**:

1. **Regulier** - Normale repertoire nummers
2. **Kerst** - Kerstnummers  
3. **Programma-onderdeel** ⭐ - Was D6 Programma type 'programma'
4. **Overig** ⭐ - Was D6 Programma type 'nummer'

## 🎯 D11 Content Types (6 stuks)

**NA deze wijziging:**
1. ✅ Activiteit
2. ✅ Nieuws
3. ✅ Pagina
4. ✅ Album
5. ✅ Locatie
6. ✅ Repertoire (inclusief voormalige Programma!)

~~Programma~~ ❌ VERVALT

## 🔄 Migratie Volgorde (KRITIEK!)

```bash
# Programma MOET voor Activiteit!
drush migrate:import thirdwing_node_locatie
drush migrate:import thirdwing_node_programma_to_repertoire  # EERST!
drush migrate:import thirdwing_node_repertoire
drush migrate:import thirdwing_node_activiteit  # Verwijst naar Programma→Repertoire
```

## 📦 Totaal Aantal Migraties: 18

**Ongewijzigd** (was al 18):
- 1 taxonomie
- 1 user
- 1 file
- 6 media
- 6 content types (Programma vervalt, wordt Repertoire)
- 3 partituren

## ⚠️ KRITIEKE AANDACHTSPUNTEN

### 1. D11 Setup
**GEEN Programma content type aanmaken!**  
Alleen Repertoire met field_repertoire_soort opties.

### 2. Field Setup
```
field_repertoire_soort (List text):
  Opties:
  - regulier | Regulier
  - kerst | Kerst  
  - programma_onderdeel | Programma-onderdeel ⭐
  - overig | Overig ⭐
```

### 3. Activiteit field_programma
```
Type: Entity reference (node:repertoire)
Optioneel filter: soort = Programma-onderdeel
```

### 4. Views/Filters
Om "oude Programma items" te tonen:
```
Filter: Content type = Repertoire
        EN Soort = Programma-onderdeel
```

## 🎉 Voordelen

- ✅ 1 content type minder te beheren
- ✅ Alles muziek-gerelateerd in Repertoire
- ✅ Consistente workflows
- ✅ Eenvoudiger permission management

## 📋 Checklist voor Migratie

- [x] Oude thirdwing_node_programma.yml verwijderd
- [x] Nieuwe thirdwing_node_programma_to_repertoire.yml aangemaakt
- [x] Activiteit field_programma dependency updated
- [x] migrate.sh volgorde gecorrigeerd
- [x] rollback.sh volgorde gecorrigeerd
- [x] Documentatie toegevoegd (PROGRAMMA_VERVALT.md)
- [ ] D11 Repertoire content type met 4 soort opties aanmaken
- [ ] Views filteren op soort
- [ ] Test migratie uitvoeren

## 🚀 Test Commando's

```bash
# Test Programma→Repertoire migratie
drush migrate:import thirdwing_node_programma_to_repertoire --limit=5

# Check resultaat
drush sqlq "SELECT nid, title, field_repertoire_soort_value 
            FROM node_field_data n
            JOIN node__field_repertoire_soort s ON n.nid = s.entity_id
            WHERE field_repertoire_soort_value IN ('Programma-onderdeel', 'Overig')
            LIMIT 10"

# Check Activiteit verwijzingen
drush sqlq "SELECT COUNT(*) 
            FROM node__field_programma 
            WHERE bundle = 'activiteit'"
```

Lees volledig: [PROGRAMMA_VERVALT.md](PROGRAMMA_VERVALT.md)
