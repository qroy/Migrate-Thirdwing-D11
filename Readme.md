# Thirdwing Drupal 11 Migration Project

A comprehensive migration system for upgrading a Drupal 6 music organization website to Drupal 11, featuring advanced media conversion, access control, content moderation, and **incremental migration capabilities**.

## 🎯 Project Overview

This project migrates a complex Drupal 6 choir/band website to modern Drupal 11 architecture. The migration transforms legacy content types into a clean, media-centric structure while preserving all editorial history and access control.

### Key Migration Features

- **Incremental Migration Strategy**: Support for ongoing content synchronization between old and new sites
- **Node-to-Media Conversion**: Legacy content types converted to modern media entities
- **Advanced Media System**: Context-aware file categorization with specialized metadata
- **Access Control Migration**: Taxonomy-based permissions using Permissions by Term
- **Editorial Workflows**: D6 workflow states mapped to D11 Content Moderation
- **Comprehensive Content Migration**: All content types, fields, and relationships preserved
- **Regular Content Sync**: Automated tools for syncing new and updated content

## 🔄 Migration Execution Strategy

### Dual-Site Migration Approach

The migration supports a **dual-site strategy** where:
- **Old D6 site remains active** during migration and testing
- **New D11 site is built incrementally** on clean installation
- **Regular content synchronization** keeps new site updated
- **Seamless cutover** when new site is ready

### Initial Migration Process (Five-Phase)

#### Phase 1: Core Data & Infrastructure
- Taxonomy vocabularies and terms
- User accounts and role migration
- File entities and basic file handling
- Content type and field structure creation

#### Phase 2: Media Entities
- Node-to-media conversion for verslag, audio, video
- Context-based file categorization
- Media entity creation with metadata
- File reference conversion

#### Phase 3: Content Nodes
- All remaining content types
- Entity references and relationships
- Field data migration
- Content moderation state setup

#### Phase 4: Advanced Features
- User role assignment and permissions
- Content moderation workflows
- Access control configuration
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

## 📋 Current Implementation Status

### ✅ **IMPLEMENTED COMPONENTS**

#### Migration Core Infrastructure
- **Custom Migration Module** (`thirdwing_migrate`): Complete migration framework
- **Content Type Structure**: 7 content types with comprehensive field definitions
- **Migration YAML Configuration**: Core migrations for taxonomy, users, files, and nodes
- **Database Connection Management**: Separate D6 database connection handling
- **Error Handling & Safety**: Robust error handling with data validation

#### Content Architecture
- **User Role Migration**: Complete implementation with comprehensive role mapping
- **User Account Migration**: Full migration with role assignment and profile fields
- **Role Permission System**: D6 to D11 role structure conversion
- **User Profile Fields**: Complete Dutch field mapping (voornaam, achternaam, etc.)
- **Content Types**: Complete field mapping for all 7 primary content types
- **Entity References**: Proper relationships between content types maintained
- **Field Structure**: Comprehensive field definitions matching D6 source data
- **File Migration**: Basic file handling and migration infrastructure

### ❌ **MISSING IMPLEMENTATIONS** (High Priority)

#### 1. Incremental Migration System
**Status**: Architecture planned, implementation needed

- **Delta Migration Source Plugins**: Custom source plugins with timestamp filtering
- **Change Detection Logic**: Identify new/modified content since last sync
- **Conflict Resolution System**: Always use old site content as authoritative source
- **Sync Command Tools**: Drush commands for incremental migration operations
- **Migration State Tracking**: Track last sync timestamps and migration status

#### 2. Advanced Media Migration System
**Status**: Specification complete, implementation needed

- Node-to-media conversion (`verslag` → document, `audio` → audio, `video` → video)
- Context-based file categorization with bundle priority logic
- Media entity creation with specialized metadata fields
- File field to media reference conversion

#### 3. Content Moderation & Workflow
**Status**: Missing implementation

- Node revision migration (currently disabled)
- D6 workflow state to D11 Content Moderation mapping
- Editorial workflow configuration
- Content moderation state field integration

#### 4. Access Control Integration
**Status**: Missing implementation

- Node revision migration (currently disabled)
- D6 workflow state to D11 Content Moderation mapping
- Editorial workflow configuration
- Content moderation state field integration

#### 5. Access Control Integration
**Status**: Architecture planned, implementation needed

- Permissions by Term module configuration
- Taxonomy-based access control for nodes and media
- Access control vocabulary preservation
- Role-based permission setup

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

**Content Types Converted to Media Entities**:
- `verslag` (meeting reports) → `document` media
- `audio` (audio recordings) → `audio` media
- `video` (video recordings) → `video` media

**Content Types Excluded**:
- `nieuwsbrief` (newsletters) - Not migrated

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
drush php:script create-content-types-and-fields.php

# Setup media types and fields
drush php:script create-media-fields.php

# Setup workflow configuration
drush php:script setup-content-moderation.php
```

#### 4. Migration Execution
```bash
# Initial full migration
./migrate-execute.sh

