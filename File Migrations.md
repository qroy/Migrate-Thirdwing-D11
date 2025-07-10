# Drupal 11 Media Migration Specification

## Project Overview

**Goal:** Convert existing files attached to nodes and entities into proper Drupal 11 media entities for a clean Drupal 11 installation.

**Migration Type:** Fresh migration (not fixing existing system)
**Update Strategy:** Ongoing incremental updates while old site remains active
**Source:** Files attached to nodes/entities only (no orphaned filesystem files)

## Core Architecture Decisions

### 1. Bundle Decision Logic: Source Field Context

Media bundle determination is based on the **source field type** where the file was originally attached:

```
Field Type/Pattern          → Media Bundle
==========================================
imagefield                 → media:image
field_mp3                   → media:audio
field_partij_*              → media:sheet_music
generic filefield           → media:document
video/youtube embed         → media:video
```

### 2. Multi-Context File Handling

**Decision:** Create **one media entity per file** (no duplication)

**Bundle Priority Logic:**
```
Priority Order (highest to lowest):
1. sheet_music (field_partij_*)
2. audio (field_mp3)  
3. image (imagefield)
4. document (generic filefield)
```

**Context Preservation:** Store minimal context metadata in JSON format:
```json
{
  "primary_field": "field_partij_band",
  "all_fields": ["field_partij_band", "field_partij_koor_l"]
}
```

### 3. Reference Conversion Strategy

**Approach:** Direct conversion (no parallel system)
- Replace file fields with media reference fields immediately
- Old site remains as backup/reference
- New site uses media entities from day one

### 4. Bundle Preservation

**Rule:** Always preserve the first-determined bundle
- Bundle never changes after initial creation
- Provides system stability
- Context metadata updates but bundle remains fixed

## Media Bundle Specifications

### Core Media Bundles

#### 1. Image Bundle (`media:image`)
- **Source Pattern:** `imagefield` fields
- **Examples:** `field_afbeeldingen`, `field_background`
- **Extensions:** `png gif jpg jpeg`
- **Standard Drupal core bundle**

#### 2. Audio Bundle (`media:audio`)
- **Source Pattern:** `field_mp3`
- **Extensions:** `mp3 wav ogg`
- **Standard Drupal core bundle**

#### 3. Video Bundle (`media:video`)
- **Source Pattern:** Video/YouTube embed fields
- **Type:** oEmbed for YouTube
- **Standard Drupal core bundle**

#### 4. Document Bundle (`media:document`)
- **Source Pattern:** Generic `filefield` (fallback)
- **Extensions:** `pdf doc docx` (general documents)
- **Standard Drupal core bundle**

### Custom Media Bundles

#### 5. Sheet Music Bundle (`media:sheet_music`)

**Bundle Configuration:**
- **Bundle ID:** `sheet_music`
- **Label:** `Partituur`
- **Description:** `Muziekpartituren en bladmuziek`
- **Source Pattern:** `field_partij_*`

**Fields:**
```yaml
field_media_document:
  type: file
  label: 'Partituur Bestand'
  required: true
  extensions: 'pdf mscz mid doc docx'

field_partituur_type:
  type: list_string
  label: 'Partij Type'
  required: true
  allowed_values:
    koor_sopraan: 'Koor - Sopraan'
    koor_alt: 'Koor - Alt'  
    koor_tenor: 'Koor - Tenor'
    koor_bas: 'Koor - Bas'
    koor_gemengd: 'Koor - Gemengde Partijen'
    band_piano: 'Band - Piano/Toetsen'
    band_gitaar: 'Band - Gitaar'
    band_bas: 'Band - Basgitaar'
    band_drums: 'Band - Drums'
    band_gemengd: 'Band - Gemengde Partijen'
    dirigent: 'Dirigentpartituur'
    tekst: 'Tekst/Liedtekst'

field_partituur_repertoire:
  type: entity_reference
  label: 'Repertoire'
  target_type: node
  target_bundles: [repertoire]
  
field_bron_contexten:
  type: text_long
  label: 'Bron Veld Contexten'
  description: 'JSON data over oorspronkelijke veld contexten'
```

