<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Generates clean role IDs from D6 role names.
 *
 * @MigrateProcessPlugin(
 *   id = "thirdwing_role_id_generator"
 * )
 */
class ThirdwingRoleIdGenerator extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return NULL;
    }

    // Convert Dutch role names to clean machine names
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
    if (isset($role_map[$value])) {
      return $role_map[$value];
    }

    // Otherwise, generate machine name from the original name
    $machine_name = strtolower($value);
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