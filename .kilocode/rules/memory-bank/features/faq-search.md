# FAQ Search Block Documentation

## Overview

The FAQ Search block provides a search input for client-side fuzzy search on FAQs, filtering the connected FAQ Display block in real-time using Fuse.js. It loads FAQ data server-side and filters DOM elements.

## File Structure

- **blocks/faq-search/package.json**: NPM build with Fuse.js dependency.
- **blocks/faq-search/faq-search.php**: Block registration and init hook.
- **blocks/faq-search/readme.txt**: User documentation with German labels.
- **blocks/faq-search/src/block.json**: Attributes (targetDisplayId, placeholder).
- **blocks/faq-search/src/index.js**: Registration with search icon.
- **blocks/faq-search/src/edit.js**: Editor with text controls for ID/placeholder.
- **blocks/faq-search/src/editor.scss**: Preview styles.
- **blocks/faq-search/src/render.php**: Outputs input with data-faq-data JSON from WP_Query, inline script for window.faqSearchData.
- **blocks/faq-search/src/style.scss**: Input focus, results count, no-results styles.
- **blocks/faq-search/src/view.js**: JS for Fuse.js search on input (debounced), hides/shows items by ID/text match, updates count.

## Key Implementation Details

- **Fuzzy Search**: Fuse.js on title/content/excerpt/section, threshold 0.4.
- **Integration**: Targets FAQ Display ID; filters sibling accordions.
- **Data**: JSON array of FAQs loaded in render.php.
- **Localization**: German placeholders (e.g., "Suchen Sie in den FAQs...").
- **Accessibility**: Aria-label on input, Escape clear.

## Usage

Place above FAQ Display; set target ID (e.g., "faq-display-abc123") for filtering.

## Testing

- Test fuzzy queries (e.g., "frge" matches "Frage").
- Verify count/no-results display.
- Ensure no page reload.
