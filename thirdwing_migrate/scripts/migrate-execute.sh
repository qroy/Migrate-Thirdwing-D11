#!/bin/bash

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
    echo "Please run ./migrate-setup.sh first."
    exit 1
fi

# Phase 1: Core Data (UPDATED - now includes roles)
echo "Phase 1: Importing core data..."
drush migrate:import d6_thirdwing_taxonomy_vocabulary --feedback="10 items"
drush migrate:import d6_thirdwing_taxonomy_term --feedback="50 items"
drush migrate:import d6_thirdwing_user_role --feedback="20 items"
drush migrate:import d6_thirdwing_user --feedback="50 items"
drush migrate:import d6_thirdwing_file --feedback="100 items"

# Phase 2: Media
echo "Phase 2: Importing media..."
drush migrate:import d6_thirdwing_media_image --feedback="100 items"
drush migrate:import d6_thirdwing_media_document --feedback="100 items"
drush migrate:import d6_thirdwing_media_audio --feedback="50 items"
drush migrate:import d6_thirdwing_media_video --feedback="50 items"
drush migrate:import d6_thirdwing_media_sheet_music --feedback="50 items"
drush migrate:import d6_thirdwing_media_report --feedback="50 items"

# Phase 3: Content
echo "Phase 3: Importing content..."
drush migrate:import d6_thirdwing_location --feedback="20 items"
drush migrate:import d6_thirdwing_repertoire --feedback="100 items"
drush migrate:import d6_thirdwing_program --feedback="50 items"
drush migrate:import d6_thirdwing_activity --feedback="100 items"
drush migrate:import d6_thirdwing_news --feedback="100 items"
drush migrate:import d6_thirdwing_page --feedback="50 items"
drush migrate:import d6_thirdwing_album --feedback="50 items"
drush migrate:import d6_thirdwing_friend --feedback="50 items"
drush migrate:import d6_thirdwing_newsletter --feedback="20 items"
drush migrate:import d6_thirdwing_comment --feedback="100 items"

echo "Migration completed!"

# Generate report
echo "Generating migration report..."
drush migrate:status --group=thirdwing_d6

echo "Post-migration tasks:"
echo "1. Clear all caches: drush cr"
echo "2. Rebuild permissions: drush eval \"node_access_rebuild();\""
echo "3. Generate URL aliases: drush pathauto:update-aliases --all"
echo "4. Configure role permissions: Visit /admin/people/permissions"
echo "5. Install and configure the Thirdwing theme"

# Optional: Show role migration results
echo ""
echo "Role migration summary:"
drush migrate:messages d6_thirdwing_user_role