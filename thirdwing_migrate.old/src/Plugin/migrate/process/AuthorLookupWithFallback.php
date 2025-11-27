<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\migrate\process\MigrationLookup;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigratePluginManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Performs user migration lookup with guaranteed fallback to valid user.
 *
 * @MigrateProcessPlugin(
 *   id = "author_lookup_with_fallback"
 * )
 */
class AuthorLookupWithFallback extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  protected $migrationPluginManager;
  protected $entityTypeManager;
  protected $logger;
  protected $migrationLookup;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigratePluginManagerInterface $migration_plugin_manager, EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->migrationPluginManager = $migration_plugin_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;

    $this->configuration += [
      'fallback_uid' => 1,
      'create_fallback_user' => TRUE,
      'fallback_user_name' => 'migrated_content_author',
    ];

    $lookup_configuration = $configuration;
    unset($lookup_configuration['fallback_uid'], $lookup_configuration['create_fallback_user'], $lookup_configuration['fallback_user_name']);
    
    $this->migrationLookup = new MigrationLookup($lookup_configuration, 'migration_lookup', []);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migrate.process'),
      $container->get('entity_type.manager'),
      $container->get('logger.channel.migrate')
    );
  }

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return $this->getFallbackUid();
    }

    try {
      $migrated_uid = $this->migrationLookup->transform($value, $migrate_executable, $row, $destination_property);
      
      if (!empty($migrated_uid) && $this->isValidUser($migrated_uid)) {
        return $migrated_uid;
      }
      
      $this->logger->warning('Migration lookup for uid @source_uid failed for node @nid, using fallback', [
        '@source_uid' => $value,
        '@nid' => $row->getSourceProperty('nid'),
      ]);
      
      return $this->getFallbackUid();
      
    } catch (\Exception $e) {
      $this->logger->error('Exception during user lookup for uid @source_uid: @message', [
        '@source_uid' => $value,
        '@message' => $e->getMessage(),
      ]);
      
      return $this->getFallbackUid();
    }
  }

  protected function getFallbackUid() {
    $fallback_uid = $this->configuration['fallback_uid'];
    
    if ($this->isValidUser($fallback_uid)) {
      return $fallback_uid;
    }
    
    if ($this->configuration['create_fallback_user']) {
      return $this->createFallbackUser();
    }
    
    $user_storage = $this->entityTypeManager->getStorage('user');
    $users = $user_storage->getQuery()
      ->condition('status', 1)
      ->range(0, 1)
      ->execute();
    
    if (!empty($users)) {
      return reset($users);
    }
    
    return 1;
  }

  protected function isValidUser($uid) {
    if (empty($uid) || !is_numeric($uid)) {
      return FALSE;
    }
    
    $user_storage = $this->entityTypeManager->getStorage('user');
    $user = $user_storage->load($uid);
    
    return $user && $user->isActive();
  }

  protected function createFallbackUser() {
    $user_storage = $this->entityTypeManager->getStorage('user');
    
    $existing_users = $user_storage->loadByProperties([
      'name' => $this->configuration['fallback_user_name']
    ]);
    
    if (!empty($existing_users)) {
      $user = reset($existing_users);
      return $user->id();
    }
    
    $user = $user_storage->create([
      'name' => $this->configuration['fallback_user_name'],
      'mail' => $this->configuration['fallback_user_name'] . '@example.com',
      'status' => 1,
      'created' => \Drupal::time()->getRequestTime(),
    ]);
    
    $user->save();
    return $user->id();
  }
}