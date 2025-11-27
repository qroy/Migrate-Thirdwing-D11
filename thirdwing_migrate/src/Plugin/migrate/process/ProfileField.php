<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;

/**
 * Gets field value from Content Profile node linked to user.
 *
 * Usage:
 * @code
 * field_voornaam:
 *   plugin: profile_field
 *   source: uid
 *   profile_field: field_voornaam
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "profile_field"
 * )
 */
class ProfileField extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // $value is the user ID
    $uid = $value;
    $field_name = $this->configuration['profile_field'];
    
    if (empty($uid) || empty($field_name)) {
      return NULL;
    }

    // Get D6 database connection
    $d6_database = Database::getConnection('default', 'drupal_6');
    
    // Find profiel node for this user
    // Content Profile creates nodes with uid = profile owner
    $profiel_query = $d6_database->select('node', 'n')
      ->fields('n', ['nid', 'vid'])
      ->condition('type', 'profiel')
      ->condition('uid', $uid)
      ->range(0, 1);
    $profiel_node = $profiel_query->execute()->fetchAssoc();
    
    if (!$profiel_node) {
      return NULL;
    }
    
    $nid = $profiel_node['nid'];
    $vid = $profiel_node['vid'];
    
    // Get field value from content_type_profiel table
    $table_name = 'content_type_profiel';
    
    if (!$d6_database->schema()->tableExists($table_name)) {
      return NULL;
    }
    
    // Query the CCK table
    $field_query = $d6_database->select($table_name, 'ct')
      ->condition('nid', $nid)
      ->condition('vid', $vid);
    
    // Get all columns for this field (value, format, etc.)
    $field_data = $field_query->execute()->fetchAssoc();
    
    if (!$field_data) {
      return NULL;
    }
    
    // Return the field value
    // Check for different column suffixes
    $possible_columns = [
      $field_name . '_value',
      $field_name . '_tid',
      $field_name,
    ];
    
    foreach ($possible_columns as $column) {
      if (isset($field_data[$column])) {
        return $field_data[$column];
      }
    }
    
    return NULL;
  }

}
