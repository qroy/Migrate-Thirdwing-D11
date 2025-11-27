# Correctie: Document Content Type Verwijderd

## ✅ Opgeschoond

Het bestand `thirdwing_node_document.yml` is **verwijderd** omdat er volgens je Excel spreadsheet **geen** "Document" content type bestaat in D6.

## 📊 Correcte Content Types (7 stuks)

Volgens je Excel spreadsheet heb je deze content types:
1. ✅ Activiteit
2. ✅ Nieuws  
3. ✅ Pagina
4. ✅ Album (was: Foto)
5. ✅ Locatie
6. ✅ Programma
7. ✅ Repertoire

**Plus:**
- User fields (was: Profiel content type → wordt nu user entity)

## 🚫 Content Types die VERVALLEN (worden Media)

Volgens je Excel spreadsheet worden deze content types **niet** als content gemigr eerd:
1. ❌ Audio → wordt Audio media entity
2. ❌ Video → wordt Remote Video media entity
3. ❌ Verslag → wordt Document media entity

## 📝 Totaal Aantal Migraties: 18

**Taxonomie:** 1
- toegang

**Users:** 1  
- users + profiel velden

**Files:** 1

**Media:** 6
- image
- audio (was content type)
- remote_video (was content type)
- document_verslag (was content type)
- document_general
- + 3 partituren (band/koor/koorregie)

**Content:** 7 (correct volgens Excel!)
1. Locatie
2. Programma
3. Repertoire
4. Nieuws
5. Pagina
6. Activiteit
7. Album

**Speciale Document Media:** 3
- bandpartituur
- koorpartituur
- koorregie

## ✅ Scripts Geüpdatet

- ✅ migrate.sh: Document verwijderd
- ✅ rollback.sh: Document verwijderd
- ✅ thirdwing_node_document.yml: Verwijderd

## 🎯 Klaar voor Productie

De module bevat nu **exact** de content types uit je Excel spreadsheet, geen meer en geen minder!
