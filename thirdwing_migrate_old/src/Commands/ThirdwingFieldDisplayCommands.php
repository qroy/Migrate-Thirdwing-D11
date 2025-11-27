<?php

namespace Drupal\thirdwing_migrate\Commands;

use Drupal\thirdwing_migrate\Service\ThirdwingFieldDisplayService;
use Drush\Commands\DrushCommands;
use Drush\Attributes as CLI;

/**
 * Drush commands for Thirdwing field display configuration.
 */
class ThirdwingFieldDisplayCommands extends DrushCommands {

  /**
   * The field display service.
   */
  protected ThirdwingFieldDisplayService $fieldDisplayService;

  /**
   * Constructor.
   */
  public function __construct(ThirdwingFieldDisplayService $field_display_service) {
    $this->fieldDisplayService = $field_display_service;
  }

  /**
   * Configure default field displays for all Thirdwing content types.
   */
  #[CLI\Command(name: 'thirdwing:setup-displays', aliases: ['tw:displays'])]
  #[CLI\Help(description: 'Configure default field displays for all Thirdwing content types using the hybrid approach.')]
  #[CLI\Usage(name: 'drush thirdwing:setup-displays', description: 'Configure all field displays')]
  #[CLI\Usage(name: 'drush tw:displays --validate', description: 'Validate existing displays without creating new ones')]
  public function setupDisplays($options = ['validate' => FALSE]): void {
    $this->output()->writeln('🎯 <info>Thirdwing Field Display Configuration</info>');
    $this->output()->writeln('<comment>========================================</comment>');
    $this->output()->writeln('');

    if ($options['validate']) {
      $this->validateDisplays();
      return;
    }

    $this->output()->writeln('📋 <info>Configuring default field displays for all content types...</info>');
    $this->output()->writeln('');

    try {
      // Configure displays for all content types and view modes
      $results = $this->fieldDisplayService->configureAllDisplays();

      // Report results
      $this->displayResults($results);

      $this->output()->writeln('');
      $this->output()->writeln('✅ <info>Validating display configurations...</info>');

      // Validate the configurations
      $validation = $this->fieldDisplayService->validateDisplays();
      $this->displayValidation($validation);

      $this->output()->writeln('');
      $this->output()->writeln('🎉 <success>Field display configuration complete!</success>');
      $this->output()->writeln('');

      $this->displayPostSetupInfo();

    } catch (\Exception $e) {
      $this->output()->writeln('<error>❌ Error configuring field displays: ' . $e->getMessage() . '</error>');
      throw $e;
    }
  }

  /**
   * Validate existing field displays.
   */
  #[CLI\Command(name: 'thirdwing:validate-displays', aliases: ['tw:validate-displays'])]
  #[CLI\Help(description: 'Validate existing field display configurations.')]
  public function validateDisplays(): void {
    $this->output()->writeln('🔍 <info>Validating field display configurations...</info>');
    $this->output()->writeln('');

    try {
      $validation = $this->fieldDisplayService->validateDisplays();
      $this->displayValidation($validation);
    } catch (\Exception $e) {
      $this->output()->writeln('<error>❌ Validation error: ' . $e->getMessage() . '</error>');
      throw $e;
    }
  }

  /**
   * Configure displays for a specific content type.
   */
  #[CLI\Command(name: 'thirdwing:setup-display-type', aliases: ['tw:display-type'])]
  #[CLI\Argument(name: 'content_type', description: 'The content type machine name')]
  #[CLI\Option(name: 'view-mode', description: 'Specific view mode to configure (optional)')]
  #[CLI\Help(description: 'Configure field displays for a specific content type.')]
  public function setupDisplayType(string $content_type, $options = ['view-mode' => NULL]): void {
    $this->output()->writeln("🎯 <info>Configuring displays for content type: {$content_type}</info>");
    $this->output()->writeln('');

    $view_modes = $options['view-mode'] ? [$options['view-mode']] : ['default', 'teaser', 'full', 'search_result'];

    foreach ($view_modes as $view_mode) {
      try {
        $this->fieldDisplayService->configureContentTypeDisplay($content_type, $view_mode);
        $this->output()->writeln("✅ <info>Configured {$content_type}.{$view_mode}</info>");
      } catch (\Exception $e) {
        $this->output()->writeln("<error>❌ Failed to configure {$content_type}.{$view_mode}: " . $e->getMessage() . "</error>");
      }
    }

    $this->output()->writeln('');
    $this->output()->writeln('🎉 <success>Content type display configuration complete!</success>');
  }

  /**
   * Display configuration results.
   */
  protected function displayResults(array $results): void {
    $total_success = 0;
    $total_error = 0;

    foreach ($results as $content_type => $view_modes) {
      $this->output()->writeln("📦 <comment>{$content_type}:</comment>");

      foreach ($view_modes as $view_mode => $status) {
        $icon = $status === 'success' ? '✅' : '❌';
        $style = $status === 'success' ? 'info' : 'error';
        $this->output()->writeln("  {$icon} <{$style}>{$view_mode}: {$status}</{$style}>");

        if ($status === 'success') {
          $total_success++;
        } else {
          $total_error++;
        }
      }
      $this->output()->writeln('');
    }

    $this->output()->writeln("<info>📊 Summary: {$total_success} successful, {$total_error} errors</info>");
  }

  /**
   * Display validation results.
   */
  protected function displayValidation(array $validation): void {
    $total_displays = 0;
    $total_components = 0;

    foreach ($validation as $content_type => $view_modes) {
      foreach ($view_modes as $view_mode => $info) {
        if ($info['exists'] && $info['status']) {
          $total_displays++;
          $total_components += $info['components'];
        }
      }
    }

    $this->output()->writeln("<success>✅ {$total_displays} displays configured with {$total_components} total field components</success>");

    // Check for any missing displays
    $missing = [];
    foreach ($validation as $content_type => $view_modes) {
      foreach ($view_modes as $view_mode => $info) {
        if (!$info['exists'] || !$info['status']) {
          $missing[] = "{$content_type}.{$view_mode}";
        }
      }
    }

    if (!empty($missing)) {
      $this->output()->writeln('<comment>⚠️  Missing or disabled displays: ' . implode(', ', $missing) . '</comment>');
    }
  }

  /**
   * Display post-setup information.
   */
  protected function displayPostSetupInfo(): void {
    $this->output()->writeln('<comment>📖 What was configured:</comment>');
    $this->output()->writeln('  • <info>Default view mode:</info> Complete field layout with proper ordering');
    $this->output()->writeln('  • <info>Teaser view mode:</info> Summary displays for listings and previews');
    $this->output()->writeln('  • <info>Full view mode:</info> Detailed content display');
    $this->output()->writeln('  • <info>Search result mode:</info> Optimized for search listings');
    $this->output()->writeln('');

    $this->output()->writeln('<comment>🛠️  Manual customization options:</comment>');
    $this->output()->writeln('  • Field reordering and grouping');
    $this->output()->writeln('  • Custom display formatters');
    $this->output()->writeln('  • Responsive display settings');
    $this->output()->writeln('  • Field-specific styling and layouts');
    $this->output()->writeln('  • Advanced view mode configurations');
    $this->output()->writeln('');

    $this->output()->writeln('<comment>📍 Next steps:</comment>');
    $this->output()->writeln('  1. Test content display on your site');
    $this->output()->writeln('  2. Customize displays as needed via UI: <info>Structure > Content types > [Type] > Manage display</info>');
    $this->output()->writeln('  3. Run migration to see displays in action');
    $this->output()->writeln('');
  }

}