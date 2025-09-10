# FAQ Ordering Documentation

## Overview

Custom ordering for FAQs and sections using meta fields '_faq_order' (post meta) and '_section_order' (term meta). Editors use drag-and-drop in admin list for FAQs and number input for sections.

## Implementation

- **Meta Registration**: In [`includes/utils/register-cpt-faq.php`](includes/utils/register-cpt-faq.php:88), `register_term_meta` for '_section_order'. Metabox for '_faq_order' in FAQ editor.
- **Admin Interface**: Enqueued [`includes/js/admin-faq-reorder.js`](includes/js/admin-faq-reorder.js:1) on FAQ admin pages. Adds drag handles to list table, jQuery UI Sortable for reordering, AJAX to `dki_wiki_update_faq_order` in functions.php.
- **Section Ordering**: Number field in term edit form via `faq_section_edit_form_fields`. Save with `edited_faq_section` and `create_faq_section` actions.
- **Query Update**: In [`blocks/faq-display/src/render.php`](blocks/faq-display/src/render.php:16), get_terms and WP_Query use 'meta_key' => '_section_order'/'_faq_order', 'orderby' => 'meta_value_num' fallback to 'title'.
- **Migration**: Updated [`includes/utils/migrate-faqs.php`](includes/utils/migrate-faqs.php:59) to set default '_faq_order' and '_section_order' during import.
- **Admin Hooks**: Columns for order display; nonce security for AJAX/save.

## Usage

- FAQs: Drag in admin list under "FAQs" to reorder, saves to meta.
- Sections: Enter number in "Reihenfolge" field when editing term.
- Frontend: Blocks/templates respect order; fallback alphabetical.

## Testing

- Drag FAQ in admin, verify frontend order.
- Edit section order number, check display.
- Migration: Run script, verify meta set.
