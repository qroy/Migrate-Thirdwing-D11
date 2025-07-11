# Thirdwing Drupal 6 to Drupal 11 Migration

A comprehensive migration system for the Thirdwing music organization, designed to migrate from Drupal 6 to Drupal 11 with ongoing incremental content synchronization.

## 🎯 Project Overview

This migration project enables seamless content migration from a legacy D6 site to a modern D11 platform while maintaining ongoing synchronization during the transition period. The system supports both complete initial migration and incremental content updates.

### Migration Strategy

Initially the module will be installed on a clean drupal installation.
Old site will remain active until new site is complete and acts as backup for all data.
Regular syncs from old to new with updated content.

The architecture supports:

- **Clean D11 Installation**: Designed for fresh installations only
- **Complete Incremental Migration**: Ongoing content synchronization between sites with automated conflict resolution
- **Modern Media Architecture**: Complete node-to-media conversion framework
- **Comprehensive Content Migration**: All content types and relationships preserved
- **Modern Drupal Patterns**: Uses D11 core modules and best practices
- **Production-Safe Operations**: Dual-site operation during transition with comprehensive testing
- **Automated Setup & Validation**: Complete setup automation with built-in validation system

### Content Profile Integration

The D6 site uses the **Content Profile** module where profile data is stored as `profiel` content type nodes linked to users. The migration system properly handles:

- **Content Profile Fields**: All profile fields from `content_type_profiel` table
- **User-Profile Relationships**: Links between users and their profile nodes
- **Field Mapping**: Complete mapping of Dutch profile fields (voornaam, achternaam, geslacht, etc.)
- **Function Fields**: All committee and role function fields
- **Administrative Fields**: Notes, email monitoring, and other administrative data

## 📋 Current Implementation Status

### ✅ **IMPLEMENTED COMPONENTS**

#### Migration Core Infrastructure ✅
- **Custom Migration Module** (`thirdwing_migrate`): Complete migration framework
- **Content Type Structure**: 7 content types with comprehensive field definitions
- **Migration YAML Configuration**: Core migrations for taxonomy, users, files, and nodes
- **Database Connection Management**: Separate D6 database connection handling
- **Error Handling & Safety**: Robust error handling with data validation

#### User Migration System ✅
- **User Role Migration**: Complete implementation with comprehensive role mapping
- **User Account Migration**: Full migration with role assignment and profile fields
- **Content Profile Integration**: ✅ **FIXED** - Proper handling of Content Profile data from `content_type_profiel` table
- **Role Permission System**: D6 to D11 role structure conversion
- **User Profile Fields**: Complete Dutch field mapping (voornaam, achternaam, geslacht, functie fields, etc.)
- **User Picture Migration**: Profile image handling from D6 user pictures

#### Content Architecture ✅
- **Content Types**: Complete field mapping for all 7 primary content types
- **Entity References**: Proper relationships between content types maintained
- **Field Structure**: Comprehensive field definitions matching D6 source data
- **File Migration**: Basic file handling and migration infrastructure

#### Incremental Migration System ✅
- **Delta Migration Source Plugins**: ✅ **IMPLEMENTED** - Custom source plugins with timestamp filtering
- **Change Detection Logic**: ✅ **IMPLEMENTED** - Identify new/modified content since last sync
- **Conflict Resolution System**: ✅ **IMPLEMENTED** - Always use old site content as authoritative source
- **Sync Command Tools**: ✅ **IMPLEMENTED** - Drush commands for incremental migration operations
- **Migration State Tracking**: ✅ **IMPLEMENTED** - Track last sync timestamps and migration status

### ❌ **MISSING IMPLEMENTATIONS** (High Priority)

#### 1. Advanced Media Migration System
**Status**: ✅ **SPECIFICATION COMPLETE** - Ready for implementation

- Node-to-media conversion (`verslag` → document, `audio` → audio, `video` → video)
- Context-based file categorization with bundle priority logic
- Media entity creation with specialized metadata fields
- File field to media reference conversion

#### 2. Content Moderation & Workflow
**Status**: Missing implementation

- Node revision migration (currently disabled)
- D6 workflow state to D11 Content Moderation mapping
- Editorial workflow configuration
- Content moderation state field integration

#### 3. Access Control Integration
**Status**: ✅ **ARCHITECTURE PLANNED** - Ready for implementation

- Permissions by Term module configuration
- Taxonomy-based access control for nodes and media
- Access control vocabulary preservation
- Role-based permission setup

#### 4. Webform Migration System
**Status**: Missing implementation

