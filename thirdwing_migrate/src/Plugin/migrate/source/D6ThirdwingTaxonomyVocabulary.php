<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Taxonomy Vocabularies.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_taxonomy_vocabulary",
 *   source_module = "taxonomy"
 * )
 */
class D6ThirdwingTaxonomyVocabulary extends SqlBase {

  use MigrationHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('vocabulary', 'v')
      ->fields('v')
      ->orderBy('v.vid');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'vid' => $this->t('Vocabulary ID'),
      'name' => $this->t('Name'),
      'description' => $this->t('Description'),
      'help' => $this->t('Help text'),
      'relations' => $this->t('Relations'),
      'hierarchy' => $this->t('Hierarchy'),
      'multiple' => $this->t('Multiple'),
      'required' => $this->t('Required'),
      'tags' => $this->t('Tags'),
      'module' => $this->t('Module'),
      'weight' => $this->t('Weight'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'vid' => [
        'type' => 'integer',
        'alias' => 'v',
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
    $this->cleanNullValues($row, ['name', 'description', 'help', 'module']);

    return TRUE;
  }
}