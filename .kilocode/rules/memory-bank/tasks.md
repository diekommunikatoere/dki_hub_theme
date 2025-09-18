# Tasks — Repeatable Workflows

## Add New Gutenberg Block

**Template blocks to copy from:**

- Use [`blocks/login-form/`](blocks/login-form/) as primary template
- Alternative: [`blocks/profile-sidebar-nav/`](blocks/profile-sidebar-nav/)
- For navigation blocks: [`blocks/header-navigation/`](blocks/header-navigation/) – includes WP menu selector + profile dropdown

**Information needed:**

- Block display name (e.g., "User Profile Widget")
- Block slug (e.g., "user-profile-widget")

**Steps:**

1. Copy entire template block directory to `blocks/<new-block-slug>/`
2. Update [`package.json`](blocks/login-form/package.json:1):
   - Change `name` field to new block slug
   - Update `description` field
3. Update [`src/block.json`](blocks/login-form/src/block.json:1):
   - Change `name` field to `dki-wiki/<new-block-slug>`
   - Update `title` field to display name
   - Update `description` field
   - Modify `textdomain` to match block slug
4. Update all PHP files:
   - Replace old block namespace with `dki-wiki/<new-block-slug>`
   - Update function names and class names
5. Update JavaScript files:
   - Replace block registration names
   - Update component names and identifiers
6. Test block registration in WordPress editor
7. Customize block functionality as needed
8. Update project documentation

**Important notes:**

- Use `dki-wiki/` namespace for all blocks
- Maintain consistent file structure with template
- Test both editor and frontend rendering after customization
- For blocks with WP menus, use core navigation data store in edit.js for selector
- Include German localization with __() for user-facing text

## Build Block Assets

**For single block:**

```bash
cd blocks/<block-name>
npm install
npm run build
```

**For development with watch mode:**

```bash
npm run start
```

**For all blocks before deployment:**

- Build each block individually
- Verify compiled assets in block build directories

## Release Theme Update

**Files to update:**

- Theme version in style headers
- Version in readme files
- Changelog documentation

**Steps:**

1. Bump version numbers in theme files
2. Build all block assets using `npm run build` in each block directory
3. Test all blocks in WordPress environment
4. Create release notes documenting changes
5. Tag release in version control system
6. Package and publish theme ZIP file

**Important notes:**

- Ensure all blocks are built with production assets
- Test theme in clean WordPress installation
- Include migration notes for breaking changes

## Update Memory Bank

**When to update:**

- After adding new blocks or major features
- When project structure changes
- After discovering new patterns or workflows
- When context significantly changes

**Steps:**

1. Review all memory bank files for accuracy
2. Update [`architecture.md`](.kilocode/rules/memory-bank/architecture.md:1) with new directory structure
3. Update [`tech.md`](.kilocode/rules/memory-bank/tech.md:1) with new dependencies or tools
4. Update [`context.md`](.kilocode/rules/memory-bank/context.md:1) with current focus and recent changes
5. Add new tasks to [`tasks.md`](.kilocode/rules/memory-bank/tasks.md:1) if applicable
6. Scan repository for new files and patterns

## Compile Global Styles

**Source files:**

- [`includes/scss/main.scss`](includes/scss/main.scss:1) - Main stylesheet
- [`includes/scss/config/`](includes/scss/config/_variables.scss:1) - Configuration files
- [`includes/scss/modules/`](includes/scss/modules/header.scss:1) - Component modules

**Steps:**

1. Modify SCSS files in `includes/scss/`
2. Compile SCSS to CSS (compilation method depends on project setup)
3. Verify compiled CSS in [`includes/css/`](includes/css/main.css:1)
4. Test styles in WordPress frontend
5. Check for any style conflicts with blocks

**Important notes:**

- Maintain modular organization by component
- Use consistent variable naming from `config/_variables.scss`
- Test styles across different screen sizes

## Reorder FAQs and Sections

**When to use:**

- After creating or editing FAQs/sections to set display order
- For editorial control over frontend presentation

**Files to modify:**

- Admin: [`includes/js/admin-faq-reorder.js`](includes/js/admin-faq-reorder.js:1) - Drag-drop JS
- PHP: [`includes/utils/register-cpt-faq.php`](includes/utils/register-cpt-faq.php:1) - Meta registration and metabox
- Frontend: Block render queries

**Steps:**

1. Drag items/sections in admin list view or edit form
2. Save order via AJAX or form submit to '_faq_order' meta or '_section_order' term meta
3. Queries use 'meta_value_num' for sorting with fallback to title
4. Test order in frontend blocks and archives

**Important notes:**

- Order is per-section for FAQs; global for sections
- Drag-drop for FAQs, number field for sections
- Defaults set automatically on creation (sequential) and bulk for existing (sorted by name)
- Fallback to alphabetical if no order meta
- Update migration to set defaults
