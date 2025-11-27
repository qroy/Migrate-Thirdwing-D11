# Thirdwing - Site Navigation Structure Documentation

## Overview
The Thirdwing website uses Drupal's menu system to organize navigation across different user types and content areas. Based on the database structure analysis, the site employs multiple menu systems to serve different audiences and functions.

## Menu System Architecture

### Core Menu Types
From the `menu_custom` table analysis, the site uses several distinct menu systems:

#### 1. **Primary Navigation (Hoofdmenu)**
- **Menu Name:** `primary-links`
- **Purpose:** Main site navigation visible to all users
- **Typical Location:** Top navigation bar
- **Content:** Major site sections and primary user pathways

#### 2. **Secondary Navigation**
- **Menu Name:** `secondary-links`
- **Purpose:** Supporting navigation for less prominent but important links
- **Typical Location:** Secondary header area or sidebar
- **Content:** Utility links, user account options

#### 3. **Footer Navigation Systems**
Multiple footer menus serve different purposes:

##### Footer Main Menu
- **Menu Name:** `menu-footer`
- **Title:** "Footer Hoofdmenu"
- **Content:** Primary footer navigation links

##### Footer Service Menu
- **Menu Name:** `menu-footer-service`
- **Title:** "Footer Service"
- **Content:** Service-related links for members

##### Footer Visitor Service Menu
- **Menu Name:** `menu-footer-service-visitor`
- **Title:** "Footer Service Visitors"
- **Content:** Service links specifically for visitors and non-members

#### 4. **Social Navigation**
- **Menu Name:** `menu-social`
- **Title:** "Social"
- **Content:** Links to social media platforms and sharing options

#### 5. **Call to Action Menu**
- **Menu Name:** `menu-startpagina-rec`
- **Title:** "Call to Action"
- **Content:** Prominent action items for the homepage

#### 6. **Administrative Menus**
- **Menu Name:** `admin`
- **Title:** "Admin"
- **Content:** Administrative functionality links

- **Menu Name:** `devel`
- **Title:** "Development"
- **Content:** Development and debugging tools

#### 7. **System Navigation**
- **Menu Name:** `navigation`
- **Title:** "Navigation"
- **Content:** Core Drupal navigation with personalized links for authenticated users

## Content-Based Navigation Structure

### Primary Content Sections
Based on the content types identified, the site likely organizes navigation around these key areas:

#### **Public-Facing Sections**
1. **Home/Start** - Landing page with calls to action
2. **About** - Information about Thirdwing choir
3. **Nieuws** - News and announcements
4. **Activiteiten** - Events and performances
5. **Repertoire** - Musical repertoire showcase
6. **Foto's** - Photo galleries
7. **Video's** - Video content
8. **Contact** - Contact information and forms

#### **Member-Specific Sections**
1. **Leden Area** - Member-only content
2. **Profiel** - Member profiles
3. **Activiteiten** - Detailed activity information with logistics
4. **Repertoire** - Full repertoire with member details
5. **Nieuws** - Member-specific news
6. **Documenten** - Member resources and files

#### **Administrative Sections**
1. **Beheer** - Content management
2. **Gebruikers** - User management
3. **Rapporten** - Reports and analytics
4. **Instellingen** - Site configuration

## Navigation Hierarchy and Access Control

### User Role-Based Navigation
The navigation system adapts based on user roles:

#### **Anonymous Users (Visitors)**
- **Access:** Public content only
- **Navigation:** Basic site structure
- **Menus:** Primary links, footer visitor services
- **Content:** News, general activity info, contact forms

#### **Authenticated Users**
- **Access:** Enhanced navigation with personalized elements
- **Navigation:** Expanded menu options
- **Menus:** Primary links, secondary links, personalized navigation
- **Content:** Member-specific news, extended activity details

#### **Members (Lid)**
- **Access:** Member-specific sections
- **Navigation:** Full member navigation
- **Menus:** All public menus plus member-specific options
- **Content:** Member profiles, detailed activities, internal news

