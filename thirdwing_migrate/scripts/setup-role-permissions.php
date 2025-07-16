# Setup workflow configuration
drush php:script modules/custom/thirdwing_migrate/scripts/setup-content-moderation.php

# Setup role permissions (content editing and viewing only)
drush php:script modules/custom/thirdwing_migrate/scripts/setup-role-permissions.php
```

#### 4. Migration Execution
```bash
# Complete system setup (one-time)
chmod +x modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh
./modules/custom/thirdwing_migrate/scripts/setup-complete-migration.sh

# Initial full migration
./modules/custom/thirdwing_migrate/scripts/migrate-execute.sh

# Regular incremental sync
./modules/custom/thirdwing_migrate/scripts/migrate-sync.sh --since="yesterday"
```

## 🚀 Usage Examples

### Permission Configuration
The setup-role-permissions.php script configures focused content permissions:

```bash
# Run permission setup
drush php:script modules/custom/thirdwing_migrate/scripts/setup-role-permissions.php

# Verify permissions are configured
drush user:role:list
drush user:role:permissions beheerder
```

**Permission Categories Configured:**
- **Content Creation**: `create [content_type] content`
- **Content Editing**: `edit own [content_type] content`, `edit any [content_type] content`
- **Content Deletion**: `delete own [content_type] content`, `delete any [content_type] content`
- **Content Viewing**: `access content`, `view media`, `view own unpublished content`
- **Field Permissions**: `view field_*`, `edit field_*` for all D6 fields
- **Basic Access**: Status and warning messages

**Content Types Covered:**
- `activiteit` (Activities)
- `audio` (Audio content)
- `foto` (Photos)
- `locatie` (Locations)
- `nieuws` (News)
- `pagina` (Pages)
- `profiel` (Profiles)
- `programma` (Programs)
- `repertoire` (Repertoire)
- `verslag` (Reports)
- `video` (Videos)
- `vriend` (Friends)

### Role-Based Permission Summary
- **Anonymous/Authenticated**: Content viewing and public field access
- **Lid** (Member): Content viewing + photo creation + member-specific field access
- **Vriend** (Friend): Limited content viewing only
- **Aspirant-lid**: Member-level viewing permissions
- **Committee Roles**: Member permissions + content creation for activities/news
- **Auteur** (Author): Content creation and editing for news/pages/photos
- **Muziekcommissie**: Repertoire management + activity creation
- **Bestuur** (Board): Member permissions + enhanced field access
- **Beheerder** (Administrator): Full content management + all field editing
- **Dirigent** (Conductor): Activity editing + specific music-related permissions<?php

/**
 * @file
 * Script to configure content editing and viewing permissions after user role migration.
 * 
 * Run with: drush php:script setup-role-permissions.php
 * 
 * This script focuses only on content editing and viewing permissions,
 * excluding administrative, webform, and system-level permissions.
 */

use Drupal\user\Entity\Role;

// Define content types that will exist after migration
$content_types = [
  'activiteit',
  'audio', 
  'foto',
  'locatie',
  'nieuws',
  'pagina',
  'profiel',
  'programma',
  'repertoire',
  'verslag',
  'video',
  'vriend',
];

// Define field permissions from the permission matrix
$field_permissions = [
  // Basic content fields
  'view field_achternaam',
  'view field_achternaam_voorvoegsel',
  'view field_afbeeldingen',
  'view field_audio_bijz',
  'view field_audio_nummer',
  'view field_audio_seizoen',
  'view field_audio_type',
  'view field_audio_uitvoerende',
  'view field_background',
  'view field_datum',
  'view field_files',
  'view field_functie_bestuur',
  'view field_functie_concert',
  'view field_functie_feest',
  'view field_functie_fl',
  'view field_functie_ir',
  'view field_functie_lw',
  'view field_functie_mc',
  'view field_functie_pr',
  'view field_functie_regie',
  'view field_functie_tec',
  'view field_geboortedatum',
  'view field_geslacht',
  'view field_inhoud',
  'view field_jaargang',
  'view field_koor',
  'view field_l_adres',
  'view field_l_bijzonderheden',
  'view field_l_plaats',
  'view field_l_postcode',
  'view field_l_routelink',
  'view field_locatie',
  'view field_mp3',
  'view field_positie',
  'view field_ref_activiteit',
  'view field_rep_arr',
  'view field_rep_arr_jaar',
  'view field_rep_componist',
  'view field_rep_componist_jaar',
  'view field_rep_genre',
  'view field_rep_sinds',
  'view field_rep_uitv',
  'view field_rep_uitv_jaar',
  'view field_uitgave',
  'view field_video',
  'view field_voornaam',
  'view field_vriend_benaming',
  'view field_vriend_soort',
  'view field_vriend_tot',
  'view field_vriend_vanaf',
  'view field_website',
  'view field_woonplaats',
  
  // Member-specific field permissions
  'view field_adres',
  'view field_basgitaar',
  'view field_bijzonderheden',
  'view field_drums',
  'view field_emailbewaking',
  'view field_gitaar',
  'view field_karrijder',
  'view field_keyboard',
  'view field_klapper',
  'view field_kledingcode',
  'view field_ledeninfo',
  'view field_lidsinds',
  'view field_mobiel',
  'view field_notes',
  'view field_partij_band',
  'view field_partij_koor_l',
  'view field_partij_tekst',
  'view field_postcode',
  'view field_prog_type',
  'view field_programma2',
  'view field_repertoire',
  'view field_sleepgroep',
  'view field_sleepgroep_1',
  'view field_sleepgroep_aanwezig',
  'view field_sleepgroep_terug',
  'view field_telefoon',
  'view field_tijd_aanwezig',
  'view field_uitkoor',
  'view field_vervoer',
  
  // Edit permissions for authorized roles
  'edit field_achternaam',
  'edit field_achternaam_voorvoegsel',
  'edit field_adres',
  'edit field_afbeeldingen',
  'edit field_audio_bijz',
  'edit field_audio_nummer',
  'edit field_audio_seizoen',
  'edit field_audio_type',
  'edit field_audio_uitvoerende',
  'edit field_background',
  'edit field_basgitaar',
  'edit field_bijzonderheden',
  'edit field_datum',
  'edit field_drums',
  'edit field_emailbewaking',
  'edit field_files',
  'edit field_functie_bestuur',
  'edit field_functie_concert',
  'edit field_functie_feest',
  'edit field_functie_fl',
  'edit field_functie_ir',
  'edit field_functie_lw',
  'edit field_functie_mc',
  'edit field_functie_pr',
  'edit field_functie_regie',
  'edit field_functie_tec',
  'edit field_geboortedatum',
  'edit field_geslacht',
  'edit field_gitaar',
  'edit field_inhoud',
  'edit field_jaargang',
  'edit field_karrijder',
  'edit field_keyboard',
  'edit field_klapper',
  'edit field_kledingcode',
  'edit field_koor',
  'edit field_l_adres',
  'edit field_l_bijzonderheden',
  'edit field_l_plaats',
  'edit field_l_postcode',
  'edit field_l_routelink',
  'edit field_ledeninfo',
  'edit field_lidsinds',
  'edit field_locatie',
  'edit field_mobiel',
  'edit field_mp3',
  'edit field_notes',
  'edit field_partij_band',
  'edit field_partij_koor_l',
  'edit field_partij_tekst',
  'edit field_positie',
  'edit field_postcode',
  'edit field_prog_type',
  'edit field_programma2',
  'edit field_ref_activiteit',
  'edit field_rep_arr',
  'edit field_rep_arr_jaar',
  'edit field_rep_componist',
  'edit field_rep_componist_jaar',
  'edit field_rep_genre',
  'edit field_rep_sinds',
  'edit field_rep_uitv',
  'edit field_rep_uitv_jaar',
  'edit field_repertoire',
  'edit field_sleepgroep',
  'edit field_sleepgroep_1',
  'edit field_sleepgroep_aanwezig',
  'edit field_sleepgroep_terug',
  'edit field_telefoon',
  'edit field_tijd_aanwezig',
  'edit field_uitgave',
  'edit field_uitkoor',
  'edit field_vervoer',
  'edit field_video',
  'edit field_voornaam',
  'edit field_vriend_benaming',
  'edit field_vriend_lengte',
  'edit field_vriend_soort',
  'edit field_vriend_tot',
  'edit field_vriend_vanaf',
  'edit field_website',
  'edit field_woonplaats',
];

// Define base permissions for content access
$base_permissions = [
  'access content',
  'view media',
  'view status messages',
  'view warning messages',
];

// Define role-specific permissions based on permission matrix
$role_permissions = [
  'anonymous' => [
    'access content',
    'view media',
    'view status messages',
    'view warning messages',
    // Anonymous users can view most public fields
    'view field_achternaam',
    'view field_achternaam_voorvoegsel',
    'view field_afbeeldingen',
    'view field_audio_bijz',
    'view field_audio_nummer',
    'view field_audio_seizoen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_background',
    'view field_datum',
    'view field_files',
    'view field_functie_bestuur',
    'view field_functie_concert',
    'view field_functie_feest',
    'view field_functie_fl',
    'view field_functie_ir',
    'view field_functie_lw',
    'view field_functie_mc',
    'view field_functie_pr',
    'view field_functie_regie',
    'view field_functie_tec',
    'view field_geboortedatum',
    'view field_geslacht',
    'view field_inhoud',
    'view field_jaargang',
    'view field_koor',
    'view field_l_adres',
    'view field_l_bijzonderheden',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_l_routelink',
    'view field_locatie',
    'view field_mp3',
    'view field_positie',
    'view field_ref_activiteit',
    'view field_rep_arr',
    'view field_rep_arr_jaar',
    'view field_rep_componist',
    'view field_rep_componist_jaar',
    'view field_rep_genre',
    'view field_rep_sinds',
    'view field_rep_uitv',
    'view field_rep_uitv_jaar',
    'view field_uitgave',
    'view field_video',
    'view field_voornaam',
    'view field_vriend_benaming',
    'view field_vriend_soort',
    'view field_vriend_tot',
    'view field_vriend_vanaf',
    'view field_website',
    'view field_woonplaats',
  ],
  
  'authenticated' => [
    'access content',
    'view media',
    'view own unpublished content',
    'view status messages',
    'view warning messages',
    // Authenticated users get same field permissions as anonymous
    'view field_achternaam',
    'view field_achternaam_voorvoegsel',
    'view field_afbeeldingen',
    'view field_audio_bijz',
    'view field_audio_nummer',
    'view field_audio_seizoen',
    'view field_audio_type',
    'view field_audio_uitvoerende',
    'view field_background',
    'view field_datum',
    'view field_files',
    'view field_functie_bestuur',
    'view field_functie_concert',
    'view field_functie_feest',
    'view field_functie_fl',
    'view field_functie_ir',
    'view field_functie_lw',
    'view field_functie_mc',
    'view field_functie_pr',
    'view field_functie_regie',
    'view field_functie_tec',
    'view field_geboortedatum',
    'view field_geslacht',
    'view field_inhoud',
    'view field_jaargang',
    'view field_koor',
    'view field_l_adres',
    'view field_l_bijzonderheden',
    'view field_l_plaats',
    'view field_l_postcode',
    'view field_l_routelink',
    'view field_locatie',
    'view field_mp3',
    'view field_positie',
    'view field_ref_activiteit',
    'view field_rep_arr',
    'view field_rep_arr_jaar',
    'view field_rep_componist',
    'view field_rep_componist_jaar',
    'view field_rep_genre',
    'view field_rep_sinds',
    'view field_rep_uitv',
    'view field_rep_uitv_jaar',
    'view field_uitgave',
    'view field_video',
    'view field_voornaam',
    'view field_vriend_benaming',
    'view field_vriend_soort',
    'view field_vriend_tot',
    'view field_vriend_vanaf',
    'view field_website',
    'view field_woonplaats',
  ],
  
  'lid' => [
    'access content',
    'view media',
    'view own unpublished content',
    'create foto content',
    'edit own foto content',
    'view status messages',
    'view warning messages',
    // Lid gets additional member-specific field permissions
    'view field_adres',
    'view field_basgitaar',
    'view field_bijzonderheden',
    'view field_drums',
    'view field_emailbewaking',
    'view field_gitaar',
    'view field_karrijder',
    'view field_keyboard',
    'view field_klapper',
    'view field_kledingcode',
    'view field_ledeninfo',
    'view field_lidsinds',
    'view field_mobiel',
    'view field_notes',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_postcode',
    'view field_prog_type',
    'view field_programma2',
    'view field_repertoire',
    'view field_sleepgroep',
    'view field_sleepgroep_1',
    'view field_sleepgroep_aanwezig',
    'view field_sleepgroep_terug',
    'view field_telefoon',
    'view field_tijd_aanwezig',
    'view field_uitkoor',
    'view field_vervoer',
  ],
  
  'vriend' => [
    'access content',
    'view media',
    'view status messages',
    'view warning messages',
    // Vriend has very limited permissions - only basic content access
  ],
  
  'aspirant_lid' => [
    'access content',
    'view media',
    'view own unpublished content',
    'view status messages',
    'view warning messages',
    // Aspirant-lid gets most member field permissions
    'view field_adres',
    'view field_basgitaar',
    'view field_bijzonderheden',
    'view field_drums',
    'view field_emailbewaking',
    'view field_gitaar',
    'view field_karrijder',
    'view field_keyboard',
    'view field_klapper',
    'view field_kledingcode',
    'view field_ledeninfo',
    'view field_lidsinds',
    'view field_mobiel',
    'view field_notes',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_postcode',
    'view field_prog_type',
    'view field_programma2',
    'view field_repertoire',
    'view field_sleepgroep',
    'view field_sleepgroep_1',
    'view field_sleepgroep_aanwezig',
    'view field_sleepgroep_terug',
    'view field_telefoon',
    'view field_tijd_aanwezig',
    'view field_uitkoor',
    'view field_vervoer',
  ],
  
  'bestuur' => [
    'access content',
    'view media',
    'view own unpublished content',
    'view status messages',
    'view warning messages',
    // Bestuur gets all member field permissions
    'view field_adres',
    'view field_geboortedatum',
    'view field_gitaar',
    'view field_karrijder',
    'view field_keyboard',
    'view field_klapper',
    'view field_kledingcode',
    'view field_ledeninfo',
    'view field_lidsinds',
    'view field_mobiel',
    'view field_postcode',
    'view field_prog_type',
    'view field_programma2',
    'view field_repertoire',
    'view field_sleepgroep',
    'view field_sleepgroep_1',
    'view field_sleepgroep_aanwezig',
    'view field_sleepgroep_terug',
    'view field_telefoon',
    'view field_tijd_aanwezig',
    'view field_uitkoor',
    'view field_vervoer',
  ],
  
  'muziekcommissie' => [
    'access content',
    'view media',
    'view own unpublished content',
    'create repertoire content',
    'edit own repertoire content',
    'edit any repertoire content',
    'create activiteit content',
    'edit own activiteit content',
    'view status messages',
    'view warning messages',
    // Muziekcommissie gets specific music-related field permissions
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
  ],
  
  'auteur' => [
    'access content',
    'view media',
    'view own unpublished content',
    'create foto content',
    'edit own foto content',
    'create nieuws content',
    'edit own nieuws content',
    'create pagina content',
    'edit own pagina content',
    'edit any activiteit content',
    'view status messages',
    'view warning messages',
    // Auteur gets content editing field permissions
    'edit field_afbeeldingen',
    'edit field_datum',
    'edit field_files',
    'edit field_ref_activiteit',
    'edit field_video',
    'view field_adres',
    'view field_basgitaar',
    'view field_bijzonderheden',
    'view field_drums',
    'view field_geboortedatum',
    'view field_gitaar',
    'view field_karrijder',
    'view field_keyboard',
    'view field_klapper',
    'view field_kledingcode',
    'view field_ledeninfo',
    'view field_lidsinds',
    'view field_mobiel',
    'view field_notes',
    'view field_partij_band',
    'view field_partij_koor_l',
    'view field_partij_tekst',
    'view field_postcode',
    'view field_prog_type',
    'view field_programma2',
    'view field_repertoire',
    'view field_sleepgroep',
    'view field_sleepgroep_1',
    'view field_sleepgroep_aanwezig',
    'view field_sleepgroep_terug',
    'view field_telefoon',
    'view field_tijd_aanwezig',
    'view field_uitkoor',
    'view field_vervoer',
  ],
  
  'beheerder' => [
    'access content',
    'view media',
    'view own unpublished content',
    'view status messages',
    'view warning messages',
    // Beheerder gets full content permissions
    'create activiteit content',
    'edit any activiteit content',
    'delete any activiteit content',
    'edit own activiteit content',
    'delete own activiteit content',
    'create audio content',
    'edit any audio content',
    'delete any audio content',
    'edit own audio content',
    'delete own audio content',
    'create foto content',
    'edit any foto content',
    'delete any foto content',
    'edit own foto content',
    'delete own foto content',
    'create locatie content',
    'edit any locatie content',
    'delete any locatie content',
    'edit own locatie content',
    'delete own locatie content',
    'create nieuws content',
    'edit any nieuws content',
    'delete any nieuws content',
    'edit own nieuws content',
    'delete own nieuws content',
    'create pagina content',
    'edit any pagina content',
    'delete any pagina content',
    'edit own pagina content',
    'delete own pagina content',
    'create profiel content',
    'edit any profiel content',
    'delete any profiel content',
    'edit own profiel content',
    'delete own profiel content',
    'create programma content',
    'edit any programma content',
    'delete any programma content',
    'edit own programma content',
    'delete own programma content',
    'create repertoire content',
    'edit any repertoire content',
    'delete any repertoire content',
    'edit own repertoire content',
    'delete own repertoire content',
    'create verslag content',
    'edit any verslag content',
    'delete any verslag content',
    'edit own verslag content',
    'delete own verslag content',
    'create video content',
    'edit any video content',
    'delete any video content',
    'edit own video content',
    'delete own video content',
    'create vriend content',
    'edit any vriend content',
    'delete any vriend content',
    'edit own vriend content',
    'delete own vriend content',
    // Beheerder gets all field edit permissions
    'edit field_achternaam',
    'edit field_achternaam_voorvoegsel',
    'edit field_adres',
    'edit field_afbeeldingen',
    'edit field_audio_bijz',
    'edit field_audio_nummer',
    'edit field_audio_seizoen',
    'edit field_audio_type',
    'edit field_audio_uitvoerende',
    'edit field_background',
    'edit field_basgitaar',
    'edit field_bijzonderheden',
    'edit field_datum',
    'edit field_drums',
    'edit field_emailbewaking',
    'edit field_files',
    'edit field_functie_bestuur',
    'edit field_functie_concert',
    'edit field_functie_feest',
    'edit field_functie_fl',
    'edit field_functie_ir',
    'edit field_functie_lw',
    'edit field_functie_mc',
    'edit field_functie_pr',
    'edit field_functie_regie',
    'edit field_functie_tec',
    'edit field_geboortedatum',
    'edit field_geslacht',
    'edit field_gitaar',
    'edit field_inhoud',
    'edit field_jaargang',
    'edit field_karrijder',
    'edit field_keyboard',
    'edit field_klapper',
    'edit field_kledingcode',
    'edit field_koor',
    'edit field_l_adres',
    'edit field_l_bijzonderheden',
    'edit field_l_plaats',
    'edit field_l_postcode',
    'edit field_l_routelink',
    'edit field_ledeninfo',
    'edit field_lidsinds',
    'edit field_locatie',
    'edit field_mobiel',
    'edit field_mp3',
    'edit field_notes',
    'edit field_partij_band',
    'edit field_partij_koor_l',
    'edit field_partij_tekst',
    'edit field_positie',
    'edit field_postcode',
    'edit field_prog_type',
    'edit field_programma2',
    'edit field_ref_activiteit',
    'edit field_rep_arr',
    'edit field_rep_arr_jaar',
    'edit field_rep_componist',
    'edit field_rep_componist_jaar',
    'edit field_rep_genre',
    'edit field_rep_sinds',
    'edit field_rep_uitv',
    'edit field_rep_uitv_jaar',
    'edit field_repertoire',
    'edit field_sleepgroep',
    'edit field_sleepgroep_1',
    'edit field_sleepgroep_aanwezig',
    'edit field_sleepgroep_terug',
    'edit field_telefoon',
    'edit field_tijd_aanwezig',
    'edit field_uitgave',
    'edit field_uitkoor',
    'edit field_vervoer',
    'edit field_video',
    'edit field_voornaam',
    'edit field_vriend_benaming',
    'edit field_vriend_lengte',
    'edit field_vriend_soort',
    'edit field_vriend_tot',
    'edit field_vriend_vanaf',
    'edit field_website',
    'edit field_woonplaats',
  ],
  
  'dirigent' => [
    'access content',
    'view media',
    'view own unpublished content',
    'edit any activiteit content',
    'view status messages',
    'view warning messages',
    // Dirigent gets specific field permissions
    'edit field_huiswerk',
    'edit field_ledeninfo',
    'view revision status messages',
    'view revisions of any activiteit content',
    'publish revisions of any activiteit content',
  ],
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
    echo "Configuring content editing and viewing permissions for role: {$role->label()}\n";
    
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

echo "Content editing and viewing permissions configuration completed!\n";
echo "\nPermissions configured for:\n";
echo "- Content creation and editing (create, edit own, edit any, delete)\n";
echo "- Content viewing (access content, view media, view own unpublished)\n";
echo "- Field permissions (view field_*, edit field_*)\n";
echo "- Basic status and warning messages\n";
echo "\nNext steps:\n";
echo "1. Visit /admin/people/permissions to review configured permissions\n";
echo "2. Test content creation and editing with different roles\n";
echo "3. Verify field-level permissions are working correctly\n";
echo "4. Configure additional permissions if needed via the UI\n";