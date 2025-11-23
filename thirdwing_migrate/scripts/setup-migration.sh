#!/bin/bash

# =============================================================================
# THIRDWING MIGRATION SETUP SCRIPT - MIGRATION ONLY
# File: thirdwing_migrate/scripts/setup-migration.sh
# 
# Configures migration from D6 to manually prepared D11 installation
# Does NOT create content types, fields, or configurations
# =============================================================================

# Color definitions
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Global variables
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"
DRUPAL_ROOT=""
SKIP_COMPOSER=false
SKIP_MODULES=false
SKIP_DATABASE=false
RECONFIGURE_DATABASE=false
DEBUG=0
DB_HOST=""
DB_NAME=""
DB_USER=""
DB_PASS=""
DB_PORT="3306"
DB_PREFIX=""

# =============================================================================
# UTILITY FUNCTIONS
# =============================================================================

print_header() {
    echo -e "${PURPLE}=================================================================${NC}"
    echo -e "${PURPLE} THIRDWING MIGRATION SETUP - MIGRATION ONLY${NC}"
    echo -e "${PURPLE} Database Configuration & Module Installation${NC}"
    echo -e "${PURPLE} Content Types MUST be created manually first!${NC}"
    echo -e "${PURPLE}=================================================================${NC}"
    echo ""
}

print_step() {
    echo -e "${CYAN}🔧 STEP $1: $2${NC}"
    echo -e "${CYAN}=================================================================${NC}"
}

print_substep() {
    echo -e "${BLUE}   ▶ $1${NC}"
}

print_success() {
    echo -e "${GREEN}   ✅ $1${NC}"
}

print_error() {
    echo -e "${RED}   ❌ ERROR: $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}   ⚠️  WARNING: $1${NC}"
}

print_info() {
    echo -e "${BLUE}   ℹ️  $1${NC}"
}

print_debug() {
    if [ "$DEBUG" -eq 1 ]; then
        echo -e "${YELLOW}   🐛 DEBUG: $1${NC}"
    fi
}

handle_error() {
    print_error "Script failed at line $1"
    echo -e "${RED}Please check the error message above and try again.${NC}"
    exit 1
}

# =============================================================================
# ARGUMENT PARSING
# =============================================================================

parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            --skip-composer)
                SKIP_COMPOSER=true
                shift
                ;;
            --skip-modules)
                SKIP_MODULES=true
                shift
                ;;
            --skip-database)
                SKIP_DATABASE=true
                shift
                ;;
            --reconfigure-db)
                RECONFIGURE_DATABASE=true
                shift
                ;;
            --debug)
                DEBUG=1
                shift
                ;;
            --help|-h)
                show_help
                exit 0
                ;;
            *)
                print_error "Unknown option: $1"
                show_help
                exit 1
                ;;
        esac
    done
}

show_help() {
    echo "Thirdwing Migration Setup Script - Migration Only"
    echo ""
    echo "⚠️  IMPORTANT: This script does NOT create content types or fields!"
    echo "Content types and fields must be created manually BEFORE running migration."
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --skip-composer     Skip composer dependency installation"
    echo "  --skip-modules      Skip module installation"
    echo "  --skip-database     Skip database configuration"
    echo "  --reconfigure-db    Force database reconfiguration"
    echo "  --debug             Enable debug output"
    echo "  --help, -h          Show this help message"
    echo ""
    echo "What this script does:"
    echo "  ✅ Install required composer packages"
    echo "  ✅ Enable necessary Drupal modules"
    echo "  ✅ Configure D6 database connection"
    echo "  ✅ Validate migration readiness"
    echo ""
    echo "What this script does NOT do:"
    echo "  ❌ Create content types"
    echo "  ❌ Create fields"
    echo "  ❌ Configure view modes"
    echo "  ❌ Set up permissions"
    echo ""
}

# =============================================================================
# VALIDATION FUNCTIONS
# =============================================================================

