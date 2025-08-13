<?php

/**
 * @file
 * Webform submission data destination plugin.
 * File: thirdwing_migrate/src/Plugin/migrate/destination/WebformSubmissionData.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Row;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Destination plugin for webform submission data.
 *
 * @MigrateDestination(
 *   id = "webform_submission_data"
 * )
 */
class WebformSubmissionData extends DestinationBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new WebformSubmissionData destination.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'entity_id' => [
        'type' => 'integer',
      ],
      'element_key' => [
        'type' => 'string',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'entity_id' => $this->t('Webform submission entity ID'),
      'webform_id' => $this->t('Webform ID'),
      'element_key' => $this->t('Element key'),
      'element_data' => $this->t('Element data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $entity_id = $row->getDestinationProperty('entity_id');
    $element_key = $row->getDestinationProperty('element_key');
    $element_data = $row->getDestinationProperty('element_data');

    // Load the webform submission
    $submission_storage = $this->entityTypeManager->getStorage('webform_submission');
    $submission = $submission_storage->load($entity_id);

    if (!$submission) {
      throw new \Exception("Webform submission {$entity_id} not found.");
    }

    // Get current submission data
    $data = $submission->getData();

    // Set the element data
    $data[$element_key] = $element_data;

    // Save the updated submission
    $submission->setData($data);
    $submission->save();

    return [$entity_id, $element_key];
  }

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    $entity_id = $destination_identifier['entity_id'];
    $element_key = $destination_identifier['element_key'];

    // Load the webform submission
    $submission_storage = $this->entityTypeManager->getStorage('webform_submission');
    $submission = $submission_storage->load($entity_id);

    if ($submission) {
      // Get current submission data
      $data = $submission->getData();

      // Remove the element data
      if (isset($data[$element_key])) {
        unset($data[$element_key]);
        $submission->setData($data);
        $submission->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function requirements() {
    return [
      'webform' => [
        'title' => $this->t('Webform module'),
        'description' => $this->t('The Webform module must be installed.'),
        'severity' => REQUIREMENT_ERROR,
      ],
    ];
  }

}