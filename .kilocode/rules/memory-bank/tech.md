# Tech — Stack and Development Setup

## Stack

- **PHP** (WordPress-compatible)
- **JavaScript** (Gutenberg/React)
- **SCSS → CSS** (Sass compilation)
- **npm** for block builds and dependency management

## Development Setup

### Requirements

- PHP 8.x or compatible
- WordPress latest compatible release
- Node.js LTS (for npm/build tools)

### Block Development

- Per-block: run `npm install` then `npm run build` or `npm run start` for development
- Global styles: compile via project's SCSS tooling (see [`includes/scss/`](includes/scss/main.scss:1))

## Common Commands

### Block Development

```bash
# Build a single block
cd blocks/<block-name> && npm install && npm run build

# Development watch mode
npm run start

# Linting and formatting
npm run lint:js
npm run lint:css
npm run format
```

### Asset Management

- Build all blocks before theme deployment
- Ensure assets enqueued via [`functions.php`](functions.php:12) use versioning for cache busting
- JavaScript modules auto-enqueued from [`includes/js/`](includes/js/) directory

## Build Configuration

### Per-Block Setup

- Each block has individual [`package.json`](blocks/profile-sidebar-nav/package.json:1)
- Uses `@wordpress/scripts` for standardized builds
- Webpack configuration handles asset bundling
- PHP files copied during build process (`--webpack-copy-php`)

### Global Styles

- Source files in [`includes/scss/`](includes/scss/config/_variables.scss:1)
- Compilation to [`includes/css/`](includes/css/main.css:1)
- Modular organization by component

## Development Constraints

### Code Organization

- Keep PHP render templates minimal; prefer JavaScript for interactive behavior
- Maintain block modularity with self-contained packages
- Use consistent naming conventions across blocks

## Dependencies

### Core Dependencies

- WordPress core (Gutenberg blocks)
- `@wordpress/scripts` for build tooling
- Sass for SCSS compilation

### Font Assets

- Custom Gibson and Gill Sans font families
- Located in [`fonts/`](fonts/Gibson/) directory
- Multiple formats supported (woff, woff2, ttf, eot, svg)
