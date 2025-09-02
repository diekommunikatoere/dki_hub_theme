#!/usr/bin/env bash
set -euo pipefail

# DKI Hub Theme Release Build Script
# Builds all blocks, compiles assets, and creates a clean release package

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

# Function to copy files while respecting .releaseignore
copy_with_ignore() {
    local src="$1"
    local dest="$2"
    
    if [ ! -f "$ROOT/.releaseignore" ]; then
        echo "⚠️  No .releaseignore found, copying all files"
        cp -r "$src" "$dest"
        return
    fi
    
    # Create exclude pattern for rsync from .releaseignore
    local exclude_file=$(mktemp)
    
    # Convert .releaseignore to rsync exclude patterns
    while IFS= read -r line; do
        # Skip comments and empty lines
        if [[ "$line" =~ ^[[:space:]]*# ]] || [[ -z "${line// }" ]]; then
            continue
        fi
        # Add to exclude file
        echo "$line" >> "$exclude_file"
    done < "$ROOT/.releaseignore"
    
    # Use rsync to copy with excludes
    rsync -a --exclude-from="$exclude_file" "$src/" "$dest/"
    
    # Clean up temp file
    rm "$exclude_file"
}

# Build each block
echo ""
echo "📦 Building blocks..."

block_count=0
for block_dir in "$ROOT/blocks"/*/; do
    if [ -f "$block_dir/package.json" ]; then
        block_name=$(basename "$block_dir")
        echo "  Building block: $block_name"
        
        cd "$block_dir"
        
        # Install dependencies
        if [ -f "package-lock.json" ]; then
            npm ci --silent
        else
            npm install --silent
        fi
        
        # Build the block
        npm run build --silent
        
        # Copy block files to release, respecting .releaseignore
        block_release_dir="$RELEASE_DIR/$block_name"
        mkdir -p "$block_release_dir"
        copy_with_ignore "$block_dir" "$block_release_dir"
        
        cd "$ROOT"
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built successfully"
    fi
done

echo "  📊 Built $block_count blocks"

# Copy theme-level files
echo ""
echo "🎨 Copying theme files..."

# Core theme files
echo "  Copying functions.php..."
cp "$ROOT/functions.php" "$RELEASE_DIR/"

# Copy templates directory if it exists
if [ -d "$ROOT/templates" ]; then
    echo "  Copying templates..."
    copy_with_ignore "$ROOT/templates" "$RELEASE_DIR/templates"
fi

# Copy includes directory (CSS, JS, etc.)
if [ -d "$ROOT/includes" ]; then
    echo "  Copying includes..."
    mkdir -p "$RELEASE_DIR/includes"
    copy_with_ignore "$ROOT/includes" "$RELEASE_DIR/includes"
fi

# Copy fonts
if [ -d "$ROOT/fonts" ]; then
    echo "  Copying fonts..."
    copy_with_ignore "$ROOT/fonts" "$RELEASE_DIR/fonts"
fi

# Copy style.css if it exists (WordPress theme requirement)
if [ -f "$ROOT/style.css" ]; then
    echo "  Copying style.css..."
    cp "$ROOT/style.css" "$RELEASE_DIR/"
fi

# Copy index.php if it exists (WordPress theme requirement)
if [ -f "$ROOT/index.php" ]; then
    echo "  Copying index.php..."
    cp "$ROOT/index.php" "$RELEASE_DIR/"
fi

# Copy screenshot if it exists
for ext in png jpg jpeg gif; do
    if [ -f "$ROOT/screenshot.$ext" ]; then
        echo "  Copying screenshot.$ext..."
        cp "$ROOT/screenshot.$ext" "$RELEASE_DIR/"
        break
    fi
done

# Clean up any remaining unwanted files in release
echo ""
echo "🧹 Cleaning up release directory..."

# Remove any .map files that might have slipped through
find "$RELEASE_DIR" -name "*.map" -delete 2>/dev/null || true

# Remove any empty directories
find "$RELEASE_DIR" -type d -empty -delete 2>/dev/null || true

# Create ZIP archive
echo ""
echo "📁 Creating release archive..."
cd "$RELEASE_DIR"
zip -r "$ZIP_NAME" . -q
mv "$ZIP_NAME" "$ROOT/release/"
cd "$ROOT"

# Display summary
echo ""
echo "✅ Release build completed successfully!"
echo "   📦 Archive: release/$ZIP_NAME"
echo "   📊 Size: $(du -h "release/$ZIP_NAME" | cut -f1)"
echo "   📁 Contents:"

# List archive contents for verification
unzip -l "release/$ZIP_NAME" | head -20
if [ $(unzip -l "release/$ZIP_NAME" | wc -l) -gt 25 ]; then
    echo "   ... ($(unzip -l "release/$ZIP_NAME" | tail -1 | awk '{print $2}') total files)"
fi

echo ""
echo "🎉 Ready for deployment!"