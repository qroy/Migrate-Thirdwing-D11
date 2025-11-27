<?php

/**
 * @file
 * Webform elements process plugin for D6 to D11 migration.
 * File: thirdwing_migrate/src/Plugin/migrate/process/WebformElements.php
 */

namespace Drupal\thirdwing_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to convert D6 webform components to D11 elements.
 *
 * @MigrateProcessPlugin(
 *   id = "webform_elements"
 * )
 */
class WebformElements extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $components = $row->getSourceProperty('components');
    
    if (empty($components)) {
      return '';
    }

    $elements = [];

    foreach ($components as $component) {
      $element_key = $component->form_key ?: 'component_' . $component->cid;
      
      // Convert D6 component to D11 element
      $element = $this->convertComponent($component);
      
      if ($element) {
        $elements[$element_key] = $element;
      }
    }

    // Convert to YAML format for webform elements
    return $this->arrayToYaml($elements);
  }

  /**
   * Convert D6 webform component to D11 webform element.
   */
  protected function convertComponent($component) {
    $extra = unserialize($component->extra);
    
    $element = [
      '#type' => $this->mapComponentType($component->type),
      '#title' => $component->name,
      '#required' => (bool) $component->mandatory,
      '#weight' => (int) $component->weight,
    ];

    // Add default value if present
    if (!empty($component->value)) {
      $element['#default_value'] = $component->value;
    }

    // Add description if present
    if (!empty($extra['description'])) {
      $element['#description'] = $extra['description'];
    }

    // Handle specific component types
    switch ($component->type) {
      case 'select':
      case 'radios':
      case 'checkboxes':
        if (!empty($extra['items'])) {
          $element['#options'] = $this->parseSelectOptions($extra['items']);
        }
        break;

      case 'textfield':
        if (!empty($extra['width'])) {
          $element['#size'] = (int) $extra['width'];
        }
        if (!empty($extra['maxlength'])) {
          $element['#maxlength'] = (int) $extra['maxlength'];
        }
        break;

      case 'textarea':
        if (!empty($extra['cols'])) {
          $element['#cols'] = (int) $extra['cols'];
        }
        if (!empty($extra['rows'])) {
          $element['#rows'] = (int) $extra['rows'];
        }
        break;

      case 'email':
        $element['#type'] = 'email';
        break;

      case 'file':
        $element['#type'] = 'managed_file';
        if (!empty($extra['filtering']['types'])) {
          $element['#file_extensions'] = implode(' ', $extra['filtering']['types']);
        }
        break;
    }

    return $element;
  }

  /**
   * Map D6 component types to D11 element types.
   */
  protected function mapComponentType($type) {
    $mapping = [
      'textfield' => 'textfield',
      'textarea' => 'textarea', 
      'select' => 'select',
      'radios' => 'radios',
      'checkboxes' => 'checkboxes',
      'email' => 'email',
      'file' => 'managed_file',
      'hidden' => 'hidden',
      'markup' => 'processed_text',
      'pagebreak' => 'webform_wizard_page',
      'fieldset' => 'fieldset',
      'date' => 'date',
      'time' => 'time',
      'number' => 'number',
    ];

    return $mapping[$type] ?? 'textfield';
  }

  /**
   * Parse select options from D6 format.
   */
  protected function parseSelectOptions($items) {
    $options = [];
    $lines = explode("\n", $items);
    
    foreach ($lines as $line) {
      $line = trim($line);
      if (empty($line)) {
        continue;
      }
      
      if (strpos($line, '|') !== FALSE) {
        list($key, $value) = explode('|', $line, 2);
        $options[trim($key)] = trim($value);
      } else {
        $options[trim($line)] = trim($line);
      }
    }
    
    return $options;
  }

  /**
   * Convert array to YAML format.
   */
  protected function arrayToYaml($array) {
    return \Drupal\Component\Serialization\Yaml::encode($array);
  }

}