**Legacy Type Mapping:**
```php
$type_mapping = [
  1 => 'koor_sopraan',
  2 => 'koor_alt', 
  3 => 'koor_tenor',
  4 => 'koor_bas',
  10 => 'band_gemengd',
  11 => 'band_piano',
  12 => 'band_gitaar', 
  13 => 'band_bas',
  14 => 'band_drums',
];
```

## Source System Analysis

### Drupal 6 Field Structure

**Image Fields:**
- `field_afbeeldingen` (foto, nieuws, pagina, vriend, etc.)
- `field_background` (activiteit)
- Widget: `imagefield_widget`
- Extensions: `png gif jpg jpeg`

**Audio Fields:**
- `field_mp3` (audio content type)
- Widget: `filefield`

**Sheet Music Fields:**
- `field_partij_band` (band sheet music)
- `field_partij_koor_l` (choir sheet music)
- `field_partij_tekst` (text/lyrics sheet music)
- Path pattern: `/repertoire/`
- Extensions: `pdf mscz mid`

**Document Fields:**
- Generic `filefield` widgets
- Various extensions

## Migration Implementation Strategy

### Phase 1: Initial Migration
1. **Bundle Creation:** Create custom media bundles
2. **File Analysis:** Scan all files and their field contexts
3. **Bundle Assignment:** Apply priority logic to determine bundles
4. **Media Entity Creation:** Create media entities with metadata
5. **Reference Conversion:** Update content to reference media entities

### Phase 2: Incremental Updates
1. **Change Detection:** Use timestamps to detect file/content changes
2. **Context Analysis:** Recalculate field contexts for changed files
3. **Metadata Updates:** Update context metadata (preserve bundle)
4. **Reference Updates:** Update entity references as needed
5. **Orphan Handling:** Mark orphaned media for review

### Technical Architecture

**Source Plugin Requirements:**
```sql
-- Pseudo-query structure
SELECT f.*, 
       GROUP_CONCAT(DISTINCT field_name) as field_contexts,
       GROUP_CONCAT(DISTINCT content_type) as content_types,
       MAX(node.changed) as last_content_change
FROM files f
LEFT JOIN [field_instances] ON f.fid = [field_reference]
LEFT JOIN node ON node.nid = [content_reference]
GROUP BY f.fid
ORDER BY f.timestamp
```

**Bundle Decision Service:**
```php
class MediaBundleDecisionService {
  public function determineBundleFromContexts($field_contexts) {
    if (preg_match('/field_partij_/', $field_contexts)) {
      return 'sheet_music';
    }
    if (strpos($field_contexts, 'field_mp3') !== false) {
      return 'audio';
    }
    if (preg_match('/imagefield/', $field_contexts)) {
      return 'image';
    }
    return 'document';
  }
}
```

**Incremental Update Strategy:**
- Use `highWaterProperty` on file modification timestamps
- Track context changes in custom field
- Handle bundle preservation logic
- Support rollback scenarios

## Benefits of This Approach

1. **Clean Architecture:** Single media entity per file
2. **Context Preservation:** Track original usage without duplication
3. **Stable System:** Bundle never changes after creation
4. **Incremental Updates:** Support ongoing synchronization
5. **No Duplication:** Efficient storage and management
6. **Drupal Best Practices:** Leverages core media system properly

## Implementation Priority

1. **Media Bundle Creation:** Create custom bundles and fields
2. **Source Plugin Development:** Build file context analysis
3. **Bundle Decision Logic:** Implement priority-based decisions
4. **Migration Configuration:** Set up YAML migration configs
5. **Incremental Update System:** Build change detection
6. **Testing & Validation:** Comprehensive migration testing

## Future Considerations

- **Performance:** Monitor migration performance with large file sets
- **Storage:** Consider file storage strategy (public/private)
- **Access Control:** Integrate with existing permission systems
- **Media Library:** Configure for optimal content editor experience
- **Backup Strategy:** Ensure media entities are included in backups