validate_prerequisites() {
    print_substep "Checking system prerequisites"
    
    # Check if we're in a Drupal environment
    if [ ! -f "web/index.php" ] && [ ! -f "index.php" ]; then
        print_error "Not in a Drupal root directory"
        print_info "Please run this script from your Drupal root directory"
        return 1
    fi
    
    # Determine Drupal root
    if [ -f "web/index.php" ]; then
        DRUPAL_ROOT="$(pwd)/web"
        print_debug "Detected composer-based structure: $DRUPAL_ROOT"
    else
        DRUPAL_ROOT="$(pwd)"
        print_debug "Detected traditional structure: $DRUPAL_ROOT"
    fi
    
    # Check for drush
    if ! command -v drush &> /dev/null; then
        print_error "Drush is not installed or not in PATH"
        print_info "Install drush: composer require drush/drush"
        return 1
    fi
    
    print_success "Drush found: $(drush --version)"
    
    # Check for composer
    if ! command -v composer &> /dev/null; then
        print_warning "Composer not found in PATH"
        if [ "$SKIP_COMPOSER" != true ]; then
            print_error "Composer is required for dependency installation"
            return 1
        fi
    else
        print_success "Composer found: $(composer --version | head -n1)"
    fi
    
    # Verify Drupal installation
    cd "$DRUPAL_ROOT"
    if ! drush status bootstrap | grep -q "Successful"; then
        print_error "Drupal is not properly bootstrapped"
        return 1
    fi
    
    print_success "Drupal installation verified"
    print_success "All prerequisites met"
    
    return 0
}

# =============================================================================
# DATABASE CONFIGURATION
# =============================================================================

