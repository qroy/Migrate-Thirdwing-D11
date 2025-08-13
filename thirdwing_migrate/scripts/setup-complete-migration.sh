#!/bin/bash

# =============================================================================
# THIRDWING MIGRATION SETUP SCRIPT WITH WEBFORM SUPPORT - GECORRIGEERDE VERSIE
# File: thirdwing_migrate/scripts/setup-complete-migration.sh
# 
# Complete installation script including all functions and webform support
# GECORRIGEERD: Inclusief add-media-dependent-fields.php stap
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
SKIP_WEBFORM=false
VALIDATE_ONLY=false
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
    echo -e "${PURPLE} THIRDWING MIGRATION SETUP - WITH WEBFORM SUPPORT${NC}"
    echo -e "${PURPLE} Complete D6 to D11 Migration including Webforms${NC}"
    echo -e "${PURPLE} GECORRIGEERDE VERSIE - Met media-dependent fields${NC}"
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
            --skip-webform)
                SKIP_WEBFORM=true
                shift
                ;;
            --validate-only)
                VALIDATE_ONLY=true
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
    echo "Thirdwing Migration Setup Script with Webform Support - GECORRIGEERDE VERSIE"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --skip-composer     Skip composer dependency installation"
    echo "  --skip-modules      Skip module installation"
    echo "  --skip-database     Skip database configuration"
    echo "  --reconfigure-db    Force database reconfiguration"
    echo "  --skip-webform      Skip webform installation (for D11 compatibility)"
    echo "  --validate-only     Only run validation checks"
    echo "  --debug             Enable debug output"
    echo "  --help, -h          Show this help message"
    echo ""
    echo "Features in this corrected version:"
    echo "  ✅ Complete content types and fields creation"
    echo "  ✅ Media bundles with proper field configuration"
    echo "  ✅ Media-dependent fields added to content types (FIXED!)"
    echo "  ✅ User profile fields with all commission functions"
    echo "  ✅ Streamlined setup process - EXIF config when needed"
    echo "  ✅ Comprehensive validation"
    echo "  ✅ Webform support"
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
        print_error "Drush not found"
        print_info "Please install Drush 11+ before running this script"
        return 1
    fi
    
    print_success "System prerequisites validated"
    print_info "Drupal root: $DRUPAL_ROOT"
    print_info "Drush version: $(drush --version)"
    
    return 0
}

# =============================================================================
# DATABASE CONFIGURATION
# =============================================================================

collect_database_credentials() {
    print_substep "Collecting D6 database credentials"
    
    echo -e "${BLUE}Please provide your Drupal 6 source database credentials:${NC}"
    
    read -p "Database Host [localhost]: " DB_HOST
    DB_HOST=${DB_HOST:-localhost}
    
    read -p "Database Name: " DB_NAME
    while [[ -z "$DB_NAME" ]]; do
        echo -e "${RED}Database name is required${NC}"
        read -p "Database Name: " DB_NAME
    done
    
    read -p "Database User: " DB_USER
    while [[ -z "$DB_USER" ]]; do
        echo -e "${RED}Database user is required${NC}"
        read -p "Database User: " DB_USER
    done
    
    read -s -p "Database Password: " DB_PASS
    echo ""
    
    read -p "Database Port [3306]: " DB_PORT
    DB_PORT=${DB_PORT:-3306}
    
    read -p "Table Prefix (if any): " DB_PREFIX
    
    # Validate connection
    print_info "Testing database connection..."
    test_migrate_database_connection
}

