<?php

/**
 * @file
 * NEW script to create user roles for Thirdwing migration.
 * Creates all D6 user roles in D11 before permissions are configured.
 *
 * Usage: drush php:script create-user-roles.php
 */

use Drupal\user\Entity\Role;

/**
 * Main execution function.
 */
function createUserRoles() {
  echo "🚀 Creating User Roles for Thirdwing Migration...\n\n";
  
  // Get role configurations
  $roles = getUserRoleDefinitions();
  
  // Create each role
  foreach ($roles as $role_id => $role_config) {
    createUserRole($role_id, $role_config);
  }
  
  echo "\n✅ User roles creation complete!\n";
  printRoleSummary();
}

/**
 * Create a single user role.
 */
function createUserRole($role_id, $role_config) {
  echo "  Creating role: {$role_id}\n";
  
  $role = Role::load($role_id);
  
  if (!$role) {
    $role = Role::create([
      'id' => $role_id,
      'label' => $role_config['label'],
      'weight' => $role_config['weight'] ?? 0,
    ]);
    
    $role->save();
    echo "    ✓ Created: {$role_config['label']}\n";
  } else {
    echo "    - Already exists: {$role_config['label']}\n";
    
    // Update label if it has changed
    if ($role->label() !== $role_config['label']) {
      $role->set('label', $role_config['label']);
      $role->save();
      echo "    ✓ Updated label: {$role_config['label']}\n";
    }
  }
}

/**
 * Get user role definitions based on D6 roles and Thirdwing requirements.
 */
function getUserRoleDefinitions() {
  return [
    // Core member roles
    'lid' => [
      'label' => 'Lid',
      'weight' => 10,
      'description' => 'Koorlid met basistoegang tot ledengedeelte'
    ],
    'aspirant_lid' => [
      'label' => 'Aspirant-lid',
      'weight' => 9,
      'description' => 'Aspirant koorlid met beperkte toegang'
    ],
    'vriend' => [
      'label' => 'Vriend',
      'weight' => 5,
      'description' => 'Vriend van de koorstichting'
    ],
    
    // Content creation roles
    'auteur' => [
      'label' => 'Auteur',
      'weight' => 20,
      'description' => 'Kan nieuws en pagina\'s aanmaken en bewerken'
    ],
    
    // Music and performance roles
    'muziekcommissie' => [
      'label' => 'Muziekcommissie',
      'weight' => 30,
      'description' => 'Lid van de muziekcommissie, kan repertoire beheren'
    ],
    'dirigent' => [
      'label' => 'Dirigent',
      'weight' => 40,
      'description' => 'Dirigent met speciale rechten voor activiteiten'
    ],
    
    // Board and administrative roles
    'bestuur' => [
      'label' => 'Bestuur',
      'weight' => 50,
      'description' => 'Bestuurslid met uitgebreide rechten'
    ],
    'beheerder' => [
      'label' => 'Beheerder',
      'weight' => 60,
      'description' => 'Systeembeheerder met volledige content rechten'
    ],
    
    // Committee roles
    'commissie_concerten' => [
      'label' => 'Commissie Concerten',
      'weight' => 25,
      'description' => 'Lid van de commissie concerten'
    ],
    'commissie_faciliteiten_logistiek' => [
      'label' => 'Commissie Faciliteiten & Logistiek',
      'weight' => 25,
      'description' => 'Lid van de commissie faciliteiten en logistiek'
    ],
    'commissie_interne_relaties' => [
      'label' => 'Commissie Interne Relaties',
      'weight' => 25,
      'description' => 'Lid van de commissie interne relaties'
    ],
    'commissie_koorregie' => [
      'label' => 'Commissie Koorregie',
      'weight' => 25,
      'description' => 'Lid van de commissie koorregie'
    ],
    'commissie_ledenwerving' => [
      'label' => 'Commissie Ledenwerving',
      'weight' => 25,
      'description' => 'Lid van de commissie ledenwerving'
    ],
    'commissie_publieke_relaties' => [
      'label' => 'Commissie Publieke Relaties',
      'weight' => 25,
      'description' => 'Lid van de commissie publieke relaties'
    ],
    'technische_commissie' => [
      'label' => 'Technische Commissie',
      'weight' => 25,
      'description' => 'Lid van de technische commissie'
    ],
    'feestcommissie' => [
      'label' => 'Feestcommissie',
      'weight' => 25,
      'description' => 'Lid van de feestcommissie'
    ]
  ];
}

