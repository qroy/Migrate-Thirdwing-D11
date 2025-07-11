<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 incremental user source plugin.
 *
 * Supports timestamp-based filtering for incremental user migration.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_incremental_user",
 *   source_module = "user"
 * )
 */
class D6IncrementalUser extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('users', 'u')
      ->fields('u', [
        'uid', 'name', 'pass', 'mail', 'mode', 'sort', 'threshold',
        'theme', 'signature', 'signature_format', 'created', 'access',
        'login', 'status', 'timezone', 'language', 'picture', 'init', 'data'
      ])
      ->condition('uid', 0, '>'); // Exclude anonymous user

    // Apply incremental filtering
    $this->applyIncrementalFilters($query);

    return $query;
  }

  /**
   * Apply incremental filtering to the query.
   */
  protected function applyIncrementalFilters($query) {
    $config = $this->configuration;

    // Filter by login date (users who logged in since date)
    if (!empty($config['since_login'])) {
      $since_timestamp = $this->parseDateFilter($config['since_login']);
      if ($since_timestamp) {
        $query->condition('u.login', $since_timestamp, '>=');
      }
    }

    // Filter by access date (users who accessed site since date)
    if (!empty($config['since_access'])) {
      $since_timestamp = $this->parseDateFilter($config['since_access']);
      if ($since_timestamp) {
        $query->condition('u.access', $since_timestamp, '>=');
      }
    }

    // Filter by created date (new user registrations)
    if (!empty($config['since_created'])) {
      $since_timestamp = $this->parseDateFilter($config['since_created']);
      if ($since_timestamp) {
        $query->condition('u.created', $since_timestamp, '>=');
      }
    }

    // Filter by date range
    if (!empty($config['date_range'])) {
      $range = $config['date_range'];
      if (!empty($range['start'])) {
        $start_timestamp = $this->parseDateFilter($range['start']);
        if ($start_timestamp) {
          $query->condition('u.access', $start_timestamp, '>=');
        }
      }
      if (!empty($range['end'])) {
        $end_timestamp = $this->parseDateFilter($range['end']);
        if ($end_timestamp) {
          $query->condition('u.access', $end_timestamp, '<=');
        }
      }
    }

    // Filter by specific user IDs
    if (!empty($config['user_ids'])) {
      $uids = is_array($config['user_ids']) ? $config['user_ids'] : [$config['user_ids']];
      $query->condition('u.uid', $uids, 'IN');
    }

    // Filter by user status
    if (isset($config['status'])) {
      $query->condition('u.status', $config['status']);
    }

    // Only active users by default
    if (empty($config['include_blocked'])) {
      $query->condition('u.status', 1);
    }
  }

  /**
   * Parse date filter string into timestamp.
   */
  protected function parseDateFilter($date_filter) {
    if (empty($date_filter)) {
      return NULL;
    }

    // Handle Unix timestamps
    if (is_numeric($date_filter)) {
      return (int) $date_filter;
    }

    // Handle relative date shortcuts
    $shortcuts = [
      'yesterday' => '-1 day',
      'last-week' => '-1 week',
      'last-month' => '-1 month',
      'last-year' => '-1 year',
      'today' => '0 days',
    ];

    if (isset($shortcuts[$date_filter])) {
      $date_filter = $shortcuts[$date_filter];
    }

    // Try to parse as strtotime
    $timestamp = strtotime($date_filter);
    if ($timestamp === FALSE) {
      \Drupal::logger('thirdwing_migrate')->warning('Invalid date filter: @filter', ['@filter' => $date_filter]);
      return NULL;
    }

    return $timestamp;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email address'),
      'mode' => $this->t('Comment display mode'),
      'sort' => $this->t('Comment sort order'),
      'threshold' => $this->t('Comment threshold'),
      'theme' => $this->t('Default theme'),
      'signature' => $this->t('Signature'),
      'signature_format' => $this->t('Signature format'),
      'created' => $this->t('Registration timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'timezone' => $this->t('Timezone'),
      'language' => $this->t('Language'),
      'picture' => $this->t('Picture'),
      'init' => $this->t('Initial email address'),
      'data' => $this->t('User data'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $uid = $row->getSourceProperty('uid');

    // Skip user 0 (anonymous)
    if ($uid == 0) {
      return FALSE;
    }

    // Add user roles
    $this->addUserRoles($row, $uid);

    // Add profile fields
    $this->addProfileFields($row, $uid);

    // Add user picture file info
    $this->addUserPicture($row);

    return parent::prepareRow($row);
  }

  /**
   * Add user roles to the row.
   */
  protected function addUserRoles(Row $row, $uid) {
    $role_query = $this->select('users_roles', 'ur')
      ->fields('ur', ['rid'])
      ->condition('ur.uid', $uid);
    
    $rids = $role_query->execute()->fetchCol();
    
    if (!empty($rids)) {
      // Get role names
      $role_name_query = $this->select('role', 'r')
        ->fields('r', ['rid', 'name'])
        ->condition('r.rid', $rids, 'IN');
      
      $roles = $role_name_query->execute()->fetchAllKeyed();
      $row->setSourceProperty('roles', $roles);
      $row->setSourceProperty('role_ids', $rids);
    }
  }

  /**
   * Add profile fields to the row.
   */
  protected function addProfileFields(Row $row, $uid) {
    // Check if profile module table exists
    if ($this->getDatabase()->schema()->tableExists('profile_values')) {
      $profile_query = $this->select('profile_values', 'pv')
        ->fields('pv', ['fid', 'value'])
        ->condition('pv.uid', $uid);
      
      $profile_query->leftJoin('profile_fields', 'pf', 'pv.fid = pf.fid');
      $profile_query->addField('pf', 'name');
      $profile_query->addField('pf', 'type');
      
      $profile_data = $profile_query->execute()->fetchAll();
      
      foreach ($profile_data as $field) {
        $field_name = 'field_' . $field->name;
        if ($field->type == 'date') {
          // Handle date fields
          $row->setSourceProperty($field_name . '_value', $field->value);
        } else {
          $row->setSourceProperty($field_name . '_value', $field->value);
        }
      }
    }

    // Also check for CCK profile fields if Content Profile module was used
    $this->addContentProfileFields($row, $uid);
  }

  /**
   * Add Content Profile fields to the row.
   */
  protected function addContentProfileFields(Row $row, $uid) {
    // Check if there are any profile content types
    $profile_types = ['profile', 'user_profile', 'member_profile'];
    
    foreach ($profile_types as $type) {
      $table_name = 'content_type_' . $type;
      
      if ($this->getDatabase()->schema()->tableExists($table_name)) {
        // Find nodes of this profile type for this user
        $profile_query = $this->select('node', 'n')
          ->fields('n', ['nid', 'vid'])
          ->condition('n.type', $type)
          ->condition('n.uid', $uid)
          ->condition('n.status', 1);
        
        $profile_nodes = $profile_query->execute()->fetchAll();
        
        foreach ($profile_nodes as $profile_node) {
          // Get profile field data
          $content_query = $this->select($table_name, 'ct')
            ->condition('ct.nid', $profile_node->nid)
            ->condition('ct.vid', $profile_node->vid);
          
          // Get all field columns
          $columns = $this->getDatabase()->schema()->getFieldNames($table_name);
          $field_columns = array_filter($columns, function($col) {
            return strpos($col, 'field_') === 0;
          });
          
          if (!empty($field_columns)) {
            $content_query->fields('ct', $field_columns);
            $content_data = $content_query->execute()->fetchAssoc();
            
            if ($content_data) {
              foreach ($content_data as $field_name => $value) {
                $row->setSourceProperty($field_name, $value);
              }
            }
          }
        }
      }
    }
  }

  /**
   * Add user picture information.
   */
  protected function addUserPicture(Row $row) {
    $picture_fid = $row->getSourceProperty('picture');
    
    if (!empty($picture_fid)) {
      $file_query = $this->select('files', 'f')
        ->fields('f', ['fid', 'filename', 'filepath', 'filemime', 'filesize'])
        ->condition('f.fid', $picture_fid);
      
      $file_data = $file_query->execute()->fetchAssoc();
      
      if ($file_data) {
        $row->setSourceProperty('picture_file', $file_data);
      }
    }
  }

}