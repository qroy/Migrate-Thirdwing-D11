#!/bin/bash

# ThirdWing Migration Rollback Script
# Draait alle migraties terug in omgekeerde volgorde

set -e

echo "=========================================="
echo "ThirdWing Migratie Rollback"
echo "=========================================="
echo ""
echo "WAARSCHUWING: Dit verwijdert alle gemigreerde content!"
echo ""
read -p "Weet je zeker dat je wilt doorgaan? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Rollback geannuleerd."
    exit 0
fi

echo ""

# Functie voor rollback
rollback_migration() {
    local migration_id=$1
    local description=$2
    
    echo ">>> Rollback: $description ($migration_id)"
    
    if drush migrate:status "$migration_id" &>/dev/null; then
        drush migrate:rollback "$migration_id"
        echo "✓ Teruggedraaid: $migration_id"
    else
        echo "⚠ Migratie $migration_id niet gevonden"
    fi
    
    echo ""
}

# Rollback in omgekeerde volgorde (LIFO)

echo "Stap 1: Content terugdraaien"
echo "----------------------------"
rollback_migration "thirdwing_node_album" "Content - Album"
rollback_migration "thirdwing_node_activiteit" "Content - Activiteit"
rollback_migration "thirdwing_node_pagina" "Content - Pagina"
rollback_migration "thirdwing_node_nieuws" "Content - Nieuws"
rollback_migration "thirdwing_node_repertoire" "Content - Repertoire"
rollback_migration "thirdwing_node_programma" "Content - Programma"
rollback_migration "thirdwing_node_locatie" "Content - Locatie"

echo "Stap 2: Partituren terugdraaien"
echo "--------------------------------"
rollback_migration "thirdwing_media_document_koorregie" "Document - Koorregie"
rollback_migration "thirdwing_media_document_koorpartituur" "Document - Koorpartituur"
rollback_migration "thirdwing_media_document_bandpartituur" "Document - Bandpartituur"

echo "Stap 3: Media terugdraaien"
echo "--------------------------"
rollback_migration "thirdwing_media_video" "Media - Video"
rollback_migration "thirdwing_media_audio" "Media - Audio"
rollback_migration "thirdwing_media_document_verslag" "Media - Verslag"
rollback_migration "thirdwing_media_document_general" "Media - Documents (general)"
rollback_migration "thirdwing_media_image" "Media - Images"

echo "Stap 4: Bestanden terugdraaien"
echo "------------------------------"
rollback_migration "thirdwing_file" "Files"

echo "Stap 5: Gebruikers terugdraaien"
echo "-------------------------------"
rollback_migration "thirdwing_user" "Users"

echo "Stap 6: Taxonomieën terugdraaien"
echo "--------------------------------"
rollback_migration "thirdwing_taxonomy_toegang" "Toegang Taxonomy"

echo "Stap 7: User Roles terugdraaien"
echo "-------------------------------"
rollback_migration "d6_user_role" "User Roles"

echo "=========================================="
echo "Rollback voltooid!"
echo "=========================================="
echo ""
