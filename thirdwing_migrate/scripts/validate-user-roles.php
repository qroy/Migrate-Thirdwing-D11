<?php

/**
 * @file
 * NEW script to validate that user roles were created correctly.
 * Checks roles against the create-user-roles.php definitions.
 *
 * Usage: drush php:script validate-user-roles.php
 */

use Drupal\user\Entity\Role;

/**
 * Main execution function.
 */
function validateUserRoles() {
  echo "🔍 Validating User Roles Creation...\n\n";
  
  $errors = [];
  $warnings = [];
  
  // Step 1: Validate core Drupal roles
  echo "📦 Validating core Drupal roles...\n";
  validateCoreRoles($errors, $warnings);
  
  // Step 2: Validate custom roles
  echo "\n👥 Validating custom user roles...\n";
  validateCustomRoles($errors, $warnings);
  
  // Step 3: Validate role hierarchy and weights
  echo "\n⚖️  Validating role hierarchy...\n";
  validateRoleHierarchy($errors, $warnings);
  
  // Generate report
  echo "\n";
  generateRoleValidationReport($errors, $warnings);
}

/**
 * Validate core Drupal roles exist.
 */
function validateCoreRoles(&$errors, &$warnings) {
  $core_roles = [
    'anonymous' => 'Anonymous user',
    'authenticated' => 'Authenticated user'
  ];
  
  foreach ($core_roles as $role_id => $expected_label) {
    $role = Role::load($role_id);
    if (!$role) {
      $errors[] = "Custom role '{$role_id}' is missing";
    } else {
      echo "  ✓ {$role->label()} ({$role_id})\n";
      
      // Validate label
      if ($role->label() !== $expected_config['label']) {
        $warnings[] = "Role '{$role_id}' has label '{$role->label()}', expected '{$expected_config['label']}'";
      }
      
      // Validate weight if specified
      if (isset($expected_config['weight']) && $role->getWeight() !== $expected_config['weight']) {
        $warnings[] = "Role '{$role_id}' has weight '{$role->getWeight()}', expected '{$expected_config['weight']}'";
      }
    }
  }
}

/**
 * Validate role hierarchy and weights.
 */
function validateRoleHierarchy(&$errors, &$warnings) {
  $all_roles = Role::loadMultiple();
  
  // Check weight progression
  $role_weights = [];
  foreach ($all_roles as $role_id => $role) {
    if (!in_array($role_id, ['anonymous', 'authenticated'])) {
      $role_weights[$role_id] = $role->getWeight();
      echo "  ✓ {$role->label()}: weight {$role->getWeight()}\n";
    }
  }
  
  // Validate that higher privilege roles have higher weights
  $hierarchy_issues = [];
  
  // Basic hierarchy checks
  if (isset($role_weights['vriend']) && isset($role_weights['lid'])) {
    if ($role_weights['vriend'] >= $role_weights['lid']) {
      $hierarchy_issues[] = "'vriend' should have lower weight than 'lid'";
    }
  }
  
  if (isset($role_weights['lid']) && isset($role_weights['beheerder'])) {
    if ($role_weights['lid'] >= $role_weights['beheerder']) {
      $hierarchy_issues[] = "'lid' should have lower weight than 'beheerder'";
    }
  }
  
  if (!empty($hierarchy_issues)) {
    foreach ($hierarchy_issues as $issue) {
      $warnings[] = "Role hierarchy issue: {$issue}";
    }
  }
}

/**
 * Generate validation report.
 */
function generateRoleValidationReport($errors, $warnings) {
  echo "📊 USER ROLES VALIDATION REPORT\n";
  echo "=" . str_repeat("=", 50) . "\n\n";
  
  if (empty($errors) && empty($warnings)) {
    echo "🎉 SUCCESS: All user roles created correctly!\n";
    echo "✅ Core roles: Present and correctly configured\n";
    echo "✅ Custom roles: All " . count(getExpectedUserRoles()) . " roles created\n";
    echo "✅ Role hierarchy: Weights properly assigned\n";
    echo "✅ Role labels: All match expected values\n\n";
    
    echo "📋 Roles Summary:\n";
    $role_groups = getExpectedRoleGroups();
    foreach ($role_groups as $group_name => $role_ids) {
      echo "  🏷️  {$group_name}: " . count($role_ids) . " roles\n";
    }
    echo "\n";
    
    echo "🚀 Ready for permission configuration!\n";
    echo "Next: Run setup-role-permissions.php\n";
    
  } else {
    if (!empty($errors)) {
      echo "❌ ERRORS FOUND (" . count($errors) . "):\n";
      foreach ($errors as $error) {
        echo "  • {$error}\n";
      }
      echo "\n";
    }
    
    if (!empty($warnings)) {
      echo "⚠️  WARNINGS (" . count($warnings) . "):\n";
      foreach ($warnings as $warning) {
        echo "  • {$warning}\n";
      }
      echo "\n";
    }
    
    if (!empty($errors)) {
      echo "🔧 FIXES NEEDED:\n";
      echo "  1. Re-run: drush php:script create-user-roles.php\n";
      echo "  2. Check for missing dependencies\n";
      echo "  3. Verify Drupal user system is working\n";
      echo "  4. Run this validation again\n\n";
    }
  }
  
  echo "📋 Role Management Commands:\n";
  echo "  drush user:role:list\n";
  echo "  drush user:role:create <role_id> <label>\n";
  echo "  drush user:role:delete <role_id>\n";
  echo "  Visit: /admin/people/roles\n";
}

