<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 users with Content Profile data and roles.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_user",
 *   source_module = "user"
 * )
 */
class D6ThirdwingUser extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Simple user query without profile joins
    $query = $this->select('users', 'u')
      ->fields('u', [
        'uid', 'name', 'pass', 'mail', 'created', 'access', 'login', 'status', 'picture'
      ])
      ->condition('u.uid', 0, '>'); // Skip anonymous user

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
      
      // Profile fields from Content Profile
      'field_voornaam_value' => $this->t('First name'),
      'field_achternaam_value' => $this->t('Last name'),
      'field_achternaam_voorvoegsel_value' => $this->t('Name prefix'),
      'field_geboortedatum_value' => $this->t('Birth date'),
      'field_geslacht_value' => $this->t('Gender'),
      'field_karrijder_value' => $this->t('Car driver'),
      'field_lidsinds_value' => $this->t('Member since'),
      'field_uitkoor_value' => $this->t('Left choir'),
      'field_adres_value' => $this->t('Address'),
      'field_postcode_value' => $this->t('Postal code'),
      'field_woonplaats_value' => $this->t('City'),
      'field_telefoon_value' => $this->t('Phone'),
      'field_mobiel_value' => $this->t('Mobile'),
      'field_sleepgroep_1_value' => $this->t('Transport group'),
      'field_koor_value' => $this->t('Choir'),
      'field_notes_value' => $this->t('Notes'),
      'field_notes_format' => $this->t('Notes format'),
      'field_functie_bestuur_value' => $this->t('Board function'),
      'field_functie_mc_value' => $this->t('Music committee function'),
      'field_functie_concert_value' => $this->t('Concert function'),
      'field_functie_feest_value' => $this->t('Party function'),
      'field_functie_regie_value' => $this->t('Direction function'),
      'field_functie_ir_value' => $this->t('Internal relations function'),
      'field_functie_pr_value' => $this->t('Public relations function'),
      'field_functie_tec_value' => $this->t('Technical function'),
      'field_positie_value' => $this->t('Position'),
      'field_functie_lw_value' => $this->t('Member recruitment function'),
      'field_functie_fl_value' => $this->t('Facilities function'),
      'field_emailbewaking_value' => $this->t('Email monitoring'),
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
    $uid = $row->getSourceProperty('uid');

    // Add user roles
    $this->addUserRoles($row, $uid);
    
    // Add Content Profile fields
    $this->addContentProfileFields($row, $uid);
    
    // Add user picture information
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
      $row->setSourceProperty('user_roles', $rids);
    }
  }

  /**
   * Add Content Profile fields to the row.
   */
  protected function addContentProfileFields(Row $row, $uid) {
    // Check for 'profiel' content type (your profile content type)
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