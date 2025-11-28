# Workflow Migratie - D6 Workflow → D11 Content Moderation

## 📋 Overzicht

Drupal 6 gebruikt **Workflow module** met custom states per content type.  
Drupal 11 gebruikt **Content Moderation module** met standaard states: `draft`, `published`, `archived`.

## 🔄 Workflow Mappings uit Excel

### Workflow 1: nieuws, pagina, repertoire
```
D6 State              ID   →  D11 State
─────────────────────────────────────────
(creation)             1   →  draft
Concept                2   →  draft
Gepubliceerd           3   →  published
Archief                4   →  archived
Prullenmand            8   →  draft
Aangeraden             9   →  published + promote=1 ⭐
```

### Workflow 3: activiteit
```
D6 State              ID   →  D11 State
─────────────────────────────────────────
(aanmaak)             10   →  draft
Actief                11   →  published
Verlopen              12   →  archived
Inactief              13   →  draft
```

### Workflow 4: locatie, programma
```
D6 State              ID   →  D11 State
─────────────────────────────────────────
(aanmaak)             14   →  draft
Concept               15   →  draft
Prullenmand           16   →  draft
Aangeraden            17   →  published + promote=1 ⭐
Archief               18   →  archived
Geen Archief          19   →  published
Gepubliceerd          20   →  published
```

### Workflow 5: album
```
D6 State              ID   →  D11 State
─────────────────────────────────────────
(aanmaak)             21   →  draft
Gepubliceerd          22   →  published
Aangeraden            23   →  published + promote=1 ⭐
```

## ⭐ Featured Content (Aangeraden)

States 9, 17, 23 ("Aangeraden") worden gemigreerd als:
- `moderation_state: published`
- `promote: 1` (Promoted to front page)

Dit betekent dat "Aangeraden" content:
1. Gepubliceerd is
2. Verschijnt op de front page

## 🔧 Implementatie

### Custom Process Plugin

**Bestand:** `src/Plugin/migrate/process/WorkflowStateMapper.php`

```php
class WorkflowStateMapper extends ProcessPluginBase {
  
  const STATE_MAP = [
    1 => 'draft',      // (creation)
    2 => 'draft',      // Concept
    3 => 'published',  // Gepubliceerd
    4 => 'archived',   // Archief
    // ... etc
  ];
  
  const FEATURED_STATES = [9, 17, 23];  // Aangeraden
  
  public function transform($value, ...) {
    // Map workflow state ID to moderation state
    $moderation_state = self::STATE_MAP[$value];
    
    // Set promote=1 for featured states
    if (in_array($value, self::FEATURED_STATES)) {
      $row->setDestinationProperty('promote', 1);
    }
    
    return $moderation_state;
  }
}
```

### In Migratie YAML

Alle content type migraties hebben nu:

```yaml
process:
  # ... andere velden
  
  # Workflow state mapping
  moderation_state:
    plugin: workflow_state_mapper
    source: workflow_stamp
  
  promote: promote
  
  # ... rest van velden
```

## 📊 D6 Database Structuur

Workflow states worden opgeslagen in:
- **Tabel:** `workflow_node`
- **Velden:**
  - `nid` - Node ID
  - `sid` - State ID (1-23, zie mappings hierboven)
  - `uid` - User die state heeft gezet
  - `stamp` - Timestamp van state change

De migratie gebruikt `workflow_stamp` als source property.

## 🎯 D11 Setup Vereisten

### 1. Content Moderation Module Installeren

```bash
drush en content_moderation -y
```

### 2. Workflow Aanmaken

Maak een workflow aan voor je content types:

**Admin UI:** `/admin/config/workflow/workflows/add`

**Naam:** "ThirdWing Editorial"

**States:**
- Draft (default)
- Published
- Archived

**Content types:** Alle 7 content types selecteren:
- Activiteit
- Nieuws
- Pagina
- Album
- Locatie
- Programma
- Repertoire

### 3. Permissions Configureren

Zorg dat rollen de juiste transitions kunnen uitvoeren:
- Draft → Published
- Published → Archived
- Archived → Draft
- etc.

## 🔍 Migratie Source Plugin Aanpassing

De `ThirdWingNode` source plugin moet workflow data ophalen:

```php
public function prepareRow(Row $row) {
  $nid = $row->getSourceProperty('nid');
  
  // Haal workflow state op
  $workflow = $this->select('workflow_node', 'wn')
    ->fields('wn', ['sid', 'stamp'])
    ->condition('nid', $nid)
    ->orderBy('stamp', 'DESC')
    ->range(0, 1)
    ->execute()
    ->fetchAssoc();
  
  if ($workflow) {
    $row->setSourceProperty('workflow_sid', $workflow['sid']);
    $row->setSourceProperty('workflow_stamp', $workflow['stamp']);
  }
  
  return parent::prepareRow($row);
}
```

## ✅ Validatie na Migratie

Controleer of workflow states correct zijn gemigreerd:

```bash
# Check moderation states
drush sqlq "SELECT type, moderation_state, COUNT(*) as count 
            FROM node_field_data 
            GROUP BY type, moderation_state"

# Check promoted content (Aangeraden)
drush sqlq "SELECT nid, title, type, promote, moderation_state 
            FROM node_field_data 
            WHERE promote = 1"
```

## 📝 Notities

1. **"Prullenmand" items** → worden `draft` (unpublished)
2. **"Aangeraden" items** → worden `published` + `promote=1`
3. **Workflow 2** is disabled in D6 en hoeft niet gemigreerd
4. **Content types zonder workflow** → gebruiken standaard `status` field:
   - `status=1` → `published`
   - `status=0` → `draft`

## 🚨 Belangrijke Waarschuwing

Content Moderation moet **geïnstalleerd en geconfigureerd** zijn in D11 **voordat** je de content migraties draait!

Anders krijg je errors zoals:
```
The "moderation_state" plugin does not exist
```

## 🔄 Migratie Volgorde (met Workflows)

```bash
# 1. Setup D11 first!
drush en content_moderation -y
# Maak workflow aan via UI

# 2. Dan migraties draaien
./migrate.sh
```

## 📈 Verwachte Resultaten

Na migratie:
- **Draft content:** Niet zichtbaar voor anonymous users
- **Published content:** Zichtbaar voor iedereen
- **Archived content:** Niet zichtbaar, maar niet verwijderd
- **Featured content (promote=1):** Zichtbaar op front page

Perfect! 🎉
