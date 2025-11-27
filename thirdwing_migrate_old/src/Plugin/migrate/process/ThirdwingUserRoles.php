<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Assigns roles to users based on D6 users_roles table.
 *
 * @MigrateProcessPlugin(
 *   id = "thirdwing_user_roles"
 * )
 */
class ThirdwingUserRoles extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $uid = $value;
    $roles = [];
    
    if (empty($uid)) {
      return $roles;
    }

    try {
      // Get D6 database connection
      $database = Database::getConnection('default', 'migrate');
      
      // Get user roles from D6 users_roles table
      $query = $database->select('users_roles', 'ur')
        ->fields('ur', ['rid'])
        ->condition('ur.uid', $uid);
      
      $user_roles = $query->execute()->fetchCol();
      
      if (!empty($user_roles)) {
        // Get role names from D6 role table
        $role_query = $database->select('role', 'r')
          ->fields('r', ['rid', 'name'])
          ->condition('r.rid', $user_roles, 'IN');
        
        $role_data = $role_query->execute()->fetchAllKeyed();
        
        foreach ($role_data as $rid => $role_name) {
          // Skip core roles - they're handled automatically
          if (in_array($rid, [1, 2])) {
            continue;
          }
          
          // Convert role name to machine name using same logic as role migration
          $machine_name = $this->convertRoleNameToMachineName($role_name);
          if ($machine_name) {
            $roles[] = $machine_name;
          }
        }
      }
      
      // Always ensure authenticated role is present for active users
      if ($row->getSourceProperty('status') == 1) {
        $roles[] = 'authenticated';
      }
      
    } catch (\Exception $e) {
      // Log error but don't fail migration
      \Drupal::logger('thirdwing_migrate')->error('Failed to migrate roles for user @uid: @error', [
        '@uid' => $uid,
        '@error' => $e->getMessage(),
      ]);
    }
    
    return array_unique($roles);
  }

  /**
   * Convert D6 role name to D11 machine name.
   * 
   * Uses same logic as ThirdwingRoleIdGenerator.
   */
  protected function convertRoleNameToMachineName($role_name) {
    $role_map = [
      'Admin' => 'admin',
      'Aspirant-lid' => 'aspirant_lid',
      'Auteur' => 'auteur',
      'Band' => 'band',
      'Beheerder' => 'beheerder',
      'Bestuur' => 'bestuur',
      'Commissie Concerten' => 'commissie_concerten',
      'Commissie Faciliteiten en Logistiek' => 'commissie_faciliteiten_logistiek',
      'Commissie Interne Relaties' => 'commissie_interne_relaties',
      'Commissie Koorregie' => 'commissie_koorregie',
      'Commissie Ledenwerving' => 'commissie_ledenwerving',
      'Commissie Publieke Relaties' => 'commissie_publieke_relaties',
      'Dirigent' => 'dirigent',
      'Feestcommissie' => 'feestcommissie',
      'Lid' => 'lid',
      'Muziekcommissie' => 'muziekcommissie',
      'Technische Commissie' => 'technische_commissie',
      'Vriend' => 'vriend',
    ];

    // Use predefined mapping if available
    if (isset($role_map[$role_name])) {
      return $role_map[$role_name];
    }

    // Otherwise, generate machine name from the original name
    $machine_name = strtolower($role_name);
    $machine_name = preg_replace('/[^a-z0-9_]/', '_', $machine_name);
    $machine_name = preg_replace('/_+/', '_', $machine_name);
    $machine_name = trim($machine_name, '_');

    // Ensure it doesn't conflict with core roles
    $reserved_roles = ['anonymous', 'authenticated', 'administrator'];
    if (in_array($machine_name, $reserved_roles)) {
      $machine_name = 'tw_' . $machine_name;
    }

    return $machine_name;
  }

}