#### **Committee Members**
- **Access:** Committee-specific navigation
- **Navigation:** Role-based menu extensions
- **Menus:** Additional committee-specific navigation items
- **Content:** Committee-specific management tools

#### **Administrators (Beheerder)**
- **Access:** Full site navigation
- **Navigation:** Complete navigation hierarchy
- **Menus:** All menus including admin and development
- **Content:** Full content management capabilities

## URL Structure and Clean URLs

### URL Alias System
The site uses Drupal's URL alias system to create SEO-friendly URLs:

#### **Content Type URL Patterns**
- **News:** `/nieuws/[title]`
- **Activities:** `/activiteiten/[title]`
- **Profiles:** `/profiel/[name]`
- **Repertoire:** `/repertoire/[title]`
- **Photos:** `/fotos/[title]`
- **Videos:** `/videos/[title]`
- **Pages:** `/[page-name]`

#### **Special Section URLs**
- **Member Area:** `/leden/`
- **Admin Area:** `/admin/`
- **Reports:** `/rapporten/`
- **Contact:** `/contact/`

## Responsive Navigation Design

### Mobile Navigation
The site likely implements responsive navigation patterns:

#### **Mobile Menu Structure**
- **Hamburger Menu:** Collapsible main navigation
- **Priority Items:** Most important links remain visible
- **Progressive Enhancement:** Touch-friendly navigation elements

#### **Desktop Navigation**
- **Horizontal Menu Bar:** Primary navigation
- **Dropdown Menus:** Secondary navigation items
- **Breadcrumbs:** Hierarchical navigation aid
- **Sidebar Navigation:** Context-specific navigation

## Special Navigation Features

### Contextual Navigation
The site includes specialized navigation based on content:

#### **Activity-Specific Navigation**
- **Event Details:** Navigation within activity pages
- **Registration Links:** Direct action items
- **Related Events:** Cross-navigation between activities

#### **Member Profile Navigation**
- **Profile Sections:** Navigation within member profiles
- **Committee Links:** Direct access to committee information
- **Contact Options:** Member-to-member communication

#### **Content Management Navigation**
- **Workflow Navigation:** Content approval processes
- **Revision History:** Version control navigation
- **Bulk Operations:** Administrative navigation tools

## Integration with Drupal Features

### Block System Integration
Navigation integrates with Drupal's block system:

#### **Menu Blocks**
- **Primary Menu Block:** Main navigation rendering
- **Secondary Menu Block:** Supporting navigation
- **Footer Menu Blocks:** Multiple footer navigation areas
- **Social Menu Block:** Social media integration

#### **Custom Navigation Blocks**
- **User-Specific Navigation:** Personalized menu options
- **Role-Based Navigation:** Committee-specific links
- **Context-Sensitive Navigation:** Content-aware navigation

### Taxonomy Integration
Navigation connects with the site's taxonomy system:

#### **Content Categorization**
- **Audience Categories:** Member/Visitor/Committee navigation
- **Activity Types:** Event-based navigation
- **Content Topics:** Subject-based navigation organization

## Maintenance and Management

### Navigation Administration
The site provides tools for navigation management:

#### **Menu Management**
- **Menu Item Creation:** Adding new navigation items
- **Hierarchy Management:** Organizing menu structure
- **Access Control:** Role-based menu visibility
- **URL Management:** Clean URL configuration

#### **Performance Considerations**
- **Menu Caching:** Optimized menu rendering
- **Progressive Loading:** Efficient navigation loading
- **Mobile Optimization:** Touch-friendly navigation

## Summary

The Thirdwing website employs a sophisticated navigation system that serves multiple user types through carefully organized menu structures. The system balances public accessibility with member-specific functionality while maintaining clear information architecture for a choir organization's diverse needs.

**Key Navigation Principles:**
- **User-Centric Design:** Navigation adapts to user roles and permissions
- **Content-Driven Structure:** Navigation reflects the choir's organizational needs
- **Responsive Implementation:** Works effectively across all devices
- **Administrative Flexibility:** Easy to maintain and update
- **SEO Optimization:** Clean URLs and logical structure