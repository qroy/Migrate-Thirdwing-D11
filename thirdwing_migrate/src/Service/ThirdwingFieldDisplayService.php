<?php

namespace Drupal\thirdwing_migrate\Service;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\field\Entity\FieldConfig;

/**
 * Service for configuring hybrid field displays for Thirdwing content types.
 * 
 * Provides automated default display configurations with manual customization options.
 * Part of the hybrid approach: automated defaults + manual customization flexibility.
 */
class ThirdwingFieldDisplayService {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The entity field manager.
   */
  protected EntityFieldManagerInterface $entityFieldManager;

  /**
   * The entity display repository.
   */
  protected EntityDisplayRepositoryInterface $displayRepository;

  /**
   * The logger factory.
   */
  protected LoggerChannelFactoryInterface $loggerFactory;

  /**
   * Constructor.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityDisplayRepositoryInterface $display_repository,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->displayRepository = $display_repository;
    $this->loggerFactory = $logger_factory;
  }

  /**
   * Configure default displays for all Thirdwing content types.
   * 
   * @return array
   *   Array of results keyed by content type and view mode.
   */
  public function configureAllDisplays(): array {
    $results = [];
    
    $content_types = $this->getThirdwingContentTypes();
    $view_modes = $this->getConfiguredViewModes();
    
    foreach ($content_types as $content_type) {
      $results[$content_type] = [];
      foreach ($view_modes as $view_mode) {
        try {
          $this->configureContentTypeDisplay($content_type, $view_mode);
          $results[$content_type][$view_mode] = 'success';
          
          $this->loggerFactory->get('thirdwing_migrate')->info(
            'Configured display for @type.@mode',
            ['@type' => $content_type, '@mode' => $view_mode]
          );
        } catch (\Exception $e) {
          $results[$content_type][$view_mode] = 'error: ' . $e->getMessage();
          
          $this->loggerFactory->get('thirdwing_migrate')->error(
            'Failed to configure display for @type.@mode: @error',
            [
              '@type' => $content_type,
              '@mode' => $view_mode,
              '@error' => $e->getMessage()
            ]
          );
        }
      }
    }
    
    return $results;
  }

  /**
   * Configure display for a specific content type and view mode.
   * 
   * @param string $content_type
   *   The content type machine name.
   * @param string $view_mode
   *   The view mode machine name.
   */
  public function configureContentTypeDisplay(string $content_type, string $view_mode): void {
    $display_id = "node.{$content_type}.{$view_mode}";
    
    // Load or create the display
    $display = $this->entityTypeManager
      ->getStorage('entity_view_display')
      ->load($display_id);
    
    if (!$display) {
      $display = $this->entityTypeManager
        ->getStorage('entity_view_display')
        ->create([
          'targetEntityType' => 'node',
          'bundle' => $content_type,
          'mode' => $view_mode,
          'status' => TRUE,
        ]);
    }

    // Get field configuration for this content type
    $field_config = $this->getFieldConfiguration($content_type, $view_mode);
    
    // Configure each field
    foreach ($field_config as $field_name => $config) {
      if ($config['visible']) {
        $display->setComponent($field_name, $config['settings']);
      } else {
        $display->removeComponent($field_name);
      }
    }
    
    // Save the display
    $display->save();
  }

  /**
   * Get field configuration for a content type and view mode.
   * 
   * @param string $content_type
   *   The content type machine name.
   * @param string $view_mode
   *   The view mode machine name.
   * 
   * @return array
   *   Field configuration array.
   */
  protected function getFieldConfiguration(string $content_type, string $view_mode): array {
    $config = [];
    
    // Get all fields for this content type
    $fields = $this->entityFieldManager->getFieldDefinitions('node', $content_type);
    
    foreach ($fields as $field_name => $field_definition) {
      // Skip base fields that are handled elsewhere
      if (in_array($field_name, ['nid', 'uuid', 'vid', 'type', 'langcode', 'revision_timestamp', 'revision_uid', 'revision_log', 'uid', 'status', 'created', 'changed', 'promote', 'sticky', 'default_langcode', 'revision_default', 'revision_translation_affected', 'path', 'menu_link', 'content_translation_source', 'content_translation_outdated', 'content_translation_uid', 'content_translation_created'])) {
        continue;
      }
      
      $field_type = $field_definition->getType();
      $config[$field_name] = $this->getFieldDisplayConfig($field_name, $field_type, $content_type, $view_mode);
    }
    
    return $config;
  }

