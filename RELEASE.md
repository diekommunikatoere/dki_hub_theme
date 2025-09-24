# DKI Hub Theme Release Process

This document describes how to create production releases of the DKI Hub Theme using the automated GitHub Actions workflow.

## Overview

The release process builds all custom Gutenberg blocks, compiles assets, and creates a clean WordPress theme package ready for deployment. Development files are automatically excluded based on the [`.releaseignore`](.releaseignore) configuration.

## Triggering a Release

### Prerequisites

- You must be on the `main` branch
- All changes should be committed and pushed
- Ensure all blocks build successfully locally

### Manual Release via GitHub Actions

1. Navigate to **Actions** tab in the GitHub repository
2. Select **"Release Build"** workflow from the left sidebar
3. Click **"Run workflow"** button
4. Fill in the required inputs:
   - **Release tag**: Version identifier (e.g., `v1.2.0`, `v1.2.0-beta.1`)
   - **Create GitHub Release**: Check this to automatically create a GitHub Release with the built theme
5. Click **"Run workflow"**

### Workflow Inputs

| Input | Required | Description | Example |
|-------|----------|-------------|---------|
| `release_tag` | Yes | Version tag for the release | `v1.2.0` |
| `create_github_release` | No | Whether to create a GitHub Release | `true` |

## What Happens During Release

### 1. Block Building

The workflow:

- Installs dependencies for each block using `npm ci`
- Runs `npm run build` for each block with a `package.json`
- Collects built assets from each block's `build/` directory

### 2. Asset Compilation

- Copies compiled CSS from [`includes/css/`](includes/css/)
- Includes font files from [`fonts/`](fonts/)
- Preserves PHP templates and core theme files

### 3. File Filtering

Files and directories listed in [`.releaseignore`](.releaseignore) are automatically excluded:

- Source files (`src/`, `scss/`)
- Development dependencies (`node_modules/`, `package-lock.json`)
- Build artifacts (`*.map` files)
- Configuration files (`.eslintrc*`, `.gitignore`, etc.)
- Documentation and development files

### 4. Packaging

- Creates a ZIP archive named `dki-hub-theme-{release_tag}.zip`
- Runs smoke tests to verify:
  - All blocks have built assets
  - Required PHP files are present
  - Theme structure is correct

### 5. Artifact Upload

- Uploads the theme ZIP as a workflow artifact
- Optionally creates a GitHub Release with the ZIP attached

## Release Artifacts

### Workflow Artifact

- **Name**: `dki-hub-theme-{release_tag}`
- **Contents**: ZIP file ready for WordPress installation
- **Retention**: 30 days

## Local Testing

You can test the release build locally before triggering the workflow:

```bash
# Make the script executable
chmod +x scripts/release-build.sh

# Run the release build
RELEASE_TAG=test-local ./scripts/release-build.sh

# Check the output
ls -la release/
unzip -l release/dki-hub-theme-test-local.zip
```

## Installation

### WordPress Installation

1. Download the theme ZIP from the workflow artifacts or GitHub Release
2. In WordPress admin, go to **Appearance > Themes > Add New > Upload Theme**
3. Upload the ZIP file and activate

### Theme Structure

The released theme includes:

```
dki-hub-theme/
├── functions.php              # Theme entry point
├── blocks/                    # Built Gutenberg blocks
│   ├── edit-docs-page/
│   ├── login-form/
│   ├── profile-nav-item/
│   ├── profile-sidebar-nav/
│   ├── revisions-profile-overview/
│   ├── schulungen-query-loop/
│   ├── schulungen-read-status-widget/
│   └── view-revisions/
├── includes/
│   ├── css/                   # Compiled stylesheets
│   ├── js/                    # JavaScript modules
│   ├── templates/             # PHP templates
│   └── utils/                 # Utility functions
├── templates/                 # WordPress template files
└── fonts/                     # Custom fonts (Gibson, Gill Sans)
```

## Included Blocks

The theme includes these custom Gutenberg blocks:

1. **edit-docs-page** - Documentation editing interface
2. **login-form** - Custom login form block
3. **profile-nav-item** - Profile navigation item
4. **profile-sidebar-nav** - Profile sidebar navigation
5. **revisions-profile-overview** - Profile revisions overview
6. **schulungen-query-loop** - Training content query loop
7. **schulungen-read-status-widget** - Training read status widget
8. **view-revisions** - Document revision viewer

## Troubleshooting

### Build Failures

- Check that all blocks have valid `package.json` files
- Ensure all dependencies are properly defined
- Verify build scripts use `--webpack-copy-php` flag

### Missing Files in Release

- Check [`.releaseignore`](.releaseignore) for overly broad exclusion patterns
- Verify files exist in source before build
- Review release script output for copy errors

### Large Release Size

- Review included files in the ZIP
- Consider adding more exclusion patterns to [`.releaseignore`](.releaseignore)
- Check for accidentally included `node_modules/` or source files

## Customizing the Release Process

### Modifying File Exclusions

Edit [`.releaseignore`](.releaseignore) to change which files are excluded from releases.

### Changing Build Process

Modify [`scripts/release-build.sh`](scripts/release-build.sh) to customize the build steps.

### Workflow Configuration

Edit [`.github/workflows/release.yml`](.github/workflows/release.yml) to modify the GitHub Actions workflow.

## Security Notes

- Only users with write access to the repository can trigger releases
- Releases are restricted to the `main` branch for security
- The workflow uses `GITHUB_TOKEN` with minimal required permissions