# Regular incremental sync
./migrate-sync.sh --since="last-week"
```

## 🔧 Technical Implementation

### Migration Module Structure
```
modules/custom/thirdwing_migrate/
├── config/install/
│   └── migrate_plus.migration.*.yml
├── src/Plugin/migrate/
│   ├── source/
│   │   ├── D6IncrementalNode.php
│   │   ├── D6IncrementalUser.php
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
**Robust Error Handling**: Comprehensive data validation and error recovery
**Memory Management**: Optimized for large datasets with batch processing  
**Dependency Management**: Proper migration sequencing and relationship handling
**Data Integrity**: Complete preservation of content relationships with old site as single source of truth
**Conflict Resolution**: Handle content modified on both old and new sites

## 🎯 Development Priorities

### Phase 1: Incremental Migration Foundation (Critical)
1. **Delta Migration Source Plugins** - Enable timestamp-based content filtering
2. **Migration State Tracking** - Track last sync timestamps and status
3. **Sync Command Interface** - Drush commands for incremental operations
4. **Conflict Detection System** - Identify and handle content conflicts

### Phase 2: Core Migration Completion (Immediate)
5. **Basic Media Migration** - Convert files to media entities  
6. **Access Control Setup** - Implement Permissions by Term integration

### Phase 3: Editorial Workflows (Short-term)
7. **Content Moderation Integration** - Enable editorial workflows
8. **Revision Migration** - Preserve editorial history
9. **Workflow Configuration** - Set up approval processes

### Phase 4: Advanced Features (Medium-term)  
10. **Advanced Media System** - Context-based categorization
11. **Webform Migration** - Enable form functionality
12. **Sheet Music Management** - Specialized music features

### Phase 5: Optimization (Long-term)
13. **Performance Optimization** - Batch processing, caching
14. **Testing & Validation** - Comprehensive testing suite
15. **Documentation** - Complete implementation guide

## 📊 Project Metrics

### Current Completion Status: ~25%

- ✅ **Core Infrastructure**: 90% complete
- ✅ **Basic Migration**: 70% complete
- ❌ **Incremental Migration**: 0% complete (priority implementation)
- ⚠️ **Media System**: 20% complete (basic files only)
- ❌ **User Roles**: 0% complete
- ❌ **Content Moderation**: 0% complete
- ❌ **Access Control**: 10% complete (planning only)

### Success Criteria

- **Incremental Migration**: Seamless ongoing content synchronization
- **Content Preservation**: 100% critical content migrated
- **Media Conversion**: All file fields converted to media references
- **Relationship Integrity**: All entity relationships maintained  
- **Access Control**: Proper permission migration and functionality
- **Editorial Workflow**: Content moderation and approval processes
- **Production Readiness**: Reliable dual-site operation during transition

## 🚀 Getting Started

### Quick Start - Initial Migration
```bash
# 1. Install Drupal 11 fresh installation
# 2. Configure migration database connection
# 3. Install required modules
drush en thirdwing_migrate permissions_by_term workflows

# 4. Create content structure
drush php:script create-content-types-and-fields.php

# 5. Run initial migration
./migrate-execute.sh
```

### Quick Start - Incremental Sync
```bash
# Daily content sync
./migrate-sync.sh --since="yesterday"

# Weekly comprehensive sync
./migrate-sync.sh --since="last-week" --include-media

# Sync specific content types
./migrate-sync.sh --content-types="nieuws,activiteit" --since="2025-01-01"
```

### Testing Migration
```bash
# Test database connection
drush eval "print_r(\Drupal\Core\Database\Database::getConnection('default', 'migrate')->select('node', 'n')->countQuery()->execute()->fetchField());"

# Check migration status
drush migrate:status --group=thirdwing_d6

# Test incremental sync (dry run)
./migrate-sync.sh --dry-run --since="yesterday"

# Run individual migrations for testing
drush migrate:import d6_thirdwing_taxonomy_vocabulary --feedback=10
```

## 🤝 Contributing

This project provides a comprehensive foundation for Drupal 6 to 11 migrations with incremental sync capabilities, especially for music organizations. The architecture supports:

- **Clean D11 Installation**: Designed for fresh installations only
- **Incremental Migration**: Ongoing content synchronization between sites
- **Modern Media Architecture**: Complete node-to-media conversion
- **Comprehensive Content Migration**: All content types and relationships
- **Modern Drupal Patterns**: Uses D11 core modules and best practices
- **Production-Safe Operations**: Dual-site operation during transition

## 📄 License

This migration project is designed for the specific Thirdwing music organization but can serve as a reference implementation for similar D6 to D11 migrations with incremental sync requirements.

---

**Note**: This migration project supports both initial full migration and ongoing incremental content synchronization, enabling safe dual-site operation during the transition period. The old D6 site remains active and serves as the primary data source until the new D11 site is ready for production cutover.