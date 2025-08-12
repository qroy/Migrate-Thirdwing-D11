<?php

/**
 * @file
 * CORRECTED script to configure content editing and viewing permissions.
 * Now matches the actual D11 content types and field structure.
 * 
 * Run with: drush php:script setup-role-permissions.php
 * 
 * This script focuses only on content editing and viewing permissions,
 * excluding administrative, webform, and system-level permissions.
 */

use Drupal\user\Entity\Role;

// CORRECTED: Define content types that actually exist in D11
$content_types = [
  'activiteit',    // ✅ Migrated
  'foto',          // ✅ Migrated  
  'locatie',       // ✅ Migrated
  'nieuws',        // ✅ Migrated
  'pagina',        // ✅ Migrated
  'programma',     // ✅ Migrated
  'repertoire',    // ✅ Migrated
  'vriend',        // ✅ Migrated
  'webform',       // ✅ Migrated
  
  // REMOVED deprecated content types:
  // 'audio',      // ❌ Now media bundle
  // 'video',      // ❌ Now media bundle  
  // 'profiel',    // ❌ Now user profile fields
  // 'verslag',    // ❌ Deprecated
];

// CORRECTED: Define field permissions matching D11 field structure
$field_permissions = [
  // Shared fields that exist in D11
  'view field_afbeeldingen',           // Images (entity_reference to media)
  'view field_audio_type',             // Audio type (list_string)
  'view field_audio_uitvoerende',      // Audio performer (string)
  'view field_datum',                  // Date and time (datetime)
  'view field_files',                  // File attachments (entity_reference to media)
  'view field_inhoud',                 // Content (entity_reference to nodes)
  'view field_l_routelink',            // Route link (link)
  'view field_partij_band',            // Band sheet music (entity_reference to media)
  'view field_partij_koor_l',          // Choir sheet music (entity_reference to media)
  'view field_partij_tekst',           // Text/choir direction (entity_reference to media)
  'view field_programma2',             // Program (entity_reference to nodes)
  'view field_ref_activiteit',         // Activity reference (entity_reference to nodes)
  'view field_repertoire',             // Repertoire reference (entity_reference to nodes)
  'view field_video',                  // Video (text_long for embedded video)
  'view field_view',                   // Extra content (string)
  'view field_woonplaats',             // City (string)
  
  // Content-type specific fields
  'view field_a_locatie',              // Activity location (string)
  'view field_a_planner',              // Activity planner (entity_reference to user)
  'view field_a_tijd_begin',           // Activity start time (string)
  'view field_a_tijd_einde',           // Activity end time (string)
  'view field_a_wijzigingen',          // Last-minute changes (text_long)
  'view field_l_ref_locatie',          // Location reference (entity_reference to nodes)
  'view field_l_adres',                // Location address (string)
  'view field_l_plaats',               // Location city (string)
  'view field_l_postcode',             // Location postal code (string)
  'view field_componist',              // Composer (string)
  'view field_arrangeur',              // Arranger (string)
  'view field_genre',                  // Genre (entity_reference to taxonomy)
  'view field_uitgave',                // Edition (string)
  'view field_toegang',                // Access (entity_reference to taxonomy)
  'view field_v_categorie',            // Friend category (entity_reference to taxonomy)
  'view field_v_website',              // Friend website (link)
  
  // Edit permissions for the same fields
  'edit field_afbeeldingen',
  'edit field_audio_type',
  'edit field_audio_uitvoerende',
  'edit field_datum',
  'edit field_files',
  'edit field_inhoud',
  'edit field_l_routelink',
  'edit field_partij_band',
  'edit field_partij_koor_l',
  'edit field_partij_tekst',
  'edit field_programma2',
  'edit field_ref_activiteit',
  'edit field_repertoire',
  'edit field_video',
  'edit field_view',
  'edit field_woonplaats',
  'edit field_a_locatie',
  'edit field_a_planner',
  'edit field_a_tijd_begin',
  'edit field_a_tijd_einde',
  'edit field_a_wijzigingen',
  'edit field_l_ref_locatie',
  'edit field_l_adres',
  'edit field_l_plaats',
  'edit field_l_postcode',
  'edit field_componist',
  'edit field_arrangeur',
  'edit field_genre',
  'edit field_uitgave',
  'edit field_toegang',
  'edit field_v_categorie',
  'edit field_v_website',
];

// Define base permissions for content access
$base_permissions = [
  'access content',
  'view media',
  'view status messages',
  'view warning messages',
];

