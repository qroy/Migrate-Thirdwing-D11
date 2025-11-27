<?php

namespace Drupal\thirdwing_migrate\Commands;

use Drush\Commands\DrushCommands;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;

/**
 * Drush commands for ThirdWing migrations.
 */
class ThirdWingMigrateCommands extends DrushCommands {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * Constructs a new ThirdWingMigrateCommands object.
   *
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_plugin_manager
   *   The migration plugin manager.
   */
  public function __construct(MigrationPluginManagerInterface $migration_plugin_manager) {
    parent::__construct();
    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * Display ThirdWing migration overview.
   *
   * @command thirdwing:overview
   * @aliases tw-overview
   * @usage thirdwing:overview
   *   Display an overview of all ThirdWing migrations.
   */
  public function overview() {
    $this->output()->writeln('');
    $this->output()->writeln('<info>===========================================</info>');
    $this->output()->writeln('<info>   ThirdWing Migration Overview</info>');
    $this->output()->writeln('<info>===========================================</info>');
    $this->output()->writeln('');

    // Get all thirdwing migrations
    $migrations = $this->migrationPluginManager->createInstancesByTag('Drupal 6');
    
    if (empty($migrations)) {
      $this->output()->writeln('<error>No migrations found!</error>');
      return;
    }

    $categories = [
      'Taxonomy' => [],
      'User' => [],
      'Files' => [],
      'Media' => [],
      'Content' => [],
    ];

    foreach ($migrations as $migration_id => $migration) {
      if (strpos($migration_id, 'thirdwing_') !== 0) {
        continue;
      }

      $status = $migration->getStatusLabel();
      $label = $migration->label();
      $source_count = $migration->getSourcePlugin()->count();
      $imported = $migration->getIdMap()->importedCount();
      
      $info = [
        'id' => $migration_id,
        'label' => $label,
        'status' => $status,
        'source' => $source_count,
        'imported' => $imported,
      ];

      // Categorize
      if (strpos($migration_id, 'taxonomy') !== false) {
        $categories['Taxonomy'][] = $info;
      }
      elseif (strpos($migration_id, 'user') !== false) {
        $categories['User'][] = $info;
      }
      elseif (strpos($migration_id, 'file') !== false) {
        $categories['Files'][] = $info;
      }
      elseif (strpos($migration_id, 'media') !== false) {
        $categories['Media'][] = $info;
      }
      elseif (strpos($migration_id, 'node') !== false) {
        $categories['Content'][] = $info;
      }
    }

    // Display by category
    foreach ($categories as $category => $items) {
      if (empty($items)) {
        continue;
      }

      $this->output()->writeln("<comment>$category:</comment>");
      $this->output()->writeln(str_repeat('-', 80));

      foreach ($items as $item) {
        $status_color = $item['status'] == 'Idle' ? 'info' : 'error';
        $progress = $item['source'] > 0 ? sprintf('%.1f%%', ($item['imported'] / $item['source']) * 100) : '0%';
        
        $line = sprintf(
          '  %-30s | <%s>%-12s</> | %5d/%-5d (%s)',
          $item['label'],
          $status_color,
          $item['status'],
          $item['imported'],
          $item['source'],
          $progress
        );
        
        $this->output()->writeln($line);
      }

      $this->output()->writeln('');
    }

    $this->output()->writeln('<info>===========================================</info>');
    $this->output()->writeln('');
    $this->output()->writeln('Run migrations with: <comment>./migrate.sh</comment>');
    $this->output()->writeln('Rollback with: <comment>./rollback.sh</comment>');
    $this->output()->writeln('');
  }

  /**
   * Run all ThirdWing migrations in correct order.
   *
   * @command thirdwing:migrate
   * @aliases tw-migrate
   * @usage thirdwing:migrate
   *   Run all ThirdWing migrations.
   */
  public function migrate() {
    $this->output()->writeln('<info>Running ThirdWing migrations...</info>');
    
    $migrations = [
      'd6_user_role' => 'User Roles',
      'thirdwing_taxonomy_toegang' => 'Toegang Taxonomy',
      'thirdwing_user' => 'Users',
      'thirdwing_file' => 'Files',
      'thirdwing_media_image' => 'Media - Images',
      'thirdwing_media_document' => 'Media - Documents',
      'thirdwing_media_audio' => 'Media - Audio',
      'thirdwing_media_video' => 'Media - Video',
      'thirdwing_node_artikel' => 'Content - Artikel',
      'thirdwing_node_document' => 'Content - Document',
      'thirdwing_node_agenda' => 'Content - Agenda',
      'thirdwing_node_pagina' => 'Content - Pagina',
    ];

    foreach ($migrations as $id => $label) {
      $this->output()->writeln('');
      $this->output()->writeln("<comment>>>> Migrating: $label ($id)</comment>");
      
      try {
        $this->processManager()->drush($this->siteAliasManager()->getSelf(), 'migrate:import', [$id], [
          'update' => true,
          'feedback' => '100 items',
        ]);
        $this->output()->writeln("<info>✓ Completed: $id</info>");
      }
      catch (\Exception $e) {
        $this->output()->writeln("<error>✗ Failed: $id - {$e->getMessage()}</error>");
      }
    }

    $this->output()->writeln('');
    $this->output()->writeln('<info>Migration completed!</info>');
  }

}
