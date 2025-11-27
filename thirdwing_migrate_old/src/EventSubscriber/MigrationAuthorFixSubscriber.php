<?php

namespace Drupal\thirdwing_migrate\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Event subscriber to ensure migrated nodes always have a valid author.
 */
class MigrationAuthorFixSubscriber implements EventSubscriberInterface {

  protected $entityTypeManager;
  protected $logger;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerChannelFactoryInterface $logger_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger_factory->get('thirdwing_migrate');
  }

  public static function getSubscribedEvents() {
    return [
      MigrateEvents::PRE_ROW_SAVE => ['onPreRowSave', 100],
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave', -100],
    ];
  }

  public function onPreRowSave(MigratePreRowSaveEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();
    
    if (!$this->isNodeMigration($migration)) {
      return;
    }

    $destination = $row->getDestination();
    $uid = $destination['uid'] ?? NULL;

    if (empty($uid) || !$this->isValidUser($uid)) {
      $fallback_uid = $this->getFallbackUid();
      $row->setDestinationProperty('uid', $fallback_uid);
      
      $this->logger->warning('Fixed missing author for node @nid, set to uid @uid', [
        '@nid' => $row->getSourceProperty('nid'),
        '@uid' => $fallback_uid,
      ]);
    }
  }

  public function onPostRowSave(MigratePostRowSaveEvent $event) {
    $migration = $event->getMigration();
    $destination_ids = $event->getDestinationIdValues();
    
    if (!$this->isNodeMigration($migration)) {
      return;
    }

    $nid = $destination_ids[0] ?? NULL;
    if (!$nid) {
      return;
    }

    $node_storage = $this->entityTypeManager->getStorage('node');
    $node = $node_storage->load($nid);
    
    if (!$node) {
      return;
    }

    $uid = $node->getOwnerId();
    
    if (empty($uid) || !$this->isValidUser($uid)) {
      $fallback_uid = $this->getFallbackUid();
      $node->setOwnerId($fallback_uid);
      $node->save();
      
      $this->logger->warning('Post-save fix: Node @nid author changed to @uid', [
        '@nid' => $nid,
        '@uid' => $fallback_uid,
      ]);
    }
  }

  protected function isNodeMigration($migration) {
    $destination_config = $migration->getDestinationConfiguration();
    return isset($destination_config['plugin']) && $destination_config['plugin'] === 'entity:node';
  }

  protected function isValidUser($uid) {
    if (empty($uid) || !is_numeric($uid)) {
      return FALSE;
    }
    
    $user_storage = $this->entityTypeManager->getStorage('user');
    $user = $user_storage->load($uid);
    
    return $user && $user->isActive();
  }

  protected function getFallbackUid() {
    if ($this->isValidUser(1)) {
      return 1;
    }
    
    $user_storage = $this->entityTypeManager->getStorage('user');
    $users = $user_storage->getQuery()
      ->condition('status', 1)
      ->range(0, 1)
      ->execute();
    
    return !empty($users) ? reset($users) : 1;
  }
}