# Drupal 6 Workflow States Reference - thirdwing_nl

## Quick Overview
- **5 workflows** total (1 disabled)
- **17 content types** with workflow enabled
- **Role-based permissions** for state transitions
- **Dutch language** state names

## Workflow 1 - General Content Workflow
**States:**
1. **(creation)** (SID: 1) - Initial creation state
2. **Concept** (SID: 2) - Draft/concept state
3. **Gepubliceerd** (SID: 3) - Published state
4. **Archief** (SID: 4) - Archived state  
5. **Prullenmand** (SID: 8) - Trash/deleted state
6. **Aangeraden** (SID: 9) - Recommended/featured state

**Allowed Transitions:**
- From (creation): → Concept, Gepubliceerd, Aangeraden
- From Concept: → Gepubliceerd, Aangeraden, Prullenmand
- From Gepubliceerd: → Archief, Aangeraden, Prullenmand
- From Aangeraden: → Gepubliceerd, Archief, Prullenmand
- From Archief: → Gepubliceerd, Aangeraden, Prullenmand
- From Prullenmand: → Concept, Gepubliceerd, Aangeraden, Archief

## Workflow 2 - Simple Publication Workflow (Disabled)
**States:**
1. **(aanmaak)** (SID: 5) - Creation state (disabled)
2. **Concept** (SID: 6) - Draft state (disabled)
3. **Gepubliceerd** (SID: 7) - Published state (disabled)

*Note: This workflow appears to be disabled (status = 0)*

## Workflow 3 - Activity/Event Workflow  
**States:**
1. **(aanmaak)** (SID: 10) - Creation state
2. **Actief** (SID: 11) - Active state
3. **Verlopen** (SID: 12) - Expired state
4. **Inactief** (SID: 13) - Inactive state

**Allowed Transitions:**
- From (aanmaak): → Actief, Verlopen, Inactief
- From Actief: → Verlopen, Inactief
- From Verlopen: → Actief, Inactief
- From Inactief: → Actief, Verlopen

## Workflow 4 - Extended Content Workflow
**States:**
1. **(aanmaak)** (SID: 14) - Creation state
2. **Concept** (SID: 15) - Draft/concept state
3. **Prullenmand** (SID: 16) - Trash state
4. **Aangeraden** (SID: 17) - Recommended state
5. **Archief** (SID: 18) - Archive state
6. **Geen Archief** (SID: 19) - No archive state
7. **Gepubliceerd** (SID: 20) - Published state

**Allowed Transitions:**
- From (aanmaak): → Concept, Archief, Aangeraden, Geen Archief, Gepubliceerd
- From Concept: → Archief, Aangeraden, Geen Archief, Prullenmand, Gepubliceerd
- From Gepubliceerd: → Concept, Aangeraden, Archief, Geen Archief, Prullenmand
- From Archief: → Concept, Aangeraden, Geen Archief, Prullenmand, Gepubliceerd
- From Aangeraden: → Concept, Archief, Geen Archief, Prullenmand, Gepubliceerd
- From Geen Archief: → Concept, Archief, Aangeraden, Prullenmand, Gepubliceerd
- From Prullenmand: → Concept, Archief, Aangeraden, Geen Archief, Gepubliceerd

## Workflow 5 - Simple Featured Content Workflow
**States:**
1. **(aanmaak)** (SID: 21) - Creation state
2. **Gepubliceerd** (SID: 22) - Published state
3. **Aangeraden** (SID: 23) - Recommended/featured state

**Allowed Transitions:**
- From (aanmaak): → Gepubliceerd, Aangeraden
- From Gepubliceerd: → Aangeraden
- From Aangeraden: → Gepubliceerd

## Content Types with Workflow Configuration

The following content types have workflow settings enabled:

1. **activiteit** (Activities/Performances/Rehearsals)
2. **audiovideo** (Audio/Video content)
3. **image** (Images)
4. **knipsel** (News clippings)
5. **locatie** (Locations)
6. **nieuws** (News)
7. **nieuwsbrief** (Newsletter)
8. **node_gallery_gallery** (Image galleries)
9. **node_gallery_image** (Gallery images)
10. **pagina** (Pages)
11. **positie** (Positions)
12. **prikker** (Bulletins/Notices)
13. **profiel** (User profiles)
14. **programma** (Programs)
15. **repertoire** (Song repertoire)
16. **verslag** (Meeting minutes/Reports)
17. **vriend** (Friends/Sponsors)

## Role-Based Permissions

The workflow transitions are controlled by role IDs:
- **author** - Content authors
- **6** - Likely site administrators or editors
- **12** - Possibly moderators or reviewers

## Key Features

- **Multiple workflow types** for different content categories
- **Comprehensive state management** including drafts, published, archived, featured, and trash states
- **Flexible transitions** allowing content to move between various states
- **Role-based access control** for state transitions
- **Special states** like "Aangeraden" (Recommended/Featured) for content promotion