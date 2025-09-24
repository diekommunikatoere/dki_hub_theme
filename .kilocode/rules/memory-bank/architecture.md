# Architecture — System Design

## Overview

WordPress theme with PHP templates and modular Gutenberg blocks architecture. Each block is self-contained with individual build processes for maximum maintainability. Includes a React-based admin application for FAQ management.

## Directory Structure

```
/
├── functions.php                    # Theme entry point, asset registration, search extensions
├── admin-faq-editor/                # React-based admin application for FAQ management
│   ├── src/
│   │   ├── components/              # React components (FAQEditor, SectionAccordion, FAQItemAccordion, CreateFAQForm, CreateSectionForm, WYSIWYGEditor, ErrorMessage, LoadingSpinner)
│   │   ├── context/                 # React context for FAQ data management (FAQContext)
│   │   ├── services/                # API service for FAQ sections and items (api.ts)
│   │   ├── styles/                  # SCSS styles (_mixins, _variables, index)
│   │   └── types/                   # TypeScript type definitions (index)
│   ├── package.json
│   ├── tsconfig.json
│   └── webpack.config.js
├── blocks/                          # Custom Gutenberg blocks
│   ├── copy-field/
│   ├── faq-display/
│   ├── faq-search/
│   ├── header-navigation/
│   ├── login-form/
│   ├── profile-nav-item/
│   ├── profile-sidebar-nav/
│   ├── revisions-profile-overview/
│   ├── schulungen-query-loop/
│   ├── schulungen-read-status-widget/
│   ├── view-revisions/
│   └── edit-docs-page/               # Additional blocks following same structure
├── includes/
│   ├── assets/
│   │   ├── css/                     # Compiled stylesheets (main.css, styles.css, modules/)
│   │   │   ├── modules/             # Component-specific CSS (archive.css, header.css, etc.)
│   │   │   ├── admin/               # Admin styles (faq_admin.css, faq-editor.css)
│   │   │   └── block-variations/    # Block variation styles
│   │   ├── js/                      # JavaScript modules
│   │   │   └── admin/faq-editor/    # Compiled admin FAQ editor (faq-editor.js)
│   │   ├── scss/                    # Source SCSS files
│   │   │   ├── config/              # Variables, fonts, normalize (_variables.scss, _fonts.scss, normalize.css)
│   │   │   ├── modules/             # Component-specific styles (archive.scss, login.scss, etc.)
│   │   │   ├── admin/               # Admin SCSS (faq_admin.scss)
│   │   │   └── block-variations.scss
│   │   └── fonts/                   # Custom font files (Gibson, Gill Sans subsets in multiple formats)
│   ├── core/                        # Core functionality
│   │   ├── allow-svg.php
│   │   ├── blocks.php
│   │   ├── favicon.php
│   │   ├── init.php
│   │   └── user-block-permissions.php
│   │   └── block-variations/
│   │       ├── button.js
│   │       └── init.php
│   ├── features/                    # Feature-specific includes
│   │   ├── init.php
│   │   ├── faq/
│   │   │   ├── faq-editor-admin-page.php
│   │   │   ├── faq-rest-api.php
│   │   │   └── register-cpt-faq.php # CPT and taxonomy registration
│   │   └── schulungen/
│   │       └── schulungen.php
├── templates/                       # WordPress template files
│   ├── archive-faq.php
│   ├── single-faq.php
│   ├── profile.html
│   └── index.html
└── parts/                           # Reusable template parts
    └── header.html
```

## Block Architecture

Each block follows a consistent structure:

- [`src/block.json`](blocks/login-form/src/block.json) - Block configuration and metadata
- [`src/index.js`](blocks/login-form/src/index.js) - Block registration
- [`src/edit.js`](blocks/login-form/src/edit.js) - Editor interface
- [`src/view.js`](blocks/login-form/src/view.js) - Frontend JavaScript (optional)
- [`src/render.php`](blocks/login-form/src/render.php) - Server-side rendering (optional)
- [`src/style.scss`](blocks/login-form/src/style.scss) - Block-specific styles
- [`src/editor.scss`](blocks/login-form/src/editor.scss) - Editor-only styles
- [`package.json`](blocks/login-form/package.json) - Build configuration
- `readme.txt` and main PHP file for some blocks

## Design Principles

### Modularity

- Each block is self-contained with its own build process
- Blocks can be developed and deployed independently
- Shared dependencies managed through WordPress core

### Styling Strategy

- Global styles centralized in [`includes/assets/scss/main.scss`](includes/assets/scss/main.scss)
- Component-specific overrides in block directories and [`includes/assets/scss/modules/`](includes/assets/scss/modules/)
- SCSS compilation to CSS for production in `includes/assets/css/`
- CSS modules organized by functionality (header, footer, profile, archive for FAQ, admin styles, etc.)

### Server-Side Rendering

- PHP render templates for dynamic content and SEO
- JavaScript handles interactive behaviors
- Minimal PHP logic in render templates

## Key Components

### Theme Entry Point

[`functions.php`](functions.php) handles:

- Asset enqueuing with cache busting
- Block registration
- WordPress hooks and filters
- Custom functionality setup, including FAQ search extension and admin enqueue for ordering
- Pre_get_posts for including 'faq' in searches

### Styling System

- **Configuration**: [`includes/assets/scss/config/`](includes/assets/scss/config/_variables.scss) - Variables, fonts, normalize
- **Modules**: [`includes/assets/scss/modules/`](includes/assets/scss/modules/header.scss) - Component styles, including archive.scss for FAQ archives
- **Admin Styles**: [`includes/assets/scss/modules/admin/`](includes/assets/scss/modules/admin/faq_admin.scss)
- **Compilation**: SCSS → CSS with source maps in `includes/assets/css/`

### Build System

- Individual [`package.json`](blocks/login-form/package.json) per block
- WordPress scripts for standardized builds
- Webpack configuration for asset bundling in blocks and admin-faq-editor
- Admin FAQ editor builds to `includes/assets/js/admin/faq-editor/`

### FAQ System

- **CPT Registration**: [`includes/features/faq/register-cpt-faq.php`](includes/features/faq/register-cpt-faq.php) - 'faq' CPT with supports, 'faq_section' taxonomy
- **Ordering**: Meta fields '_faq_order' (post meta) and '_section_order' (term meta) for custom sort.
- **Admin Management**: Dedicated React-based admin editor (`admin-faq-editor/`) provides a comprehensive interface for managing FAQ sections and items. This includes:
  - Creating, editing, and deleting sections and FAQ items.
  - Drag-and-drop reordering for both FAQ sections and FAQ items within their respective sections.
  - Legacy drag-drop in admin list via enqueued scripts.
- **REST API**: [`includes/features/faq/faq-rest-api.php`](includes/features/faq/faq-rest-api.php)
- **Admin Page**: [`includes/features/faq/faq-editor-admin-page.php`](includes/features/faq/faq-editor-admin-page.php)
