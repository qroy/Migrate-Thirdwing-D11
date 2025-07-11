# Media Bundles Design Decision

## Overview
For Priority 1 of the Thirdwing D6→D11 migration, we need to define all media bundles that will handle the various file types from the old site.

## Current Analysis from D6 Site
Based on the existing migration configuration and D6 database analysis, the following file types and usage patterns were identified:

### File Categories in D6
- **Images**: User photos, activity images, repertoire covers, video thumbnails
- **Documents**: PDF reports, Word docs, general documents  
- **Audio Files**: MP3, WAV, OGG recordings and music files
- **Video Files**: MP4, AVI, MOV video content + embedded videos (YouTube)
- **Sheet Music**: PDF musical scores and partitions with specific metadata
- **Reports**: Meeting minutes, administrative documents with date context

## Final Media Bundle Count: **4 Bundles** ✅

1. **🖼️ Image** - Photos, user pictures, thumbnails
2. **📄 Document** - PDFs, docs, sheet music, reports (with type field)
3. **🎵 Audio** - Music files and recordings  
4. **🎬 Video** - Video files + embedded content

## Media Bundle Structure (Using D6 Field Structure) ✅

### 1. **Image Bundle** (`image`)
- **Source Field**: `field_media_image` (Image field)
- **File Extensions**: jpg, jpeg, png, gif, webp
- **Usage**: Photos, thumbnails, covers, user pictures
- **Special Fields**:
  - Alt text (built-in)
  - Title (built-in)
  - Caption (optional)
  - `field_toegang` (Access Level) - **FROM D6 TAXONOMY VID 4**

### 2. **Document Bundle** (`document`) - **CONSOLIDATED** 
- **Source Field**: `field_media_document` (File field)
- **File Extensions**: pdf, doc, docx, txt, xls, xlsx, mid, kar
- **Usage**: General documents, sheet music, reports, meeting minutes
- **Special Fields** (from D6 content types):
  - **Document Type** (taxonomy reference): 
    - General Document
    - Sheet Music (Full Score)
    - Sheet Music (Voice Part)
    - Meeting Report
    - Annual Report  
    - MIDI File
  - `field_datum` (Date) - **FROM D6 VERSLAG**
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**
  - `field_gerelateerd_repertoire` (Related repertoire)
  - `field_componist` (Composer - for sheet music)
  - `field_stemsoort` (Voice part - for sheet music parts)

### 3. **Audio Bundle** (`audio`)
- **Source Field**: `field_media_audio_file` (File field) 
- **File Extensions**: mp3, wav, ogg, m4a, aac
- **Usage**: Music recordings, audio content
- **Special Fields** (from D6 audio content type):
  - `field_datum` (Date) - **FROM D6 AUDIO**
  - `field_audio_type` (Audio type) - **FROM D6 AUDIO**
  - `field_audio_uitvoerende` (Performer/Artist) - **FROM D6 AUDIO**
  - `field_audio_bijz` (Audio notes/description) - **FROM D6 AUDIO**
  - `field_ref_activiteit` (Related activity) - **FROM D6 AUDIO**
  - `field_repertoire` (Related repertoire) - **FROM D6 AUDIO**
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**

### 4. **Video Bundle** (`video`)
- **Source Field**: `field_media_video_file` (File field) + `field_video` (Embedded)
- **File Extensions**: mp4, avi, mov, wmv, flv
- **Usage**: Video files and embedded content
- **Special Fields** (from D6 video content type):
  - `field_video` (Embedded video) - **FROM D6 VIDEO** 
  - `field_datum` (Date) - **FROM D6 VIDEO**
  - `field_audio_type` (Media type) - **FROM D6 VIDEO**
  - `field_audio_uitvoerende` (Performer) - **FROM D6 VIDEO** 
  - `field_ref_activiteit` (Related activity) - **FROM D6 VIDEO**
  - `field_repertoire` (Related repertoire) - **FROM D6 VIDEO**
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**

## Content Type Media References ✅

Content types will use **clear media reference fields** instead of direct file fields:

### **Updated Field Naming Convention:**
- ❌ `field_files` (confusing - files or media?)
- ❌ `field_afbeeldingen` (confusing - files or media?)
- ✅ `field_media_documents` (clearly references document media entities)
- ✅ `field_media_images` (clearly references image media entities)
- ✅ `field_media_audio` (clearly references audio media entities)
- ✅ `field_media_video` (clearly references video media entities)

### **Clear Separation of Concerns:**
- **Media Entities**: Store actual files (`field_media_document`, `field_media_image`, etc.)
- **Content Types**: Reference media entities (`field_media_documents`, `field_media_images`, etc.)

## ✅ **EXISTING D6 ACCESS CONTROL SYSTEM - COMPLETE DISCOVERY**

