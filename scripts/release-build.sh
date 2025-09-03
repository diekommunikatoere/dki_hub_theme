#!/usr/bin/env bash
set -euo pipefail

ROOT="$(pwd)"
TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RELEASE_TAG="${RELEASE_TAG:-dev-$TIMESTAMP}"
DRY_RUN="${DRY_RUN:-false}"

echo "🚀 Starting DKI Hub Theme release build..."
echo "   Timestamp: $TIMESTAMP"
echo ""

# Prepare release directory
RELEASE_DIR="$ROOT/release"
rm -rf "$RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

echo "📁 Copying entire theme structure to release directory..."
# Copy all theme files first
cp -r "$ROOT/"* "$RELEASE_DIR/" 2>/dev/null || true
# Remove the release directory from itself to avoid recursion
rm -rf "$RELEASE_DIR/release"
echo "✅ Theme structure copied"
echo ""

echo "📦 Building blocks..."
block_count=0
for block_dir in "$ROOT/blocks"/*/; do
    if [ -f "$block_dir/package.json" ]; then
        block_name=$(basename "$block_dir")
        echo "  Building block: $block_name"
        cd "$block_dir"
        if [ -f "package-lock.json" ]; then
            npm ci
        else
            npm install
        fi
        npm run build
        cd "$ROOT"
        
        # Set up proper block structure in release
        release_block_dir="$RELEASE_DIR/blocks/$block_name"
        
        # Remove src and other development files from release block
        rm -rf "$release_block_dir/src"
        rm -rf "$release_block_dir/node_modules"
        rm -f "$release_block_dir/package.json"
        rm -f "$release_block_dir/package-lock.json"
        rm -f "$release_block_dir/readme.txt"
        rm -rf "$release_block_dir/.editorconfig"
        rm -rf "$release_block_dir/.gitignore"
        
        # Copy built assets to build subfolder
        if [ -d "$block_dir/build" ]; then
            mkdir -p "$release_block_dir/build"
            cp -r "$block_dir/build/." "$release_block_dir/build/"
        fi
        
        # Ensure the block PHP file exists
        block_php_file="$block_name.php"
        if [ -f "$block_dir/$block_php_file" ]; then
            cp "$block_dir/$block_php_file" "$release_block_dir/"
        else
            echo "⚠️  Warning: $block_php_file not found for $block_name"
        fi
        
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built and structured"
    fi
done
echo "  📊 Built $block_count blocks"
echo ""

echo "🎨 Compiling SCSS to CSS for main theme..."
if [ -d "./includes/scss" ]; then
    if command -v sass >/dev/null 2>&1; then
        sass --no-source-map ./includes/scss:./includes/css
        echo "Finished compiling SCSS to CSS"
        # Update CSS in release directory
        cp -r ./includes/css/. "$RELEASE_DIR/includes/css/"
    else
        echo "⚠️ sass CLI not found, skipping SCSS compilation."
    fi
else
    echo "No SCSS directory found, skipping SCSS compilation."
fi
echo ""

echo "🗑️ Removing files and directories listed in .releaseignore ..."
if [ -f "$ROOT/.releaseignore" ]; then
    cd "$RELEASE_DIR"
    while IFS= read -r pattern; do
        [ -z "$pattern" ] && continue
        # Skip empty lines and comments
        [[ "$pattern" =~ ^[[:space:]]*$ ]] && continue
        [[ "$pattern" =~ ^[[:space:]]*# ]] && continue
        
        if [ "$DRY_RUN" = "true" ]; then
            echo "Would remove: $pattern"
        else
            # Use find to handle patterns safely
            find . -path "./$pattern" -exec rm -rf {} + 2>/dev/null || true
            # Also handle direct file/directory removal
            rm -rf "$pattern" 2>/dev/null || true
        fi
    done < "$ROOT/.releaseignore"
    cd "$ROOT"
    echo "Finished removing files from .releaseignore"
fi

echo ""
echo "✅ Release build complete. Artifacts are in $RELEASE_DIR"
echo "📁 Theme structure preserved with built blocks"