  /**
   * Get display configuration for a specific field.
   * 
   * @param string $field_name
   *   The field name.
   * @param string $field_type
   *   The field type.
   * @param string $content_type
   *   The content type.
   * @param string $view_mode
   *   The view mode.
   * 
   * @return array
   *   Field display configuration.
   */
  protected function getFieldDisplayConfig(string $field_name, string $field_type, string $content_type, string $view_mode): array {
    // Default configuration
    $config = [
      'visible' => TRUE,
      'settings' => [
        'weight' => $this->getFieldWeight($field_name, $content_type),
        'label' => $this->getFieldLabel($field_name, $view_mode),
        'type' => $this->getFieldFormatter($field_type, $view_mode),
        'settings' => $this->getFormatterSettings($field_type, $field_name, $view_mode),
        'third_party_settings' => [],
      ],
    ];

    // Special handling for specific fields and view modes
    $config = $this->applySpecialFieldHandling($field_name, $field_type, $content_type, $view_mode, $config);
    
    return $config;
  }

  /**
   * Get field weight for proper ordering.
   * 
   * @param string $field_name
   *   The field name.
   * @param string $content_type
   *   The content type.
   * 
   * @return int
   *   The field weight.
   */
  protected function getFieldWeight(string $field_name, string $content_type): int {
    // Define field order by priority
    $priority_fields = [
      'title' => -10,
      'body' => 0,
      'field_datum' => 10,
      'field_afbeeldingen' => 20,
      'field_video' => 25,
      'field_files' => 30,
      'field_repertoire' => 40,
      'field_ref_activiteit' => 50,
      'field_audio_uitvoerende' => 60,
      'field_audio_type' => 70,
      'field_l_adres' => 80,
      'field_l_plaats' => 90,
      'field_l_postcode' => 100,
      'field_l_routelink' => 110,
      'field_view' => 120,
    ];
    
    return $priority_fields[$field_name] ?? 1000;
  }

  /**
   * Get field label setting for view mode.
   * 
   * @param string $field_name
   *   The field name.
   * @param string $view_mode
   *   The view mode.
   * 
   * @return string
   *   The label setting.
   */
  protected function getFieldLabel(string $field_name, string $view_mode): string {
    // For teaser and search, use inline or hidden labels
    if (in_array($view_mode, ['teaser', 'search_result'])) {
      // Hide labels for media fields in compact views
      if (in_array($field_name, ['field_afbeeldingen', 'field_video', 'field_files'])) {
        return 'hidden';
      }
      return 'inline';
    }
    
    // For full view, use above labels
    return 'above';
  }

  /**
   * Get appropriate formatter for field type and view mode.
   * 
   * @param string $field_type
   *   The field type.
   * @param string $view_mode
   *   The view mode.
   * 
   * @return string
   *   The formatter name.
   */
  protected function getFieldFormatter(string $field_type, string $view_mode): string {
    $formatters = [
      'text_long' => $view_mode === 'teaser' ? 'text_trimmed' : 'text_default',
      'text_with_summary' => $view_mode === 'teaser' ? 'text_summary_or_trimmed' : 'text_default',
      'string' => 'string',
      'string_long' => $view_mode === 'teaser' ? 'string_trimmed' : 'string',
      'datetime' => 'datetime_default',
      'entity_reference' => $this->getEntityReferenceFormatter($view_mode),
      'list_string' => 'list_default',
      'link' => 'link',
      'file' => 'file_default',
      'image' => $view_mode === 'teaser' ? 'image' : 'image',
    ];
    
    return $formatters[$field_type] ?? 'string';
  }

  /**
   * Get entity reference formatter based on view mode.
   * 
   * @param string $view_mode
   *   The view mode.
   * 
   * @return string
   *   The formatter name.
   */
  protected function getEntityReferenceFormatter(string $view_mode): string {
    switch ($view_mode) {
      case 'teaser':
      case 'search_result':
        return 'entity_reference_label';
      
      case 'full':
      default:
        return 'entity_reference_entity_view';
    }
  }

