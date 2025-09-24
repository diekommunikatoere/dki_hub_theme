# Context — Current state and focus

## Current work focus

- Implemented searchable FAQ feature with CPT, taxonomy, blocks, templates, and custom ordering
- FAQ system includes drag-and-drop reordering for items and sections
- Project now contains 5 custom Gutenberg blocks under [`blocks/`](blocks/) directory (copy-field, faq-display, faq-search, header-navigation, login-form)
- All features localized in German

## Recent changes

- **REORGANIZED**: Assets to [`includes/assets/`](includes/assets/) with subdirs css/, js/, scss/, fonts/; SCSS sources in includes/assets/scss/
- **MOVED**: FAQ files from includes/utils/ to [`includes/features/faq/`](includes/features/faq/) (faq-editor-admin-page.php, faq-rest-api.php, register-cpt-faq.php)
- **NEW**: [`includes/features/faq/register-cpt-faq.php`](includes/features/faq/register-cpt-faq.php) - CPT and taxonomy for FAQs with order meta
- **NEW**: [`includes/assets/js/admin/faq-reorder.js`](includes/assets/js/admin/faq-reorder.js) - Admin drag-drop for ordering (updated path)
- **NEW**: [`includes/utils/migrate-faqs.php`](includes/utils/migrate-faqs.php) - Migration from BetterDocs with order meta
- **UPDATED**: [`includes/features/faq/register-cpt-faq.php`](includes/features/faq/register-cpt-faq.php) - Added hooks for default '_section_order' meta on creation and bulk init for existing terms
- **UPDATED**: [`functions.php`](functions.php) - Added init hook for bulk section orders; Include CPT, search extension, admin enqueue, AJAX handlers
- **UPDATED**: [`includes/assets/scss/modules/archive.scss`](includes/assets/scss/modules/archive.scss) - Styles for FAQ archives
- Implemented German localization throughout new features
- **NEW**: Implemented drag-and-drop reordering for FAQ items within sections in the FAQ editor.
- Updated memory bank documentation with new FAQ system details

## Next steps

- Monitor project for any structural changes that require memory bank updates
- Continue maintaining and extending Gutenberg blocks as needed
- Test new features in WordPress environment
- Keep context.md updated as work progresses

## Project status

- **Active blocks**: 5 custom Gutenberg blocks (copy-field, faq-display, faq-search, header-navigation, login-form)
- **Latest addition**: FAQ system with CPT 'faq', taxonomy 'faq_section', blocks, templates, fuzzy search (Fuse.js threshold adjusted to 0.5), and custom ordering (drag-drop for sections and items, number field for sections)
- **Language**: German UI implemented across all user-facing elements
- **Styling system**: SCSS modular architecture with compiled CSS output, extended for FAQ
- **Build system**: Individual npm builds per block using @wordpress/scripts
- **Font assets**: Gibson and Gill Sans families integrated
