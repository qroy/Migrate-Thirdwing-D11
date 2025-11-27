<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D6 toegang taxonomy term IDs to D11 term IDs.
 *
 * Gebruik:
 * @code
 * field_toegang:
 *   plugin: thirdwing_toegang_mapper
 *   source: taxonomy
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "thirdwing_toegang_mapper"
 * )
 */
class ToegangMapper extends ProcessPluginBase {

  /**
   * Mapping van D6 term namen naar D11 term IDs of nieuwe waardes.
   */
  const TOEGANG_MAP = [
    'Publiek' => 'publiek',
    'Leden' => 'leden',
    'Bestuur' => 'bestuur',
    'Commissie' => 'commissie',
    'Werkgroep' => 'werkgroep',
    // Voeg meer mappings toe indien nodig
  ];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Als value een array van term IDs is
    if (!is_array($value)) {
      $value = [$value];
    }

    $result = [];

    foreach ($value as $tid) {
      // Haal term naam op uit D6 database
      $term_name = $this->getTermName($tid);
      
      if ($term_name && isset(self::TOEGANG_MAP[$term_name])) {
        // Map naar nieuwe waarde
        $result[] = [
          'target_id' => $this->getD11TermId(self::TOEGANG_MAP[$term_name]),
        ];
      }
    }

    return $result;
  }

  /**
   * Haal term naam op uit D6 database.
   */
  protected function getTermName($tid) {
    // Dit is een vereenvoudigd voorbeeld
    // In werkelijkheid zou je de database moeten queryen
    // of een lookup service gebruiken
    
    // Placeholder - implementeer de daadwerkelijke lookup
    return NULL;
  }

  /**
   * Haal D11 term ID op basis van machine name.
   */
  protected function getD11TermId($machine_name) {
    // Query D11 database voor term ID
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $terms = $storage->loadByProperties([
      'vid' => 'toegang',
      'name' => $machine_name,
    ]);

    if (!empty($terms)) {
      $term = reset($terms);
      return $term->id();
    }

    return NULL;
  }

}
