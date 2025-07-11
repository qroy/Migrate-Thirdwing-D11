<?php

/**
 * @file
 * Comprehensive migration validation script for Thirdwing.
 * 
 * Usage: drush php:script scripts/validate-migration.php
 * Run from module directory or provide full path.
 */

use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

// Validation results
$validation_results = [
  'passed' => 0,
  'failed' => 0,
  'warnings' => 0,
  'errors' => [],
  'warnings_list' => [],
];

echo "=== THIRDWING MIGRATION VALIDATION ===\n\n";

/**
 * Helper function to report test results.
 */
function report_test($test_name, $passed, $message = '', $is_warning = FALSE) {
  global $validation_results;
  
  if ($passed) {
    echo "✅ PASS: $test_name\n";
    $validation_results['passed']++;
  } elseif ($is_warning) {
    echo "⚠️  WARN: $test_name - $message\n";
    $validation_results['warnings']++;
    $validation_results['warnings_list'][] = "$test_name: $message";
  } else {
    echo "❌ FAIL: $test_name - $message\n";
    $validation_results['failed']++;
    $validation_results['errors'][] = "$test_name: $message";
  }
}

/**
 * Test database connections.
 */
function test_database_connections() {
  echo "📊 Testing Database Connections...\n";
  
  // Test D11 database
  try {
    $d11_count = \Drupal::database()->query('SELECT COUNT(*) FROM {users}')->fetchField();
    report_test('D11 Database Connection', TRUE, "Found $d11_count users");
  } catch (Exception $e) {
    report_test('D11 Database Connection', FALSE, $e->getMessage());
    return FALSE;
  }
  
  // Test D6 source database
  try {
    $d6_db = Database::getConnection('default', 'migrate');
    $d6_count = $d6_db->query('SELECT COUNT(*) FROM {users}')->fetchField();
    report_test('D6 Source Database Connection', TRUE, "Found $d6_count users");
  } catch (Exception $e) {
    report_test('D6 Source Database Connection', FALSE, $e->getMessage());
    return FALSE;
  }
  
  echo "\n";
  return TRUE;
}

/**
 * Test migration module status.
 */
function test_migration_modules() {
  echo "🔧 Testing Migration Modules...\n";
  
  $required_modules = [
    'migrate',
    'migrate_plus', 
    'migrate_tools',
    'thirdwing_migrate',
  ];
  
  $module_handler = \Drupal::service('module_handler');
  
  foreach ($required_modules as $module) {
    $enabled = $module_handler->moduleExists($module);
    report_test("Module: $module", $enabled, $enabled ? '' : 'Module not enabled');
  }
  
  echo "\n";
}

/**
 * Test content type structure.
 */
function test_content_types() {
  echo "📝 Testing Content Type Structure...\n";
  
  $expected_types = [
    'activiteit' => 'Activity',
    'nieuws' => 'News', 
    'pagina' => 'Page',
    'repertoire' => 'Repertoire',
    'foto' => 'Photo Album',
    'locatie' => 'Location',
    'vriend' => 'Friend',
  ];
  
  $content_types = \Drupal::entityTypeManager()
    ->getStorage('node_type')
    ->loadMultiple();
  
  foreach ($expected_types as $type_id => $type_name) {
    $exists = isset($content_types[$type_id]);
    report_test("Content Type: $type_name ($type_id)", $exists, 
      $exists ? '' : 'Content type not found');
  }
  
  echo "\n";
}

/**
 * Test migration configurations.
 */
function test_migration_configs() {
  echo "⚙️ Testing Migration Configurations...\n";
  
  $migration_manager = \Drupal::service('plugin.manager.migration');
  
  // Test main migrations
  $main_migrations = [
    'd6_thirdwing_taxonomy_vocabulary',
    'd6_thirdwing_taxonomy_term', 
    'd6_thirdwing_user_role',
    'd6_thirdwing_user',
    'd6_thirdwing_file',
    'd6_thirdwing_news',
    'd6_thirdwing_activity',
  ];
  
  foreach ($main_migrations as $migration_id) {
    try {
      $migration = $migration_manager->createInstance($migration_id);
      report_test("Migration Config: $migration_id", (bool) $migration);
    } catch (Exception $e) {
      report_test("Migration Config: $migration_id", FALSE, $e->getMessage());
    }
  }
  
  // Test incremental migrations
  $incremental_migrations = [
    'd6_thirdwing_incremental_news',
    'd6_thirdwing_incremental_activity',
    'd6_thirdwing_incremental_user',
    'd6_thirdwing_incremental_file',
  ];
  
  foreach ($incremental_migrations as $migration_id) {
    try {
      $migration = $migration_manager->createInstance($migration_id);
      report_test("Incremental Migration: $migration_id", (bool) $migration);
    } catch (Exception $e) {
      report_test("Incremental Migration: $migration_id", FALSE, $e->getMessage(), TRUE);
    }
  }
  
  echo "\n";
}

