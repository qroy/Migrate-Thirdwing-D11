#!/bin/bash

# Comprehensive Thirdwing Migration Setup Script
# Sets up both initial migration and incremental sync capabilities

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Script directory and paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"
DRUPAL_ROOT="$(cd "$MODULE_DIR/../../../.." && pwd)"

# Ensure we're in the Drupal root
cd "$DRUPAL_ROOT"

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  THIRDWING MIGRATION COMPLETE SETUP${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Function to show step header
show_step() {
    echo -e "${PURPLE}=== $1 ===${NC}"
}

# Function to show success
show_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Function to show warning
show_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Function to show error
show_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check prerequisites
check_prerequisites() {
    show_step "Step 1: Checking Prerequisites"
    
    # Check if we're in Drupal root
    if [ ! -f "index.php" ] || [ ! -d "core" ]; then
        show_error "Not in Drupal root directory"
        exit 1
    fi
    show_success "Drupal root directory confirmed"
    
    # Check Drush
    if ! command -v drush &> /dev/null; then
        show_error "Drush is not installed or not in PATH"
        exit 1
    fi
    show_success "Drush available"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        show_warning "Composer not found - some dependencies may need manual installation"
    else
        show_success "Composer available"
    fi
    
    echo ""
}

# Install required modules
install_modules() {
    show_step "Step 2: Installing Required Modules"
    
    echo "Installing migration modules..."
    composer require drupal/migrate_plus drupal/migrate_tools drupal/migrate_upgrade || show_warning "Some composer packages may already be installed"
    
    echo "Enabling core migration modules..."
    drush en migrate migrate_drupal migrate_plus migrate_tools -y
    
    echo "Enabling thirdwing_migrate module..."
    drush en thirdwing_migrate -y
    
    echo "Enabling additional required modules..."
    drush en permissions_by_term permissions_by_entity workflows content_moderation -y || show_warning "Some modules may not be available yet"
    
    show_success "Modules installed and enabled"
    echo ""
}

# Clear caches
clear_caches() {
    show_step "Step 3: Clearing Caches"
    
    drush cr
    show_success "Caches cleared"
    echo ""
}

# Test database connections
test_database() {
    show_step "Step 4: Testing Database Connections"
    
    # Test D11 database
    if drush sqlq "SELECT COUNT(*) FROM users" > /dev/null 2>&1; then
        show_success "D11 database connection working"
    else
        show_error "D11 database connection failed"
        exit 1
    fi
    
    # Test D6 migration database
    if drush eval "\\Drupal\\Core\\Database\\Database::getConnection('default', 'migrate')->query('SELECT COUNT(*) FROM {users}')->fetchField();" > /dev/null 2>&1; then
        show_success "D6 source database connection working"
    else
        show_error "D6 source database connection failed - check settings.php configuration"
        echo ""
        echo "Add this to your settings.php:"
        echo "\$databases['migrate']['default'] = ["
        echo "  'database' => 'drupal6_database',"
        echo "  'username' => 'db_user',"
        echo "  'password' => 'db_password',"
        echo "  'host' => 'localhost',"
        echo "  'port' => '3306',"
        echo "  'driver' => 'mysql',"
        echo "  'prefix' => '',"
        echo "];"
        exit 1
    fi
    
    echo ""
}

# Create content structure
create_content_structure() {
    show_step "Step 5: Creating Content Structure"
    
    if [ -f "$SCRIPT_DIR/create-content-types-and-fields.php" ]; then
        echo "Running content type creation script..."
        drush php:script "$SCRIPT_DIR/create-content-types-and-fields.php"
        show_success "Content types and fields created"
    else
        show_warning "Content type creation script not found - you may need to create it manually"
    fi
    
    echo ""
}

# Import migration configurations
import_configs() {
    show_step "Step 6: Importing Migration Configurations"
    
    # Check if config files exist
    CONFIG_DIR="$MODULE_DIR/config/install"
    if [ -d "$CONFIG_DIR" ]; then
        echo "Importing migration configurations..."
        drush config:import --source="$CONFIG_DIR" --partial -y || show_warning "Some configs may not import - this is normal for new installations"
        show_success "Migration configurations processed"
    else
        show_warning "Migration config directory not found"
    fi
    
    echo ""
}

# Validate setup
validate_setup() {
    show_step "Step 7: Validating Setup"
    
    if [ -f "$SCRIPT_DIR/validate-migration.php" ]; then
        echo "Running comprehensive validation..."
        drush php:script "$SCRIPT_DIR/validate-migration.php"
    else
        # Basic validation
        echo "Running basic validation..."
        
        # Check migrations are available
        if drush migrate:status --group=thirdwing_d6 > /dev/null 2>&1; then
            show_success "Main migration group available"
        else
            show_warning "Main migration group not found"
        fi
        
        # Check incremental migrations
        if drush migrate:status --group=thirdwing_d6_incremental > /dev/null 2>&1; then
            show_success "Incremental migration group available"
        else
            show_warning "Incremental migration group not found"
        fi
        
        # Check sync commands
        if drush list | grep -q "thirdwing:sync"; then
            show_success "Incremental sync commands available"
        else
            show_warning "Incremental sync commands not found"
        fi
    fi
    
    echo ""
}

# Make scripts executable
setup_scripts() {
    show_step "Step 8: Setting Up Scripts"
    
    chmod +x "$SCRIPT_DIR"/*.sh
    show_success "All scripts made executable"
    
    echo ""
}

# Show completion summary
show_completion() {
    show_step "Setup Complete!"
    
    echo -e "${GREEN}🎉 Thirdwing migration system is ready!${NC}"
    echo ""
    echo "Available commands:"
    echo "• Full migration:      $SCRIPT_DIR/migrate-execute.sh"
    echo "• Incremental sync:    $SCRIPT_DIR/migrate-sync.sh --since=yesterday"
    echo "• Check status:        $SCRIPT_DIR/migration-status-check.sh"
    echo "• Validate system:     drush php:script $SCRIPT_DIR/validate-migration.php"
    echo ""
    echo "Quick start:"
    echo "1. Run full migration: $SCRIPT_DIR/migrate-execute.sh"
    echo "2. Test incremental:   $SCRIPT_DIR/migrate-sync.sh --dry-run --since=yesterday"
    echo "3. Set up regular sync: Add to cron or run manually as needed"
    echo ""
    echo -e "${BLUE}Documentation: See README.md for detailed usage instructions${NC}"
    echo ""
}

# Main execution
main() {
    check_prerequisites
    install_modules
    clear_caches
    test_database
    create_content_structure
    import_configs
    validate_setup
    setup_scripts
    show_completion
}

# Handle script arguments
case "${1:-}" in
    --help)
        echo "Thirdwing Migration Complete Setup"
        echo ""
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  --help          Show this help message"
        echo "  --validate-only Run only validation checks"
        echo "  --skip-modules  Skip module installation"
        echo ""
        echo "This script sets up the complete Thirdwing migration system"
        echo "including both initial migration and incremental sync capabilities."
        exit 0
        ;;
    --validate-only)
        echo -e "${BLUE}Running validation only...${NC}"
        echo ""
        test_database
        validate_setup
        exit 0
        ;;
    --skip-modules)
        echo -e "${YELLOW}Skipping module installation...${NC}"
        echo ""
        clear_caches
        test_database
        create_content_structure
        import_configs
        validate_setup
        setup_scripts
        show_completion
        exit 0
        ;;
    "")
        # Run full setup
        main
        ;;
    *)
        echo -e "${RED}Unknown option: $1${NC}"
        echo "Use --help for usage information"
        exit 1
        ;;
esac