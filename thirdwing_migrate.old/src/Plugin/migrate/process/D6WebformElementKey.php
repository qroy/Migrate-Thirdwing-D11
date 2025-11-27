<?php

/**
 * @file
 * D6 webform element key process plugin for D6 to D11 migration.
 * File: thirdwing_migrate/src/Plugin/migrate/process/D6WebformElementKey.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to get webform element key from component ID.
 *
 * @MigrateProcessPlugin(
 *   id = "d6_webform_element_key"
 * )
 */
class D6WebformElementKey extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    list($nid, $cid) = $value;
    
    // Get source database connection
    $database = \Drupal::database()->getConnection('default', 'migrate');
    
    // Get the form key for this component
    $form_key = $database->select('webform_component', 'wc')
      ->fields('wc', ['form_key'])
      ->condition('nid', $nid)
      ->condition('cid', $cid)
      ->execute()
      ->fetchField();

    return $form_key ?: 'component_' . $cid;
  }

}