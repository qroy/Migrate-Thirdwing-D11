# Updated Media Bundle Section for README.md

## 📦 Media Bundle System ✅ **COMPLETE SPECIFICATION**

### **Migration Strategy for Media Titles**

#### **Name Field Migration Sources:**
- **Document Bundle**: D6 `field_files` description field → `name` (fallback to filename)
- **Audio Bundle**: D6 node title → `name` field  
- **Video Bundle**: D6 node title → `name` field
- **Image Bundle**: D6 filename or alt text → `name` field

#### **Image Date Migration Sources:**
- **Primary**: D6 `field_exif_datetimeoriginal` (existing EXIF extraction)
- **Fallback 1**: Fresh EXIF extraction for images without D6 EXIF data  
- **Fallback 2**: File timestamp for non-EXIF formats (PNG, GIF)

### Final Media Bundle Architecture: **4 Bundles** ✅

#### 1. **🖼️ Image Bundle** (`image`)
- **Source Field**: `field_media_image` (Image field)
- **File Extensions**: jpg, jpeg, png, gif, webp
- **Usage**: Photos, thumbnails, covers, user pictures
- **Storage**: `/sites/default/files/media/image/`
- **Fields**:
  - `name` (Built-in) - Image title (from filename or alt text)
  - Alt text (built-in) - Image description
  - Title (built-in) - Image title attribute
  - `field_datum` (Date) - **Photo date from EXIF or file timestamp**
  - `field_toegang` (Entity Reference) - Access Level (D6 TAXONOMY VID 4)

##### **EXIF Date Migration Strategy**:
- **Primary Source**: D6 `field_exif_datetimeoriginal` (existing EXIF data)
- **Fallback 1**: Extract EXIF from image files without D6 data
- **Fallback 2**: File timestamp for non-EXIF images (PNG, GIF)
- **Format**: Convert EXIF `Y:m:d H:i:s` to Drupal date field

#### 2. **📄 Document Bundle** (`document`) - ⭐ **COMPLETE SPECIFICATION**
- **Source Field**: `field_media_document` (File field)
- **File Extensions**: pdf, doc, docx, txt, xls, xlsx, **mscz** (MuseScore)
- **Usage**: General documents, sheet music, reports, meeting minutes
- **Storage**: `/sites/default/files/media/document/`

##### **Fields**:
1. **`name`** (Built-in) - **ALWAYS REQUIRED**
   - **Migration Source**: D6 `field_files` description field
   - **Fallback**: Filename if description is empty

2. **`field_document_soort`** (Selection Field) - **ALWAYS REQUIRED**
   - `verslag` → "Verslag"
   - `partituur` → "Partituur" 
   - `overig` → "Overig"

3. **`field_verslag_type`** (Selection Field) - **REQUIRED when document_soort = "verslag"**
   - `algemene_ledenvergadering` → "Algemene Ledenvergadering"
   - `bestuursvergadering` → "Bestuursvergadering"
   - `combo_overleg` → "Combo Overleg"
   - `concertcommissie` → "Concertcommissie"
   - `jaarevaluatie_dirigent` → "Jaarevaluatie Dirigent"
   - `jaarverslag` → "Jaarverslag"
   - `overige_vergadering` → "Overige Vergadering"
   - `vergadering_muziekcommissie` → "Vergadering Muziekcommissie"

4. **`field_datum`** (Date) - **REQUIRED when document_soort = "verslag"**
   - Document date (from D6 verslag content)

5. **`field_toegang`** (Entity Reference) - **OPTIONAL**
   - Access level (D6 taxonomy VID 4)

6. **`field_gerelateerd_repertoire`** (Entity Reference) - **REQUIRED when document_soort = "partituur"**
   - **Target**: `node` (repertoire content type)
   - **Cardinality**: Single value

##### **Migration Logic**:
- **D6 Verslag content** → `field_document_soort` = "verslag" + map D6 verslagen taxonomy to `field_verslag_type`
- **D6 Repertoire attached files** → `field_document_soort` = "partituur" + link to repertoire node
- **All MuseScore files (.mscz)** → `field_document_soort` = "partituur" + auto-link to D6 repertoire node
- **All other documents** → `field_document_soort` = "overig"
- **Name field** → D6 `field_files` description, fallback to filename

#### 3. **🎵 Audio Bundle** (`audio`)
- **Source Field**: `field_media_audio_file` (File field) 
- **File Extensions**: mp3, wav, ogg, m4a, aac, **mid, kar** (MIDI files moved from document bundle)
- **Usage**: Music recordings, audio content, MIDI files
- **Storage**: `/sites/default/files/media/audio/`
- **Fields**:
  - `name` (Built-in) - Audio title (from D6 node title)
  - `field_datum` (Date) - Recording date (**FROM D6 AUDIO**)
  - `field_audio_type` (Audio type) - **FROM D6 AUDIO**
  - `field_audio_uitvoerende` (Performer/Artist) - **FROM D6 AUDIO**
  - `field_audio_bijz` (Audio notes/description) - **FROM D6 AUDIO**
  - `field_gerelateerd_activiteit` (Related activity) - **OPTIONAL** (renamed from field_ref_activiteit)
  - `field_gerelateerd_repertoire` (Related repertoire) - **OPTIONAL** (renamed from field_repertoire)
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**

