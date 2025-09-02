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
        # Copy built assets to release directory
        if [ -d "$block_dir/build" ]; then
            mkdir -p "$RELEASE_DIR/blocks/$block_name"
            cp -r "$block_dir/build/." "$RELEASE_DIR/blocks/$block_name/"
        fi
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built and copied"
    fi
done
echo "  📊 Built $block_count blocks"
echo ""

echo "🎨 Compiling SCSS to CSS for main theme..."
if [ -d "./includes/scss" ]; then
    if command -v sass >/dev/null 2>&1; then
        sass --no-source-map ./includes/scss:./includes/css
        echo "Finished compiling SCSS to CSS"
        # Copy compiled CSS to release directory
        mkdir -p "$RELEASE_DIR/includes/css"
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
    while IFS= read -r pattern; do
        [ -z "$pattern" ] && continue
        if [ "$DRY_RUN" = "true" ]; then
            echo "Would remove: $pattern"
        else
            find "$RELEASE_DIR" -path "$RELEASE_DIR/$pattern" -exec rm -rf {} +
        fi
    done < "$ROOT/.releaseignore"
    echo "Finished removing files from .releaseignore"
fi

echo ""
echo "✅ Release build complete. Artifacts are in $RELEASE_DIR"