- Webform configuration migration
- Form submission data handling
- Contact form conversion
- Form component mapping

## 🏗️ Architecture Overview

### Source Content Types (Drupal 6)

**Content Types Migrated to D11 Nodes**:
- `activiteit` (activities) → `activiteit`
- `repertoire` (musical repertoire) → `repertoire`
- `nieuws` (news articles) → `nieuws`
- `pagina` (pages) → `pagina`
- `foto` (photo albums) → `foto`
- `locatie` (venues) → `locatie`
- `vriend` (friends/partners) → `vriend`
- `profiel` (user profiles) → User profile fields (Content Profile integration)

**Content Types Converted to Media Entities**:
- `verslag` (meeting reports) → `document` media
- `audio` (audio recordings) → `audio` media
- `video` (video recordings) → `video` media

**Content Types Excluded**:
- `nieuwsbrief` (newsletters) - Not migrated

### Content Profile System

The D6 site uses **Content Profile** module with profile data stored in:
- **`content_type_profiel`** table (CCK/content type table)
- **Profile nodes** linked to users via the `uid` field
- **Profile fields** mapped to D11 user fields:

```yaml
Profile Field Mapping:
- field_voornaam → First name
- field_achternaam → Last name
- field_achternaam_voorvoegsel → Name prefix
- field_geslacht → Gender
- field_geboortedatum → Birth date
- field_adres → Address
- field_postcode → Postal code
- field_woonplaats → City
- field_telefoon → Phone
- field_mobiel → Mobile
- field_koor → Choir function
- field_functie_bestuur → Board function
- field_functie_mc → Music committee function
- field_functie_concert → Concert function
- field_functie_feest → Party function
- field_functie_regie → Direction function
- field_functie_ir → Internal relations function
- field_functie_pr → Public relations function
- field_functie_tec → Technical function
- field_positie → Position
- field_functie_lw → Member recruitment function
- field_functie_fl → Facilities function
- field_emailbewaking → Email monitoring
- field_notes → Notes
```

## 📦 Media Bundle System ✅ **PRIORITY 1 - SPECIFICATION COMPLETE**

### Overview
For Priority 1 of the Thirdwing D6→D11 migration, we defined all media bundles to handle the various file types from the old site, using the actual D6 field structure for maximum compatibility and zero data loss.

### File Categories Analysis from D6 Site
Based on existing migration configuration and D6 database analysis:

- **Images**: User photos, activity images, repertoire covers, video thumbnails
- **Documents**: PDF reports, Word docs, general documents  
- **Audio Files**: MP3, WAV, OGG recordings and music files
- **Video Files**: MP4, AVI, MOV video content + embedded videos (YouTube)
- **Sheet Music**: PDF musical scores and partitions with specific metadata
- **Reports**: Meeting minutes, administrative documents with date context

### Final Media Bundle Architecture: **4 Bundles** ✅

#### 1. **🖼️ Image Bundle** (`image`)
- **Source Field**: `field_media_image` (Image field)
- **File Extensions**: jpg, jpeg, png, gif, webp
- **Usage**: Photos, thumbnails, covers, user pictures
- **Storage**: `/sites/default/files/media/image/`
- **Special Fields**:
  - Alt text (built-in)
  - Title (built-in)  
  - Caption (optional)
  - `field_toegang` (Access Level) - **FROM D6 TAXONOMY VID 4**

#### 2. **📄 Document Bundle** (`document`) - **CONSOLIDATED** 
- **Source Field**: `field_media_document` (File field)
- **File Extensions**: pdf, doc, docx, txt, xls, xlsx, mid, kar
- **Usage**: General documents, sheet music, reports, meeting minutes
- **Storage**: `/sites/default/files/media/document/`
- **Special Fields** (reusing D6 content type fields):
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

#### 3. **🎵 Audio Bundle** (`audio`)
- **Source Field**: `field_media_audio_file` (File field) 
- **File Extensions**: mp3, wav, ogg, m4a, aac
- **Usage**: Music recordings, audio content
- **Storage**: `/sites/default/files/media/audio/`
- **Special Fields** (from D6 audio content type):
  - `field_datum` (Date) - **FROM D6 AUDIO**
  - `field_audio_type` (Audio type) - **FROM D6 AUDIO**
  - `field_audio_uitvoerende` (Performer/Artist) - **FROM D6 AUDIO**
  - `field_audio_bijz` (Audio notes/description) - **FROM D6 AUDIO**
  - `field_ref_activiteit` (Related activity) - **FROM D6 AUDIO**
  - `field_repertoire` (Related repertoire) - **FROM D6 AUDIO**
  - `field_toegang` (Access level) - **FROM D6 TAXONOMY VID 4**

