/**
 * Database connection for Drupal 6 source site
 * 
 * Add this to your settings.php file:
 */

$databases['drupal_6']['default'] = [
  'database' => 'thirdwing_d6',
  'username' => 'your_username',
  'password' => 'your_password',
  'host' => 'localhost',
  'port' => '3306',
  'driver' => 'mysql',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
];