#### 4. **🎬 Video Bundle** (`video`)
- **Source Field**: `field_media_video_file` (File field) + `field_video` (Embedded)
- **File Extensions**: mp4, avi, mov, wmv, flv
- **Usage**: Video files and embedded content (YouTube, etc.)
- **Storage**: `/sites/default/files/media/video/`
- **Fields**:
  - `name` (Built-in) - Video title (from D6 node title)
  - `field_video` (Embedded video) - **FROM D6 VIDEO** 
  - `field_datum` (Date) - Video date (**FROM D6 VIDEO**)
  - `field_audio_type` (Media type) - **FROM D6 VIDEO**
  - `field_audio_uitvoerende` (Performer) - **FROM D6 VIDEO** 
  - `field_gerelateerd_activiteit` (Related activity) - **OPTIONAL** (renamed from field_ref_activiteit)
  - `field_gerelateerd_repertoire` (Related repertoire) - **OPTIONAL** (renamed from field_repertoire)
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**

### Content Type Media Integration ✅

#### Clear Media Reference Fields
To avoid confusion between file storage and content relationships, content types use semantic field names:

**Previous (Confusing)**:
- `field_files` → Could be files or media references?
- `field_afbeeldingen` → Could be images or media references?

**New (Clear)**:
- `field_media_documents` → Obviously references document media entities
- `field_media_images` → Obviously references image media entities
- `field_media_audio` → Obviously references audio media entities
- `field_media_video` → Obviously references video media entities

#### Separation of Concerns
- **Media Entities**: Store actual files (`field_media_document`, `field_media_image`, etc.)
- **Content Types**: Reference media entities (`field_media_documents`, `field_media_images`, etc.)

### Access Control System Discovery ✅

#### Existing D6 TAC Lite Implementation
- **Module**: TAC Lite (Taxonomy Access Control Lite) - ACTIVE
- **Access Vocabulary**: Vocabulary ID 4 
- **Field Usage**: `field_toegang` (already implemented in migration source plugins)
- **Admin Interface**: `/admin/user/access/tac_lite`

#### 12-Level Access Hierarchy (Vocabulary ID 4)

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

#### Access Migration Strategy ✅
1. **Migrate existing vocabulary 4** with all 12 terms intact
2. **Use `field_toegang`** field name across all media bundles and content types
3. **Implement Permissions by Term** module for D11 (equivalent of TAC Lite)
4. **Preserve all existing access relationships** and user expectations

### Field Naming Strategy ✅

#### Dutch Field Names (Preserved)
All field names maintain Dutch labels for user familiarity:
- `field_datum` (Date) - **FROM D6**
- `field_toegang` (Access) - **FROM D6 TAXONOMY VID 4**
- `field_audio_uitvoerende` (Performer) - **FROM D6**

#### Consistent Relationship Field Names
All relationship fields follow the same pattern:
- `field_gerelateerd_repertoire` (Related repertoire) - **CONSISTENT NAMING**
- `field_gerelateerd_activiteit` (Related activity) - **CONSISTENT NAMING**

#### Media Reference Fields (New Semantic Naming)
- `field_media_documents` (References document media entities)
- `field_media_images` (References image media entities)
- `field_media_audio` (References audio media entities)
- `field_media_video` (References video media entities)

### Implementation Benefits ✅

- **Zero Training Required**: Content editors already understand field structure and 12 access levels
- **Proven Granular Control**: Committee-specific access already working in D6
- **Role Alignment**: Access terms match existing user roles perfectly
- **Data Preservation**: All existing access relationships and field data maintained
- **Clean Architecture**: Clear separation between file storage (media entities) and content relationships (nodes)
- **Migration Simplicity**: Direct field-to-field mapping using existing D6 field names
- **Consistent Naming**: All relationship fields follow `field_gerelateerd_*` pattern
- **Meaningful Titles**: User-entered descriptions from D6 preserved as media titles

### Media Bundle Implementation Status

**Status**: ✅ **COMPLETE SPECIFICATION** - Ready for code implementation

#### Next Implementation Steps:
1. **Create media bundle setup script** using complete D6 field structure
2. **Migrate existing taxonomy vocabulary** (12 access terms)
3. **Configure bundle-specific fields** with D6 field names and dependencies
4. **Set up bundle-based file directory structure**
5. **Update existing migration configurations** for new media architecture
6. **Implement name field migration** from D6 descriptions and titles
7. **Test media entity creation** and file organization
8. **Implement Permissions by Term** module for access control

#### Key Implementation Notes:
- **MIDI files (.mid, .kar)** moved from document to audio bundle
- **MuseScore files (.mscz)** remain in document bundle (all attached to repertoire)
- **Field dependencies**: Document fields conditionally required based on type
- **Name migration**: D6 `field_files` description → D11 `name` field with filename fallback
- **Consistent relationships**: All `field_gerelateerd_*` fields follow same pattern
- **EXIF date extraction**: Images get dates from EXIF metadata when available
- **Photo date accuracy**: Activity photos use actual photo dates, not upload dates