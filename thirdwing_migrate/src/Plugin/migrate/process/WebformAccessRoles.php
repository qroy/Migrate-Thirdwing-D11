<?php

/**
 * @file
 * Webform access roles process plugin for D6 to D11 migration.
 * File: thirdwing_migrate/src/Plugin/migrate/process/WebformAccessRoles.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin for webform access roles.
 *
 * @MigrateProcessPlugin(
 *   id = "webform_access_roles"
 * )
 */
class WebformAccessRoles extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $nid = $value;
    
    // Get source database connection
    $database = \Drupal::database()->getConnection('default', 'migrate');
    
    // Get webform roles for this form
    $roles = $database->select('webform_roles', 'wr')
      ->fields('wr', ['rid'])
      ->condition('nid', $nid)
      ->execute()
      ->fetchCol();

    // If no specific roles, default to authenticated users
    if (empty($roles)) {
      return [
        'create' => [
          'roles' => ['authenticated'],
          'users' => [],
          'permissions' => [],
        ],
      ];
    }

    // Use the same D6 to D11 role mapping as the main thirdwing migration
    // Based on your existing user roles migration
    $role_mapping = [
      1 => 'anonymous',
      2 => 'authenticated',
      3 => 'admin',
      4 => 'content_manager', 
      5 => 'editor',
      6 => 'beheerder',
      7 => 'super_admin',
      8 => 'member',
      9 => 'friend',
      10 => 'moderator',
      11 => 'webform_manager',
      12 => 'pr',
      13 => 'regie',
      14 => 'tec',
      15 => 'concert',
      16 => 'feest',
      17 => 'bestuur',
      18 => 'mc',
      19 => 'ir',
      20 => 'lw',
      21 => 'fl',
    ];

    $mapped_roles = [];
    foreach ($roles as $rid) {
      if (isset($role_mapping[$rid])) {
        $mapped_roles[] = $role_mapping[$rid];
      }
    }

    // If no mapped roles found, default to authenticated
    if (empty($mapped_roles)) {
      $mapped_roles = ['authenticated'];
    }

    return [
      'create' => [
        'roles' => $mapped_roles,
        'users' => [],
        'permissions' => [],
      ],
      'view_any' => [
        'roles' => ['admin', 'beheerder', 'super_admin', 'editor', 'content_manager'],
        'users' => [],
        'permissions' => [],
      ],
      'update_any' => [
        'roles' => ['admin', 'beheerder', 'super_admin', 'webform_manager'],
        'users' => [],
        'permissions' => [],
      ],
      'delete_any' => [
        'roles' => ['admin', 'beheerder', 'super_admin'],
        'users' => [],
        'permissions' => [],
      ],
      'purge_any' => [
        'roles' => ['admin', 'super_admin'],
        'users' => [],
        'permissions' => [],
      ],
    ];
  }

}