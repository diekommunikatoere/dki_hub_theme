#!/usr/bin/env bash
set -euo pipefail

# -----------------------------
# Config
# -----------------------------
ROOT_DIR="$(pwd)"
RELEASE_DIR="$ROOT_DIR/release"
RELEASEIGNORE="$ROOT_DIR/.releaseignore"

VERBOSE=false
PARALLEL=false

# Parse CLI flags
for arg in "$@"; do
  case $arg in
    --verbose) VERBOSE=true ;;
    --parallel) PARALLEL=true ;;
  esac
done

# Env vars from workflow
RELEASE_TAG="${RELEASE_TAG:-}"
FORCE_REBUILD_ALL="${FORCE_REBUILD_ALL:-false}"
CHANGED_BLOCKS="${CHANGED_BLOCKS:-}"

# -----------------------------
# Helpers
# -----------------------------
log() {
  echo -e "[$(date +'%H:%M:%S')] $*"
}

run() {
  if [ "$VERBOSE" = true ]; then
    echo "+ $*"
  fi
  "$@"
}

# Check and install Sass if not available
if ! command -v sass &> /dev/null; then
  log "Sass not found, installing globally..."
  run npm install -g sass
fi

# -----------------------------
# Clean release directory
# -----------------------------
log "Cleaning release directory..."
rm -rf "$RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

# -----------------------------
# Install dependencies & build blocks
# -----------------------------
BLOCKS_DIR="$ROOT_DIR/blocks"

TARGET_BLOCKS=$(ls "$BLOCKS_DIR")

build_block() {
  block=$1
  block_path="$BLOCKS_DIR/$block"
  if [ -d "$block_path" ]; then
    log "Building block: $block"
    pushd "$block_path" >/dev/null
    run npm install
    run npm run build
    popd >/dev/null
    mkdir -p "$RELEASE_DIR/blocks/$block/build"
    cp -r "$block_path/build/." "$RELEASE_DIR/blocks/$block/build/"
    # Copy the PHP file if it exists
    if [ -f "$block_path/$block.php" ]; then
      cp "$block_path/$block.php" "$RELEASE_DIR/blocks/$block/"
    fi

    # Clean up dev files in release block dir
    rm -rf "$RELEASE_DIR/blocks/$block/src"
    rm -rf "$RELEASE_DIR/blocks/$block/node_modules"
    rm -rf "$RELEASE_DIR/blocks/$block/package.json"
    rm -rf "$RELEASE_DIR/blocks/$block/package-lock.json"
    rm -rf "$RELEASE_DIR/blocks/$block/readme.txt"

  else
    log "⚠️ Block directory not found: $block"
  fi
}
export -f log run build_block
export RELEASE_DIR BLOCKS_DIR VERBOSE

if [ "$PARALLEL" = true ]; then
  echo "$TARGET_BLOCKS" | xargs -n1 -P"$(nproc)" bash -c 'build_block "$@"' _
else
  for block in $TARGET_BLOCKS; do
    build_block "$block"
  done
fi

log "Compiling global SCSS..."
cd includes/assets
sass scss/:css/ --style=compressed --no-source-map
cd "$ROOT_DIR"

log "Building admin FAQ editor..."
if [ -d admin-faq-editor ]; then
pushd admin-faq-editor >/dev/null
run npm install
run npm run build
fi

# -----------------------------
# Copy theme files with .releaseignore
# -----------------------------
log "Copying theme files..."
RSYNC_EXCLUDES=""
if [ -f "$RELEASEIGNORE" ]; then
  RSYNC_EXCLUDES="--exclude-from=$RELEASEIGNORE"
fi

# Exclude blocks/ to preserve clean built structure
rsync -a $RSYNC_EXCLUDES --exclude=blocks/ "$ROOT_DIR/" "$RELEASE_DIR/"

# Explicitly copy SCSS sources (bypasses .releaseignore exclusion)
if [ -d "$ROOT_DIR/includes/assets/scss" ]; then
  mkdir -p "$RELEASE_DIR/includes/assets/scss"
  cp -r "$ROOT_DIR/includes/assets/scss/." "$RELEASE_DIR/includes/assets/scss/"
  log "Explicitly copied includes/assets/scss/"
fi

# Copy utils if exists
if [ -d includes/utils ]; then
  mkdir -p "$RELEASE_DIR/includes/utils"
  cp -r includes/utils/ "$RELEASE_DIR/includes/utils/"
  log "Copied includes/utils/"
fi

# -----------------------------
# Version bumping
# -----------------------------
if [ -n "$RELEASE_TAG" ]; then
  log "Bumping versions to $RELEASE_TAG"
  sed -i "s/Version: [0-9.]\+;/Version: $RELEASE_TAG;/" "$RELEASE_DIR/style.css"
  if [ -f "$RELEASE_DIR/theme.json" ]; then
    sed -i "s/\"version\": \"[0-9.]\+\"/\"version\": \"$RELEASE_TAG\"/g" "$RELEASE_DIR/theme.json"
  fi
  for block in $(ls "$RELEASE_DIR/blocks" 2>/dev/null || true); do
    if [ -f "$RELEASE_DIR/blocks/$block/build/block.json" ]; then
      sed -i "s/\"version\": \"[0-9.]\+\"/\"version\": \"$RELEASE_TAG\"/g" "$RELEASE_DIR/blocks/$block/build/block.json"
    fi
  done
fi

# Clean up source maps
find "$RELEASE_DIR" -name "*.map" -delete
log "Cleaned up source maps"

# -----------------------------
# Tag marker
# -----------------------------
if [ -n "$RELEASE_TAG" ]; then
  echo "$RELEASE_TAG" > "$RELEASE_DIR/.release-version"
  log "Release tag written to .release-version"
fi

log "✅ Release build finished. Output in: $RELEASE_DIR"