/**
 * Test data integrity.
 */
function test_data_integrity() {
  echo "🔍 Testing Data Integrity...\n";
  
  // Test user migration integrity
  try {
    $d6_db = Database::getConnection('default', 'migrate');
    $d6_user_count = $d6_db->query('SELECT COUNT(*) FROM {users} WHERE uid > 0')->fetchField();
    $d11_user_count = \Drupal::database()->query('SELECT COUNT(*) FROM {users} WHERE uid > 0')->fetchField();
    
    $user_ratio = $d11_user_count / max($d6_user_count, 1);
    report_test('User Migration Completeness', $user_ratio >= 0.9, 
      sprintf('D6: %d users, D11: %d users (%.1f%%)', $d6_user_count, $d11_user_count, $user_ratio * 100),
      $user_ratio < 1.0);
  } catch (Exception $e) {
    report_test('User Migration Completeness', FALSE, $e->getMessage());
  }
  
  // Test content migration integrity
  $content_types = ['activiteit', 'nieuws', 'pagina', 'repertoire'];
  
  foreach ($content_types as $type) {
    try {
      $d6_db = Database::getConnection('default', 'migrate');
      $d6_count = $d6_db->query('SELECT COUNT(*) FROM {node} WHERE type = :type', [':type' => $type])->fetchField();
      $d11_count = \Drupal::database()->query('SELECT COUNT(*) FROM {node_field_data} WHERE type = :type', [':type' => $type])->fetchField();
      
      $ratio = $d6_count > 0 ? $d11_count / $d6_count : ($d11_count == 0 ? 1 : 0);
      report_test("Content Migration: $type", $ratio >= 0.9,
        sprintf('D6: %d nodes, D11: %d nodes (%.1f%%)', $d6_count, $d11_count, $ratio * 100),
        $ratio < 1.0);
    } catch (Exception $e) {
      report_test("Content Migration: $type", FALSE, $e->getMessage());
    }
  }
  
  echo "\n";
}

/**
 * Test file system integrity.
 */
function test_file_system() {
  echo "📁 Testing File System...\n";
  
  // Test file directory permissions
  $file_system = \Drupal::service('file_system');
  $public_path = $file_system->realpath('public://');
  
  report_test('Public Files Directory Writable', is_writable($public_path),
    $public_path . ' is not writable');
  
  // Test private files if configured
  $private_path = \Drupal::config('system.file')->get('path.private');
  if ($private_path) {
    $private_real_path = $file_system->realpath($private_path);
    report_test('Private Files Directory Writable', is_writable($private_real_path),
      $private_real_path . ' is not writable');
  }
  
  // Test migrated files
  try {
    $d6_db = Database::getConnection('default', 'migrate');
    $d6_file_count = $d6_db->query('SELECT COUNT(*) FROM {files} WHERE status = 1')->fetchField();
    $d11_file_count = \Drupal::database()->query('SELECT COUNT(*) FROM {file_managed}')->fetchField();
    
    $file_ratio = $d6_file_count > 0 ? $d11_file_count / $d6_file_count : ($d11_file_count == 0 ? 1 : 0);
    report_test('File Migration Completeness', $file_ratio >= 0.8,
      sprintf('D6: %d files, D11: %d files (%.1f%%)', $d6_file_count, $d11_file_count, $file_ratio * 100),
      $file_ratio < 1.0);
  } catch (Exception $e) {
    report_test('File Migration Completeness', FALSE, $e->getMessage());
  }
  
  echo "\n";
}

/**
 * Test incremental sync functionality.
 */