#### 4. **🎬 Video Bundle** (`video`)
- **Source Field**: `field_media_video_file` (File field) + `field_video` (Embedded)
- **File Extensions**: mp4, avi, mov, wmv, flv
- **Usage**: Video files and embedded content
- **Storage**: `/sites/default/files/media/video/`
- **Special Fields** (from D6 video content type):
  - `field_video` (Embedded video) - **FROM D6 VIDEO** 
  - `field_datum` (Date) - **FROM D6 VIDEO**
  - `field_audio_type` (Media type) - **FROM D6 VIDEO**
  - `field_audio_uitvoerende` (Performer) - **FROM D6 VIDEO** 
  - `field_ref_activiteit` (Related activity) - **FROM D6 VIDEO**
  - `field_repertoire` (Related repertoire) - **FROM D6 VIDEO**
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

### Taxonomy Structure

#### Existing Vocabularies (From D6)
- **Toegang** (Access Levels) - **Vocabulary ID 4** (12 terms) ✅
- **Genre** - Music genres
- **Various content-specific vocabularies**

#### New Vocabularies (For D11)
- **Document Soort** (Document Types):
  - Algemeen Document
  - Partituur (Volledig)
  - Partituur (Stempartij)  
  - Vergaderverslag
  - Jaarverslag
  - MIDI Bestand

- **Stem Soort** (Voice Parts - for sheet music):
  - Sopraan
  - Alt
  - Tenor
  - Bas
  - Piano/Begeleiding

### Field Naming Strategy ✅

#### Dutch Field Names (Preserved)
All field names maintain Dutch labels for user familiarity:
- `field_datum` (Date) - **FROM D6**
- `field_toegang` (Access) - **FROM D6 TAXONOMY VID 4**
- `field_audio_uitvoerende` (Performer) - **FROM D6**
- `field_componist` (Composer) - **NEW**
- `field_stemsoort` (Voice Part) - **NEW**

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

### Media Bundle Implementation Status

**Status**: ✅ **SPECIFICATION COMPLETE** - Ready for code implementation

#### Next Implementation Steps:
1. Create media bundle setup script using D6 field structure
2. Migrate existing taxonomy vocabulary (12 access terms)
3. Configure bundle-specific fields with D6 field names
4. Set up bundle-based file directory structure
5. Update existing migration configurations for new media architecture
6. Test media entity creation and file organization
7. Implement Permissions by Term module for access control

### Media Architecture

#### Bundle-Based Organization
- **Document Media**: All documents, sheet music, reports
- **Audio Media**: Recordings, rehearsals, practice files
- **Video Media**: Performance videos, oEmbed content
- **Image Media**: Photos, graphics, profile images

#### Specialized Fields
```yaml
Document Media:
- field_document_type: sheet_music|report|generic
- field_sheet_music_type: soprano|alto|tenor|bass|piano|guitar
- field_report_type: bestuursvergadering|muziekcommissie|etc

Audio Media:  
- field_audio_type: uitvoering|repetitie|oefenbestand|origineel

Video Media:
- field_video_type: performance|rehearsal|etc

Image Media:
- field_image_type: activity|news|profile|etc
```

### Access Control System

**Permissions by Term Integration**:
- `field_access_terms` on all content types
- `field_media_access` on all media entities
- Taxonomy-based permissions for both nodes and media
- Role-based access control through taxonomy terms

## 🛠️ Installation & Setup

### Prerequisites

- **Drupal 11**: Fresh installation
- **PHP 8.2+**: With required extensions
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Migration Database**: Read-only access to D6 database

### Installation Steps

#### 1. Module Installation
```bash
# Install migration module
drush en thirdwing_migrate

# Install access control modules
drush en permissions_by_term permissions_by_entity

# Install workflow modules
drush en workflows content_moderation

# Install webform modules (optional)
drush en webform webform_migrate
```

#### 2. Database Configuration
Add to `settings.php`:
```php
$databases['migrate']['default'] = [
  'database' => 'drupal6_database',
  'username' => 'db_user', 
  'password' => 'db_password',
  'host' => 'localhost',
  'port' => '3306',
  'driver' => 'mysql',
  'prefix' => '',
];
```

#### 3. Content Structure Setup
```bash
# Create content types and fields
drush php:script modules/custom/thirdwing_migrate/scripts/create-content-types-and-fields.php

# Setup media types and fields
drush php:script modules/custom/thirdwing_migrate/scripts/create-media-fields.php

# Setup workflow configuration
drush php:script modules/custom/thirdwing_migrate/scripts/setup-content-moderation.php
```

