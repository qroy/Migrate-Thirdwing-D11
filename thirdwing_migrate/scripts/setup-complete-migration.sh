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
# - HYBRID FIELD DISPLAY CONFIGURATION (NEW)
# - Migration preparation and validation
# 
# Usage: ./setup-complete-migration.sh [OPTIONS]
# Options:
#   --help          Show help information
#   --validate-only Run validation checks only
#   --skip-modules  Skip module installation
#   --skip-displays Skip field display configuration
# 
# Part of hybrid field display approach: automated defaults + manual customization
# =============================================================================

set -e  # Exit on any error

# Color definitions for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Configuration
SKIP_MODULES=false
SKIP_DISPLAYS=false
VALIDATE_ONLY=false

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
    echo ""
}

print_step() {
    echo ""
    echo -e "${GREEN}📋 $1${NC}"
    echo "----------------------------------------"
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

# =============================================================================
# Validation Functions
# =============================================================================

check_prerequisites() {
    print_step "Checking Prerequisites"
    
    # Check if we're in a Drupal installation
    if [ ! -f "web/index.php" ] && [ ! -f "index.php" ]; then
        print_error "Not in a Drupal installation directory"
        exit 1
    fi
    
    # Check if Drush is available
    if ! command -v drush &> /dev/null; then
        print_error "Drush is not installed or not in PATH"
        exit 1
    fi
    
    # Check Drupal status
    if ! drush status > /dev/null 2>&1; then
        print_error "Drupal site is not properly installed"
        exit 1
    fi
    
    print_success "All prerequisites met"
}

test_database() {
    print_step "Testing Database Connections"
    
    # Test default database
    if ! drush sql:query "SELECT 1" > /dev/null 2>&1; then
        print_error "Cannot connect to default Drupal database"
        exit 1
    fi
    print_success "Default database connection working"
    
    # Test migration source database (if configured)
    if drush config:get migrate_plus.migration_group.thirdwing --format=string > /dev/null 2>&1; then
        print_success "Migration source database configuration found"
    else
        print_warning "Migration source database not yet configured"
        echo "  Configure source database in settings.php before running migration"
    fi
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
        "migrate_plus"
        "migrate_tools"
        "content_moderation"
        "workflows"
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
    )
    
    echo "