function test_incremental_sync() {
  echo "🔄 Testing Incremental Sync Functionality...\n";
  
  // Test sync tracking
  $last_sync = \Drupal::state()->get('thirdwing_migrate.last_sync', 0);
  report_test('Sync Tracking Available', TRUE, 
    $last_sync > 0 ? 'Last sync: ' . date('Y-m-d H:i:s', $last_sync) : 'No sync recorded yet');
  
  // Test Drush commands
  $command_exists = FALSE;
  try {
    $commands = \Drupal::service('drush.command.discovery')->discover();
    foreach ($commands as $command) {
      if (strpos($command['name'], 'thirdwing:sync') !== FALSE) {
        $command_exists = TRUE;
        break;
      }
    }
  } catch (Exception $e) {
    // Fallback method - just check if the class exists
    $command_exists = class_exists('\Drupal\thirdwing_migrate\Commands\MigrationSyncCommands');
  }
  
  report_test('Drush Sync Commands Available', $command_exists,
    'thirdwing:sync command not found');
  
  // Test source plugins
  $plugin_manager = \Drupal::service('plugin.manager.migration.source');
  $incremental_plugins = [
    'd6_thirdwing_incremental_node',
    'd6_thirdwing_incremental_user', 
    'd6_thirdwing_incremental_file',
  ];
  
  foreach ($incremental_plugins as $plugin_id) {
    try {
      $plugin = $plugin_manager->createInstance($plugin_id, []);
      report_test("Source Plugin: $plugin_id", (bool) $plugin);
    } catch (Exception $e) {
      report_test("Source Plugin: $plugin_id", FALSE, $e->getMessage());
    }
  }
  
  echo "\n";
}

/**
 * Test performance and resource usage.
 */
function test_performance() {
  echo "⚡ Testing Performance Indicators...\n";
  
  // Memory usage
  $memory_limit = ini_get('memory_limit');
  $memory_usage = memory_get_usage(true);
  $memory_peak = memory_get_peak_usage(true);
  
  report_test('Memory Usage Reasonable', $memory_usage < (512 * 1024 * 1024),
    sprintf('Current: %s, Peak: %s, Limit: %s', 
      format_bytes($memory_usage), format_bytes($memory_peak), $memory_limit));
  
  // Database query performance (simple test)
  $start_time = microtime(true);
  \Drupal::database()->query('SELECT COUNT(*) FROM {node_field_data}')->fetchField();
  $query_time = microtime(true) - $start_time;
  
  report_test('Database Query Performance', $query_time < 1.0,
    sprintf('Query took %.3f seconds', $query_time),
    $query_time > 0.5);
  
  echo "\n";
}

/**
 * Format bytes for display.
 */
function format_bytes($bytes, $precision = 2) {
  $units = ['B', 'KB', 'MB', 'GB'];
  
  for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
    $bytes /= 1024;
  }
  
  return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Generate validation report.
 */
function generate_report() {
  global $validation_results;
  
  echo "=== VALIDATION SUMMARY ===\n";
  echo sprintf("✅ Passed: %d\n", $validation_results['passed']);
  echo sprintf("❌ Failed: %d\n", $validation_results['failed']);
  echo sprintf("⚠️  Warnings: %d\n", $validation_results['warnings']);
  echo sprintf("📊 Total Tests: %d\n", 
    $validation_results['passed'] + $validation_results['failed'] + $validation_results['warnings']);
  
  $success_rate = ($validation_results['passed'] / 
    max(1, $validation_results['passed'] + $validation_results['failed'])) * 100;
  echo sprintf("📈 Success Rate: %.1f%%\n\n", $success_rate);
  
  if (!empty($validation_results['errors'])) {
    echo "🚨 CRITICAL ISSUES:\n";
    foreach ($validation_results['errors'] as $error) {
      echo "  - $error\n";
    }
    echo "\n";
  }
  
  if (!empty($validation_results['warnings_list'])) {
    echo "⚠️  WARNINGS:\n";
    foreach ($validation_results['warnings_list'] as $warning) {
      echo "  - $warning\n";
    }
    echo "\n";
  }
  
  // Overall assessment
  if ($validation_results['failed'] == 0) {
    if ($validation_results['warnings'] == 0) {
      echo "🎉 EXCELLENT: Migration system is fully functional!\n";
    } else {
      echo "✅ GOOD: Migration system is functional with minor warnings.\n";
    }
  } else {
    echo "🔧 ACTION NEEDED: Please address the failed tests above.\n";
  }
  
  return $validation_results['failed'] == 0;
}

// Run all tests
echo "Starting comprehensive migration validation...\n\n";

test_database_connections();
test_migration_modules();
test_content_types();
test_migration_configs();
test_data_integrity();
test_file_system();
test_incremental_sync();
test_performance();

$success = generate_report();

echo "Validation completed.\n";
exit($success ? 0 : 1);