# FAQ Display Block Documentation

## Overview

The FAQ Display block renders FAQs from the 'faq' CPT as nested accordions, with sections (faq_section taxonomy) as outer accordions and individual items as inner. Supports custom ordering via meta. Display-only for frontend, preview in editor.

## File Structure

- **blocks/faq-display/package.json**: NPM build config.
- **blocks/faq-display/faq-display.php**: Block registration and init hook.
- **blocks/faq-display/readme.txt**: User documentation with German labels.
- **blocks/faq-display/src/block.json**: Attributes (showSections, accordionStyle: default/modern).
- **blocks/faq-display/src/index.js**: Registration with accordion icon.
- **blocks/faq-display/src/edit.js**: Editor preview with inspector controls for attributes (German labels).
- **blocks/faq-display/src/editor.scss**: Preview styles for nested structure.
- **blocks/faq-display/src/render.php**: Server-side: Queries sections/terms with '_section_order' meta, then FAQs per section with '_faq_order' meta. Renders `<details>` HTML for accordions. Fallback messages in German.
- **blocks/faq-display/src/style.scss**: Frontend styles for default/modern modes (transitions, gradients, shadows).
- **blocks/faq-display/src/view.js**: Enhances `<details>` with custom toggle (+/- rotate), keyboard support.

## Key Implementation Details

- **Ordering**: Queries order by meta_value_num fallback to title.
- **Nested Rendering**: Uses WP_Query for sections/FAQs; `apply_filters('the_content')` for answer.
- **Localization**: German fallbacks (e.g., "Keine FAQs gefunden").
- **Accessibility**: ARIA-hidden icons, keyboard toggle.

## Usage

Insert block in page; it queries all FAQs/sections. Toggle styles via attribute.

## Testing

- Render nested accordions with order.
- Verify fallback to alphabetical if no meta.