// Define role-specific permissions based on corrected content types
$role_permissions = [
  'anonymous' => array_merge($base_permissions, [
    // Anonymous users can view most public fields
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_ref_activiteit',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
    'view field_video',
    'view field_woonplaats',
    'view field_v_categorie',
    'view field_v_website',
  ]),
  
  'authenticated' => array_merge($base_permissions, [
    'view own unpublished content',
    // Authenticated users get same field permissions as anonymous
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_ref_activiteit',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
    'view field_video',
    'view field_woonplaats',
    'view field_v_categorie',
    'view field_v_website',
  ]),
  
  'vriend' => array_merge($base_permissions, [
    'view own unpublished content',
    // Friends get limited field access
    'view field_afbeeldingen',
    'view field_datum',
    'view field_files',
    'view field_l_routelink',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_componist',
    'view field_arrangeur',
    'view field_video',
    'view field_woonplaats',
  ]),
  
  'aspirant_lid' => array_merge($base_permissions, [
    'view own unpublished content',
    // Aspiring members get member-level viewing permissions
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_programma2',
    'view field_ref_activiteit',
    'view field_repertoire',
    'view field_video',
    'view field_woonplaats',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
  ]),
  
  'lid' => array_merge($base_permissions, [
    'view own unpublished content',
    'create foto content',
    'edit own foto content',
    // Members get extensive field access
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_programma2',
    'view field_ref_activiteit',
    'view field_repertoire',
    'view field_video',
    'view field_view',
    'view field_woonplaats',
    'view field_a_locatie',
    'view field_a_tijd_begin',
    'view field_a_tijd_einde',
    'view field_l_ref_locatie',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
    'view field_toegang',
    'view field_v_categorie',
    'view field_v_website',
    // Edit permissions for photos
    'edit field_afbeeldingen',
    'edit field_audio_type',
    'edit field_audio_uitvoerende',
    'edit field_datum',
    'edit field_ref_activiteit',
    'edit field_video',
  ]),
  
  'auteur' => array_merge($base_permissions, [
    'view own unpublished content',
    'create nieuws content',
    'create pagina content',
    'create foto content',
    'edit own nieuws content',
    'edit own pagina content',
    'edit own foto content',
    // Authors get content creation permissions plus field editing
    'view field_afbeeldingen',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_ref_activiteit',
    'view field_video',
    'view field_view',
    'view field_woonplaats',
    'edit field_afbeeldingen',
    'edit field_datum',
    'edit field_files',
    'edit field_inhoud',
    'edit field_ref_activiteit',
    'edit field_video',
    'edit field_view',
  ]),
  
  'muziekcommissie' => array_merge($base_permissions, [
    'view own unpublished content',
    'create activiteit content',
    'create repertoire content',
    'edit own activiteit content',
    'edit own repertoire content',
    'edit any repertoire content',
    // Music committee gets repertoire management
    'view field_afbeeldingen',
    'view field_datum',
    'view field_files',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_programma2',
    'view field_repertoire',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
    'view field_toegang',
    'edit field_afbeeldingen',
    'edit field_datum',
    'edit field_files',
    'edit field_partij_band',
    'edit field_partij_koor_l',
    'edit field_partij_tekst',
    'edit field_programma2',
    'edit field_repertoire',
    'edit field_componist',
    'edit field_arrangeur',
    'edit field_genre',
    'edit field_uitgave',
    'edit field_toegang',
  ]),
  
  'bestuur' => array_merge($base_permissions, [
    'view own unpublished content',
    'create activiteit content',
    'create nieuws content',
    'edit own activiteit content',
    'edit own nieuws content',
    'edit any activiteit content',
    // Board members get enhanced permissions
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_programma2',
    'view field_ref_activiteit',
    'view field_repertoire',
    'view field_video',
    'view field_view',
    'view field_woonplaats',
    'view field_a_locatie',
    'view field_a_planner',
    'view field_a_tijd_begin',
    'view field_a_tijd_einde',
    'view field_a_wijzigingen',
    'view field_l_ref_locatie',
    'edit field_afbeeldingen',
    'edit field_datum',
    'edit field_files',
    'edit field_inhoud',
    'edit field_ref_activiteit',
    'edit field_a_locatie',
    'edit field_a_planner',
    'edit field_a_tijd_begin',
    'edit field_a_tijd_einde',
    'edit field_a_wijzigingen',
  ]),
  
  'beheerder' => array_merge($base_permissions, [
    'view own unpublished content',
    // Create permissions for all content types
    'create activiteit content',
    'create foto content',
    'create locatie content',
    'create nieuws content',
    'create pagina content',
    'create programma content',
    'create repertoire content',
    'create vriend content',
    'create webform content',
    // Edit any permissions for all content types
    'edit any activiteit content',
    'edit any foto content',
    'edit any locatie content',
    'edit any nieuws content',
    'edit any pagina content',
    'edit any programma content',
    'edit any repertoire content',
    'edit any vriend content',
    'edit any webform content',
    // Edit own permissions for all content types
    'edit own activiteit content',
    'edit own foto content',
    'edit own locatie content',
    'edit own nieuws content',
    'edit own pagina content',
    'edit own programma content',
    'edit own repertoire content',
    'edit own vriend content',
    'edit own webform content',
    // Delete permissions for all content types
    'delete any activiteit content',
    'delete any foto content',
    'delete any locatie content',
    'delete any nieuws content',
    'delete any pagina content',
    'delete any programma content',
    'delete any repertoire content',
    'delete any vriend content',
    'delete any webform content',
    'delete own activiteit content',
    'delete own foto content',
    'delete own locatie content',
    'delete own nieuws content',
    'delete own pagina content',
    'delete own programma content',
    'delete own repertoire content',
    'delete own vriend content',
    'delete own webform content',
    // All field permissions
    'view field_afbeeldingen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_datum',
    'view field_files',
    'view field_inhoud',
    'view field_l_routelink',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_programma2',
    'view field_ref_activiteit',
    'view field_repertoire',
    'view field_video',
    'view field_view',
    'view field_woonplaats',
    'view field_a_locatie',
    'view field_a_planner',
    'view field_a_tijd_begin',
    'view field_a_tijd_einde',
    'view field_a_wijzigingen',
    'view field_l_ref_locatie',
    'view field_l_adres',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_componist',
    'view field_arrangeur',
    'view field_genre',
    'view field_uitgave',
    'view field_toegang',
    'view field_v_categorie',
    'view field_v_website',
    'edit field_afbeeldingen',
    'edit field_audio_type',
    'edit field_audio_uitvoerende',
    'edit field_datum',
    'edit field_files',
    'edit field_inhoud',
    'edit field_l_routelink',
    'edit field_partij_band',
    'edit field_partij_koor_l',
    'edit field_partij_tekst',
    'edit field_programma2',
    'edit field_ref_activiteit',
    'edit field_repertoire',
    'edit field_video',
    'edit field_view',
    'edit field_woonplaats',
    'edit field_a_locatie',
    'edit field_a_planner',
    'edit field_a_tijd_begin',
    'edit field_a_tijd_einde',
    'edit field_a_wijzigingen',
    'edit field_l_ref_locatie',
    'edit field_l_adres',
    'edit field_l_plaats',
    'edit field_l_postcode',
    'edit field_componist',
    'edit field_arrangeur',
    'edit field_genre',
    'edit field_uitgave',
    'edit field_toegang',
    'edit field_v_categorie',
    'edit field_v_website',
  ]),
  
  'dirigent' => array_merge($base_permissions, [
    'view own unpublished content',
    'edit any activiteit content',
    'view revision status messages',
    'view revisions of any activiteit content',
    'publish revisions of any activiteit content',
    // Dirigent gets specific activity editing permissions
    'view field_afbeeldingen',
    'view field_datum',
    'view field_files',
    'view field_programma2',
    'view field_ref_activiteit',
    'view field_a_locatie',
    'view field_a_planner',
    'view field_a_tijd_begin',
    'view field_a_tijd_einde',
    'view field_a_wijzigingen',
    'edit field_datum',
    'edit field_programma2',
    'edit field_a_locatie',
    'edit field_a_tijd_begin',
    'edit field_a_tijd_einde',
    'edit field_a_wijzigingen',
  ]),
];