/**
 * Get expected user roles (should match create-user-roles.php).
 */
function getExpectedUserRoles() {
  return [
    // Core member roles
    'lid' => [
      'label' => 'Lid',
      'weight' => 10
    ],
    'aspirant_lid' => [
      'label' => 'Aspirant-lid',
      'weight' => 9
    ],
    'vriend' => [
      'label' => 'Vriend',
      'weight' => 5
    ],
    
    // Content creation roles
    'auteur' => [
      'label' => 'Auteur',
      'weight' => 20
    ],
    
    // Music and performance roles
    'muziekcommissie' => [
      'label' => 'Muziekcommissie',
      'weight' => 30
    ],
    'dirigent' => [
      'label' => 'Dirigent',
      'weight' => 40
    ],
    
    // Board and administrative roles
    'bestuur' => [
      'label' => 'Bestuur',
      'weight' => 50
    ],
    'beheerder' => [
      'label' => 'Beheerder',
      'weight' => 60
    ],
    
    // Committee roles
    'commissie_concerten' => [
      'label' => 'Commissie Concerten',
      'weight' => 25
    ],
    'commissie_faciliteiten_logistiek' => [
      'label' => 'Commissie Faciliteiten & Logistiek',
      'weight' => 25
    ],
    'commissie_interne_relaties' => [
      'label' => 'Commissie Interne Relaties',
      'weight' => 25
    ],
    'commissie_koorregie' => [
      'label' => 'Commissie Koorregie',
      'weight' => 25
    ],
    'commissie_ledenwerving' => [
      'label' => 'Commissie Ledenwerving',
      'weight' => 25
    ],
    'commissie_publieke_relaties' => [
      'label' => 'Commissie Publieke Relaties',
      'weight' => 25
    ],
    'technische_commissie' => [
      'label' => 'Technische Commissie',
      'weight' => 25
    ],
    'feestcommissie' => [
      'label' => 'Feestcommissie',
      'weight' => 25
    ]
  ];
}

/**
 * Get expected role groups for summary.
 */
function getExpectedRoleGroups() {
  return [
    'Core Member Roles' => ['lid', 'aspirant_lid', 'vriend'],
    'Content Management' => ['auteur'],
    'Music & Performance' => ['muziekcommissie', 'dirigent'],
    'Leadership' => ['bestuur', 'beheerder'],
    'Committees' => [
      'commissie_concerten',
      'commissie_faciliteiten_logistiek',
      'commissie_interne_relaties',
      'commissie_koorregie',
      'commissie_ledenwerving',
      'commissie_publieke_relaties',
      'technische_commissie',
      'feestcommissie'
    ]
  ];
}

/**
 * Check if role configuration allows proper permission setup.
 */
function validateRolePermissionReadiness() {
  echo "🔐 Checking role readiness for permission configuration...\n";
  
  $critical_roles = ['lid', 'beheerder', 'auteur', 'bestuur'];
  $missing_critical = [];
  
  foreach ($critical_roles as $role_id) {
    $role = Role::load($role_id);
    if (!$role) {
      $missing_critical[] = $role_id;
    }
  }
  
  if (empty($missing_critical)) {
    echo "  ✅ All critical roles present for permission setup\n";
    return true;
  } else {
    echo "  ❌ Missing critical roles: " . implode(', ', $missing_critical) . "\n";
    echo "  Permission setup will fail without these roles\n";
    return false;
  }
}

// Execute the script
try {
  validateUserRoles();
  
  echo "\n";
  validateRolePermissionReadiness();
  
} catch (Exception $e) {
  echo "❌ Validation failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}
      $errors[] = "Core role '{$role_id}' is missing - serious Drupal installation problem";
    } else {
      echo "  ✓ {$role->label()} ({$role_id})\n";
      
      if ($role->label() !== $expected_label) {
        $warnings[] = "Core role '{$role_id}' has label '{$role->label()}', expected '{$expected_label}'";
      }
    }
  }
}

/**
 * Validate custom user roles.
 */
function validateCustomRoles(&$errors, &$warnings) {
  $expected_roles = getExpectedUserRoles();
  
  foreach ($expected_roles as $role_id => $expected_config) {
    $role = Role::load($role_id);
    
    if (!$role) {