<?php

/**
 * @file
 * Script to configure default field displays for all Thirdwing content types.
 * 
 * Part of the hybrid field display approach: creates automated default displays
 * that provide immediate functionality while allowing manual customization.
 * 
 * Run with: drush php:script setup-field-displays.php
 * 
 * This script:
 * - Creates default display configurations for all view modes
 * - Ensures immediate functionality after content type creation
 * - Provides sensible field ordering and formatting
 * - Allows manual customization without breaking functionality
 */

use Drupal\thirdwing_migrate\Service\ThirdwingFieldDisplayService;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
Settings::initialize(dirname(__DIR__), DrupalKernel::findSitePath($request), $autoloader);
$kernel->boot();
$container = $kernel->getContainer();

/**
 * Main execution function.
 */
function main() {
  echo "🎯 Thirdwing Field Display Configuration\n";
  echo "========================================\n\n";
  
  try {
    // Get the field display service
    $display_service = \Drupal::service('thirdwing_migrate.field_display');
    
    echo "📋 Configuring default field displays for all content types...\n\n";
    
    // Configure displays for all content types and view modes
    $results = $display_service->configureAllDisplays();
    
    // Report results
    displayResults($results);
    
    echo "\n✅ Validating display configurations...\n";
    
    // Validate the configurations
    $validation = $display_service->validateDisplays();
    displayValidation($validation);
    
    echo "\n🎉 Field display configuration complete!\n\n";
    
    echo "📖 What was configured:\n";
    echo "  • Default view mode: Complete field layout with proper ordering\n";
    echo "  • Teaser view mode: Summary displays for listings and previews\n";
    echo "  • Full view mode: Detailed content display\n";
    echo "  • Search result mode: Optimized for search listings\n\n";
    
    echo "🛠️  Manual customization options:\n";
    echo "  • Field reordering and grouping\n";
    echo "  • Custom display formatters\n";
    echo "  • Responsive display settings\n";
    echo "  • Field-specific styling and layouts\n";
    echo "  • Advanced view mode configurations\n\n";
    
    echo "📍 Next steps:\n";
    echo "  1. Test content display on your site\n";
    echo "  2. Customize displays as needed via UI: Structure > Content types > [Type] > Manage display\n";
    echo "  3. Run migration to see displays in action\n\n";
    
  } catch (\Exception $e) {
    echo "❌ Error configuring field displays: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
  }
}

/**
 * Display configuration results.
 */
function displayResults(array $results): void {
  $total_success = 0;
  $total_error = 0;
  
  foreach ($results as $content_type => $view_modes) {
    echo "📦 {$content_type}:\n";
    
    foreach ($view_modes as $view_mode => $status) {
      $icon = $status === 'success' ? '✅' : '❌';
      echo "  {$icon} {$view_mode}: {$status}\n";
      
      if ($status === 'success') {
        $total_success++;
      } else {
        $total_error++;
      }
    }
    echo "\n";
  }
  
  echo "📊 Summary: {$total_success} successful, {$total_error} errors\n";
}

/**
 * Display validation results.
 */
function displayValidation(array $validation): void {
  $total_displays = 0;
  $total_components = 0;
  
  foreach ($validation as $content_type => $view_modes) {
    foreach ($view_modes as $view_mode => $info) {
      if ($info['exists'] && $info['status']) {
        $total_displays++;
        $total_components += $info['components'];
      }
    }
  }
  
  echo "✅ {$total_displays} displays configured with {$total_components} total field components\n";
  
  // Check for any missing displays
  $missing = [];
  foreach ($validation as $content_type => $view_modes) {
    foreach ($view_modes as $view_mode => $info) {
      if (!$info['exists'] || !$info['status']) {
        $missing[] = "{$content_type}.{$view_mode}";
      }
    }
  }
  
  if (!empty($missing)) {
    echo "⚠️  Missing or disabled displays: " . implode(', ', $missing) . "\n";
  }
}

/**
 * Check prerequisites.
 */
function checkPrerequisites(): bool {
  echo "🔍 Checking prerequisites...\n";
  
  // Check if content types exist
  $content_types = [
    'activiteit',
    'foto', 
    'locatie',
    'nieuws',
    'pagina',
    'programma',
    'repertoire',
    'persoon',
    'vriend',
  ];
  
  $node_type_storage = \Drupal::entityTypeManager()->getStorage('node_type');
  $missing_types = [];
  
  foreach ($content_types as $type) {
    if (!$node_type_storage->load($type)) {
      $missing_types[] = $type;
    }
  }
  
  if (!empty($missing_types)) {
    echo "❌ Missing content types: " . implode(', ', $missing_types) . "\n";
    echo "   Please run content type creation script first.\n";
    return FALSE;
  }
  
  echo "✅ All required content types found\n";
  
  // Check if service is available
  try {
    $service = \Drupal::service('thirdwing_migrate.field_display');
    echo "✅ Field display service available\n";
  } catch (\Exception $e) {
    echo "❌ Field display service not available: " . $e->getMessage() . "\n";
    return FALSE;
  }
  
  echo "✅ All prerequisites met\n\n";
  return TRUE;
}

/**
 * Display help information.
 */
function displayHelp(): void {
  echo "Thirdwing Field Display Configuration Script\n";
  echo "==========================================\n\n";
  
  echo "This script configures default field displays for all Thirdwing content types\n";
  echo "using the hybrid approach: automated defaults with manual customization options.\n\n";
  
  echo "Usage:\n";
  echo "  drush php:script setup-field-displays.php\n";
  echo "  drush php:script setup-field-displays.php --help\n";
  echo "  drush php:script setup-field-displays.php --validate-only\n\n";
  
  echo "Options:\n";
  echo "  --help           Show this help message\n";
  echo "  --validate-only  Only validate existing displays, don't create new ones\n\n";
  
  echo "What this script does:\n";
  echo "  • Creates default display configurations for all view modes\n";
  echo "  • Configures proper field ordering and formatting\n";
  echo "  • Sets up responsive display settings\n";
  echo "  • Ensures immediate functionality after installation\n";
  echo "  • Preserves ability for manual customization\n\n";
  
  echo "View modes configured:\n";
  echo "  • default: Complete field layout\n";
  echo "  • teaser: Summary for listings\n";
  echo "  • full: Detailed display\n";
  echo "  • search_result: Search optimized\n\n";
  
  echo "After running this script, you can customize displays via:\n";
  echo "  Structure > Content types > [Type] > Manage display\n\n";
}

// Handle command line arguments
if (isset($argv)) {
  foreach ($argv as $arg) {
    if ($arg === '--help') {
      displayHelp();
      exit(0);
    } elseif ($arg === '--validate-only') {
      echo "🔍 Validation mode: checking existing displays only\n\n";
      
      if (!checkPrerequisites()) {
        exit(1);
      }
      
      try {
        $display_service = \Drupal::service('thirdwing_migrate.field_display');
        $validation = $display_service->validateDisplays();
        displayValidation($validation);
      } catch (\Exception $e) {
        echo "❌ Validation error: " . $e->getMessage() . "\n";
        exit(1);
      }
      
      exit(0);
    }
  }
}

// Check prerequisites before running
if (!checkPrerequisites()) {
  echo "\n❌ Prerequisites not met. Please resolve the issues above before running this script.\n";
  exit(1);
}

// Run main configuration
main();