  /**
   * Get formatter settings for field type.
   * 
   * @param string $field_type
   *   The field type.
   * @param string $field_name
   *   The field name.
   * @param string $view_mode
   *   The view mode.
   * 
   * @return array
   *   Formatter settings.
   */
  protected function getFormatterSettings(string $field_type, string $field_name, string $view_mode): array {
    $settings = [];
    
    switch ($field_type) {
      case 'text_long':
      case 'text_with_summary':
        if ($view_mode === 'teaser') {
          $settings['trim_length'] = 200;
        }
        break;
        
      case 'entity_reference':
        if (strpos($field_name, 'field_afbeeldingen') !== FALSE) {
          // Image media settings
          if ($view_mode === 'teaser') {
            $settings['view_mode'] = 'thumbnail';
          } else {
            $settings['view_mode'] = 'default';
          }
        } elseif (strpos($field_name, 'field_files') !== FALSE) {
          // File media settings
          $settings['view_mode'] = 'compact';
        } else {
          // Node reference settings
          if ($view_mode === 'teaser') {
            $settings['link'] = TRUE;
          } else {
            $settings['view_mode'] = 'teaser';
          }
        }
        break;
        
      case 'datetime':
        $settings['format_type'] = 'medium';
        break;
        
      case 'image':
        if ($view_mode === 'teaser') {
          $settings['image_style'] = 'thumbnail';
        } else {
          $settings['image_style'] = 'large';
        }
        break;
    }
    
    return $settings;
  }

  /**
   * Apply special handling for specific fields.
   * 
   * @param string $field_name
   *   The field name.
   * @param string $field_type
   *   The field type.
   * @param string $content_type
   *   The content type.
   * @param string $view_mode
   *   The view mode.
   * @param array $config
   *   The current configuration.
   * 
   * @return array
   *   Updated configuration.
   */
  protected function applySpecialFieldHandling(string $field_name, string $field_type, string $content_type, string $view_mode, array $config): array {
    // Hide certain fields in teaser view
    if ($view_mode === 'teaser') {
      $hidden_in_teaser = [
        'field_files',
        'field_view',
        'field_l_routelink',
        'field_partij_band',
        'field_partij_koor_l',
        'field_partij_tekst',
      ];
      
      if (in_array($field_name, $hidden_in_teaser)) {
        $config['visible'] = FALSE;
      }
    }
    
    // Hide non-essential fields in search results
    if ($view_mode === 'search_result') {
      $visible_in_search = [
        'body',
        'field_datum',
        'field_afbeeldingen',
      ];
      
      if (!in_array($field_name, $visible_in_search)) {
        $config['visible'] = FALSE;
      }
    }
    
    // Content type specific handling
    switch ($content_type) {
      case 'locatie':
        // For locations, prioritize address fields
        if (in_array($field_name, ['field_l_adres', 'field_l_plaats', 'field_l_postcode'])) {
          $config['settings']['label'] = 'inline';
        }
        break;
        
      case 'repertoire':
        // For repertoire, emphasize musical details
        if ($field_name === 'field_audio_uitvoerende') {
          $config['settings']['label'] = 'inline';
        }
        break;
        
      case 'activiteit':
        // For activities, prioritize date and location
        if ($field_name === 'field_datum') {
          $config['settings']['weight'] = -5;
        }
        break;
    }
    
    return $config;
  }

  /**
   * Get all Thirdwing content types.
   * 
   * @return array
   *   Array of content type machine names.
   */
  protected function getThirdwingContentTypes(): array {
    return [
      'activiteit',
      'foto',
      'locatie',
      'nieuws',
      'pagina',
      'programma',
      'repertoire',
      'persoon',
      'vriend',
    ];
  }

  /**
   * Get configured view modes.
   * 
   * @return array
   *   Array of view mode machine names.
   */
  protected function getConfiguredViewModes(): array {
    return [
      'default',
      'teaser',
      'full',
      'search_result',
    ];
  }

  /**
   * Validate that all displays are properly configured.
   * 
   * @return array
   *   Validation results.
   */
  public function validateDisplays(): array {
    $results = [];
    $content_types = $this->getThirdwingContentTypes();
    $view_modes = $this->getConfiguredViewModes();
    
    foreach ($content_types as $content_type) {
      $results[$content_type] = [];
      foreach ($view_modes as $view_mode) {
        $display_id = "node.{$content_type}.{$view_mode}";
        $display = $this->entityTypeManager
          ->getStorage('entity_view_display')
          ->load($display_id);
        
        $results[$content_type][$view_mode] = [
          'exists' => $display !== NULL,
          'status' => $display ? $display->status() : FALSE,
          'components' => $display ? count($display->getComponents()) : 0,
        ];
      }
    }
    
    return $results;
  }

}