### Current D6 TAC Lite Implementation
- **Module**: TAC Lite (Taxonomy Access Control Lite) - ACTIVE
- **Access Vocabulary**: Vocabulary ID 4 
- **Field Usage**: `field_toegang` (already implemented in migration source plugins)
- **Admin Interface**: `/admin/user/access/tac_lite`

### ✅ **ACTUAL ACCESS TERMS DISCOVERED** (Vocabulary ID 4)

**General Access Levels:**
1. **Bezoekers** - Visitors/Public access
2. **Vrienden** - Friends/Supporters 
3. **Aspirant-Leden** - Aspiring members
4. **Leden** - Full members
5. **Bestuur** - Board members
6. **Beheer** - Administrators

**Committee-Specific Access:**
7. **Muziekcommissie** - Music committee
8. **Concertcommissie** - Concert committee  
9. **Commissie Interne Relaties** - Internal relations committee
10. **Commissie Koorregie** - Choir direction committee
11. **Feestcommissie** - Party/events committee
12. **Band** - Band members

### Migration Strategy ✅
1. **✅ CONFIRMED**: Migrate existing vocabulary 4 with all 12 terms
2. **✅ CONFIRMED**: Use `field_toegang` field name across all entities
3. **✅ CONFIRMED**: Apply to all 4 media bundles and content types
4. **✅ CONFIRMED**: Implement Permissions by Term module for D11

### Access Level Hierarchy (Permission Scope)
```
Beheer (Admin) → Full access
Bestuur (Board) → Administrative content
Committee roles → Committee-specific content + member content
Leden (Members) → Member-only content + public content
Aspirant-Leden → Limited member content + public content
Vrienden (Friends) → Supporter content + public content
Bezoekers (Visitors) → Public content only
```

### Perfect Implementation Benefits ✅
- **Zero Training Required** - Content editors already understand these 12 access levels
- **Proven Granular Control** - Committee-specific access already working
- **Role Alignment** - Access terms match existing user roles perfectly
- **Data Preservation** - All existing access relationships maintained

## Field Names Summary ✅ **USING ACTUAL D6 FIELDS**

### **Document Bundle:**
- `field_document_soort` (Document Type) - **NEW TAXONOMY**
- `field_datum` (Date) - **FROM D6 VERSLAG/AUDIO/VIDEO**
- `field_toegang` (Access Level) - **FROM D6 TAXONOMY VID 4**
- `field_gerelateerd_repertoire` (Related Repertoire) - **NEW**
- `field_componist` (Composer) - **NEW**
- `field_stemsoort` (Voice Part) - **NEW**

### **Audio Bundle:**
- `field_audio_type` (Audio Type) - **FROM D6 AUDIO**
- `field_audio_uitvoerende` (Performer) - **FROM D6 AUDIO/VIDEO**
- `field_audio_bijz` (Audio Notes) - **FROM D6 AUDIO**
- `field_datum` (Date) - **FROM D6 AUDIO**
- `field_ref_activiteit` (Related Activity) - **FROM D6 AUDIO/VIDEO**
- `field_repertoire` (Related Repertoire) - **FROM D6 AUDIO/VIDEO**
- `field_toegang` (Access Level) - **FROM D6 TAXONOMY VID 4**

### **Video Bundle:**
- `field_video` (Embedded Video) - **FROM D6 VIDEO**
- `field_datum` (Date) - **FROM D6 VIDEO**
- `field_audio_type` (Media Type) - **FROM D6 VIDEO**
- `field_audio_uitvoerende` (Performer) - **FROM D6 VIDEO**
- `field_ref_activiteit` (Related Activity) - **FROM D6 VIDEO**
- `field_repertoire` (Related Repertoire) - **FROM D6 VIDEO**
- `field_toegang` (Access Level) - **FROM D6 TAXONOMY VID 4**

### **Content Type Media Reference Fields:**
- `field_media_documents` (References document media entities)
- `field_media_images` (References image media entities)
- `field_media_audio` (References audio media entities)
- `field_media_video` (References video media entities)

## Taxonomy Vocabularies

### **Document Soort** (Document Types) - **NEW**:
- Algemeen Document
- Partituur (Volledig)
- Partituur (Stempartij)
- Vergaderverslag
- Jaarverslag
- MIDI Bestand

### **✅ Toegang** (Access Levels) - **MIGRATED FROM D6 VOCABULARY ID 4**:
- Bezoekers
- Vrienden  
- Aspirant-Leden
- Leden
- Bestuur
- Muziekcommissie
- Concertcommissie
- Commissie Interne Relaties
- Commissie Koorregie
- Feestcommissie
- Band
- Beheer