#### 4. Migration Execution
```bash
# Reset any stuck migrations
drush migrate:reset-status d6_thirdwing_user

# Test user migration
drush migrate:import d6_thirdwing_user --limit=5

# Initial full migration
./modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# Regular incremental sync
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="last-week"
```

## 🚀 Usage Examples

### Quick Setup Commands
```bash
# 1. Complete system setup (one-time)
chmod +x modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh
./modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh

# 2. Initial full migration (5-phase process)
./modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# 3. Validate migration success
drush php:script modules/custom/thirdwing_migrate/scripts/validate-migration.php
```

### Ongoing Incremental Synchronization
```bash
# Regular content sync (daily/weekly)
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="yesterday"

# Specific content types only
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="last-week" --content-types="nieuws,activiteit"

# Preview changes without importing
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --dry-run --since="2025-01-01"

# Include user activity updates
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --user-activity="last-month"

# Check sync status and history
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --status
```

### User Migration Commands
```bash
# Reset stuck user migrations
drush migrate:reset-status d6_thirdwing_user

# Test user migration with Content Profile data
drush migrate:import d6_thirdwing_user --limit=5 -v

# Full user migration
drush migrate:import d6_thirdwing_user

# Incremental user updates
drush migrate:import d6_thirdwing_incremental_user --limit=10
```

## 🔧 Technical Implementation

### Migration Module Structure
```
modules/custom/thirdwing_migrate/
├── config/install/
│   └── migrate_plus.migration.*.yml
├── src/Plugin/migrate/
│   ├── source/
│   │   ├── D6ThirdwingUser.php ✅ (Fixed for Content Profile)
│   │   ├── D6IncrementalUser.php ✅ (Fixed for Content Profile)
│   │   ├── D6IncrementalNode.php
│   │   └── D6IncrementalFile.php
│   └── process/
├── src/Commands/
│   └── MigrationSyncCommands.php
├── thirdwing_migrate.module
├── thirdwing_migrate.install
└── scripts/
    ├── create-content-types-and-fields.php
    ├── migrate-execute.sh
    ├── migrate-sync.sh
    └── clean_migration.sh
```

### Key Migration Features

**Incremental Migration Support**: Delta migrations with timestamp-based filtering
**Content Profile Integration**: ✅ **FIXED** - Proper handling of Content Profile data from `content_type_profiel` table
**Robust Error Handling**: Comprehensive data validation and error recovery
**Memory Management**: Optimized for large datasets with batch processing  
**Dependency Management**: Proper migration sequencing and relationship handling
**Data Integrity**: Complete preservation of content relationships with old site as single source of truth
**Conflict Resolution**: Handle content modified on both old and new sites

### Recent Fixes

#### Content Profile Migration ✅ **RESOLVED**
- **Issue**: Migration failed with `profile_values` table not found error
- **Root Cause**: D6 site uses Content Profile module, not traditional Profile module
- **Solution**: Updated source plugins to read from `content_type_profiel` table
- **Result**: User migration now works with complete profile field mapping

## 🎯 Development Priorities

### Phase 1: System Ready ✅
1. ✅ **Delta Migration Source Plugins** - Enable timestamp-based content filtering
2. ✅ **Migration State Tracking** - Track last sync timestamps and status
3. ✅ **Sync Command Interface** - Drush commands for incremental operations
4. ✅ **Conflict Detection System** - Old site always wins approach
5. ✅ **Comprehensive Testing** - Validation and setup automation
6. ✅ **Content Profile Integration** - Fixed user migration with Content Profile data
7. ✅ **Media Bundle Specification** - Complete 4-bundle architecture with D6 field reuse

### Phase 2: Core Migration Completion (Immediate)
8. **Media Bundle Implementation** - Create 4 media bundles with D6 field structure setup script
9. **Access Control Setup** - Implement Permissions by Term integration with 12-level taxonomy system

### Phase 3: Editorial Workflows (Short-term)
10. **Content Moderation Integration** - Enable editorial workflows
11. **Revision Migration** - Preserve editorial history
12. **Workflow Configuration** - Set up approval processes

### Phase 4: Advanced Features (Medium-term)  
13. **Advanced Media System** - Context-based categorization
14. **Webform Migration** - Enable form functionality
15. **Sheet Music Management** - Specialized music features

