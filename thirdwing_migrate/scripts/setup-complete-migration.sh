#!/bin/bash

# =============================================================================
# Thirdwing Migration Complete Setup Script
# =============================================================================
# 
# Complete setup for Thirdwing D6 to D11 migration including:
# - Module installation and dependencies
# - Content type and field creation
# - Media bundle configuration
# - Workflow and content moderation setup
# - HYBRID FIELD DISPLAY CONFIGURATION
# - Role and permission setup
# - Migration preparation and validation
# 
# Usage: ./setup-complete-migration.sh [OPTIONS]
# Options:
#   --help          Show help information
#   --validate-only Run validation checks only
#   --skip-modules  Skip module installation
#   --skip-displays Skip field display configuration
#   --skip-permissions Skip role permission setup
#   --dry-run       Show what would be done without executing
#   --verbose       Show detailed output
# 
# Part of hybrid field display approach: automated defaults + manual customization
# =============================================================================

set -e  # Exit on any error

# Color definitions for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"

# Configuration variables
SKIP_MODULES=false
SKIP_DISPLAYS=false
SKIP_PERMISSIONS=false
VALIDATE_ONLY=false
DRY_RUN=false
VERBOSE=false

# =============================================================================
# Helper Functions
# =============================================================================

print_header() {
    echo ""
    echo -e "${BLUE}=============================================================================${NC}"
    echo -e "${BLUE} Thirdwing Migration Complete Setup${NC}"
    echo -e "${BLUE}=============================================================================${NC}"
    echo ""
    echo "🎯 Complete D6 to D11 migration setup including hybrid field displays"
    echo "📁 Module: $(basename "$MODULE_DIR")"
    echo "📂 Script: $(basename "$0")"
    echo ""
}

print_step() {
    echo ""
    echo -e "${GREEN}📋 $1${NC}"
    echo "----------------------------------------"
}

print_substep() {
    echo -e "${CYAN}   📌 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

print_verbose() {
    if [ "$VERBOSE" = true ]; then
        echo -e "${PURPLE}   🔍 $1${NC}"
    fi
}

show_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --help            Show this help message"
    echo "  --validate-only   Run validation checks only"
    echo "  --skip-modules    Skip module installation"
    echo "  --skip-displays   Skip field display configuration"
    echo "  --skip-permissions Skip role permission setup"
    echo "  --dry-run         Show what would be done without executing"
    echo "  --verbose         Show detailed output"
    echo ""
    echo "Examples:"
    echo "  $0                          # Full setup"
    echo "  $0 --validate-only          # Check prerequisites only"
    echo "  $0 --skip-modules           # Skip module installation"
    echo "  $0 --dry-run --verbose      # Show what would be done"
    echo ""
}

# =============================================================================
# Validation Functions
# =============================================================================

check_prerequisites() {
    print_step "Checking Prerequisites"
    
    # Check if we're in a Drupal installation
    if [ ! -f "web/index.php" ] && [ ! -f "index.php" ]; then
        print_error "Not in a Drupal installation directory"
        print_info "Expected to find web/index.php or index.php"
        exit 1
    fi
    print_verbose "Found Drupal installation files"
    
    # Check if Drush is available
    if ! command -v drush &> /dev/null; then
        print_error "Drush is not installed or not in PATH"
        print_info "Install Drush: composer require drush/drush"
        exit 1
    fi
    
    # Get Drush version
    DRUSH_VERSION=$(drush --version | grep -oP '\d+\.\d+' | head -1)
    print_verbose "Found Drush version: $DRUSH_VERSION"
    
    # Check Drupal status
    if ! drush status > /dev/null 2>&1; then
        print_error "Drupal site is not properly installed"
        print_info "Install Drupal first: drush site:install"
        exit 1
    fi
    
    # Get Drupal version
    DRUPAL_VERSION=$(drush status --field=drupal-version 2>/dev/null || echo "Unknown")
    print_verbose "Drupal version: $DRUPAL_VERSION"
    
    # Check if this is Drupal 11
    if [[ ! "$DRUPAL_VERSION" =~ ^11\. ]]; then
        print_warning "Expected Drupal 11, found: $DRUPAL_VERSION"
        print_info "This script is designed for Drupal 11"
    fi
    
    # Check if module directory exists
    if [ ! -d "$MODULE_DIR" ]; then
        print_error "Module directory not found: $MODULE_DIR"
        exit 1
    fi
    print_verbose "Module directory found: $MODULE_DIR"
    
    # Check if thirdwing_migrate module files exist
    if [ ! -f "$MODULE_DIR/thirdwing_migrate.info.yml" ]; then
        print_error "thirdwing_migrate.info.yml not found in $MODULE_DIR"
        exit 1
    fi
    print_verbose "Module info file found"
    
    print_success "All prerequisites met"
}