collect_database_credentials() {
    print_substep "Collecting D6 database credentials"
    
    read -p "D6 Database host [localhost]: " DB_HOST
    DB_HOST=${DB_HOST:-localhost}
    
    read -p "D6 Database name: " DB_NAME
    if [ -z "$DB_NAME" ]; then
        print_error "Database name is required"
        return 1
    fi
    
    read -p "D6 Database user: " DB_USER
    if [ -z "$DB_USER" ]; then
        print_error "Database user is required"
        return 1
    fi
    
    read -sp "D6 Database password: " DB_PASS
    echo
    if [ -z "$DB_PASS" ]; then
        print_error "Database password is required"
        return 1
    fi
    
    read -p "D6 Database port [3306]: " DB_PORT
    DB_PORT=${DB_PORT:-3306}
    
    read -p "D6 Database prefix (if any): " DB_PREFIX
    
    print_success "Database credentials collected"
    
    # Test connection
    print_substep "Testing D6 database connection"
    
    local original_dir=$(pwd)
    cd "$DRUPAL_ROOT"
    
    local db_test_result=$(drush php:eval "
        try {
            \$connection_info = [
                'database' => '$DB_NAME',
                'username' => '$DB_USER',
                'password' => '$DB_PASS',
                'host' => '$DB_HOST',
                'port' => '$DB_PORT',
                'prefix' => '$DB_PREFIX',
                'namespace' => 'Drupal\\\\Core\\\\Database\\\\Driver\\\\mysql',
                'driver' => 'mysql',
            ];
            
            \$db = \Drupal\Core\Database\Database::getConnection('default', 'migrate');
            \$result = \$db->query('SELECT VERSION()')->fetchField();
            echo 'SUCCESS:' . \$result;
        } catch (Exception \$e) {
            echo 'FAILED:' . \$e->getMessage();
        }" 2>/dev/null)
    
    cd "$original_dir"
    
    if [[ $db_test_result == SUCCESS:* ]]; then
        local mysql_version=${db_test_result#SUCCESS:}
        print_success "Database connection successful"
        print_info "MySQL version: $mysql_version"
        return 0
    else
        local error_message=${db_test_result#FAILED:}
        print_error "Database connection failed: $error_message"
        
        echo -e "${YELLOW}Would you like to re-enter database credentials? [Y/n]: ${NC}"
        read -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            return 1
        else
            collect_database_credentials
            return $?
        fi
    fi
}

configure_migrate_database() {
    if [ "$SKIP_DATABASE" = true ]; then
        print_success "Database configuration skipped"
        return 0
    fi
    
    print_substep "Configuring migrate database connection"
    
    # Check if already configured
    local settings_file=""
    local possible_settings=(
        "$DRUPAL_ROOT/sites/default/settings.php"
        "sites/default/settings.php"
        "web/sites/default/settings.php"
    )
    
    for settings_path in "${possible_settings[@]}"; do
        if [ -f "$settings_path" ]; then
            settings_file="$settings_path"
            break
        fi
    done
    
    if [ -z "$settings_file" ]; then
        print_error "Could not find settings.php file"
        return 1
    fi
    
    print_info "Using settings file: $settings_file"
    
    # Check if migrate database already configured
    if grep -q "databases\['migrate'\]" "$settings_file" && [ "$RECONFIGURE_DATABASE" != true ]; then
        print_warning "Migrate database already configured in settings.php"
        echo -e "${YELLOW}Reconfigure database connection? [Y/n]: ${NC}"
        read -n 1 -r
        echo
        if [[ $REPLY =~ ^[Nn]$ ]]; then
            print_info "Keeping existing database configuration"
            return 0
        fi
    fi
    
    collect_database_credentials || return 1
    
    # Add migrate database configuration
    local migrate_config="
// Thirdwing D6 Migration Database Configuration
\$databases['migrate']['default'] = [
  'database' => '$DB_NAME',
  'username' => '$DB_USER',
  'password' => '$DB_PASS',
  'prefix' => '$DB_PREFIX',
  'host' => '$DB_HOST',
  'port' => '$DB_PORT',
  'namespace' => 'Drupal\\\\Core\\\\Database\\\\Driver\\\\mysql',
  'driver' => 'mysql',
];"
    
    # Remove existing migrate configuration if present
    sed -i '/Thirdwing D6 Migration Database Configuration/,/^];$/d' "$settings_file"
    
    # Add new configuration
    echo "$migrate_config" >> "$settings_file"
    
    print_success "Database configuration added to settings.php"
    return 0
}

# =============================================================================
# COMPOSER AND MODULE INSTALLATION
# =============================================================================

install_composer_dependencies() {
    if [ "$SKIP_COMPOSER" = true ]; then
        print_success "Composer installation skipped"
        return 0
    fi
    
    print_substep "Installing composer dependencies"
    
    # Required packages for migration
    local packages=(
        "drupal/migrate_plus:^6.0"
        "drupal/migrate_tools:^6.0"
        "drupal/webform:^6.2"
        "drupal/admin_toolbar:^3.0"
        "drupal/pathauto:^1.8"
        "drupal/token:^1.9"
    )
    
    print_info "Installing required packages..."
    for package in "${packages[@]}"; do
        print_info "Installing: $package"
        if ! composer require "$package" --no-interaction; then
            print_warning "Failed to install $package, continuing..."
        fi
    done
    
    print_success "Composer dependencies installed"
    return 0
}

install_core_modules() {
    if [ "$SKIP_MODULES" = true ]; then
        print_success "Core module installation skipped"
        return 0
    fi
    
    print_substep "Enabling core modules"
    
    cd "$DRUPAL_ROOT"
    
    local core_modules=(
        "media"
        "media_library"
        "file"
        "image"
        "text"
        "link"
        "datetime"
        "number"
        "options"
        "telephone"
        "field_ui"
        "views"
        "views_ui"
        "user"
        "node"
        "taxonomy"
        "path"
        "dblog"
    )
    
    local modules_to_enable=()
    for module in "${core_modules[@]}"; do
        if ! drush pm:list --status=enabled --type=module --format=list | grep -q "^$module$"; then
            modules_to_enable+=("$module")
        fi
    done
    
    if [ ${#modules_to_enable[@]} -gt 0 ]; then
        print_info "Enabling core modules: ${modules_to_enable[*]}"
        if ! drush pm:enable -y "${modules_to_enable[@]}"; then
            print_error "Failed to enable core modules"
            return 1
        fi
        print_success "Core modules enabled successfully"
    else
        print_success "All core modules already enabled"
    fi
    
    return 0
}

install_contrib_modules() {
    if [ "$SKIP_MODULES" = true ]; then
        print_success "Contrib module installation skipped"
        return 0
    fi
    
    print_substep "Enabling contrib modules"
    
    cd "$DRUPAL_ROOT"
    
    local contrib_modules=(
        "migrate_plus"
        "migrate_tools"
        "admin_toolbar"
        "admin_toolbar_tools"
        "pathauto"
        "token"
        "webform"
        "webform_ui"
    )
    
    local modules_to_enable=()
    for module in "${contrib_modules[@]}"; do
        if ! drush pm:list --status=enabled --type=module --format=list | grep -q "^$module$"; then
            modules_to_enable+=("$module")
        fi
    done
    
    if [ ${#modules_to_enable[@]} -gt 0 ]; then
        print_info "Enabling contrib modules: ${modules_to_enable[*]}"
        if ! drush pm:enable -y "${modules_to_enable[@]}"; then
            print_error "Failed to enable contrib modules"
            return 1
        fi
        print_success "Contrib modules enabled successfully"
    else
        print_success "All contrib modules already enabled"
    fi
    
    return 0
}

install_custom_module() {
    print_substep "Enabling Thirdwing migrate module"
    
    cd "$DRUPAL_ROOT"
    
    if ! drush pm:list --status=enabled --type=module --format=list | grep -q "^thirdwing_migrate$"; then
        print_info "Enabling thirdwing_migrate module..."
        
        if ! drush pm:enable -y thirdwing_migrate; then
            print_error "Failed to enable thirdwing_migrate module"
            return 1
        fi
        
        print_success "Thirdwing migrate module enabled successfully"
    else
        print_success "Thirdwing migrate module already enabled"
    fi
    
    # Clear cache
    print_info "Clearing cache..."
    drush cr
    
    return 0
}

# =============================================================================
# VALIDATION
# =============================================================================

validate_migration_readiness() {
    print_substep "Validating migration readiness"
    
    cd "$DRUPAL_ROOT"
    
    local validation_errors=0
    
    # Check if migrate database is accessible
    print_info "Testing D6 database connection..."
    if ! drush php:eval "\$db = \Drupal\Core\Database\Database::getConnection('default', 'migrate'); echo 'Connected: ' . \$db->query('SELECT DATABASE()')->fetchField();" 2>&1 | grep -q "Connected:"; then
        print_error "Cannot connect to D6 migrate database"
        ((validation_errors++))
    else
        print_success "D6 database connection verified"
    fi
    
    # Check if migration group is available
    print_info "Checking migration definitions..."
    if drush migrate:status --group=thirdwing_d6 2>&1 | grep -q "No migrations found"; then
        print_error "No thirdwing_d6 migrations found"
        ((validation_errors++))
    else
        print_success "Migration definitions found"
    fi
    
    # List available migrations
    print_info "Available migrations:"
    drush migrate:status --group=thirdwing_d6 --format=list
    
    if [ $validation_errors -gt 0 ]; then
        print_error "Validation found $validation_errors error(s)"
        return 1
    fi
    
    print_success "Migration readiness validation passed"
    return 0
}

# =============================================================================
# SUCCESS MESSAGE
# =============================================================================

print_success_message() {
    echo ""
    echo -e "${GREEN}=================================================================${NC}"
    echo -e "${GREEN} 🎉 MIGRATION SETUP COMPLETE!${NC}"
    echo -e "${GREEN}=================================================================${NC}"
    echo ""
    
    echo -e "${BLUE}✅ What was configured:${NC}"
    echo "  • D6 database connection established"
    echo "  • Required modules installed and enabled"
    echo "  • Migration definitions loaded"
    echo ""
    
    echo -e "${YELLOW}⚠️  IMPORTANT REMINDERS:${NC}"
    echo "  • Content types MUST be created manually before migration"
    echo "  • Fields MUST be configured manually according to documentation"
    echo "  • View modes and displays MUST be set up manually"
    echo "  • Refer to: D11 Content Types and Fields.md"
    echo ""
    
    echo -e "${CYAN}📋 Next Steps:${NC}"
    echo "  1. Verify content types are created: drush config:status"
    echo "  2. Check migration status: drush migrate:status --group=thirdwing_d6"
    echo "  3. Test with small batch: drush migrate:import d6_thirdwing_user --limit=5"
    echo "  4. Review migration: drush migrate:messages d6_thirdwing_user"
    echo "  5. Full migration: bash scripts/migrate-execute.sh"
    echo ""
    
    echo -e "${BLUE}📖 Documentation:${NC}"
    echo "  • Migration guide: README.md"
    echo "  • Content structure: D11 Content Types and Fields.md"
    echo "  • D6 reference: D6 Content Types and Fields.md"
    echo ""
    
    echo -e "${GREEN}Ready to migrate!${NC}"
    echo ""
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================

main() {
    # Set error handling
    set -e
    trap 'handle_error $LINENO' ERR
    
    print_header
    
    # Parse command line arguments
    parse_arguments "$@"
    
    # Execute setup steps
    print_step "1" "Prerequisites Validation"
    validate_prerequisites || exit 1
    
    print_step "2" "Database Configuration"
    configure_migrate_database || exit 1
    
    print_step "3" "Composer Dependencies"
    install_composer_dependencies || exit 1
    
    print_step "4" "Core Module Installation"
    install_core_modules || exit 1
    
    print_step "5" "Contrib Module Installation"
    install_contrib_modules || exit 1
    
    print_step "6" "Custom Module Installation"
    install_custom_module || exit 1
    
    print_step "7" "Migration Readiness Validation"
    validate_migration_readiness || exit 1
    
    print_success_message
    
    return 0
}

# Execute main function with all arguments
main "$@"
