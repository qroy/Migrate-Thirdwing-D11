<?php

/**
 * @file
 * Script to configure role permissions after user role migration.
 * 
 * Run with: drush php:script setup-role-permissions.php
 */

use Drupal\user\Entity\Role;

// Define permission mappings for migrated roles
$role_permissions = [
  'admin' => [
    'administer site configuration',
    'administer users',
    'administer permissions',
    'administer content types',
    'administer taxonomy',
    'administer menu',
    'access administration pages',
    'view the administration theme',
    'administer nodes',
    'bypass node access',
    'administer media',
    'administer workflows',
    'use editorial transition create_new_draft',
    'use editorial transition publish',
    'use editorial transition archive',
  ],
  
  'beheerder' => [
    'access administration pages',
    'administer nodes',
    'create activiteit content',
    'edit any activiteit content',
    'delete any activiteit content',
    'create nieuws content',
    'edit any nieuws content',
    'delete any nieuws content',
    'create pagina content',
    'edit any pagina content',
    'administer menu',
    'administer taxonomy',
    'administer media',
    'use editorial transition publish',
  ],
  
  'bestuur' => [
    'access administration pages',
    'create activiteit content',
    'edit own activiteit content',
    'edit any activiteit content',
    'create nieuws content',
    'edit own nieuws content',
    'edit any nieuws content',
    'create pagina content',
    'edit own pagina content',
    'create repertoire content',
    'edit own repertoire content',
    'use editorial transition create_new_draft',
    'use editorial transition needs_review',
  ],
  
  'dirigent' => [
    'access administration pages',
    'create activiteit content',
    'edit any activiteit content',
    'create repertoire content',
    'edit any repertoire content',
    'delete any repertoire content',
    'create document media',
    'edit any document media',
    'create audio media',
    'edit any audio media',
    'use editorial transition publish',
  ],
  
  'muziekcommissie' => [
    'create activiteit content',
    'edit own activiteit content',
    'create repertoire content',
    'edit own repertoire content',
    'edit any repertoire content',
    'create document media',
    'edit own document media',
    'create audio media',
    'edit own audio media',
  ],
  
  'auteur' => [
    'create nieuws content',
    'edit own nieuws content',
    'create pagina content',
    'edit own pagina content',
    'create foto content',
    'edit own foto content',
    'create image media',
    'edit own image media',
    'use editorial transition create_new_draft',
    'use editorial transition needs_review',
  ],
  
  'lid' => [
    'access content',
    'view media',
    'view own unpublished content',
    'create foto content',
    'edit own foto content',
    'create image media',
    'edit own image media',
  ],
  
  'aspirant_lid' => [
    'access content',
    'view media',
    'view own unpublished content',
  ],
  
  'vriend' => [
    'access content',
    'view published content',
  ],
];

// Committee roles get similar permissions to 'lid' plus specific committee permissions
$committee_roles = [
  'commissie_concerten',
  'commissie_faciliteiten_logistiek',
  'commissie_interne_relaties',
  'commissie_koorregie',
  'commissie_ledenwerving',
  'commissie_publieke_relaties',
  'technische_commissie',
  'feestcommissie',
];

foreach ($committee_roles as $committee_role) {
  $role_permissions[$committee_role] = array_merge(
    $role_permissions['lid'],
    [
      'create activiteit content',
      'edit own activiteit content',
      'create nieuws content',
      'edit own nieuws content',
    ]
  );
}

// Apply permissions to roles
foreach ($role_permissions as $role_id => $permissions) {
  $role = Role::load($role_id);
  
  if ($role) {
    echo "Configuring permissions for role: {$role->label()}\n";
    
    foreach ($permissions as $permission) {
      try {
        $role->grantPermission($permission);
        echo "  ✓ Granted: {$permission}\n";
      } catch (\Exception $e) {
        echo "  ✗ Failed to grant: {$permission} - {$e->getMessage()}\n";
      }
    }
    
    $role->save();
    echo "  Role saved successfully.\n\n";
    
  } else {
    echo "Role '{$role_id}' not found. Skipping...\n\n";
  }
}

// Set up administrator role for uid 1
$admin_role = Role::load('administrator');
if (!$admin_role) {
  echo "Creating administrator role...\n";
  $admin_role = Role::create([
    'id' => 'administrator',
    'label' => 'Administrator',
    'weight' => 15,
    'is_admin' => TRUE,
  ]);
  $admin_role->save();
  echo "Administrator role created.\n";
}

echo "Role permissions configuration completed!\n";
echo "\nNext steps:\n";
echo "1. Visit /admin/people/permissions to review and adjust permissions\n";
echo "2. Configure Permissions by Term taxonomy access\n";
echo "3. Set up Content Moderation workflows\n";