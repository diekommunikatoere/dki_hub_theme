# Architecture — System Design

## Overview

WordPress theme with PHP templates and modular Gutenberg blocks architecture. Each block is self-contained with individual build processes for maximum maintainability.

## Directory Structure

```
/
├── functions.php                    # Theme entry point, asset registration, search extensions
├── admin-faq-editor/                # React-based admin application for FAQ management
│   ├── src/
│   │   ├── components/              # React components (FAQEditor, SectionAccordion, FAQItemAccordion)
│   │   ├── context/                 # React context for FAQ data management
│   │   ├── services/                # API service for FAQ sections and items
│   │   ├── styles/                  # SCSS styles for the editor
│   │   └── types/                   # TypeScript type definitions
│   └── [...build files]
├── blocks/                          # Custom Gutenberg blocks
│   ├── edit-docs-page/
│   ├── login-form/
│   ├── profile-nav-item/
│   ├── profile-sidebar-nav/
│   ├── revisions-profile-overview/
│   ├── schulungen-query-loop/
│   ├── schulungen-read-status-widget/
│   ├── view-revisions/
│   ├── faq-display/
│   ├── faq-search/
│   └── [...additional blocks]
├── includes/
│   ├── css/                         # Compiled stylesheets
│   ├── scss/                        # Source SCSS files
│   │   ├── config/                  # Variables, fonts, normalize
│   │   └── modules/                 # Component-specific styles, including archive for FAQ templates
│   ├── js/                          # JavaScript modules, including admin-faq-reorder.js for legacy drag-drop
│   ├── templates/                   # PHP template files
│   └── utils/                       # Utility functions, including register-cpt-faq.php for CPTs/taxonomies
├── templates/                       # WordPress template files, including archive-faq.php and single-faq.php
└── fonts/                           # Custom font files (Gibson, Gill Sans)
    ├── Gibson/
    └── GillSans/
```

## Block Architecture

Each block follows a consistent structure:

- [`src/block.json`](blocks/login-form/src/block.json:1) - Block configuration and metadata
- [`src/index.js`](blocks/login-form/src/index.js:1) - Block registration
- [`src/edit.js`](blocks/login-form/src/edit.js:1) - Editor interface
- [`src/view.js`](blocks/login-form/src/view.js:1) - Frontend JavaScript (optional)
- [`src/render.php`](blocks/login-form/src/render.php:1) - Server-side rendering (optional)
- [`src/style.scss`](blocks/login-form/src/style.scss:1) - Block-specific styles
- [`src/editor.scss`](blocks/login-form/src/editor.scss:1) - Editor-only styles
- [`package.json`](blocks/profile-sidebar-nav/package.json:1) - Build configuration

## Design Principles

### Modularity

- Each block is self-contained with its own build process
- Blocks can be developed and deployed independently
- Shared dependencies managed through WordPress core

### Styling Strategy

- Global styles centralized in [`includes/scss/`](includes/scss/main.scss:1)
- Component-specific overrides in block directories
- SCSS compilation to CSS for production
- CSS modules organized by functionality (header, footer, profile, archive for FAQ, etc.)

### Server-Side Rendering

- PHP render templates for dynamic content and SEO
- JavaScript handles interactive behaviors
- Minimal PHP logic in render templates

## Key Components

### Theme Entry Point

[`functions.php`](functions.php:1) handles:

- Asset enqueuing with cache busting
- Block registration
- WordPress hooks and filters
- Custom functionality setup, including FAQ search extension and admin enqueue for ordering
- Pre_get_posts for including 'faq' in searches

### Styling System

- **Configuration**: [`includes/scss/config/`](includes/scss/config/_variables.scss:1) - Variables, fonts, normalize
- **Modules**: [`includes/scss/modules/`](includes/scss/modules/header.scss:1) - Component styles, including archive.scss for FAQ archives
- **Compilation**: SCSS → CSS with source maps

### Build System

- Individual [`package.json`](blocks/profile-sidebar-nav/package.json:1) per block
- WordPress scripts for standardized builds
- Webpack configuration for asset bundling

### FAQ System

- **CPT Registration**: [`includes/utils/register-cpt-faq.php`](includes/utils/register-cpt-faq.php:1) - 'faq' CPT with supports, 'faq_section' taxonomy
- **Ordering**: Meta fields '_faq_order' (post meta) and '_section_order' (term meta) for custom sort.
- **Admin Management**: A dedicated React-based admin editor (`admin-faq-editor/`) provides a comprehensive interface for managing FAQ sections and items. This includes:
  - Creating, editing, and deleting sections and FAQ items.
  - Drag-and-drop reordering for both FAQ sections and FAQ items within their respective sections.
  - The legacy [`includes/js/admin-faq-reorder.js`](includes/js/admin-faq-reorder.js:1) handles drag-drop for the traditional WordPress FAQs list.
- **Templates**: [`templates/archive-faq.php`](templates/archive-faq.php:1) and [`templates/single-faq.php`](templates/single-faq.php:1) for CPT views
