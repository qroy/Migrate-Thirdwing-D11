# Thirdwing Migration Module Documentation

## Overview

The Thirdwing Migration module is a comprehensive custom Drupal module designed to migrate content from a Drupal 6 installation to Drupal 11. The module handles the migration of a multi-lingual music organization website with complex content relationships, media files, and user-generated content.

## Module Information

- **Module Name**: thirdwing_migrate
- **Type**: Custom Migration Module
- **Drupal Compatibility**: Drupal 10 and 11
- **Package**: Custom
- **Description**: Migration module for Thirdwing D6 to D11

## Dependencies

### Required Drupal Core Modules
- `migrate` - Core migration framework
- `migrate_drupal` - Drupal-specific migration tools

### Required Contributed Modules
- `migrate_plus` - Enhanced migration functionality
- `migrate_tools` - Command-line migration tools

## Architecture

### Migration Group
All migrations are organized under the `thirdwing_d6` migration group, ensuring proper dependency management and execution order.

### Key Features
- **Clean Installation Ready**: Designed for fresh Drupal 11 installations
- **Dependency Management**: Proper migration ordering with dependency resolution
- **Data Integrity**: Comprehensive NULL value handling and data type conversion
- **Error Handling**: Robust error handling with fallback mechanisms
- **Core Migration Cleanup**: Automatically removes conflicting core migrations

## Migration Types

### 1. Core Data Migrations

#### Taxonomy System
- **d6_thirdwing_taxonomy_vocabulary**: Migrates taxonomy vocabularies
- **d6_thirdwing_taxonomy_term**: Migrates taxonomy terms with hierarchical relationships

#### User Management
- **d6_thirdwing_user**: Migrates user accounts with proper role mapping

#### File System
- **d6_thirdwing_file**: Migrates files with URL rewriting from `http://www.thirdwing.nl/` to `public://`

### 2. Media Migrations

The module creates specialized media entities for different file types:

- **d6_thirdwing_media_image**: Image files → Media Image entities
- **d6_thirdwing_media_document**: Documents → Media Document entities  
- **d6_thirdwing_media_audio**: Audio files → Media Audio entities
- **d6_thirdwing_media_video**: Video files → Media Video entities
- **d6_thirdwing_media_sheet_music**: Sheet music files → Media Sheet Music entities
- **d6_thirdwing_media_report**: Report files → Media Report entities

### 3. Content Migrations

#### Primary Content Types
- **d6_thirdwing_location**: Venue/location content (maps to 'locatie' content type)
- **d6_thirdwing_repertoire**: Musical repertoire entries
- **d6_thirdwing_program**: Concert programs with location and repertoire references
- **d6_thirdwing_activity**: Events and activities
- **d6_thirdwing_news**: News articles
- **d6_thirdwing_page**: Static pages
- **d6_thirdwing_album**: Photo albums with activity references
- **d6_thirdwing_friend**: Friend/partner organization listings
- **d6_thirdwing_newsletter**: Newsletter content

#### Community Content
- **d6_thirdwing_comment**: User comments with proper entity relationships

## Technical Implementation

### Custom Source Plugins

The module includes custom source plugins located in `src/Plugin/migrate/source/`:
- `D6ThirdwingTaxonomyVocabulary`: Handles taxonomy vocabulary data extraction
- `D6ThirdwingTaxonomyTerm`: Manages taxonomy term extraction with parent relationships

### Custom Process Plugins

#### AuthorLookupWithFallback
Located in `src/Plugin/migrate/process/AuthorLookupWithFallback.php`

**Purpose**: Ensures all content has valid author assignments, even when original authors don't exist.

**Features**:
- Primary lookup against migrated users
- Automatic fallback to UID 1 (admin)
- Optional creation of fallback users
- Configurable fallback user names

**Configuration**:
```yaml
uid:
  plugin: author_lookup_with_fallback
  migration: d6_thirdwing_user
  source: uid
  fallback_uid: 1
```

### Data Processing

#### NULL Value Handling
The module implements aggressive NULL value cleaning through the `hook_migrate_prepare_row()` implementation:

- **String Fields**: NULL values converted to empty strings
- **Numeric Fields**: NULL values converted to 0
- **Type Enforcement**: Ensures proper data types for all fields
- **Pattern Recognition**: Automatic field type detection based on naming patterns

#### Field Mapping Patterns

**String Field Patterns**:
- `name`, `title`, `description`, `body`, `mail`, `filename`
- `filepath`, `url`, `link`, `text`, `_value`, `_title`

**Numeric Field Patterns**:
- `nid`, `vid`, `tid`, `uid`, `fid`, `gid`, `weight`, `status`
- `created`, `changed`, `access`, `login`, `timestamp`, `filesize`

### Text Format Migration

The module includes text format mapping for content fields:

```yaml
body/format:
  plugin: static_map
  source: format
  map:
    1: basic_html
    3: full_html
    6: basic_html
  default_value: basic_html
```

## Installation and Setup

### Prerequisites
1. Clean Drupal 11 installation
2. Access to original Drupal 6 database
3. Composer for dependency management

### Installation Steps

1. **Install Dependencies**:
   ```bash
   composer require drupal/migrate_plus drupal/migrate_tools
   ```

