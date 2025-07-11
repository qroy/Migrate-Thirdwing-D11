#!/bin/bash

# Thirdwing Incremental Migration Sync Script
# Provides easy-to-use wrapper around Drush sync commands

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default values
SINCE=""
CONTENT_TYPES=""
DRY_RUN=false
BACKUP=true
FORCE_UPDATE=false
USER_ACTIVITY=""
VERBOSE=false

# Script directory and paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MODULE_DIR="$(dirname "$SCRIPT_DIR")"
DRUPAL_ROOT="$(cd "$MODULE_DIR/../../../.." && pwd)"

# Ensure we're in the Drupal root for drush commands
cd "$DRUPAL_ROOT"

# Function to show usage
show_usage() {
    echo -e "${BLUE}Thirdwing Incremental Migration Sync${NC}"
    echo ""
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --since DATE              Sync content changed since this date"
    echo "                            Examples: 'yesterday', 'last-week', '2025-01-01', '-7 days'"
    echo "  --content-types TYPES     Comma-separated list of content types to sync"
    echo "                            Examples: 'nieuws,activiteit', 'pagina'"
    echo "  --user-activity DATE      Include users with activity since this date"
    echo "  --dry-run                 Preview changes without importing"
    echo "  --no-backup               Skip database backup"
    echo "  --force-update            Force update existing content"
    echo "  --verbose                 Show detailed output"
    echo "  --help                    Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 --since=yesterday"
    echo "  $0 --since='last-week' --content-types='nieuws,activiteit'"
    echo "  $0 --dry-run --since='2025-01-01'"
    echo "  $0 --user-activity='last-month'"
    echo ""
    echo "Quick Commands:"
    echo "  $0 --status               Show sync status and history"
    echo "  $0 --reset                Reset sync tracking"
    echo ""
    echo "Related Scripts:"
    echo "  ./scripts/migrate-execute.sh           Run full initial migration"
    echo "  ./scripts/setup-complete-migration.sh  Complete system setup"
    echo "  ./scripts/migration-status-check.sh    Check migration status"
    echo "  ./scripts/validate-migration.php       Comprehensive validation"
    echo ""
}

# Function to check if Drush is available and we're in Drupal root
check_drush() {
    if ! command -v drush &> /dev/null; then
        echo -e "${RED}Error: Drush is not installed or not in PATH${NC}"
        exit 1
    fi
    
    # Ensure we're in the correct directory
    if [ ! -f "index.php" ] || [ ! -d "core" ]; then
        echo -e "${RED}Error: Not in Drupal root directory${NC}"
        echo "Current directory: $(pwd)"
        echo "Expected to find index.php and core/ directory"
        exit 1
    fi
    
    # Check if Drupal is properly configured
    if ! drush status --format=list | grep -q "drupal-version"; then
        echo -e "${RED}Error: Drupal not properly configured${NC}"
        exit 1
    fi
}

# Function to check migration module
check_migration_module() {
    if ! drush pm:list --status=enabled --format=list | grep -q "thirdwing_migrate"; then
        echo -e "${RED}Error: thirdwing_migrate module is not enabled${NC}"
        echo "Please enable it with: drush en thirdwing_migrate"
        exit 1
    fi
}

# Function to validate date format
validate_date() {
    local date_input="$1"
    
    if [ -z "$date_input" ]; then
        return 0
    fi
    
    # Check if it's a valid date using date command
    if ! date -d "$date_input" &> /dev/null; then
        echo -e "${RED}Error: Invalid date format: '$date_input'${NC}"
        echo "Valid formats: 'yesterday', 'last-week', '2025-01-01', '-7 days'"
        exit 1
    fi
}

# Function to show sync status
show_status() {
    echo -e "${BLUE}=== Thirdwing Sync Status ===${NC}"
    drush thirdwing:sync-status
}

# Function to reset sync tracking
reset_sync() {
    echo -e "${YELLOW}This will reset all sync tracking data.${NC}"
    read -p "Are you sure? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        drush thirdwing:sync-reset
        echo -e "${GREEN}Sync tracking reset successfully.${NC}"
    else
        echo "Reset cancelled."
    fi
}

