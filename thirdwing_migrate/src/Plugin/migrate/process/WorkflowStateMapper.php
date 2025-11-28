<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D6 Workflow states to D11 Content Moderation states.
 *
 * Usage:
 * @code
 * moderation_state:
 *   plugin: workflow_state_mapper
 *   source: workflow_state
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "workflow_state_mapper"
 * )
 */
class WorkflowStateMapper extends ProcessPluginBase {

  /**
   * D6 Workflow state ID to D11 moderation state mapping.
   */
  const STATE_MAP = [
    // Workflow 1: nieuws, pagina, repertoire
    1 => 'draft',      // (creation)
    2 => 'draft',      // Concept
    3 => 'published',  // Gepubliceerd
    4 => 'archived',   // Archief
    8 => 'draft',      // Prullenmand
    9 => 'published',  // Aangeraden (also sets promote=1)
    
    // Workflow 3: activiteit
    10 => 'draft',     // (aanmaak)
    11 => 'published', // Actief
    12 => 'archived',  // Verlopen
    13 => 'draft',     // Inactief
    
    // Workflow 4: locatie, programma
    14 => 'draft',     // (aanmaak)
    15 => 'draft',     // Concept
    16 => 'draft',     // Prullenmand
    17 => 'published', // Aangeraden (also sets promote=1)
    18 => 'archived',  // Archief
    19 => 'published', // Geen Archief
    20 => 'published', // Gepubliceerd
    
    // Workflow 5: album
    21 => 'draft',     // (aanmaak)
    22 => 'published', // Gepubliceerd
    23 => 'published', // Aangeraden (also sets promote=1)
  ];

  /**
   * States that should also set promote=1 (Featured/Aangeraden).
   */
  const FEATURED_STATES = [9, 17, 23];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // If no workflow state, use status field
    if (empty($value)) {
      $status = $row->getSourceProperty('status');
      return $status ? 'published' : 'draft';
    }

    // Map workflow state ID to moderation state
    if (isset(self::STATE_MAP[$value])) {
      $moderation_state = self::STATE_MAP[$value];
      
      // Set promote=1 for "Aangeraden" states
      if (in_array($value, self::FEATURED_STATES)) {
        $row->setDestinationProperty('promote', 1);
      }
      
      return $moderation_state;
    }

    // Fallback: use status field
    $status = $row->getSourceProperty('status');
    return $status ? 'published' : 'draft';
  }

}
