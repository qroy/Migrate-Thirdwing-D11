<?php

/**
 * @file
 * Webform migration Drush commands.
 * File: thirdwing_migrate/src/Commands/ThirdwingWebformCommands.php
 */

namespace Drupal\thirdwing_migrate\Commands;

use Drush\Commands\DrushCommands;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;

/**
 * Drush commands for Thirdwing webform migration.
 */
class ThirdwingWebformCommands extends DrushCommands {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ThirdwingWebformCommands object.
   */
  public function __construct(MigrationPluginManagerInterface $migration_plugin_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->migrationPluginManager = $migration_plugin_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Import webforms from Drupal 6.
   *
   * @command thirdwing:import-webforms
   * @aliases tw-webforms
   * @option limit Limit the number of items to process
   * @option update Run in update mode
   * @usage thirdwing:import-webforms
   *   Import all webforms from D6
   * @usage thirdwing:import-webforms --limit=5
   *   Import first 5 webforms only
   */
  public function importWebforms($options = ['limit' => NULL, 'update' => FALSE]) {
    $this->output()->writeln('<info>Starting webform migration from Drupal 6...</info>');

    // Define webform migrations in dependency order
    $webform_migrations = [
      'webform_forms',
      'webform_submissions', 
      'webform_submission_data',
    ];

    $total_processed = 0;
    $errors = [];

    foreach ($webform_migrations as $migration_id) {
      $this->output()->writeln("<comment>Processing migration: {$migration_id}</comment>");
      
      try {
        $migration = $this->migrationPluginManager->createInstance($migration_id);
        
        if (!$migration) {
          $errors[] = "Migration {$migration_id} not found";
          continue;
        }

        // Set options
        if ($options['limit']) {
          $migration->getIdMap()->setTrackLastImported(TRUE);
        }

        if ($options['update']) {
          $migration->getIdMap()->prepareUpdate();
        }

        // Execute migration
        $executable = new MigrateExecutable($migration, new MigrateMessage());
        
        if ($options['limit']) {
          $result = $executable->import(['limit' => $options['limit']]);
        } else {
          $result = $executable->import();
        }

        if ($result == MigrationInterface::RESULT_COMPLETED) {
          $processed = $migration->getIdMap()->processedCount();
          $total_processed += $processed;
          $this->output()->writeln("<info>✅ {$migration_id}: {$processed} items processed</info>");
        } else {
          $errors[] = "Migration {$migration_id} failed with result: {$result}";
        }

      } catch (\Exception $e) {
        $errors[] = "Migration {$migration_id} error: " . $e->getMessage();
      }
    }

    // Final summary
    if (empty($errors)) {
      $this->output()->writeln("<info>🎉 Webform migration completed successfully!</info>");
      $this->output()->writeln("<info>Total items processed: {$total_processed}</info>");
    } else {
      $this->output()->writeln("<error>❌ Webform migration completed with errors:</error>");
      foreach ($errors as $error) {
        $this->output()->writeln("<error>  - {$error}</error>");
      }
    }

    // Show webform statistics
    $this->showWebformStats();
  }

  /**
   * Rollback webform migrations.
   *
   * @command thirdwing:rollback-webforms
   * @aliases tw-rollback-webforms
   * @usage thirdwing:rollback-webforms
   *   Rollback all webform migrations
   */
  public function rollbackWebforms() {
    $this->output()->writeln('<info>Rolling back webform migrations...</info>');

    // Rollback in reverse order
    $webform_migrations = [
      'webform_submission_data',
      'webform_submissions',
      'webform_forms',
    ];

    foreach ($webform_migrations as $migration_id) {
      $this->output()->writeln("<comment>Rolling back: {$migration_id}</comment>");
      
      try {
        $migration = $this->migrationPluginManager->createInstance($migration_id);
        
        if ($migration) {
          $executable = new MigrateExecutable($migration, new MigrateMessage());
          $executable->rollback();
          $this->output()->writeln("<info>✅ {$migration_id} rolled back</info>");
        }
      } catch (\Exception $e) {
        $this->output()->writeln("<error>❌ Error rolling back {$migration_id}: " . $e->getMessage() . "</error>");
      }
    }

    $this->output()->writeln('<info>Webform rollback completed</info>');
  }

  /**
   * Show webform migration status.
   *
   * @command thirdwing:webform-status
   * @aliases tw-webform-status
   * @usage thirdwing:webform-status
   *   Show status of webform migrations
   */
  public function webformStatus() {
    $this->output()->writeln('<info>Webform Migration Status</info>');
    $this->output()->writeln('<info>========================</info>');

    $webform_migrations = [
      'webform_forms',
      'webform_submissions',
      'webform_submission_data',
    ];

    foreach ($webform_migrations as $migration_id) {
      try {
        $migration = $this->migrationPluginManager->createInstance($migration_id);
        
        if ($migration) {
          $source_count = $migration->getSourcePlugin()->count();
          $imported_count = $migration->getIdMap()->importedCount();
          $status = $migration->getStatusLabel();
          
          $this->output()->writeln("<comment>{$migration_id}:</comment>");
          $this->output()->writeln("  Status: {$status}");
          $this->output()->writeln("  Source: {$source_count} items");
          $this->output()->writeln("  Imported: {$imported_count} items");
          $this->output()->writeln("");
        }
      } catch (\Exception $e) {
        $this->output()->writeln("<error>Error checking {$migration_id}: " . $e->getMessage() . "</error>");
      }
    }

    $this->showWebformStats();
  }

  /**
   * Validate webform migration.
   *
   * @command thirdwing:validate-webforms
   * @aliases tw-validate-webforms
   * @usage thirdwing:validate-webforms
   *   Validate webform migration results
   */
  public function validateWebforms() {
    $this->output()->writeln('<info>Validating webform migration...</info>');

    $validation_errors = [];

    // Check webform entities
    try {
      $webform_storage = $this->entityTypeManager->getStorage('webform');
      $webforms = $webform_storage->loadMultiple();
      $webform_count = count($webforms);
      
      $this->output()->writeln("<info>✅ Found {$webform_count} webforms</info>");

      // Validate each webform
      foreach ($webforms as $webform) {
        $elements = $webform->getElementsOriginalRaw();
        if (empty($elements)) {
          $validation_errors[] = "Webform {$webform->id()} has no elements";
        }
      }

    } catch (\Exception $e) {
      $validation_errors[] = "Error loading webforms: " . $e->getMessage();
    }

    // Check webform submissions
    try {
      $submission_storage = $this->entityTypeManager->getStorage('webform_submission');
      $submissions = $submission_storage->loadMultiple();
      $submission_count = count($submissions);
      
      $this->output()->writeln("<info>✅ Found {$submission_count} submissions</info>");

      // Validate submissions have data
      $empty_submissions = 0;
      foreach ($submissions as $submission) {
        $data = $submission->getData();
        if (empty($data)) {
          $empty_submissions++;
        }
      }

      if ($empty_submissions > 0) {
        $validation_errors[] = "{$empty_submissions} submissions have no data";
      }

    } catch (\Exception $e) {
      $validation_errors[] = "Error loading submissions: " . $e->getMessage();
    }

    // Check database connectivity
    try {
      $database = \Drupal::database()->getConnection('default', 'migrate');
      $source_webforms = $database->query('SELECT COUNT(*) FROM webform')->fetchField();
      $source_submissions = $database->query('SELECT COUNT(*) FROM webform_submissions')->fetchField();
      
      $this->output()->writeln("<info>Source D6 data: {$source_webforms} webforms, {$source_submissions} submissions</info>");
      
    } catch (\Exception $e) {
      $validation_errors[] = "Error accessing source database: " . $e->getMessage();
    }

    // Final validation result
    if (empty($validation_errors)) {
      $this->output()->writeln('<info>🎉 Webform validation passed!</info>');
    } else {
      $this->output()->writeln('<error>❌ Webform validation failed:</error>');
      foreach ($validation_errors as $error) {
        $this->output()->writeln("<error>  - {$error}</error>");
      }
    }
  }

  /**
   * Show webform statistics.
   */
  protected function showWebformStats() {
    $this->output()->writeln('<info>Webform Statistics</info>');
    $this->output()->writeln('<info>==================</info>');

    try {
      // Webforms
      $webform_storage = $this->entityTypeManager->getStorage('webform');
      $webform_count = $webform_storage->getQuery()->count()->execute();
      
      // Submissions
      $submission_storage = $this->entityTypeManager->getStorage('webform_submission');
      $submission_count = $submission_storage->getQuery()->count()->execute();
      
      $this->output()->writeln("Total webforms: {$webform_count}");
      $this->output()->writeln("Total submissions: {$submission_count}");
      
      // Submissions by webform
      if ($webform_count > 0) {
        $webforms = $webform_storage->loadMultiple();
        $this->output()->writeln("");
        $this->output()->writeln("Submissions per webform:");
        
        foreach ($webforms as $webform) {
          $count = $submission_storage->getQuery()
            ->condition('webform_id', $webform->id())
            ->count()
            ->execute();
          $this->output()->writeln("  {$webform->label()}: {$count}");
        }
      }
      
    } catch (\Exception $e) {
      $this->output()->writeln("<error>Error getting statistics: " . $e->getMessage() . "</error>");
    }
    
    $this->output()->writeln("");
  }

  /**
   * Sync webforms incrementally.
   *
   * @command thirdwing:sync-webforms
   * @aliases tw-sync-webforms
   * @option since Sync submissions since timestamp
   * @usage thirdwing:sync-webforms
   *   Sync new webform submissions
   * @usage thirdwing:sync-webforms --since=1234567890
   *   Sync submissions since specific timestamp
   */
  public function syncWebforms($options = ['since' => NULL]) {
    $this->output()->writeln('<info>Syncing webform submissions...</info>');

    try {
      // Get last sync time or use provided timestamp
      $since = $options['since'] ?: \Drupal::state()->get('thirdwing_migrate.webform_last_sync', 0);
      
      if ($since) {
        $this->output()->writeln("<comment>Syncing submissions since: " . date('Y-m-d H:i:s', $since) . "</comment>");
      } else {
        $this->output()->writeln("<comment>Performing full webform sync</comment>");
      }

      // Reset and run submission migrations
      $migrations = ['webform_submissions', 'webform_submission_data'];
      
      foreach ($migrations as $migration_id) {
        $migration = $this->migrationPluginManager->createInstance($migration_id);
        
        if ($migration) {
          // Add timestamp filter if specified
          if ($since) {
            $source_config = $migration->getSourceConfiguration();
            $source_config['timestamp_filter'] = $since;
            $migration->set('source', $source_config);
          }
          
          $executable = new MigrateExecutable($migration, new MigrateMessage());
          $result = $executable->import();
          
          if ($result == MigrationInterface::RESULT_COMPLETED) {
            $processed = $migration->getIdMap()->processedCount();
            $this->output()->writeln("<info>✅ {$migration_id}: {$processed} items synced</info>");
          }
        }
      }

      // Update last sync timestamp
      \Drupal::state()->set('thirdwing_migrate.webform_last_sync', time());
      
      $this->output()->writeln('<info>🎉 Webform sync completed!</info>');
      
    } catch (\Exception $e) {
      $this->output()->writeln("<error>❌ Webform sync failed: " . $e->getMessage() . "</error>");
    }
  }

}