test_database() {
    print_step "Testing Database Connections"
    
    # Test default database
    if ! drush sql:query "SELECT 1" > /dev/null 2>&1; then
        print_error "Cannot connect to default Drupal database"
        print_info "Check database configuration in settings.php"
        exit 1
    fi
    print_success "Default database connection working"
    
    # Test migration source database (if configured)
    if drush config:get migrate_plus.migration_group.thirdwing_d6 --format=string > /dev/null 2>&1; then
        print_success "Migration source database configuration found"
    else
        print_warning "Migration source database not yet configured"
        print_info "Configure source database in settings.php before running migration:"
        echo ""
        echo "  \$databases['migrate']['default'] = ["
        echo "    'driver' => 'mysql',"
        echo "    'database' => 'thirdwing_d6',"
        echo "    'username' => 'your_username',"
        echo "    'password' => 'your_password',"
        echo "    'host' => 'localhost',"
        echo "    'prefix' => '',"
        echo "  ];"
        echo ""
    fi
}

check_file_permissions() {
    print_step "Checking File Permissions"
    
    # Check if files directory is writable
    FILES_DIR=$(drush status --field=files 2>/dev/null || echo "sites/default/files")
    if [ ! -d "$FILES_DIR" ]; then
        print_warning "Files directory does not exist: $FILES_DIR"
        if [ "$DRY_RUN" = false ]; then
            mkdir -p "$FILES_DIR"
            chmod 755 "$FILES_DIR"
            print_success "Created files directory: $FILES_DIR"
        fi
    elif [ ! -w "$FILES_DIR" ]; then
        print_error "Files directory is not writable: $FILES_DIR"
        print_info "Fix with: chmod 755 $FILES_DIR"
        exit 1
    fi
    print_verbose "Files directory writable: $FILES_DIR"
    
    # Check temp directory
    TEMP_DIR=$(drush status --field=temp 2>/dev/null || echo "/tmp")
    if [ ! -w "$TEMP_DIR" ]; then
        print_warning "Temp directory not writable: $TEMP_DIR"
    else
        print_verbose "Temp directory writable: $TEMP_DIR"
    fi
    
    print_success "File permissions OK"
}

# =============================================================================
# Installation Functions
# =============================================================================

install_modules() {
    if [ "$SKIP_MODULES" = true ]; then
        print_warning "Skipping module installation"
        return
    fi
    
    print_step "Installing Required Modules"
    
    # Core modules needed for migration
    CORE_MODULES=(
        "migrate"
        "migrate_drupal"
        "media"
        "file"
        "image"
        "datetime"
        "link"
        "field"
        "entity_reference"
        "text"
        "options"
        "taxonomy"
        "menu_ui"
        "path"
        "workflows"
        "content_moderation"
    )
    
    print_substep "Installing core modules"
    for module in "${CORE_MODULES[@]}"; do
        if drush pm:list --status=enabled --type=module | grep -q "^$module"; then
            print_verbose "$module already enabled"
        else
            print_verbose "Enabling $module"
            if [ "$DRY_RUN" = false ]; then
                drush pm:enable "$module" -y
            fi
        fi
    done
    
    # Contrib modules (if available)
    CONTRIB_MODULES=(
        "migrate_plus"
        "migrate_tools"
        "permissions_by_term"
        "permissions_by_entity"
        "field_permissions"
    )
    
    print_substep "Installing contrib modules (if available)"
    for module in "${CONTRIB_MODULES[@]}"; do
        if drush pm:list --status=enabled --type=module | grep -q "^$module"; then
            print_verbose "$module already enabled"
        elif drush pm:list --status=disabled --type=module | grep -q "^$module"; then
            print_verbose "Enabling $module"
            if [ "$DRY_RUN" = false ]; then
                drush pm:enable "$module" -y
            fi
        else
            print_warning "$module not available (optional)"
        fi
    done
    
    # Install thirdwing_migrate module
    print_substep "Installing thirdwing_migrate module"
    if drush pm:list --status=enabled --type=module | grep -q "^thirdwing_migrate"; then
        print_verbose "thirdwing_migrate already enabled"
    else
        print_verbose "Enabling thirdwing_migrate"
        if [ "$DRY_RUN" = false ]; then
            drush pm:enable thirdwing_migrate -y
        fi
    fi
    
    print_success "Module installation completed"
}