# Function to build Drush command
build_drush_command() {
    local cmd="drush thirdwing:sync"
    
    if [ -n "$SINCE" ]; then
        cmd="$cmd --since='$SINCE'"
    fi
    
    if [ -n "$CONTENT_TYPES" ]; then
        cmd="$cmd --content-types='$CONTENT_TYPES'"
    fi
    
    if [ -n "$USER_ACTIVITY" ]; then
        cmd="$cmd --user-activity='$USER_ACTIVITY'"
    fi
    
    if [ "$DRY_RUN" = true ]; then
        cmd="$cmd --dry-run"
    fi
    
    if [ "$BACKUP" = false ]; then
        cmd="$cmd --no-backup"
    fi
    
    if [ "$FORCE_UPDATE" = true ]; then
        cmd="$cmd --force-update"
    fi
    
    echo "$cmd"
}

# Function to show sync summary
show_sync_summary() {
    echo -e "${BLUE}=== Sync Configuration ===${NC}"
    [ -n "$SINCE" ] && echo "Since: $SINCE"
    [ -n "$CONTENT_TYPES" ] && echo "Content Types: $CONTENT_TYPES"
    [ -n "$USER_ACTIVITY" ] && echo "User Activity Since: $USER_ACTIVITY"
    [ "$DRY_RUN" = true ] && echo -e "${YELLOW}Mode: DRY RUN (no changes will be made)${NC}"
    [ "$BACKUP" = false ] && echo -e "${YELLOW}Backup: DISABLED${NC}"
    [ "$FORCE_UPDATE" = true ] && echo "Force Update: ENABLED"
    echo ""
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --since=*)
            SINCE="${1#*=}"
            shift
            ;;
        --since)
            SINCE="$2"
            shift 2
            ;;
        --content-types=*)
            CONTENT_TYPES="${1#*=}"
            shift
            ;;
        --content-types)
            CONTENT_TYPES="$2"
            shift 2
            ;;
        --user-activity=*)
            USER_ACTIVITY="${1#*=}"
            shift
            ;;
        --user-activity)
            USER_ACTIVITY="$2"
            shift 2
            ;;
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --no-backup)
            BACKUP=false
            shift
            ;;
        --force-update)
            FORCE_UPDATE=true
            shift
            ;;
        --verbose)
            VERBOSE=true
            shift
            ;;
        --status)
            show_status
            exit 0
            ;;
        --reset)
            reset_sync
            exit 0
            ;;
        --help)
            show_usage
            exit 0
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            show_usage
            exit 1
            ;;
    esac
done

# Check prerequisites
echo -e "${BLUE}Checking prerequisites...${NC}"
check_drush
check_migration_module

# Validate inputs
validate_date "$SINCE"
validate_date "$USER_ACTIVITY"

# If no parameters provided, show usage
if [ -z "$SINCE" ] && [ -z "$CONTENT_TYPES" ] && [ -z "$USER_ACTIVITY" ]; then
    echo -e "${YELLOW}No sync parameters provided.${NC}"
    echo ""
    show_usage
    exit 1
fi

# Show sync summary
show_sync_summary

# Build and execute command
DRUSH_CMD=$(build_drush_command)

if [ "$VERBOSE" = true ]; then
    echo -e "${BLUE}Executing: $DRUSH_CMD${NC}"
    echo ""
fi

# Execute the sync
if [ "$DRY_RUN" = true ]; then
    echo -e "${YELLOW}=== DRY RUN MODE - No changes will be made ===${NC}"
fi

eval "$DRUSH_CMD"

# Show completion message
if [ $? -eq 0 ]; then
    if [ "$DRY_RUN" = true ]; then
        echo -e "${GREEN}Dry run completed successfully!${NC}"
    else
        echo -e "${GREEN}Sync completed successfully!${NC}"
        echo ""
        echo "Next steps:"
        echo "1. Clear caches: drush cr"
        echo "2. Check site functionality"
        echo "3. Run again with different parameters if needed"
        echo ""
        echo "Other useful commands:"
        echo "- Check full migration status: $SCRIPT_DIR/migration-status-check.sh"
        echo "- Run full migration: $SCRIPT_DIR/migrate-execute.sh"
        echo "- Complete system setup: $SCRIPT_DIR/setup-complete-migration.sh"
        echo "- Validate system: drush php:script $SCRIPT_DIR/validate-migration.php"
    fi
else
    echo -e "${RED}Sync failed. Check the output above for errors.${NC}"
    exit 1
fi