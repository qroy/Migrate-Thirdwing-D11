<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Database\Database;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Gets file ID from Content Profile node and looks up in file migration.
 *
 * Usage:
 * @code
 * user_picture:
 *   plugin: profile_field_file
 *   source: uid
 *   profile_field: field_foto
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "profile_field_file"
 * )
 */
class ProfileFieldFile extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected $migrateLookup;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrateLookupInterface $migrate_lookup) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migrateLookup = $migrate_lookup;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('migrate.lookup')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $uid = $value;
    $field_name = $this->configuration['profile_field'];
    
    if (empty($uid) || empty($field_name)) {
      return NULL;
    }

    $d6_database = Database::getConnection('default', 'drupal_6');
    
    // Find profiel node
    $profiel_query = $d6_database->select('node', 'n')
      ->fields('n', ['nid', 'vid'])
      ->condition('type', 'profiel')
      ->condition('uid', $uid)
      ->range(0, 1);
    $profiel_node = $profiel_query->execute()->fetchAssoc();
    
    if (!$profiel_node) {
      return NULL;
    }
    
    // Get file ID from CCK table
    $table_name = 'content_type_profiel';
    
    if (!$d6_database->schema()->tableExists($table_name)) {
      return NULL;
    }
    
    $field_query = $d6_database->select($table_name, 'ct')
      ->condition('nid', $profiel_node['nid'])
      ->condition('vid', $profiel_node['vid']);
    
    $field_data = $field_query->execute()->fetchAssoc();
    
    if (!$field_data) {
      return NULL;
    }
    
    // Get FID
    $fid_column = $field_name . '_fid';
    if (!isset($field_data[$fid_column])) {
      return NULL;
    }
    
    $source_fid = $field_data[$fid_column];
    
    // Lookup in file migration
    $lookup_result = $this->migrateLookup->lookup('thirdwing_file', [$source_fid]);
    
    if (!empty($lookup_result)) {
      return $lookup_result[0]['fid'];
    }
    
    return NULL;
  }

}