// Committee roles get similar permissions to 'lid' plus content creation
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
    
    $granted_count = 0;
    $failed_count = 0;
    
    foreach ($permissions as $permission) {
      try {
        $role->grantPermission($permission);
        $granted_count++;
      } catch (\Exception $e) {
        echo "  ✗ Failed to grant: {$permission} - {$e->getMessage()}\n";
        $failed_count++;
      }
    }
    
    $role->save();
    echo "  ✓ Granted {$granted_count} permissions successfully\n";
    if ($failed_count > 0) {
      echo "  ⚠ Failed to grant {$failed_count} permissions\n";
    }
    echo "  Role saved successfully.\n\n";
    
  } else {
    echo "Role '{$role_id}' not found. Skipping...\n\n";
  }
}

echo "✅ Content permissions configuration completed!\n\n";

echo "📋 Permissions configured for:\n";
echo "- Content creation and editing (create, edit own, edit any, delete)\n";
echo "- Content viewing (access content, view media, view own unpublished)\n";
echo "- Field permissions (view field_*, edit field_*)\n";
echo "- Basic status and warning messages\n\n";

echo "📦 Content Types Covered (D11 only):\n";
foreach ($content_types as $content_type) {
  echo "  • {$content_type}\n";
}
echo "\n";

echo "🚫 Deprecated Content Types (NOT configured):\n";
echo "  • audio (now media bundle)\n";
echo "  • video (now media bundle)\n";
echo "  • profiel (now user profile fields)\n";
echo "  • verslag (deprecated)\n";
echo "  • nieuwsbrief (not migrated)\n\n";

echo "📋 Next steps:\n";
echo "1. Visit /admin/people/permissions to review configured permissions\n";
echo "2. Test content creation and editing with different roles\n";
echo "3. Verify field-level permissions are working correctly\n";
echo "4. Configure additional permissions if needed via the UI\n";
echo "5. Test media bundle permissions separately\n";