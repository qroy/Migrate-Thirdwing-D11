#!/bin/bash

echo "=========================================="
echo "  THIRDWING MIGRATION STATUS CHECK"
echo "=========================================="

# Check if migration database is accessible
echo "1. Testing migration database connection..."
RESULT=$(drush eval "
try {
  \$connection = \Drupal\Core\Database\Database::getConnection('default', 'migrate');
  \$user_count = \$connection->query('SELECT COUNT(*) FROM {users}')->fetchField();
  \$role_count = \$connection->query('SELECT COUNT(*) FROM {role}')->fetchField();
  print \"SUCCESS: Found \$user_count users and \$role_count roles in D6 database\";
} catch (Exception \$e) {
  print \"ERROR: \" . \$e->getMessage();
}
")

echo "   $RESULT"
echo ""

# Check migration status
echo "2. Migration status overview:"
drush migrate:status --group=thirdwing_d6

echo ""
echo "3. Role migration details:"
if drush migrate:status d6_thirdwing_user_role > /dev/null 2>&1; then
    echo "   ✓ Role migration exists"
    drush migrate:status d6_thirdwing_user_role
else
    echo "   ✗ Role migration not found"
fi

echo ""
echo "4. User migration details:"
if drush migrate:status d6_thirdwing_user > /dev/null 2>&1; then
    echo "   ✓ User migration exists"
    drush migrate:status d6_thirdwing_user
else
    echo "   ✗ User migration not found"
fi

echo ""
echo "5. Current D11 roles:"
drush eval "
\$roles = \Drupal\user\Entity\Role::loadMultiple();
foreach (\$roles as \$role) {
  if (!\$role->get('is_admin')) {
    print '   ' . \$role->id() . ' - ' . \$role->label() . PHP_EOL;
  } else {
    print '   ' . \$role->id() . ' - ' . \$role->label() . ' (admin)' . PHP_EOL;
  }
}
"

echo ""
echo "6. Sample user role assignments:"
drush eval "
\$users = \Drupal\user\Entity\User::loadMultiple(array_slice(range(1, 10), 0, 5));
foreach (\$users as \$user) {
  if (\$user->id() > 0) {
    \$roles = \$user->getRoles(TRUE);
    print '   User ' . \$user->id() . ' (' . \$user->getAccountName() . '): ' . implode(', ', \$roles) . PHP_EOL;
  }
}
"

echo ""
echo "=========================================="
echo "Status check completed!"
echo "=========================================="