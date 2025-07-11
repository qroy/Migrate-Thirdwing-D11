<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 users with profile data and roles.
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
    $query = $this->select('users', 'u')
      ->fields('u', [
        'uid', 'name', 'pass', 'mail', 'created', 'access', 'login', 'status'
      ])
      ->condition('u.uid', 0, '>'); // Skip anonymous user

    // Join with profile values to get all profile fields
    $query->leftJoin('profile_values', 'pv', 'u.uid = pv.uid');
    $query->leftJoin('profile_fields', 'pf', 'pv.fid = pf.fid');
    
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
      // Profile fields
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
      'field_woonplaats' => $this->t('City'),
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

    // Get all profile field values for this user
    $profile_query = $this->select('profile_values', 'pv')
      ->fields('pv', ['value'])
      ->fields('pf', ['name'])
      ->condition('pv.uid', $uid);
    $profile_query->leftJoin('profile_fields', 'pf', 'pv.fid = pf.fid');
    
    $profile_values = $profile_query->execute();
    
    // Set profile field values on the row
    foreach ($profile_values as $profile_value) {
      if (!empty($profile_value['name'])) {
        $row->setSourceProperty($profile_value['name'] . '_value', $profile_value['value']);
      }
    }

    // Get user roles for this user (used by the roles process plugin)
    $roles_query = $this->select('users_roles', 'ur')
      ->fields('ur', ['rid'])
      ->condition('ur.uid', $uid);
    
    $user_roles = $roles_query->execute()->fetchCol();
    $row->setSourceProperty('user_roles', $user_roles);

    return parent::prepareRow($row);
  }

}