2. **Enable Modules**:
   ```bash
   drush en migrate migrate_drupal migrate_plus migrate_tools thirdwing_migrate -y
   ```

3. **Configure Database Connection**:
   Add to `settings.php`:
   ```php
   $databases['migrate']['default'] = [
     'driver' => 'mysql',
     'database' => 'thirdwing_d6',
     'username' => 'your_username',
     'password' => 'your_password',
     'host' => 'localhost',
     'prefix' => '',
   ];
   ```

4. **Import Configurations**:
   ```bash
   drush config:import --source=modules/custom/thirdwing_migrate/config/install
   ```

## Migration Execution

### Setup Script (`migrate-setup.sh`)
Automated setup script that:
- Installs required modules via Composer
- Enables necessary modules
- Clears caches
- Imports migration configurations

### Execution Script (`migrate-execute.sh`)
Three-phase migration execution:

**Phase 1: Core Data**
- Taxonomy vocabularies and terms
- User accounts
- File entities

**Phase 2: Media**
- All media type migrations
- File association and URL rewriting

**Phase 3: Content**
- Content nodes with proper relationships
- Comments and user-generated content

### Cleanup Script (`clean_migration.sh`)
Maintenance script for:
- Database connection testing
- Migration status reset
- Cache clearing
- Drupal version detection

## File Handling

### Source Configuration
```yaml
constants:
  source_base_path: 'http://www.thirdwing.nl/'
  destination_base_path: 'public://'
```

### File Copy Process
```yaml
uri:
  plugin: file_copy
  source:
    - filepath
    - '@constants/source_base_path'
    - '@constants/destination_base_path'
```

Files are automatically downloaded from the original website and stored in the Drupal public files directory.

## Content Relationships

### Entity References
The module properly handles complex entity relationships:

- **Programs → Locations**: Concert venues
- **Programs → Repertoire**: Musical pieces
- **Albums → Activities**: Event photo galleries
- **Comments → Multiple Content Types**: Threaded discussions

### Reference Mapping Example
```yaml
field_locatie:
  plugin: migration_lookup
  migration: d6_thirdwing_location
  source: field_locatie_nid

field_repertoire:
  plugin: sub_process
  source: repertoire_items
  process:
    target_id:
      plugin: migration_lookup
      migration: d6_thirdwing_repertoire
      source: field_repertoire_nid
```

## Error Handling and Logging

### Migration Logging
- Comprehensive logging via Drupal's logging system
- Migration progress tracking
- Error reporting with context
- Performance monitoring

### Fallback Mechanisms
- Author fallback for orphaned content
- Default value assignment for missing fields
- Status preservation with safe defaults

## Module Lifecycle Management

### Installation Hook
```php
function thirdwing_migrate_install() {
  \Drupal::messenger()->addStatus(t('Thirdwing Migration module installed.'));
  \Drupal::logger('thirdwing_migrate')->info('Module installed.');
}
```

### Uninstallation Hook
Comprehensive cleanup including:
- Migration status reset
- Configuration removal
- Database table cleanup
- Cache clearing

### Requirements Hook
Runtime validation of:
- Database connectivity
- Migration dependencies
- System requirements

## Configuration Files

### Migration Group Configuration
`config/install/migrate_plus.migration_group.thirdwing_d6.yml`

### Individual Migration Configurations
Located in `config/install/` directory:
- One YAML file per migration
- Proper dependency declarations
- Source and destination mappings
- Process pipeline definitions

## Performance Considerations

### Batch Processing
- Configurable feedback intervals
- Memory-efficient processing
- Progress reporting

### Database Optimization
- Efficient query patterns
- Minimal data loading
- Connection pooling

## Security Considerations

### Data Sanitization
- Input validation for all source data
- XSS protection through format mapping
- SQL injection prevention

### Access Control
- Proper user role migration
- Permission preservation
- Content access validation

## Troubleshooting

### Common Issues
1. **Database Connection**: Verify migration database settings
2. **Missing Dependencies**: Check required module installation
3. **Memory Limits**: Adjust PHP memory for large migrations
4. **File Permissions**: Ensure proper file directory permissions

### Debugging Tools
- Migration status reporting: `drush migrate:status --group=thirdwing_d6`
- Error message viewing: `drush migrate:messages [migration_id]`
- Migration reset: `drush migrate:reset-status [migration_id]`

## Post-Migration Tasks

### Recommended Actions
1. Clear all caches: `drush cr`
2. Rebuild permissions: `drush eval "node_access_rebuild();"`
3. Generate URL aliases: `drush pathauto:update-aliases --all`
4. Install and configure the Thirdwing theme
5. Verify content relationships
6. Test media file accessibility
7. Validate user accounts and permissions

## Maintenance

### Module Updates
- Version control for configuration changes
- Testing migration modifications
- Rollback procedures

### Data Validation
- Content integrity checks
- Relationship verification
- Media file validation

## Conclusion

The Thirdwing Migration module provides a robust, production-ready solution for migrating a complex Drupal 6 music organization website to Drupal 11. Its comprehensive approach to data integrity, relationship preservation, and error handling ensures a successful migration with minimal data loss and maximum content preservation.

The module's design emphasizes clean installation compatibility, making it suitable for fresh Drupal 11 deployments while maintaining the flexibility to handle complex content relationships and media management requirements.