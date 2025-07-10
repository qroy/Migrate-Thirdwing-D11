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

    // Convert all numeric string fields to actual integers
    $numeric_fields = ['vid', 'weight', 'hierarchy', 'multiple', 'required', 'relations', 'tags'];
    foreach ($numeric_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value !== null) {
        $row->setSourceProperty($field, (int) $value);
      } else {
        $row->setSourceProperty($field, 0);
      }
    }
    
    // Clean string fields
    $string_fields = ['name', 'description', 'help', 'module'];
    foreach ($string_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null) {
        $row->setSourceProperty($field, '');
      }
    }

    return TRUE;
  }
}