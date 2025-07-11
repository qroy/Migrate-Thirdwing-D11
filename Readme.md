# Updated README Section - Profile Migration Architecture

## Content Profile System ✅ **CORRECTLY IMPLEMENTED**

### D6 Content Profile Integration

The D6 site uses the **Content Profile** module where profile data is stored as `profiel` content type nodes linked to users. The migration system properly handles this by:

- **Reading from `content_type_profiel` table**: Profile field data from CCK/content type table
- **User-Profile Relationships**: Links between users and their profile nodes via `uid` field  
- **Modern D11 Migration**: Converts profile nodes to user profile fields (not separate content)
- **Complete Field Preservation**: All Dutch profile fields and function fields are preserved

### ✅ **FIXED ARCHITECTURE**

```yaml
D6 Content Profile → D11 User Profile Fields Migration:

Source (D6):
├── content_type_profiel table (CCK data)
├── Profile nodes (type: profiel) 
└── User accounts linked via uid

Destination (D11):
├── User accounts with profile fields ✅
├── No separate profile content type ✅  
└── Modern Drupal user profile approach ✅
```

### Profile Field Mapping ✅

**Complete mapping of D6 Content Profile fields to D11 user fields:**

#### Personal Information
- `field_voornaam` → First name
- `field_achternaam` → Last name
- `field_tussenvoegsel` → Name prefix (voorvoegsel)
- `field_geslacht` → Gender (m/v)
- `field_geboortedatum` → Birth date

#### Contact Information  
- `field_adres` → Address
- `field_postcode` → Postal code
- `field_woonplaats` → City
- `field_telefoon` → Phone
- `field_mobiel` → Mobile

#### Choir Membership
- `field_lidsinds` → Member since date
- `field_uitkoor` → Left choir date
- `field_koor` → Choir function
- `field_positie` → Voice position (soprano/alt/tenor/bas)
- `field_karrijder` → Car driver (transport)
- `field_sleepgroep` → Transport group

#### Committee Functions
- `field_functie_bestuur` → Board function
- `field_functie_mc` → Music committee function
- `field_functie_concert` → Concert committee function
- `field_functie_feest` → Party committee function
- `field_functie_regie` → Direction committee function
- `field_functie_ir` → Internal relations committee function
- `field_functie_pr` → Public relations committee function
- `field_functie_tec` → Technical committee function
- `field_functie_lw` → Member recruitment committee function
- `field_functie_fl` → Facilities committee function

#### Administrative
- `field_emailbewaking` → Email monitoring
- `field_notes` → Administrative notes

### Migration Implementation ✅

#### Source Plugins (Content Profile Integration)
```php
// D6ThirdwingUser.php and D6IncrementalUser.php
protected function addContentProfileFields(Row $row, $uid) {
  // Find profile node for this user
  $profile_query = $this->select('node', 'n')
    ->condition('n.type', 'profiel')
    ->condition('n.uid', $uid);
    
  // Read from content_type_profiel table
  $content_query = $this->select('content_type_profiel', 'ct')
    ->condition('ct.nid', $profile_node['nid']);
    
  // Map all field_* columns to user properties
}
```

#### Migration Configuration
```yaml
# d6_thirdwing_user.yml
destination:
  plugin: 'entity:user'  # ✅ Users, not nodes

process:
  # All profile fields mapped to user fields
  field_voornaam: field_voornaam_value
  field_achternaam: field_achternaam_value
  # ... complete field mapping
```

### Setup Scripts ✅

#### 1. Content Types (Profile Removed)
```bash
# create-content-types-and-fields.php
# ✅ FIXED: 'profiel' content type REMOVED
# Only creates actual content types (activiteit, nieuws, etc.)
```

#### 2. User Profile Fields  
```bash
# user-profile-fields.php  
# ✅ NEW: Creates user profile fields for Content Profile migration
# Creates all profile fields as user fields
```

### Architecture Benefits ✅

1. **Modern Drupal Approach**: User profile fields instead of separate profile content
2. **Data Preservation**: All D6 Content Profile data is completely preserved
3. **Clean Architecture**: No unnecessary content types or complex relationships
4. **Incremental Migration Support**: Profile fields update during incremental syncs
5. **Access Control Ready**: User profile fields work with standard Drupal permissions

### Validation ✅

**Migration Results:**
- ✅ Users have complete profile information as fields
- ✅ No separate `profiel` content type created
- ✅ All Dutch field names preserved for consistency
- ✅ Committee function fields properly migrated
- ✅ Administrative and membership data preserved

**What Was Fixed:**
- ❌ **REMOVED**: Obsolete `profiel` content type creation
- ❌ **REMOVED**: Profile node migration (unnecessary)
- ✅ **ADDED**: User profile field creation script
- ✅ **IMPROVED**: Clear separation between content and user data