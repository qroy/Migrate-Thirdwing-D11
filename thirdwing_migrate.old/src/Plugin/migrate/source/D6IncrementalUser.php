<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 incremental user migration - CORRECTED WITH DUTCH LABELS.
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
        'uid', 'name', 'pass', 'mail', 'created', 'access', 'login', 'status', 'picture'
      ])
      ->condition('u.uid', 0, '>'); // Skip anonymous user

    // Add incremental filtering based on configuration
    $config = $this->configuration;
    
    if (!empty($config['since_access'])) {
      $query->condition('u.access', $config['since_access'], '>=');
    }
    
    if (!empty($config['since_created'])) {
      $query->condition('u.created', $config['since_created'], '>=');
    }
    
    if (!empty($config['date_range']['start']) && !empty($config['date_range']['end'])) {
      $query->condition('u.access', [$config['date_range']['start'], $config['date_range']['end']], 'BETWEEN');
    }
    
    if (empty($config['include_blocked'])) {
      $query->condition('u.status', 1);
    }

    return $query->orderBy('u.uid');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'pass' => $this->t('Password'),
      'mail' => $this->t('Email'),
      'created' => $this->t('Created timestamp'),
      'access' => $this->t('Last access timestamp'),
      'login' => $this->t('Last login timestamp'),
      'status' => $this->t('Status'),
      'picture' => $this->t('Picture file ID'),
      
      // User roles
      'roles' => $this->t('User roles (name => rid)'),
      'role_ids' => $this->t('User role IDs'),
      'user_roles' => $this->t('User roles array'),
      
      // CORRECTED: Profile fields with Dutch descriptions matching D6 labels
      'field_voornaam_value' => $this->t('Voornaam'),  // CORRECTED: Dutch, not 'First name'
      'field_achternaam_value' => $this->t('Achternaam'),  // CORRECTED: Dutch, not 'Last name'
      'field_achternaam_voorvoegsel_value' => $this->t('Achternaam voorvoegsel'),  // CORRECTED: Dutch, not 'Name prefix'
      'field_geboortedatum_value' => $this->t('Geboortedatum'),  // CORRECTED: Dutch, not 'Birth date'
      'field_geslacht_value' => $this->t('Geslacht'),  // CORRECTED: Dutch, not 'Gender'
      'field_karrijder_value' => $this->t('Karrijder'),  // CORRECTED: Dutch, not 'Car driver'
      'field_lidsinds_value' => $this->t('Lid Sinds'),  // CORRECTED: Dutch, not 'Member since'
      'field_uitkoor_value' => $this->t('Uit koor per'),  // CORRECTED: Dutch, not 'Left choir'
      'field_adres_value' => $this->t('Adres'),  // CORRECTED: Dutch, not 'Address'
      'field_postcode_value' => $this->t('Postcode'),  // CORRECTED: Dutch, not 'Postal code'
      'field_woonplaats_value' => $this->t('Woonplaats'),  // CORRECTED: Dutch, not 'City'
      'field_telefoon_value' => $this->t('Telefoon'),  // CORRECTED: Dutch, not 'Phone'
      'field_mobiel_value' => $this->t('Mobiel'),  // CORRECTED: Dutch, not 'Mobile'
      'field_sleepgroep_1_value' => $this->t('Sleepgroep'),  // CORRECTED: Dutch, not 'Transport group'
      'field_koor_value' => $this->t('Koorfunctie'),  // CORRECTED: Dutch, not 'Choir'
      'field_notes_value' => $this->t('Notities'),  // CORRECTED: Dutch, not 'Notes'
      'field_notes_format' => $this->t('Notities format'),  // CORRECTED: Dutch, not 'Notes format'
      'field_emailbewaking_value' => $this->t('Email origineel'),  // CORRECTED: Dutch, not 'Email monitoring'
      
      // CORRECTED: Committee function fields with Dutch descriptions
      'field_functie_bestuur_value' => $this->t('Functie Bestuur'),  // CORRECTED: Dutch, not 'Board function'
      'field_functie_mc_value' => $this->t('Functie Muziekcommissie'),  // CORRECTED: Dutch, not 'Music committee function'
      'field_functie_concert_value' => $this->t('Functie Commissie Concerten'),  // CORRECTED: Dutch, not 'Concert function'
      'field_functie_feest_value' => $this->t('Functie Feestcommissie'),  // CORRECTED: Dutch, not 'Party function'
      'field_functie_regie_value' => $this->t('Functie Commissie Koorregie'),  // CORRECTED: Dutch, not 'Direction function'
      'field_functie_ir_value' => $this->t('Functie Commissie Interne Relaties'),  // CORRECTED: Dutch, not 'Internal relations function'
      'field_functie_pr_value' => $this->t('Functie Commissie PR'),  // CORRECTED: Dutch, not 'Public relations function'
      'field_functie_tec_value' => $this->t('Functie Technische Commissie'),  // CORRECTED: Dutch, not 'Technical function'
      'field_positie_value' => $this->t('Positie'),  // CORRECTED: Dutch, not 'Position'
      'field_functie_lw_value' => $this->t('Functie ledenwerf'),  // CORRECTED: Dutch, not 'Member recruitment function'
      'field_functie_fl_value' => $this->t('Functie Faciliteiten'),  // CORRECTED: Dutch, not 'Facilities function'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'u',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $uid = $row->getSourceProperty('uid');

    // Add user roles
    $this->addUserRoles($row, $uid);
    
    // Add Content Profile fields from content_type_profiel
    $this->addContentProfileFields($row, $uid);
    
    // CORRECTED: Add shared field data for woonplaats (same fix as main migration)
    $this->addSharedFieldData($row, $uid);
    
    // Add user picture
    $this->addUserPicture($row);

    return TRUE;
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
      $row->setSourceProperty('user_roles', $rids);
    }
  }

  /**
   * Add Content Profile fields to the row.
   */
  protected function addContentProfileFields(Row $row, $uid) {
    // Check for 'profiel' content type (profile content type)
    $profile_types = ['profiel'];
    
    foreach ($profile_types as $type) {
      $table_name = 'content_type_' . $type;
      
      if ($this->getDatabase()->schema()->tableExists($table_name)) {
        // Find the profile node for this user
        $profile_query = $this->select('node', 'n')
          ->fields('n', ['nid', 'vid'])
          ->condition('n.type', $type)
          ->condition('n.uid', $uid)
          ->condition('n.status', 1)
          ->range(0, 1); // Only get the first profile node
        
        $profile_node = $profile_query->execute()->fetchAssoc();
        
        if ($profile_node) {
          // Get profile field data from content_type_profiel table
          $content_query = $this->select($table_name, 'ct')
            ->condition('ct.nid', $profile_node['nid'])
            ->condition('ct.vid', $profile_node['vid']);
          
          // Get all fields from the content_type_profiel table
          $content_query->fields('ct');
          $content_data = $content_query->execute()->fetchAssoc();
          
          if ($content_data) {
            // Filter and set only the field_ columns
            foreach ($content_data as $field_name => $value) {
              if (strpos($field_name, 'field_') === 0) {
                // Set the field value on the row
                $row->setSourceProperty($field_name, $value);
              }
            }
          }
        }
      }
    }
  }

  /**
   * CORRECTED: Add shared field data for fields like woonplaats.
   * (Same implementation as main user migration)
   */
  protected function addSharedFieldData(Row $row, $uid) {
    // Get woonplaats from shared field table
    $woonplaats_table = 'content_field_woonplaats';
    
    if ($this->getDatabase()->schema()->tableExists($woonplaats_table)) {
      try {
        // For shared fields attached to users, we need to find the profile node first
        $profile_query = $this->select('node', 'n')
          ->fields('n', ['nid', 'vid'])
          ->condition('n.type', 'profiel')
          ->condition('n.uid', $uid)
          ->condition('n.status', 1)
          ->range(0, 1);
        
        $profile_node = $profile_query->execute()->fetchAssoc();
        
        if ($profile_node) {
          // Get woonplaats value using the profile node ID
          $woonplaats_query = $this->select($woonplaats_table, 'cfw')
            ->fields('cfw', ['field_woonplaats_value'])
            ->condition('cfw.nid', $profile_node['nid'])
            ->condition('cfw.vid', $profile_node['vid'])
            ->range(0, 1);
          
          $woonplaats_data = $woonplaats_query->execute()->fetchAssoc();
          
          if ($woonplaats_data && !empty($woonplaats_data['field_woonplaats_value'])) {
            $row->setSourceProperty('field_woonplaats_value', $woonplaats_data['field_woonplaats_value']);
          }
        }
      } catch (\Exception $e) {
        // Log error but don't fail migration
        \Drupal::logger('thirdwing_migrate')->warning('Could not fetch woonplaats for user @uid: @error', [
          '@uid' => $uid,
          '@error' => $e->getMessage(),
        ]);
      }
    }
  }

  /**
   * Add user picture information to the row.
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