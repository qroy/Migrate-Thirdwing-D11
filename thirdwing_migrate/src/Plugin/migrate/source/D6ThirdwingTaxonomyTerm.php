<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Taxonomy Terms.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_taxonomy_term",
 *   source_module = "taxonomy"
 * )
 */
class D6ThirdwingTaxonomyTerm extends SqlBase {

  use MigrationHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('term_data', 'td')
      ->fields('td')
      ->orderBy('td.tid');

    $query->leftJoin('term_hierarchy', 'th', 'td.tid = th.tid');
    $query->addField('th', 'parent');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'tid' => $this->t('Term ID'),
      'vid' => $this->t('Vocabulary ID'),
      'name' => $this->t('Name'),
      'description' => $this->t('Description'),
      'weight' => $this->t('Weight'),
      'parent' => $this->t('Parent term ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'tid' => [
        'type' => 'integer',
        'alias' => 'td',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    // Clean up null/empty values to prevent Html::escape() errors
    $this->cleanNullValues($row, ['name', 'description']);

    // Handle parent hierarchy - if parent is 0, set to NULL for proper hierarchy
    $parent = $row->getSourceProperty('parent');
    if ($parent == 0) {
      $row->setSourceProperty('parent', NULL);
    }

    return TRUE;
  }
}