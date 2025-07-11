#!/bin/bash

# Script directory and paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"
DRUPAL_ROOT="$(cd "$MODULE_DIR/../../../.." && pwd)"

# Ensure we're in the Drupal root
cd "$DRUPAL_ROOT"

echo "Cleaning Thirdwing Migration Data..."

# Verwijder problematische services file als die bestaat
if [ -f "modules/custom/thirdwing_migrate/thirdwing_migrate.services.yml" ]; then
    rm modules/custom/thirdwing_migrate/thirdwing_migrate.services.yml
    echo "Removed problematic services file"
fi

# Clear all caches
echo "Clearing caches..."
drush cr

# Reset all migration statuses
echo "Resetting migration statuses..."
drush migrate:reset-status --all

# Clear caches again after reset
drush cr

echo "=== Testing database connection ==="
# Test database connection
drush eval "
try {
  \$db = \Drupal\Core\Database\Database::getConnection('default', 'migrate');
  \$count = \$db->select('node', 'n')->countQuery()->execute()->fetchField();
  echo \"✓ Migration database connected. Found \$count nodes.\n\";
  
  // Check for D6 vs D7 tables
  \$d6_tables = ['node_revisions', 'vocabulary'];
  \$d7_tables = ['node_revision', 'taxonomy_vocabulary'];
  
  foreach (\$d6_tables as \$table) {
    if (\$db->schema()->tableExists(\$table)) {
      echo \"✓ Found D6 table: \$table\n\";
    }
  }
  
  foreach (\$d7_tables as \$table) {
    if (\$db->schema()->tableExists(\$table)) {
      echo \"⚠ Found D7 table: \$table (this might cause conflicts)\n\";
    }
  }
  
} catch (Exception \$e) {
  echo \"✗ Database connection failed: \" . \$e->getMessage() . \"\n\";
}
"

echo "=== Listing only Thirdwing migrations ==="
# Only show OUR migrations
drush migrate:status --group=thirdwing_d6 2>/dev/null || echo "No Thirdwing migrations found yet"

echo "=== Ready to run migrations ==="
echo "You can now run:"
echo "  drush migrate:import d6_thirdwing_taxonomy_vocabulary --feedback=10"
echo "  drush migrate:import d6_thirdwing_taxonomy_term --feedback=50"