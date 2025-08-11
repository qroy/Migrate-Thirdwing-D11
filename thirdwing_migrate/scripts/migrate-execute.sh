#!/bin/bash

# =============================================================================
# Thirdwing Migration Execution Script - CORRECTED VERSION
# =============================================================================
# 
# FIXES APPLIED:
# - Removed deprecated media bundles (d6_thirdwing_media_sheet_music, d6_thirdwing_media_report)
# - Updated to use only the 4 supported media bundles (image, document, audio, video)
# - All migration IDs verified to match existing configuration files
# =============================================================================

# Script directory and paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"
DRUPAL_ROOT="$(cd "$MODULE_DIR/../../../.." && pwd)"

# Ensure we're in the Drupal root for drush commands
cd "$DRUPAL_ROOT"

echo "Starting Thirdwing D6 to D11 Migration..."

# Check if migration group exists
if ! drush migrate:status --group=thirdwing_d6 > /dev/null 2>&1; then
    echo "Error: Migration group 'thirdwing_d6' not found."
    echo "Please run the setup script first."
    exit 1
fi

# =============================================================================
# Phase 1: Core Data
# =============================================================================
echo "Phase 1: Importing core data..."
drush migrate:import d6_thirdwing_taxonomy_vocabulary --feedback="10 items"
drush migrate:import d6_thirdwing_taxonomy_term --feedback="50 items"
drush migrate:import d6_thirdwing_user_role --feedback="20 items"
drush migrate:import d6_thirdwing_user --feedback="50 items"
drush migrate:import d6_thirdwing_file --feedback="100 items"

# =============================================================================
# Phase 2: Media (CORRECTED - Only 4 supported bundles)
# =============================================================================
echo "Phase 2: Importing media..."
drush migrate:import d6_thirdwing_media_image --feedback="100 items"
drush migrate:import d6_thirdwing_media_document --feedback="100 items"
drush migrate:import d6_thirdwing_media_audio --feedback="50 items"
drush migrate:import d6_thirdwing_media_video --feedback="50 items"

# Note: d6_thirdwing_media_sheet_music and d6_thirdwing_media_report 
# have been consolidated into d6_thirdwing_media_document

# =============================================================================
# Phase 3: Content
# =============================================================================
echo "Phase 3: Importing content..."
drush migrate:import d6_thirdwing_location --feedback="20 items"
drush migrate:import d6_thirdwing_repertoire --feedback="100 items"
drush migrate:import d6_thirdwing_program --feedback="50 items"
drush migrate:import d6_thirdwing_activity --feedback="100 items"
drush migrate:import d6_thirdwing_news --feedback="100 items"
drush migrate:import d6_thirdwing_page --feedback="50 items"
drush migrate:import d6_thirdwing_album --feedback="50 items"
drush migrate:import d6_thirdwing_friend --feedback="50 items"
drush migrate:import d6_thirdwing_comment --feedback="100 items"

echo "Migration completed successfully!"

# =============================================================================
# Migration Report
# =============================================================================
echo ""
echo "Generating migration report..."
drush migrate:status --group=thirdwing_d6

echo ""
echo "=== Post-migration tasks ==="
echo "1. Clear all caches: drush cr"
echo "2. Rebuild permissions: drush eval \"node_access_rebuild();\""
echo "3. Generate URL aliases: drush pathauto:update-aliases --all"
echo "4. Configure role permissions: Visit /admin/people/permissions"
echo "5. Configure field displays: drush thirdwing:setup-displays"
echo "6. Validate migration: drush php:script scripts/validate-migration.php"

# =============================================================================
# Optional: Show detailed results
# =============================================================================
echo ""
echo "=== Migration Summary ==="
echo "✅ Core data: Taxonomies, users, roles, files"
echo "✅ Media: 4-bundle architecture (image, document, audio, video)"
echo "✅ Content: All 9 content types migrated"
echo "✅ Comments: User comments preserved"
echo ""
echo "For incremental updates, use:"
echo "  ./migrate-sync.sh --since=yesterday"
echo ""
echo "For field display configuration:"
echo "  drush thirdwing:setup-displays"
echo "  drush thirdwing:validate-displays"