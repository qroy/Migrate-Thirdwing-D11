<?php
/**
 * @file
 * Script to validate migration results.
 * 
 * Run with: drush php:script validate-migration.php
 */

use Drupal\Core\Database\Database;

echo "=== Thirdwing Migration Validation ===\n\n";

// Get database connections
$default_db = Database::getConnection('default');
$migrate_db = Database::getConnection('default', 'migrate');

// Check if migrate database is accessible
try {
  $migrate_db->select('node', 'n')->countQuery()->execute()->fetchField();
  echo "✓ D6 database connection working\n";
} catch (Exception $e) {
  echo "✗ D6 database connection failed: " . $e->getMessage() . "\n";
  exit(1);
}

echo "\n=== Content Counts Comparison ===\n";

$content_types = [
  'activiteit' => 'activity',
  'repertoire' => 'repertoire', 
  'nieuws' => 'news',
  'pagina' => 'page',
  'foto' => 'album',
  'locatie' => 'location',
  'vriend' => 'friend',
  'nieuwsbrief' => 'newsletter',
];

foreach ($content_types as $d6_type => $d11_type) {
  // Count D6 nodes
  $d6_count = $migrate_db->select('node', 'n')
    ->condition('type', $d6_type)
    ->countQuery()
    ->execute()
    ->fetchField();
    
  // Count D11 nodes
  $d11_count = $default_db->select('node', 'n')
    ->condition('type', $d11_type)
    ->countQuery()
    ->execute()
    ->fetchField();
    
  $status = ($d6_count == $d11_count) ? '✓' : '⚠';
  echo "$status $d6_type: $d6_count → $d11_type: $d11_count\n";
}

echo "\n=== User Migration ===\n";
$d6_users = $migrate_db->select('users', 'u')
  ->condition('uid', 0, '>')
  ->countQuery()
  ->execute()
  ->fetchField();
  
$d11_users = $default_db->select('users', 'u')
  ->condition('uid', 0, '>')
  ->countQuery()
  ->execute()
  ->fetchField();
  
$status = ($d6_users <= $d11_users) ? '✓' : '⚠';
echo "$status Users: $d6_users → $d11_users\n";

echo "\n=== File Migration ===\n";
$d6_files = $migrate_db->select('files', 'f')
  ->countQuery()
  ->execute()
  ->fetchField();
  
$d11_files = $default_db->select('file_managed', 'f')
  ->countQuery()
  ->execute()
  ->fetchField();
  
$status = ($d6_files <= $d11_files) ? '✓' : '⚠';
echo "$status Files: $d6_files → $d11_files\n";

echo "\n=== Media Entities ===\n";
$media_bundles = ['image', 'document', 'audio', 'video', 'sheet_music'];

foreach ($media_bundles as $bundle) {
  $count = $default_db->select('media', 'm')
    ->condition('bundle', $bundle)
    ->countQuery()
    ->execute()
    ->fetchField();
  echo "✓ $bundle: $count entities\n";
}

echo "\n=== Migration Status ===\n";
$migrations = \Drupal::service('plugin.manager.migration')->createInstances([]);

foreach ($migrations as $migration_id => $migration) {
  if (strpos($migration_id, 'thirdwing') !== false) {
    $status = $migration->getStatus();
    $imported = $migration->getIdMap()->importedCount();
    $total = $migration->getIdMap()->messageCount();
    
    echo "$migration_id: $status ($imported imported)\n";
  }
}

echo "\n=== Validation Complete ===\n";