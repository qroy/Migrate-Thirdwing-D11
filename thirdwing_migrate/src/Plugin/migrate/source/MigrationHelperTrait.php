<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Helper trait for migration source plugins.
 */
trait MigrationHelperTrait {

  /**
   * Clean up null/empty values to prevent Html::escape() errors.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param array $fields
   *   Array of field names to clean.
   */
  protected function cleanNullValues(Row $row, array $fields) {
    foreach ($fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null) {
        $row->setSourceProperty($field, '');
      }
    }
  }

  /**
   * Clean and validate numeric fields.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param array $fields
   *   Array of field names that should be numeric.
   */
  protected function cleanNumericFields(Row $row, array $fields) {
    foreach ($fields as $field) {
      $value = $row->getSourceProperty($field);
      
      if ($value === null || $value === '') {
        $row->setSourceProperty($field, 0);
      } elseif (!is_numeric($value)) {
        // Try to extract numbers from string
        $numeric_value = preg_replace('/[^0-9.-]/', '', $value);
        if (is_numeric($numeric_value)) {
          $row->setSourceProperty($field, (int) $numeric_value);
        } else {
          $row->setSourceProperty($field, 0);
        }
      } else {
        $row->setSourceProperty($field, (int) $value);
      }
    }
  }

  /**
   * Clean and validate ID fields (should be positive integers).
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param array $fields
   *   Array of field names that should be positive integers.
   */
  protected function cleanIdFields(Row $row, array $fields) {
    foreach ($fields as $field) {
      $value = $row->getSourceProperty($field);
      
      if ($value === null || $value === '' || !is_numeric($value) || $value <= 0) {
        $row->setSourceProperty($field, NULL);
      } else {
        $row->setSourceProperty($field, (int) $value);
      }
    }
  }

  /**
   * Clean all string fields in a row.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   */
  protected function cleanAllStringFields(Row $row) {
    $source = $row->getSource();
    
    foreach ($source as $key => $value) {
      if ($value === null && $this->isStringField($key)) {
        $row->setSourceProperty($key, '');
      }
    }
  }

  /**
   * Check if a field is likely to be a string field.
   *
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if the field is likely a string field.
   */
  protected function isStringField($field_name) {
    // Common string field patterns
    $string_patterns = [
      'name', 'title', 'description', 'body', 'summary', 'help',
      'mail', 'filename', 'filepath', 'url', 'link', 'text',
      '_value', '_title', '_description', '_name', '_mail'
    ];

    foreach ($string_patterns as $pattern) {
      if (strpos($field_name, $pattern) !== FALSE) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Check if a field is likely to be a numeric field.
   *
   * @param string $field_name
   *   The field name.
   *
   * @return bool
   *   TRUE if the field is likely a numeric field.
   */
  protected function isNumericField($field_name) {
    $numeric_patterns = [
      'nid', 'vid', 'tid', 'uid', 'fid', 'gid', 'weight', 'status',
      'created', 'changed', 'access', 'login', 'timestamp', 'filesize',
      '_id', '_count', '_number', '_year', '_delta', 'hierarchy',
      'multiple', 'required', 'relations', 'tags'
    ];

    foreach ($numeric_patterns as $pattern) {
      if (strpos($field_name, $pattern) !== FALSE) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Transform boolean-like values to proper boolean.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param string $field
   *   The field name.
   * @param array $true_values
   *   Values that should be considered TRUE.
   */
  protected function transformBooleanField(Row $row, $field, array $true_values = ['1', 1, 'yes', 'Yes', 'ja', 'Ja']) {
    $value = $row->getSourceProperty($field);
    if ($value !== null) {
      $row->setSourceProperty($field, in_array($value, $true_values, TRUE) ? 1 : 0);
    }
  }

  /**
   * Transform empty values to NULL for proper database storage.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param array $fields
   *   Array of field names to process.
   */
  protected function transformEmptyToNull(Row $row, array $fields) {
    foreach ($fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === '' || $value === '0') {
        $row->setSourceProperty($field, NULL);
      }
    }
  }

  /**
   * Clean all fields in a row automatically.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   */
  protected function cleanAllFields(Row $row) {
    $source = $row->getSource();
    $string_fields = [];
    $numeric_fields = [];
    $id_fields = [];
    
    foreach ($source as $key => $value) {
      if ($this->isStringField($key)) {
        $string_fields[] = $key;
      } elseif ($this->isNumericField($key)) {
        if (strpos($key, 'id') !== FALSE || strpos($key, 'nid') !== FALSE || 
            strpos($key, 'vid') !== FALSE || strpos($key, 'tid') !== FALSE || 
            strpos($key, 'uid') !== FALSE || strpos($key, 'fid') !== FALSE) {
          $id_fields[] = $key;
        } else {
          $numeric_fields[] = $key;
        }
      }
    }
    
    $this->cleanNullValues($row, $string_fields);
    $this->cleanNumericFields($row, $numeric_fields);
    $this->cleanIdFields($row, $id_fields);
  }
}