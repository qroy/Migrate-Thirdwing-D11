#!/bin/bash
# File: modules/custom/thirdwing_migrate/scripts/cleanup-obsolete-media-files.sh

# Script to remove obsolete media bundle files and update architecture
# Run from Drupal root directory

echo "🧹 Cleaning up obsolete media bundle files..."

# Define base path
BASE_PATH="modules/custom/thirdwing_migrate"

# Files to remove (obsolete sheet music bundle)
OBSOLETE_FILES=(
    "$BASE_PATH/config/install/migrate_plus.migration.d6_thirdwing_media_sheet_music.yml"
    "$BASE_PATH/src/Plugin/migrate/source/D6ThirdwingSheetMusic.php"
)

# Remove obsolete files
echo "📁 Removing obsolete files:"
for file in "${OBSOLETE_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "   ❌ Removing: $file"
        rm "$file"
    else
        echo "   ⚠️  File not found: $file"
    fi
done

# Update existing migration files that reference sheet music bundle
echo ""
echo "🔄 Updating migration group configuration..."

# Update migration group to remove sheet music references
GROUP_FILE="$BASE_PATH/config/install/migrate_plus.migration_group.thirdwing_d6.yml"
if [ -f "$GROUP_FILE" ]; then
    echo "   ✏️  Updating migration group configuration"
    # Remove any references to sheet music migrations
    sed -i '/sheet_music/d' "$GROUP_FILE"
else
    echo "   ⚠️  Migration group file not found"
fi

echo ""
echo "🔧 Updating content type field configurations..."

# Update content type scripts to use media reference fields
CONTENT_TYPES_SCRIPT="$BASE_PATH/scripts/create-content-types-and-fields.php"
if [ -f "$CONTENT_TYPES_SCRIPT" ]; then
    echo "   ✏️  Updating content types to use media reference fields"
    # This will be handled manually as it requires PHP code changes
    echo "   📝 Manual update required for content type media reference fields"
else
    echo "   ⚠️  Content types script not found"
fi

echo ""
echo "📋 Creating backup of removed files list..."

# Create a backup log of what was removed
BACKUP_LOG="$BASE_PATH/REMOVED_FILES_$(date +%Y%m%d_%H%M%S).log"
cat > "$BACKUP_LOG" << EOF
# Obsolete Media Bundle Files Removed - $(date)

## Files Removed:
$(printf '%s\n' "${OBSOLETE_FILES[@]}")

## Reason for Removal:
- Sheet music bundle consolidated into document bundle
- MuseScore files (.mscz) moved to document bundle  
- MIDI files (.mid, .kar) moved to audio bundle
- Simplified 4-bundle architecture: image, document, audio, video

## Migration Path:
- Sheet music files now handled by document bundle with field_document_soort = "partituur"
- All repertoire-related files link via field_gerelateerd_repertoire
- Access control maintained via field_toegang

## Next Steps:
1. Run new media bundle setup script
2. Update content type media reference fields  
3. Test migration with new bundle architecture
4. Verify file categorization logic
EOF

echo "   📄 Backup log created: $BACKUP_LOG"

echo ""
echo "✅ Cleanup completed successfully!"
echo ""
echo "📋 Summary of changes:"
echo "   • Removed obsolete sheet music bundle files"
echo "   • Updated migration group configuration"  
echo "   • Created backup log of removed files"
echo ""
echo "🔧 Next steps:"
echo "   1. Run: drush php:script $BASE_PATH/scripts/create-media-bundles-and-fields.php"
echo "   2. Update content type media reference fields"
echo "   3. Test new migration configurations"
echo "   4. Verify file directory permissions"