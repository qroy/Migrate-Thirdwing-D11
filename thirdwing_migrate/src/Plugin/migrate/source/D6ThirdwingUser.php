<?php
// File: modules/custom/thirdwing_migrate/src/Plugin/migrate/source/D6ThirdwingUser.php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for D6 Users with profile data.
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
      ->fields('u')
      ->condition('u.uid', 0, '>')
      ->orderBy('u.uid');

    // Join profile data from profiel content type
    $query->leftJoin('node', 'pn', 'u.uid = pn.uid AND pn.type = \'profiel\'');
    $query->leftJoin('content_type_profiel', 'ctp', 'pn.nid = ctp.nid AND pn.vid = ctp.vid');
    $query->fields('ctp');

    // Join woonplaats field
    $query->leftJoin('content_field_woonplaats', 'cfw', 'pn.nid = cfw.nid AND pn.vid = cfw.vid');
    $query->addField('cfw', 'field_woonplaats_value', 'field_woonplaats');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'uid' => $this->t('User ID'),
      'name' => $this->t('Username'),
      'mail' => $this->t('Email'),
      'created' => $this->t('Created'),
      'access' => $this->t('Last access'),
      'login' => $this->t('Last login'),
      'status' => $this->t('Status'),
      'field_voornaam_value' => $this->t('First name'),
      'field_achternaam_value' => $this->t('Last name'),
      'field_achternaam_voorvoegsel_value' => $this->t('Name prefix'),
      'field_geboortedatum_value' => $this->t('Birth date'),
      'field_geslacht_value' => $this->t('Gender'),
      'field_karrijder_value' => $this->t('Car driver'),
      'field_lidsinds_value' => $this->t('Member since'),
      'field_uitkoor_value' => $this->t('Member until'),
      'field_adres_value' => $this->t('Address'),
      'field_postcode_value' => $this->t('Postal code'),
      'field_woonplaats' => $this->t('City'),
      'field_telefoon_value' => $this->t('Phone'),
      'field_mobiel_value' => $this->t('Mobile phone'),
      'field_sleepgroep_1_value' => $this->t('Sleep group'),
      'field_koor_value' => $this->t('Choir function'),
      'field_notes_value' => $this->t('Notes'),
      'field_notes_format' => $this->t('Notes format'),
      'field_functie_bestuur_value' => $this->t('Board function'),
      'field_functie_mc_value' => $this->t('Music committee'),
      'field_functie_concert_value' => $this->t('Concert committee'),
      'field_functie_feest_value' => $this->t('Party committee'),
      'field_functie_regie_value' => $this->t('Choir direction'),
      'field_functie_ir_value' => $this->t('Internal relations'),
      'field_functie_pr_value' => $this->t('Public relations'),
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
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    // Clean postal code (remove spaces)
    $postcode = $row->getSourceProperty('field_postcode_value');
    if ($postcode) {
      $row->setSourceProperty('field_postcode_value', str_replace(' ', '', $postcode));
    }

    // Transform car driver value
    $karrijder = $row->getSourceProperty('field_karrijder_value');
    if ($karrijder === '*') {
      $row->setSourceProperty('field_karrijder_value', 1);
    }

    return TRUE;
  }
}