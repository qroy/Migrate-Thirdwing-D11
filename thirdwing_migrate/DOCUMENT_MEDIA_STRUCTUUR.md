# Document Media Entity - Structuur Uitleg

## ✅ Alle Document Migraties → ÉÉN Media Type

Je hebt **5 document migraties**, maar die gaan allemaal naar **één Document media entity**. Ze verschillen alleen in:
1. **Bron** (waar komt het document vandaan)
2. **Document type** (classificatie veld)
3. **Toegangsrechten** (welke rollen mogen het zien)

## 📊 Overzicht Document Migraties

| Migratie | Bron | Document Type | Toegang |
|----------|------|---------------|---------|
| **thirdwing_media_document_verslag** | Verslag content type | `verslag` | Uit taxonomy |
| **thirdwing_media_document_bandpartituur** | field_partij_band (Repertoire) | `bandpartituur` | Band, Muziekcommissie |
| **thirdwing_media_document_koorpartituur** | field_partij_koor_l (Repertoire) | `koorpartituur` | Leden, Aspirant-Leden, MC |
| **thirdwing_media_document_koorregie** | field_partij_tekst (Repertoire) | `koorregie` | Leden, Aspirant-Leden, MC |
| **thirdwing_media_document_general** | field_files (div. content types) | `overige` | Uit parent content |

## 🗂️ Document Media Entity Structuur

**Destination:** Allemaal `entity:media` met `bundle: document`

**Velden:**
```yaml
name: Media naam
field_media_document: Het document bestand (file reference)
field_document_type: List veld met opties:
  - verslag
  - bandpartituur
  - koorpartituur
  - koorregie
  - huiswerk
  - overige
field_datum: Datum (optioneel)
field_verslag_type: List veld (alleen voor verslag)
field_toegang: Taxonomy term reference
field_repertoire: Entity reference (alleen voor partituren)
field_activiteit: Entity reference (optioneel)
```

## 🔄 Migratie Flow

### 1. Verslag Content Type
```
D6: Verslag node (nid:123, title:"Bestuursvergadering 2024")
   └─ field_files → bestand.pdf
   
D11: Document media (mid:123)
   ├─ name: "Bestuursvergadering 2024"
   ├─ field_media_document: bestand.pdf
   ├─ field_document_type: "verslag"
   ├─ field_datum: 2024-01-15
   └─ field_toegang: [bestuur]
```

### 2. Repertoire Partituren
```
D6: Repertoire node (nid:456, title:"Amazing Grace")
   ├─ field_partij_band → band.pdf
   ├─ field_partij_koor_l → koor.pdf
   └─ field_partij_tekst → tekst.pdf
   
D11: 3 Document media entities:
   
   Media 1 (mid:bp_123):
   ├─ name: "Amazing Grace - Bandpartituur"
   ├─ field_media_document: band.pdf
   ├─ field_document_type: "bandpartituur"
   ├─ field_repertoire: → node 456 (Amazing Grace)
   └─ field_toegang: [band, muziekcommissie]
   
   Media 2 (mid:kp_124):
   ├─ name: "Amazing Grace - Koorpartituur"
   ├─ field_media_document: koor.pdf
   ├─ field_document_type: "koorpartituur"
   ├─ field_repertoire: → node 456 (Amazing Grace)
   └─ field_toegang: [leden, aspirant_leden, muziekcommissie]
   
   Media 3 (mid:kr_125):
   ├─ name: "Amazing Grace - Koorregie"
   ├─ field_media_document: tekst.pdf
   ├─ field_document_type: "koorregie"
   ├─ field_repertoire: → node 456 (Amazing Grace)
   └─ field_toegang: [leden, aspirant_leden, muziekcommissie]
```

### 3. Algemene Bijlagen (field_files)
```
D6: Activiteit node
   └─ field_files → bijlage.pdf
   
D11: Document media
   ├─ name: "bijlage.pdf"
   ├─ field_media_document: bijlage.pdf
   ├─ field_document_type: "overige"
   └─ field_toegang: (wordt gekoppeld via parent)
```

## 🎯 In D11 Aanmaken

Je maakt **ÉÉN** Document media type aan met:

**Machine name:** `document`

**Fields:**
1. `field_media_document` (File) - standaard media source field
2. `field_document_type` (List text) - **Verplicht**
   - Opties: verslag, bandpartituur, koorpartituur, koorregie, huiswerk, overige
3. `field_datum` (Date) - Optioneel
4. `field_verslag_type` (List text) - Optioneel, alleen voor type "verslag"
5. `field_toegang` (Entity reference: taxonomy_term) - Optioneel
6. `field_repertoire` (Entity reference: node:repertoire) - Optioneel
7. `field_activiteit` (Entity reference: node:activiteit) - Optioneel

## ✅ Voordelen van Deze Aanpak

**Alle documenten zijn één media type:**
- ✅ Consistent beheer
- ✅ Uniform zoeken
- ✅ Dezelfde workflows
- ✅ Gedeelde display modes

**Maar wel onderscheiden via field_document_type:**
- ✅ Verschillende toegangsrechten per type
- ✅ Verschillende display per type
- ✅ Filteren op document type
- ✅ Type-specifieke validatie

## 🚀 Migratie Volgorde

Belangrijk: partituren MOETEN **na** Repertoire nodes:

```bash
# 1. Eerst basis media
drush migrate:import thirdwing_media_document_general
drush migrate:import thirdwing_media_document_verslag

# 2. Dan Repertoire nodes
drush migrate:import thirdwing_node_repertoire

# 3. Dan partituren (hebben repertoire nodig voor field_repertoire)
drush migrate:import thirdwing_media_document_bandpartituur
drush migrate:import thirdwing_media_document_koorpartituur
drush migrate:import thirdwing_media_document_koorregie
```

Dit staat al correct in `migrate.sh`! 🎉