setup_content_structure() {
    print_step "Setting Up Content Structure"
    
    # Check if setup scripts exist
    SETUP_SCRIPTS=(
        "setup-content-types.php"
        "setup-media-bundles.php"
        "setup-shared-fields.php"
        "setup-content-moderation.php"
    )
    
    for script in "${SETUP_SCRIPTS[@]}"; do
        SCRIPT_PATH="$MODULE_DIR/scripts/$script"
        if [ -f "$SCRIPT_PATH" ]; then
            print_substep "Running $script"
            if [ "$DRY_RUN" = false ]; then
                drush php:script "$SCRIPT_PATH"
            else
                print_verbose "Would run: drush php:script $SCRIPT_PATH"
            fi
        else
            print_warning "Setup script not found: $script"
            print_info "Expected at: $SCRIPT_PATH"
        fi
    done
    
    print_success "Content structure setup completed"
}

setup_field_displays() {
    if [ "$SKIP_DISPLAYS" = true ]; then
        print_warning "Skipping field display configuration"
        return
    fi
    
    print_step "Configuring Field Displays (Hybrid Approach)"
    
    print_substep "Setting up automated field displays"
    if [ "$DRY_RUN" = false ]; then
        # Setup displays for all content types
        if drush list | grep -q "thirdwing:setup-displays"; then
            drush thirdwing:setup-displays
        else
            print_warning "Field display commands not available yet"
            print_info "Field displays will be configured when migration runs"
        fi
    else
        print_verbose "Would run: drush thirdwing:setup-displays"
    fi
    
    print_substep "Validating field display configuration"
    if [ "$DRY_RUN" = false ]; then
        if drush list | grep -q "thirdwing:validate-displays"; then
            drush thirdwing:validate-displays
        else
            print_verbose "Validation command not available yet"
        fi
    else
        print_verbose "Would run: drush thirdwing:validate-displays"
    fi
    
    print_info "Manual customization available at: Structure > Content types > [Type] > Manage display"
    print_success "Field display configuration completed"
}

setup_permissions() {
    if [ "$SKIP_PERMISSIONS" = true ]; then
        print_warning "Skipping permission setup"
        return
    fi
    
    print_step "Setting Up Roles and Permissions"
    
    PERMISSION_SCRIPT="$MODULE_DIR/scripts/setup-role-permissions.php"
    if [ -f "$PERMISSION_SCRIPT" ]; then
        print_substep "Configuring role permissions"
        if [ "$DRY_RUN" = false ]; then
            drush php:script "$PERMISSION_SCRIPT"
        else
            print_verbose "Would run: drush php:script $PERMISSION_SCRIPT"
        fi
    else
        print_warning "Permission setup script not found"
        print_info "Expected at: $PERMISSION_SCRIPT"
    fi
    
    print_success "Permission setup completed"
}

validate_installation() {
    print_step "Validating Installation"
    
    # Check if validation script exists
    VALIDATION_SCRIPT="$MODULE_DIR/scripts/validate-migration.php"
    if [ -f "$VALIDATION_SCRIPT" ]; then
        print_substep "Running comprehensive validation"
        if [ "$DRY_RUN" = false ]; then
            drush php:script "$VALIDATION_SCRIPT"
        else
            print_verbose "Would run: drush php:script $VALIDATION_SCRIPT"
        fi
    else
        print_warning "Validation script not found"
        print_info "Expected at: $VALIDATION_SCRIPT"
    fi
    
    # Basic validation checks
    print_substep "Checking module status"
    if drush pm:list --status=enabled --type=module | grep -q "^thirdwing_migrate"; then
        print_success "thirdwing_migrate module is enabled"
    else
        print_error "thirdwing_migrate module is not enabled"
    fi
    
    # Check content types
    print_substep "Checking content types"
    EXPECTED_TYPES=("activiteit" "nieuws" "pagina" "repertoire" "locatie" "vriend")
    for type in "${EXPECTED_TYPES[@]}"; do
        if drush entity:list node_type | grep -q "$type"; then
            print_verbose "Content type $type exists"
        else
            print_warning "Content type $type not found"
        fi
    done
    
    # Check media bundles
    print_substep "Checking media bundles"
    EXPECTED_BUNDLES=("audio" "video" "image" "document")
    for bundle in "${EXPECTED_BUNDLES[@]}"; do
        if drush entity:list media_type | grep -q "$bundle"; then
            print_verbose "Media bundle $bundle exists"
        else
            print_warning "Media bundle $bundle not found"
        fi
    done
    
    print_success "Installation validation completed"
}

