# Thirdwing Drupal 11 Migration Project

A comprehensive migration system for upgrading a Drupal 6 music organization website to Drupal 11, featuring advanced media conversion, access control, and content moderation.

## 🎯 Project Overview

This project migrates a complex Drupal 6 choir/band website to modern Drupal 11 architecture. The migration transforms legacy content types into a clean, media-centric structure while preserving all editorial history and access control.

### Key Migration Features

- **Node-to-Media Conversion**: Legacy content types converted to modern media entities
- **Advanced Media System**: Context-aware file categorization with specialized metadata
- **Access Control Migration**: Taxonomy-based permissions using Permissions by Term
- **Editorial Workflows**: D6 workflow states mapped to D11 Content Moderation
- **Comprehensive Content Migration**: All content types, fields, and relationships preserved

## 📋 Current Implementation Status

### ✅ **IMPLEMENTED COMPONENTS**

#### Migration Core Infrastructure
- **Custom Migration Module** (`thirdwing_migrate`): Complete migration framework
- **Content Type Structure**: 7 content types with comprehensive field definitions
- **Migration YAML Configuration**: Core migrations for taxonomy, users, files, and nodes
- **Database Connection Management**: Separate D6 database connection handling
- **Error Handling & Safety**: Robust error handling with data validation

#### Content Architecture
- **Content Types**: Complete field mapping for all 7 primary content types
- **Entity References**: Proper relationships between content types maintained
- **Field Structure**: Comprehensive field definitions matching D6 source data
- **File Migration**: Basic file handling and migration infrastructure

### ❌ **MISSING IMPLEMENTATIONS** (High Priority)

#### 1. Advanced Media Migration System
**Status**: Specification complete, implementation needed

- Node-to-media conversion (`verslag` → document, `audio` → audio, `video` → video)
- Context-based file categorization with bundle priority logic
- Media entity creation with specialized metadata fields
- File field to media reference conversion

#### 2. User Role Migration
**Status**: Critical gap - currently disabled

- D6 user role migration (currently disabled in migration list)
- Role assignment during user migration process
- Role permission mapping and configuration
- Integration with access control system

#### 3. Content Moderation & Workflow
**Status**: Missing implementation

- Node revision migration (currently disabled)
- D6 workflow state to D11 Content Moderation mapping
- Editorial workflow configuration
- Content moderation state field integration

#### 4. Access Control Integration
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

# Setup media types and fields (when implemented)
drush php:script create-media-fields.php

# Setup workflow configuration (when implemented) 
drush php:script setup-content-moderation.php
```

#### 4. Migration Execution
```bash
# Clean and prepare migration
./clean_migration.sh

# Execute migration (5-phase approach)
./migrate-execute.sh
```

## 🔄 Migration Execution Strategy

### Five-Phase Migration Process

#### Phase 1: Core Data
- Taxonomy vocabularies and terms
- User accounts (without roles currently)
- File entities and basic file handling

#### Phase 2: Media Entities (Implementation Needed)
- Node-to-media conversion for verslag, audio, video
- Context-based file categorization
- Media entity creation with metadata

#### Phase 3: Content Nodes
- All remaining content types
- Entity references and relationships
- Field data migration

#### Phase 4: Advanced Features (Implementation Needed)
- User role assignment
- Content moderation states
- Access control configuration

#### Phase 5: Webforms (Optional)
- Webform configuration migration
- Form submission data migration

## 🔧 Technical Implementation

### Migration Module Structure
```
modules/custom/thirdwing_migrate/
├── config/install/
│   └── migrate_plus.migration.*.yml
├── src/Plugin/migrate/
│   ├── source/
│   └── process/
├── thirdwing_migrate.module
├── thirdwing_migrate.install
└── scripts/
    ├── create-content-types-and-fields.php
    └── clean_migration.sh
```

### Key Migration Features

**Robust Error Handling**: Comprehensive data validation and error recovery
**Memory Management**: Optimized for large datasets with batch processing  
**Dependency Management**: Proper migration sequencing and relationship handling
**Data Integrity**: Complete preservation of content relationships

## 🎯 Development Priorities

### Phase 1: Critical Implementation (Immediate)
1. **User Role Migration** - Enable proper access control
2. **Basic Media Migration** - Convert files to media entities  
3. **Access Control Setup** - Implement Permissions by Term integration

### Phase 2: Editorial Workflows (Short-term)
4. **Content Moderation Integration** - Enable editorial workflows
5. **Revision Migration** - Preserve editorial history
6. **Workflow Configuration** - Set up approval processes

### Phase 3: Advanced Features (Medium-term)  
7. **Advanced Media System** - Context-based categorization
8. **Webform Migration** - Enable form functionality
9. **Sheet Music Management** - Specialized music features

### Phase 4: Optimization (Long-term)
10. **Performance Optimization** - Batch processing, caching
11. **Testing & Validation** - Comprehensive testing suite
12. **Documentation** - Complete implementation guide

## 📊 Project Metrics

### Current Completion Status: ~30%

- ✅ **Core Infrastructure**: 90% complete
- ✅ **Basic Migration**: 70% complete
- ⚠️ **Media System**: 20% complete (basic files only)
- ❌ **User Roles**: 0% complete
- ❌ **Content Moderation**: 0% complete
- ❌ **Access Control**: 10% complete (planning only)

### Success Criteria

- **Content Preservation**: 100% critical content migrated
- **Media Conversion**: All file fields converted to media references
- **Relationship Integrity**: All entity relationships maintained  
- **Access Control**: Proper permission migration and functionality
- **Editorial Workflow**: Content moderation and approval processes

## 🚀 Getting Started

### Quick Start
```bash
# 1. Install Drupal 11 fresh installation
# 2. Configure migration database connection
# 3. Install required modules
drush en thirdwing_migrate permissions_by_term workflows

# 4. Create content structure
drush php:script create-content-types-and-fields.php

# 5. Run migration
./migrate-execute.sh
```

### Testing Migration
```bash
# Test database connection
drush eval "print_r(\Drupal\Core\Database\Database::getConnection('default', 'migrate')->select('node', 'n')->countQuery()->execute()->fetchField());"

# Check migration status
drush migrate:status --group=thirdwing_d6

# Run individual migrations for testing
drush migrate:import d6_thirdwing_taxonomy_vocabulary --feedback=10
```

## 🤝 Contributing

This project provides a solid foundation for Drupal 6 to 11 migrations, especially for music organizations. The architecture supports:

- **Clean D11 Installation**: Designed for fresh installations only
- **Modern Media Architecture**: Complete node-to-media conversion
- **Comprehensive Content Migration**: All content types and relationships
- **Modern Drupal Patterns**: Uses D11 core modules and best practices

## 📄 License

This migration project is designed for the specific Thirdwing music organization but can serve as a reference implementation for similar D6 to D11 migrations.

---

**Note**: This is a migration-only project focused on data architecture and content preservation. Theming and frontend presentation are handled separately using standard Drupal 11 themes or custom theme development.