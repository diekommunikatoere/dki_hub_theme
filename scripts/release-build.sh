#!/usr/bin/env bash
set -euo pipefail

# DKI Hub Theme Release Build Script (Refactored)
# 1. Copies repo from main branch into release/
# 2. Builds blocks in-place in release/blocks/
# 3. Removes dev/source files, keeps only built assets and runtime files

ROOT="$(pwd)"
RELEASE_DIR="$ROOT/release"
TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RELEASE_TAG="${RELEASE_TAG:-dev-$TIMESTAMP}"
ZIP_NAME="dki-hub-theme-$RELEASE_TAG.zip"

echo "🚀 Starting DKI Hub Theme release build..."
echo "   Release tag: $RELEASE_TAG"
echo "   Timestamp: $TIMESTAMP"
echo "   Output: $ZIP_NAME"

# Clean and create release directory
rm -rf "$RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

echo ""
echo "📦 Copying repository from main branch into release directory..."
git -C "$ROOT" archive --format=tar main | tar -x -C "$RELEASE_DIR"

echo ""
echo "📦 Building blocks in release/blocks/..."
block_count=0
for block_dir in "$RELEASE_DIR/blocks"/*/; do
    if [ -f "$block_dir/package.json" ]; then
        block_name=$(basename "$block_dir")
        echo "  Building block: $block_name"
        cd "$block_dir"
        if [ -f "package-lock.json" ]; then
            npm ci --silent
        else
            npm install --silent
        fi
        npm run build --silent
        cd "$ROOT"
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built successfully"
    fi
done
echo "  📊 Built $block_count blocks"

echo ""
echo "🧹 Pruning dev/source files from blocks..."
for block_dir in "$RELEASE_DIR/blocks"/*/; do
    # Remove dev/source files and folders
    rm -rf "$block_dir/src" \
           "$block_dir/node_modules" \
           "$block_dir/tests" \
           "$block_dir/.github"
    rm -f "$block_dir/package.json" \
          "$block_dir/package-lock.json" \
          "$block_dir/yarn.lock" \
          "$block_dir/.eslintrc"* \
          "$block_dir/.prettierrc"* \
          "$block_dir/.editorconfig" \
          "$block_dir/.gitignore"
    find "$block_dir" -name "*.scss" -type f -delete
    find "$block_dir" -name "*.map" -type f -delete
done

echo ""
echo "🎨 Copying theme-level files (functions.php, includes/, fonts/, style.css, etc.)..."
cp "$ROOT/functions.php" "$RELEASE_DIR/" || true
if [ -d "$ROOT/includes" ]; then
    cp -r "$ROOT/includes" "$RELEASE_DIR/"
fi
if [ -d "$ROOT/fonts" ]; then
    cp -r "$ROOT/fonts" "$RELEASE_DIR/"
fi
if [ -f "$ROOT/style.css" ]; then
    cp "$ROOT/style.css" "$RELEASE_DIR/"
fi
if [ -f "$ROOT/index.php" ]; then
    cp "$ROOT/index.php" "$RELEASE_DIR/"
fi
if [ -d "$ROOT/templates" ]; then
    cp -r "$ROOT/templates" "$RELEASE_DIR/"
fi
for ext in png jpg jpeg gif; do
    if [ -f "$ROOT/screenshot.$ext" ]; then
        cp "$ROOT/screenshot.$ext" "$RELEASE_DIR/"
        break
    fi
done

echo ""
echo "🧹 Cleaning up release directory..."
find "$RELEASE_DIR" -name "*.map" -delete 2>/dev/null || true
find "$RELEASE_DIR" -type d -empty -delete 2>/dev/null || true

echo ""
echo "📁 Creating release archive..."
cd "$RELEASE_DIR"
zip -r "$ZIP_NAME" . -q
cd "$ROOT"

echo ""
echo "✅ Release build completed successfully!"
echo "   📦 Archive: release/$ZIP_NAME"
echo "   📊 Size: $(du -h "release/$ZIP_NAME" | cut -f1)"
echo "   📁 Contents:"
unzip -l "release/$ZIP_NAME" | head -20
if [ $(unzip -l "release/$ZIP_NAME" | wc -l) -gt 25 ]; then
    echo "   ... ($(unzip -l "release/$ZIP_NAME" | tail -1 | awk '{print $2}') total files)"
fi
echo ""
echo "🎉 Ready for deployment!"