<?php

namespace Drupal\thirdwing_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Drupal 6 Thirdwing album source plugin.
 *
 * Handles migration of album content type with all CCK fields and relationships.
 *
 * @MigrateSource(
 *   id = "d6_thirdwing_album",
 *   source_module = "node"
 * )
 */
class D6ThirdwingAlbum extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = $this->select('node', 'n')
      ->fields('n', [
        'nid', 'vid', 'type', 'language', 'title', 'uid', 'status',
        'created', 'changed', 'comment', 'promote', 'moderate', 'sticky'
      ])
      ->condition('n.type', 'album');

    // Join with node_revisions for body content
    $query->leftJoin('node_revisions', 'nr', 'n.vid = nr.vid');
    $query->addField('nr', 'body');
    $query->addField('nr', 'teaser');
    $query->addField('nr', 'format');

    // Album-specific fields
    $query->leftJoin('content_type_album', 'cta', 'n.vid = cta.vid');
    $query->addField('cta', 'field_album_datum_value');
    $query->addField('cta', 'field_album_locatie_value');
    $query->addField('cta', 'field_album_beschrijving_value');
    $query->addField('cta', 'field_album_photographer_value');

    // Order by node ID for consistent processing
    $query->orderBy('n.nid');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('Version ID'),
      'type' => $this->t('Content type'),
      'language' => $this->t('Language'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Author ID'),
      'status' => $this->t('Published status'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Changed timestamp'),
      'comment' => $this->t('Comment status'),
      'promote' => $this->t('Promoted to front page'),
      'moderate' => $this->t('Moderation status'),
      'sticky' => $this->t('Sticky status'),
      'body' => $this->t('Body content'),
      'teaser' => $this->t('Teaser'),
      'format' => $this->t('Text format'),
      'field_album_datum_value' => $this->t('Album date'),
      'field_album_locatie_value' => $this->t('Album location'),
      'field_album_beschrijving_value' => $this->t('Album description'),
      'field_album_photographer_value' => $this->t('Photographer'),
      'images' => $this->t('Album images'),
      'cover_image' => $this->t('Cover image'),
      'taxonomy_terms' => $this->t('Taxonomy terms'),
      'related_activities' => $this->t('Related activities'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'n',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $vid = $row->getSourceProperty('vid');

    // Get album images
    $images = $this->getAttachedFiles($nid, $vid, 'field_afbeeldingen');
    $row->setSourceProperty('images', $images);

    // Get cover image (single image field)
    $cover_image = $this->getCoverImage($nid, $vid);
    $row->setSourceProperty('cover_image', $cover_image);

    // Get related activities
    $related_activities = $this->getRelatedActivities($nid, $vid);
    $row->setSourceProperty('related_activities', $related_activities);

    // Get taxonomy terms
    $taxonomy_terms = $this->getTaxonomyTerms($nid, $vid);
    $row->setSourceProperty('taxonomy_terms', $taxonomy_terms);

    // Clean up data
    $this->cleanupRowData($row);

    return parent::prepareRow($row);
  }

  /**
   * Get attached files for a specific field.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   * @param string $field_name
   *   Field name.
   *
   * @return array
   *   Array of file data.
   */
  protected function getAttachedFiles($nid, $vid, $field_name) {
    $table_name = 'content_' . $field_name;
    
    try {
      $query = $this->select($table_name, 'cf')
        ->fields('cf', [
          'nid', 'vid', 'delta',
          $field_name . '_fid',
          $field_name . '_list',
          $field_name . '_data'
        ])
        ->condition('cf.nid', $nid)
        ->condition('cf.vid', $vid)
        ->orderBy('cf.delta');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      // Table might not exist or field might not have files
      return [];
    }
  }

  /**
   * Get cover image for the album.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   *
   * @return array|null
   *   Cover image data or null.
   */
  protected function getCoverImage($nid, $vid) {
    try {
      $query = $this->select('content_field_cover_image', 'cfci')
        ->fields('cfci', [
          'field_cover_image_fid',
          'field_cover_image_list',
          'field_cover_image_data'
        ])
        ->condition('cfci.nid', $nid)
        ->condition('cfci.vid', $vid)
        ->range(0, 1);

      $result = $query->execute()->fetchAssoc();
      return $result ?: null;
    } catch (\Exception $e) {
      return null;
    }
  }

  /**
   * Get related activities for the album.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   *
   * @return array
   *   Array of related activity node IDs.
   */
  protected function getRelatedActivities($nid, $vid) {
    try {
      $query = $this->select('content_field_gerelateerd_activiteit', 'cfga')
        ->fields('cfga', [
          'nid', 'vid', 'delta',
          'field_gerelateerd_activiteit_nid'
        ])
        ->condition('cfga.nid', $nid)
        ->condition('cfga.vid', $vid)
        ->orderBy('cfga.delta');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Get taxonomy terms for the node.
   *
   * @param int $nid
   *   Node ID.
   * @param int $vid
   *   Version ID.
   *
   * @return array
   *   Array of taxonomy term data.
   */
  protected function getTaxonomyTerms($nid, $vid) {
    try {
      $query = $this->select('term_node', 'tn')
        ->fields('tn', ['tid'])
        ->condition('tn.nid', $nid);

      // Join with term_data to get additional term info
      $query->leftJoin('term_data', 'td', 'tn.tid = td.tid');
      $query->addField('td', 'name');
      $query->addField('td', 'vid', 'vocabulary_id');
      $query->addField('td', 'weight');

      return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      return [];
    }
  }

  /**
   * Clean up row data for consistent migration.
   *
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   */
  protected function cleanupRowData(Row $row) {
    // Handle null values for string fields
    $string_fields = [
      'title', 'body', 'teaser', 'field_album_locatie_value',
      'field_album_beschrijving_value', 'field_album_photographer_value'
    ];
    
    foreach ($string_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null) {
        $row->setSourceProperty($field, '');
      }
    }

    // Handle date fields - ensure proper format
    $date_fields = ['field_album_datum_value'];
    foreach ($date_fields as $field) {
      $value = $row->getSourceProperty($field);
      if ($value === null || $value === '') {
        $row->setSourceProperty($field, null);
      }
    }

    // Ensure arrays are properly formatted
    $array_fields = ['images', 'related_activities', 'taxonomy_terms'];
    foreach ($array_fields as $field) {
      $value = $row->getSourceProperty($field);
      if (!is_array($value)) {
        $row->setSourceProperty($field, []);
      }
    }

    // Handle cover image
    $cover_image = $row->getSourceProperty('cover_image');
    if (!is_array($cover_image)) {
      $row->setSourceProperty('cover_image', null);
    }
  }
}