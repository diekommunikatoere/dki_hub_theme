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
TARGET_BLOCKS=""

if [ "$FORCE_REBUILD_ALL" = "true" ] || [ -z "$CHANGED_BLOCKS" ]; then
  log "Rebuilding all blocks..."
  TARGET_BLOCKS=$(ls "$BLOCKS_DIR")
else
  log "Rebuilding only changed blocks: $CHANGED_BLOCKS"
  TARGET_BLOCKS="$CHANGED_BLOCKS"
fi

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

# -----------------------------
# Copy theme files with .releaseignore
# -----------------------------
log "Copying theme files..."
RSYNC_EXCLUDES=""
if [ -f "$RELEASEIGNORE" ]; then
  RSYNC_EXCLUDES="--exclude-from=$RELEASEIGNORE"
fi

rsync -a $RSYNC_EXCLUDES "$ROOT_DIR/" "$RELEASE_DIR/"

# -----------------------------
# Tag marker
# -----------------------------
if [ -n "$RELEASE_TAG" ]; then
  echo "$RELEASE_TAG" > "$RELEASE_DIR/.release-version"
  log "Release tag written to .release-version"
fi

log "✅ Release build finished. Output in: $RELEASE_DIR"
