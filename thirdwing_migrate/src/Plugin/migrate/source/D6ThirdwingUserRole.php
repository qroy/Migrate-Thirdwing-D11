<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 user roles.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_user_role",
 *   source_module = "user"
 * )
 */
class D6ThirdwingUserRole extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('role', 'r')
      ->fields('r', ['rid', 'name'])
      ->condition('r.rid', [1, 2], 'NOT IN') // Skip anonymous/authenticated
      ->orderBy('r.rid');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'rid' => $this->t('Role ID'),
      'name' => $this->t('Role name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'rid' => [
        'type' => 'integer',
        'alias' => 'r',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // Skip if this is a core role that should be preserved
    $preserve_roles = ['anonymous user', 'authenticated user'];
    if (in_array($row->getSourceProperty('name'), $preserve_roles)) {
      return FALSE;
    }
    
    return parent::prepareRow($row);
  }

}