### **Stem Soort** (Voice Parts) - **NEW** (for sheet music):
- Sopraan
- Alt
- Tenor
- Bas
- Piano/Begeleiding

## Bundle Field Mapping from D6 (Updated)

### Common Fields (All Bundles)
- `name` ← D6 filename
- `uid` ← D6 file owner
- `status` ← Published status
- `created` ← D6 timestamp
- `changed` ← D6 timestamp

### Bundle-Specific Mapping
- **Images**: `field_media_image` ← D6 files table + image processing
- **Documents**: `field_media_document` ← D6 files table + type classification
- **Audio**: `field_media_audio_file` ← D6 files + audio metadata
- **Video**: Both file upload and URL fields for embedded content

### Document Type Classification Logic
```
D6 Source → Document Type:
- content_type_verslag → "Meeting Report"  
- Sheet music files → "Sheet Music"
- General files → "General Document"
- Annual reports → "Annual Report"
- MIDI files → "MIDI File"
```

## Access Control Strategy

### Public Access
- **Images**: Generally public (except private user photos)
- **Video**: Public promotional content
- **Audio**: Public recordings and demos

### Member Access  
- **Sheet Music**: Members only for active repertoire
- **Reports**: Members only for meeting minutes
- **Documents**: Varies by document type

### Committee-Specific Access
- **Committee documents**: Accessible only to relevant committee members
- **Board documents**: Board-only access
- **Administrative content**: Admin-only access

## Technical Implementation Notes

### File Storage
- **Reorganize by bundle**: `/sites/default/files/media/{bundle}/`
- **Document Bundle**: `/sites/default/files/media/document/`
- **Audio Bundle**: `/sites/default/files/media/audio/`
- **Video Bundle**: `/sites/default/files/media/video/`
- **Image Bundle**: `/sites/default/files/media/image/`

### Migration Strategy  
- Phase 1: Core file migration (`d6_thirdwing_file`)
- Phase 2: Media entity creation by bundle
- Phase 3: Content relationships and references

### Drupal 11 Media Module
- Use standard Media module bundles
- Leverage entity reference fields for relationships  
- Implement access control via Permissions by Term module (D11 equivalent of TAC Lite)
- Use media library for content management

## Decision Points - **ALL RESOLVED** ✅

1. **Bundle Count**: ✅ **4 bundles** (merged sheet music + reports into document)
2. **Field Names**: ✅ **Dutch field names** (`field_document_soort`, `field_toegang`, etc.)
3. **Access Control**: ✅ **Existing D6 TAC Lite vocabulary** (12 access levels)
4. **Embedded Media**: ✅ **Handle in video bundle** with URL field for YouTube/external
5. **File Organization**: ✅ **Reorganize by bundle** (`/sites/default/files/media/{bundle}/`)
6. **Field Structure**: ✅ **Use actual D6 fields** (field_datum, field_audio_type, etc.)
7. **Media References**: ✅ **Clear naming** (field_media_documents vs field_files)

## Perfect Implementation Benefits ✅

- **Zero Training Required** - Content editors already understand the 12 access levels and field structure
- **Proven Granular Control** - Committee-specific access already working
- **Role Alignment** - Access terms match existing user roles perfectly
- **Data Preservation** - All existing access relationships and field data maintained
- **Clean Architecture** - Clear separation between file storage (media entities) and content relationships (nodes)
- **Migration Simplicity** - Direct field-to-field mapping using existing D6 field names

## Next Steps - **READY FOR IMPLEMENTATION** 🚀

1. ✅ **ALL DECISIONS CONFIRMED** - Media bundle structure finalized
2. **AWAITING CONFIRMATION**: Ready to create media bundle setup code
3. **IMPLEMENTATION PLAN**:
   - Create script to set up 4 media bundles
   - Migrate existing taxonomy vocabulary (12 access terms)
   - Configure bundle-specific fields using D6 field names
   - Set up file directory structure by bundle
   - Update existing migration configurations
   - Test media entity creation and file organization
   - Implement Permissions by Term module for access control

## Implementation Scope
- **Media Bundle Creation**: 4 bundles with proper field configuration using D6 field structure
- **Taxonomy Migration**: Access control vocabulary (vid 4) + new document types + voice parts
- **File Migration**: Reorganize from D6 paths to bundle-based structure  
- **Migration Updates**: Update existing YAML configs for new structure
- **Access Control**: Implement Permissions by Term module with migrated taxonomy
- **Content Type Updates**: Use clear media reference field names

**Status**: ✅ Ready for code creation - awaiting confirmation to proceed

---
*This document reflects the complete media bundle design based on thorough analysis of the D6 database structure, existing field usage, and access control requirements. All decisions preserve existing functionality while modernizing the architecture for Drupal 11.*