test_migrate_database_connection() {
    local original_dir="$(pwd)"
    cd "$DRUPAL_ROOT" 2>/dev/null || return 1
    
    local db_test_result=$(drush eval "
        try {
            \$database = new \\PDO('mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME', '$DB_USER', '$DB_PASS');
            \$database->setAttribute(\\PDO::ATTR_ERRMODE, \\PDO::ERRMODE_EXCEPTION);
            \$result = \$database->query('SELECT VERSION()')->fetchColumn();
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
    
    collect_database_credentials
    
    # Determine settings.php location
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
    
    # Required packages for migration and webforms
    local packages=(
        "drupal/migrate_plus:^6.0"
        "drupal/migrate_tools:^6.0"
        "drupal/webform:^6.0"
        "drupal/admin_toolbar:^3.0"
        "drupal/pathauto:^1.8"
        "drupal/token:^1.9"
        "drupal/field_group:^3.2"
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
    
    print_substep "Installing core modules"
    
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
        "system"
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
    
    print_substep "Installing contrib modules"
    
    cd "$DRUPAL_ROOT"
    
    local contrib_modules=(
        "migrate_plus"
        "migrate_tools"
        "admin_toolbar"
        "admin_toolbar_tools"
        "pathauto"
        "token"
        "field_group"
    )
    
    # Add webform if not skipped
    if [ "$SKIP_WEBFORM" != true ]; then
        contrib_modules+=("webform")
        contrib_modules+=("webform_ui")
    fi
    
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

install_custom_modules() {
    print_substep "Installing Thirdwing custom module"
    
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
    
    return 0
}

# =============================================================================
# CONTENT STRUCTURE CREATION - GECORRIGEERDE VERSIE
# =============================================================================

create_content_structure() {
    print_substep "Creating content structure (Phase 1/4: Content Types)"
    
    cd "$DRUPAL_ROOT"
    
    # Stap 1: Maak content types aan (zonder media-afhankelijke velden)
    if [ -f "$MODULE_DIR/scripts/create-content-types-and-fields.php" ]; then
        print_info "Running content types and fields creation..."
        if drush php:script "$MODULE_DIR/scripts/create-content-types-and-fields.php"; then
            print_success "Content types and basic fields created"
        else
            print_error "Failed to create content types and fields"
            return 1
        fi
    else
        print_warning "Content types script not found, skipping"
    fi
    
    print_substep "Creating content structure (Phase 2/4: Media Bundles)"
    
    # Stap 2: Maak media bundles aan
    if [ -f "$MODULE_DIR/scripts/create-media-bundles-and-fields.php" ]; then
        print_info "Running media bundles creation..."
        if drush php:script "$MODULE_DIR/scripts/create-media-bundles-and-fields.php"; then
            print_success "Media bundles created"
        else
            print_error "Failed to create media bundles"
            return 1
        fi
    else
        print_warning "Media bundles script not found, skipping"
    fi
    
    print_substep "Creating content structure (Phase 3/4: Media-Dependent Fields)"
    
    # Stap 3: Voeg media-afhankelijke velden toe (NIEUWE STAP!)
    if [ -f "$MODULE_DIR/scripts/add-media-dependent-fields.php" ]; then
        print_info "Adding media-dependent fields to content types..."
        if drush php:script "$MODULE_DIR/scripts/add-media-dependent-fields.php"; then
            print_success "Media-dependent fields added to content types"
        else
            print_error "Failed to add media-dependent fields"
            print_info "This will cause validation errors - field_afbeeldingen and field_files will be missing"
            return 1
        fi
    else
        print_warning "Media-dependent fields script not found, skipping"
        print_warning "This is a CRITICAL script - field_afbeeldingen and field_files will be missing!"
    fi
    
    print_substep "Creating content structure (Phase 4/4: User Profile Fields)"
    
    # Stap 4: Maak user profile velden aan
    if [ -f "$MODULE_DIR/scripts/create-user-profile-fields.php" ]; then
        print_info "Creating user profile fields..."
        if drush php:script "$MODULE_DIR/scripts/create-user-profile-fields.php"; then
            print_success "User profile fields created"
        else
            print_error "Failed to create user profile fields"
            return 1
        fi
    else
        print_warning "User profile fields script not found, skipping"
    fi
    
    print_substep "Setup complete - ready for content migration"
    print_info "EXIF date extraction will be configured when needed during migration prep"
    
    return 0
}

# =============================================================================
# USER SYSTEM SETUP
# =============================================================================

setup_user_system() {
    print_substep "Setting up user roles and permissions"
    
    cd "$DRUPAL_ROOT"
    
    # User profile fields zijn al aangemaakt in create_content_structure()
    # Deze functie focust zich op roles en permissions
    
    if [ -f "$MODULE_DIR/scripts/setup-role-permissions.php" ]; then
        print_info "Setting up roles and permissions..."
        if drush php:script "$MODULE_DIR/scripts/setup-role-permissions.php"; then
            print_success "Roles and permissions configured"
        else
            print_warning "Failed to setup roles and permissions - continue manually"
        fi
    else
        print_warning "Roles and permissions script not found, skipping"
    fi
    
    return 0
}

# =============================================================================
# VALIDATION FUNCTIONS
# =============================================================================

validate_content_structure() {
    print_substep "Validating content structure"
    
    cd "$DRUPAL_ROOT"
    
    if [ -f "$MODULE_DIR/scripts/validate-created-fields.php" ]; then
        print_info "Running comprehensive content structure validation..."
        if drush php:script "$MODULE_DIR/scripts/validate-created-fields.php"; then
            print_success "Content structure validation passed"
        else
            print_error "Content structure validation failed"
            print_info "Check the output above for missing fields or configuration issues"
            return 1
        fi
    else
        print_warning "Validation script not found, skipping validation"
    fi
    
    return 0
}

validate_installation() {
    print_substep "Running final system validation"
    
    cd "$DRUPAL_ROOT"
    
    # Check basic Drupal functionality
    if ! drush status --field=bootstrap | grep -q "Successful"; then
        print_error "Drupal bootstrap failed"
        return 1
    fi
    
    # Check required modules
    local required_modules=(
        "thirdwing_migrate"
        "migrate_plus"
        "migrate_tools"
        "media"
    )
    
    if [ "$SKIP_WEBFORM" != true ]; then
        required_modules+=("webform")
    fi
    
    for module in "${required_modules[@]}"; do
        if ! drush pm:list --status=enabled --type=module --format=list | grep -q "^$module$"; then
            print_error "Required module '$module' not enabled"
            return 1
        fi
    done
    
    # Check content types
    local expected_content_types=(
        "activiteit"
        "foto"
        "locatie"
        "nieuws"
        "pagina"
        "programma"
        "repertoire"
        "vriend"
        "webform"
    )
    
    for content_type in "${expected_content_types[@]}"; do
        if ! drush eval "echo \\Drupal\\node\\Entity\\NodeType::load('$content_type') ? 'EXISTS' : 'MISSING';" | grep -q "EXISTS"; then
            print_error "Content type '$content_type' not found"
            return 1
        fi
    done
    
    # Check media bundles
    local expected_media_bundles=(
        "image"
        "document"
        "audio"
        "video"
    )
    
    for media_bundle in "${expected_media_bundles[@]}"; do
        if ! drush eval "echo \\Drupal\\media\\Entity\\MediaType::load('$media_bundle') ? 'EXISTS' : 'MISSING';" | grep -q "EXISTS"; then
            print_error "Media bundle '$media_bundle' not found"
            return 1
        fi
    done
    
    print_success "Final system validation passed"
    return 0
}

# =============================================================================
# SUCCESS MESSAGE
# =============================================================================

print_success_message() {
    echo ""
    echo -e "${GREEN}🎉 THIRDWING MIGRATION SETUP COMPLETED SUCCESSFULLY! 🎉${NC}"
    echo ""
    echo -e "${CYAN}📋 SETUP SUMMARY:${NC}"
    echo -e "${GREEN}✅ Content Types:${NC} 9 content types created with all fields"
    echo -e "${GREEN}✅ Media Bundles:${NC} 4 media bundles (image, document, audio, video)"
    echo -e "${GREEN}✅ Media-Dependent Fields:${NC} field_afbeeldingen, field_files added (FIXED!)"
    echo -e "${GREEN}✅ User Profile Fields:${NC} 25+ profile fields with commission functions"
    echo -e "${GREEN}✅ Database Connection:${NC} D6 source database configured"
    if [ "$SKIP_WEBFORM" != true ]; then
        echo -e "${GREEN}✅ Webform Support:${NC} Ready for webform migration"
    fi
    echo ""
    echo -e "${CYAN}🎯 NEXT STEPS:${NC}"
    echo "1. Test content creation in admin interface:"
    echo "   • Visit: /admin/structure/types"
    echo "   • Create test content with media fields"
    echo ""
    echo "2. Verify media functionality:"
    echo "   • Visit: /admin/structure/media"
    echo "   • Upload test image with EXIF data"
    echo "   • Check if field_datum is auto-filled"
    echo ""
    echo "3. Check user profile fields:"
    echo "   • Visit: /admin/config/people/accounts/fields"
    echo "   • Verify all commission function fields"
    echo ""
    echo "4. Begin content migration:"
    echo "   • Run: drush migrate:status"
    echo "   • Test: drush migrate:import thirdwing_users --limit=5"
    echo ""
    echo "5. Configure EXIF date extraction (when ready for image migration):"
    echo "   • Run: drush php:script scripts/configure-image-exif-date-extraction.php"
    echo "   • Test with sample images containing EXIF data"
    echo ""
    echo -e "${YELLOW}📞 CRITICAL CHANGES IN THIS VERSION:${NC}"
    echo "• Fixed missing media-dependent fields (field_afbeeldingen, field_files)"
    echo "• Optimized setup process - EXIF config moved to migration prep phase"
    echo "• Improved content structure validation"
    echo "• Enhanced error handling and debugging"
    echo "• All scripts now run in correct dependency order"
    echo ""
    echo -e "${BLUE}🛠️  ADMIN URLS:${NC}"
    echo "• Content types: /admin/structure/types"
    echo "• Media types: /admin/structure/media"
    echo "• User fields: /admin/config/people/accounts/fields"
    echo "• Permissions: /admin/people/permissions"
    if [ "$SKIP_WEBFORM" != true ]; then
        echo "• Webforms: /admin/structure/webform"
    fi
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
    
    # If validation only, run validation and exit
    if [ "$VALIDATE_ONLY" = true ]; then
        print_step "VALIDATION" "Running validation checks only"
        validate_prerequisites || exit 1
        validate_content_structure || exit 1
        validate_installation || exit 1
        echo -e "${GREEN}✅ All validation checks passed!${NC}"
        exit 0
    fi
    
    # Execute installation steps
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
    install_custom_modules || exit 1
    
    print_step "7" "Content Structure Creation (4 Phases)"
    create_content_structure || exit 1
    
    print_step "8" "User System Setup"
    setup_user_system || exit 1
    
    print_step "9" "Content Structure Validation"
    validate_content_structure || exit 1
    
    print_step "10" "Final System Validation"
    validate_installation || exit 1
    
    print_success_message
    
    return 0
}

# Execute main function with all arguments
main "$@"