clear_caches() {
    print_step "Clearing Caches"
    
    if [ "$DRY_RUN" = false ]; then
        drush cache:rebuild
        print_success "Caches cleared"
    else
        print_verbose "Would run: drush cache:rebuild"
    fi
}

# =============================================================================
# Main Execution Flow
# =============================================================================

show_summary() {
    print_step "Setup Summary"
    
    echo "Configuration:"
    echo "  Skip modules: $SKIP_MODULES"
    echo "  Skip displays: $SKIP_DISPLAYS"
    echo "  Skip permissions: $SKIP_PERMISSIONS"
    echo "  Validate only: $VALIDATE_ONLY"
    echo "  Dry run: $DRY_RUN"
    echo "  Verbose: $VERBOSE"
    echo ""
    
    if [ "$VALIDATE_ONLY" = true ]; then
        echo "Mode: Validation checks only"
    elif [ "$DRY_RUN" = true ]; then
        echo "Mode: Dry run (no changes will be made)"
    else
        echo "Mode: Full setup execution"
    fi
    echo ""
}

show_next_steps() {
    print_step "Next Steps"
    
    echo "🎯 Your Thirdwing migration system is now set up!"
    echo ""
    echo "Next actions:"
    echo ""
    echo "1. 📋 Configure source database (if not done):"
    echo "   Edit settings.php and add D6 database connection"
    echo ""
    echo "2. 🔄 Run initial migration:"
    echo "   $MODULE_DIR/scripts/migrate-execute.sh"
    echo ""
    echo "3. ✅ Validate migration results:"
    echo "   drush php:script $MODULE_DIR/scripts/validate-migration.php"
    echo ""
    echo "4. 🔄 Set up regular sync (optional):"
    echo "   $MODULE_DIR/scripts/migrate-sync.sh --since='yesterday'"
    echo ""
    echo "5. 🎨 Customize field displays (optional):"
    echo "   Navigate to: Structure > Content types > [Type] > Manage display"
    echo ""
    echo "Useful commands:"
    echo "  drush thirdwing:setup-displays     # Configure field displays"
    echo "  drush thirdwing:validate-displays  # Validate displays"
    echo "  drush thirdwing:sync-incremental   # Incremental sync"
    echo ""
}

# =============================================================================
# Command Line Argument Parsing
# =============================================================================

parse_arguments() {
    while [[ $# -gt 0 ]]; do
        case $1 in
            --help)
                show_usage
                exit 0
                ;;
            --validate-only)
                VALIDATE_ONLY=true
                shift
                ;;
            --skip-modules)
                SKIP_MODULES=true
                shift
                ;;
            --skip-displays)
                SKIP_DISPLAYS=true
                shift
                ;;
            --skip-permissions)
                SKIP_PERMISSIONS=true
                shift
                ;;
            --dry-run)
                DRY_RUN=true
                shift
                ;;
            --verbose)
                VERBOSE=true
                shift
                ;;
            *)
                print_error "Unknown option: $1"
                show_usage
                exit 1
                ;;
        esac
    done
}

# =============================================================================
# Main Execution
# =============================================================================

main() {
    # Parse command line arguments
    parse_arguments "$@"
    
    # Show header
    print_header
    
    # Show configuration summary
    show_summary
    
    # Always run prerequisite checks
    check_prerequisites
    test_database
    check_file_permissions
    
    # If validate-only mode, stop here
    if [ "$VALIDATE_ONLY" = true ]; then
        print_success "Validation completed successfully!"
        print_info "Run without --validate-only to perform full setup"
        exit 0
    fi
    
    # Full setup execution
    install_modules
    setup_content_structure
    setup_field_displays
    setup_permissions
    clear_caches
    validate_installation
    
    # Show completion message
    print_success "🎉 Thirdwing migration setup completed successfully!"
    
    # Show next steps
    show_next_steps
}

# Execute main function with all arguments
main "$@"