/**
 * Print summary of created roles.
 */
function printRoleSummary() {
  $roles = getUserRoleDefinitions();
  $role_groups = getRoleGroups();
  
  echo "\n📊 User Roles Summary:\n";
  echo "  • Total Roles Created: " . count($roles) . "\n\n";
  
  echo "📋 Roles by Category:\n";
  foreach ($role_groups as $group_name => $role_ids) {
    echo "  🏷️  {$group_name}:\n";
    foreach ($role_ids as $role_id) {
      if (isset($roles[$role_id])) {
        $role_config = $roles[$role_id];
        echo "    • {$role_config['label']} ({$role_id})\n";
      }
    }
    echo "\n";
  }
  
  echo "🔄 Role Migration Benefits:\n";
  echo "  • Preserves D6 role structure in D11\n";
  echo "  • Maintains user access levels and permissions\n";
  echo "  • Enables committee-based content management\n";
  echo "  • Supports hierarchical access control\n";
  echo "  • Ready for permission configuration\n\n";
  
  echo "📋 Next Steps:\n";
  echo "  1. Run permission configuration: drush php:script setup-role-permissions.php\n";
  echo "  2. Verify roles created: drush user:role:list\n";
  echo "  3. Check role hierarchy: /admin/people/roles\n";
  echo "  4. Test role assignments during user migration\n";
  echo "  5. Configure additional permissions if needed\n\n";
  
  echo "📋 Verification Commands:\n";
  echo "  drush user:role:list\n";
  echo "  drush config:export\n";
  echo "  Visit: /admin/people/roles\n";
}

/**
 * Get role groupings for better organization.
 */
function getRoleGroups() {
  return [
    'Core Member Roles' => [
      'lid', 'aspirant_lid', 'vriend'
    ],
    'Content Management' => [
      'auteur'
    ],
    'Music & Performance' => [
      'muziekcommissie', 'dirigent'
    ],
    'Leadership' => [
      'bestuur', 'beheerder'
    ],
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
 * Validate that core Drupal roles exist.
 */
function validateCoreRoles() {
  echo "🔍 Validating core Drupal roles...\n";
  
  $core_roles = ['anonymous', 'authenticated'];
  $missing_core = [];
  
  foreach ($core_roles as $role_id) {
    $role = Role::load($role_id);
    if (!$role) {
      $missing_core[] = $role_id;
    } else {
      echo "  ✓ Core role exists: {$role->label()}\n";
    }
  }
  
  if (!empty($missing_core)) {
    echo "  ❌ Missing core roles: " . implode(', ', $missing_core) . "\n";
    echo "  This indicates a serious Drupal installation problem.\n";
    return false;
  }
  
  echo "  ✅ All core roles present\n\n";
  return true;
}

/**
 * Get role hierarchy for weight assignment.
 */
function getRoleHierarchy() {
  return [
    'Role Hierarchy (by access level):',
    '  1. anonymous (0) - No access',
    '  2. authenticated (1) - Basic access', 
    '  3. vriend (5) - Friend access',
    '  4. aspirant_lid (9) - Aspiring member',
    '  5. lid (10) - Full member',
    '  6. auteur (20) - Content creator',
    '  7. Committee roles (25) - Specialized access',
    '  8. muziekcommissie (30) - Music management',
    '  9. dirigent (40) - Conductor privileges',
    '  10. bestuur (50) - Board member',
    '  11. beheerder (60) - Full administrator'
  ];
}

// Execute the script
try {
  // Validate core roles first
  if (!validateCoreRoles()) {
    echo "❌ Cannot proceed without core Drupal roles\n";
    exit(1);
  }
  
  // Create custom roles
  createUserRoles();
  
  // Show hierarchy info
  echo "📋 Role Hierarchy Information:\n";
  $hierarchy = getRoleHierarchy();
  foreach ($hierarchy as $line) {
    echo "  {$line}\n";
  }
  echo "\n";
  
} catch (Exception $e) {
  echo "❌ Script failed: " . $e->getMessage() . "\n";
  echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
  exit(1);
}