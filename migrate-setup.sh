#!/bin/bash

echo "Setting up Thirdwing Migration Environment..."

# Install required modules
echo "Installing migration modules..."
composer require drupal/migrate_plus drupal/migrate_tools drupal/migrate_upgrade

# Enable modules
echo "Enabling modules..."
drush en migrate migrate_drupal migrate_plus migrate_tools thirdwing_migrate -y

# Clear caches
echo "Clearing caches..."
drush cr

# Import configuration
echo "Importing migration configurations..."
drush config:import --source=modules/custom/thirdwing_migrate/config/install

echo "Setup complete! You can now run the migration."
echo "Next steps:"
echo "1. Configure your D6 database connection in settings.php"
echo "2. Run ./migrate-execute.sh to start the migration"