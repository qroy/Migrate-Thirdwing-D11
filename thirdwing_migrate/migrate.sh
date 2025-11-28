#!/bin/bash

# ThirdWing Migration Script
# Voert alle migraties uit in de juiste volgorde

set -e  # Stop bij fouten

echo "=========================================="
echo "ThirdWing D6 naar D11 Migratie"
echo "=========================================="
echo ""

# Functie voor het uitvoeren van een migratie
run_migration() {
    local migration_id=$1
    local description=$2
    
    echo ">>> Migratie: $description ($migration_id)"
    
    if drush migrate:status "$migration_id" &>/dev/null; then
        drush migrate:import "$migration_id" --feedback="100 items" --update
        echo "✓ Voltooid: $migration_id"
    else
        echo "⚠ Migratie $migration_id niet gevonden"
    fi
    
    echo ""
}

# Check of drush beschikbaar is
if ! command -v drush &> /dev/null; then
    echo "Fout: Drush is niet gevonden. Installeer drush eerst."
    exit 1
fi

echo "Stap 1: User Roles"
echo "-------------------"
run_migration "d6_user_role" "User Roles"

echo "Stap 2: Taxonomieën"
echo "-------------------"
run_migration "thirdwing_taxonomy_toegang" "Toegang Taxonomy"
# Voeg hier andere taxonomieën toe
# run_migration "thirdwing_taxonomy_document_type" "Document Type Taxonomy"

echo "Stap 3: Gebruikers"
echo "-------------------"
run_migration "thirdwing_user" "Users met profiel velden"

echo "Stap 4: Bestanden"
echo "-------------------"
run_migration "thirdwing_file" "Files"

echo "Stap 5: Media"
echo "-------------------"
run_migration "thirdwing_media_image" "Media - Images"
run_migration "thirdwing_media_document_general" "Media - Documents (general)"
run_migration "thirdwing_media_document_verslag" "Media - Documents (Verslag)"
run_migration "thirdwing_media_audio" "Media - Audio (was: Audio content type)"
run_migration "thirdwing_media_video" "Media - Remote Video (was: Video content type)"

echo "Stap 6: Content"
echo "-------------------"
run_migration "thirdwing_node_locatie" "Content - Locatie"
run_migration "thirdwing_node_programma_to_repertoire" "Content - Programma (→Repertoire)"
run_migration "thirdwing_node_repertoire" "Content - Repertoire"
run_migration "thirdwing_node_nieuws" "Content - Nieuws"
run_migration "thirdwing_node_pagina" "Content - Pagina"
run_migration "thirdwing_node_activiteit" "Content - Activiteit"
run_migration "thirdwing_node_album" "Content - Album (was: Foto)"

echo "Stap 7: Partituren (Document media van Repertoire)"
echo "---------------------------------------------------"
run_migration "thirdwing_media_document_bandpartituur" "Document - Bandpartituur"
run_migration "thirdwing_media_document_koorpartituur" "Document - Koorpartituur"
run_migration "thirdwing_media_document_koorregie" "Document - Koorregie"

echo "=========================================="
echo "Migratie voltooid!"
echo "=========================================="
echo ""
echo "Controleer de status met:"
echo "  drush migrate:status --group=thirdwing"
echo ""
echo "Bekijk eventuele fouten met:"
echo "  drush migrate:messages [migration-id]"
echo ""
