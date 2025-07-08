<?php
/**
 * @file
 * Script to create Thirdwing content types.
 * 
 * Run with: drush php:script create-content-types.php
 */

use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

$content_types = [
  'activity' => [
    'name' => 'Activity',
    'description' => 'Choir activities and events',
  ],
  'repertoire' => [
    'name' => 'Repertoire', 
    'description' => 'Musical repertoire items',
  ],
  'news' => [
    'name' => 'News',
    'description' => 'News articles',
  ],
  'album' => [
    'name' => 'Album',
    'description' => 'Photo albums',
  ],
  'location' => [
    'name' => 'Location',
    'description' => 'Performance and rehearsal locations',
  ],
  'friend' => [
    'name' => 'Friend',
    'description' => 'Friends and sponsors of the choir',
  ],
  'newsletter' => [
    'name' => 'Newsletter',
    'description' => 'Newsletter issues',
  ],
];

echo "Creating content types...\n";

foreach ($content_types as $type_id => $type_info) {
  // Check if content type already exists
  if (NodeType::load($type_id)) {
    echo "Content type '$type_id' already exists, skipping.\n";
    continue;
  }

  // Create content type
  $node_type = NodeType::create([
    'type' => $type_id,
    'name' => $type_info['name'],
    'description' => $type_info['description'],
    'help' => '',
    'new_revision' => TRUE,
    'preview_mode' => 1,
    'display_submitted' => TRUE,
  ]);
  
  $node_type->save();
  echo "Created content type: {$type_info['name']}\n";
}

echo "\nContent types created successfully!\n";
echo "Next steps:\n";
echo "1. Add fields to content types via admin UI or additional scripts\n";
echo "2. Configure form and display modes\n";
echo "3. Run the migration\n";