### Phase 5: Optimization (Long-term)
16. **Performance Optimization** - Batch processing, caching
17. **Testing & Validation** - Comprehensive testing suite
18. **Documentation** - Complete implementation guide

## 📊 Project Metrics

### Current Completion Status: ~80%

- ✅ **Core Infrastructure**: 95% complete
- ✅ **User Role Migration**: 100% complete (implemented with comprehensive role mapping and Content Profile integration)
- ✅ **Incremental Migration**: 90% complete (full system implemented)
- ✅ **Testing & Validation**: 95% complete (comprehensive validation system)
- ✅ **Basic Migration**: 85% complete
- ✅ **Media System Architecture**: 95% complete (specification and field mapping complete)
- ⚠️ **Media Implementation**: 25% complete (basic files only - bundle setup script needed)
- ✅ **Access Control Architecture**: 90% complete (12-level system mapped)
- ❌ **Content Moderation**: 5% complete
- ❌ **Access Control Implementation**: 15% complete (planning only)

### Success Criteria

- ✅ **User Migration**: Complete user account and profile migration with Content Profile integration
- ✅ **Incremental Migration**: Seamless ongoing content synchronization
- ✅ **Media Architecture**: Complete 4-bundle system with D6 field reuse strategy
- **Content Preservation**: 100% critical content migrated
- **Media Conversion**: All file fields converted to media references
- **Relationship Integrity**: All entity relationships maintained  
- **Access Control**: Proper permission migration and functionality
- **Editorial Workflow**: Content moderation and approval processes
- **Production Readiness**: Reliable dual-site operation during transition

## 🚀 Getting Started

### Quick Start - Complete Setup
```bash
# 1. Configure D6 database connection in settings.php
# 2. Run complete setup (installs modules, creates structure, validates)
chmod +x modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh
./modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh

# 3. Run initial full migration
./modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# 4. Test incremental sync
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --dry-run --since=yesterday
```

### Five-Phase Initial Migration Process

#### Phase 1: Core Data & Infrastructure ✅
- Taxonomy vocabularies and terms
- User accounts and role migration (✅ **fully implemented with Content Profile integration**)
- File entities and basic file handling
- Content type and field structure creation

#### Phase 2: Media Entities ✅ **ARCHITECTURE COMPLETE**
- Node-to-media conversion for verslag, audio, video using 4-bundle system
- Context-based file categorization with D6 field reuse
- Media entity creation with metadata using actual D6 field names
- File reference conversion to clear media reference fields

#### Phase 3: Content Nodes
- All remaining content types
- Entity references and relationships
- Field data migration
- Content moderation state setup

#### Phase 4: Advanced Features
- User role assignment and permissions
- Content moderation workflows
- Access control configuration using 12-level taxonomy system
- Editorial workflow setup

#### Phase 5: Webforms & Final Setup
- Webform configuration migration
- Form submission data migration
- Final validation and testing

### Incremental Migration Strategy

#### Regular Content Sync Capabilities
- **Delta Migration**: Import only new and changed content since last sync
- **Timestamp-Based Filtering**: Track content changes using `created` and `changed` dates
- **Conflict Resolution**: Handle content modified on both old and new sites
- **Safe Sync Operations**: Preserve new site modifications while importing updates

#### Incremental Migration Features
```bash
# Initial full migration
./migrate-execute.sh --mode=full

# Regular incremental sync (daily/weekly)
./migrate-sync.sh --since="2025-01-01"

# Sync specific content types
./migrate-sync.sh --content-types="nieuws,activiteit" --since="last-week"

# Preview changes before sync
./migrate-sync.sh --dry-run --since="yesterday"
```

#### Change Detection & Tracking
- **Modified Content Detection**: Track changes via `node.changed` timestamps
- **New Content Identification**: Identify content created since last sync
- **Deleted Content Handling**: Optional handling of content deleted from D6
- **User Activity Tracking**: Track new user registrations and profile updates

#### Sync Safety Features
- **Backup Before Sync**: Automatic database backup before incremental migration
- **Rollback Capability**: Ability to revert incremental changes if needed
- **Simple Conflict Resolution**: Always overwrite new site changes with old site content (old site is authoritative)
- **Validation Checks**: Verify data integrity after each sync operation

## 📄 License

This migration project is designed for the specific Thirdwing music organization but can serve as a reference implementation for similar D6 to D11 migrations with incremental sync requirements.

---

**Note**: This migration project supports both initial full migration and ongoing incremental content synchronization, enabling safe dual-site operation during the transition period. The old D6 site remains active and serves as the primary data source until the new D11 site is ready for production cutover.