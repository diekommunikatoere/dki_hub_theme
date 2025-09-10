# Context — Current state and focus

## Current work focus

- Implemented searchable FAQ feature with CPT, taxonomy, blocks, templates, and custom ordering
- FAQ system includes drag-and-drop reordering for items and sections
- Project now contains 9 custom Gutenberg blocks under [`blocks/`](blocks/) directory (added faq-display and faq-search)
- All features localized in German

## Recent changes

- **NEW**: Added [`includes/utils/register-cpt-faq.php`](includes/utils/register-cpt-faq.php:1) - CPT and taxonomy for FAQs with order meta
- **NEW**: Added [`includes/js/admin-faq-reorder.js`](includes/js/admin-faq-reorder.js:1) - Admin drag-drop for ordering
- **NEW**: Added templates [`templates/archive-faq.php`](templates/archive-faq.php:1) and [`templates/single-faq.php`](templates/single-faq.php:1)
- **NEW**: Added [`includes/utils/migrate-faqs.php`](includes/utils/migrate-faqs.php:1) - Migration from BetterDocs with order meta
- **UPDATED**: [`includes/utils/register-cpt-faq.php`](includes/utils/register-cpt-faq.php:1) - Added hooks for default '_section_order' meta on creation and bulk init for existing terms
- **UPDATED**: [`functions.php`](functions.php:1) - Added init hook for bulk section orders; Include CPT, search extension, admin enqueue, AJAX handlers
- **UPDATED**: [`includes/scss/modules/archive.scss`](includes/scss/modules/archive.scss:1) - Styles for FAQ archives
- Implemented German localization throughout new features
- Updated memory bank documentation with new FAQ system details

## Next steps

- Monitor project for any structural changes that require memory bank updates
- Continue maintaining and extending Gutenberg blocks as needed
- Test the new default ordering in FAQ display block
- Test new features in WordPress environment
- Keep context.md updated as work progresses

## Project status

- **Active blocks**: 11 custom Gutenberg blocks (edit-docs-page, login-form, profile-nav-item, profile-sidebar-nav, revisions-profile-overview, schulungen-query-loop, schulungen-read-status-widget, view-revisions, copy-field, faq-display, faq-search)
- **Latest addition**: FAQ system with CPT 'faq', taxonomy 'faq_section', blocks, templates, fuzzy search (Fuse.js threshold adjusted to 0.5), and custom ordering (drag-drop for items, number field for sections)
- **Language**: German UI implemented across all user-facing elements
- **Styling system**: SCSS modular architecture with compiled CSS output, extended for FAQ
- **Build system**: Individual npm builds per block using @wordpress/scripts
- **Font assets**: Gibson and Gill Sans families integrated
