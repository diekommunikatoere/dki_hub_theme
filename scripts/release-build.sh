#!/usr/bin/env bash
set -euo pipefail

ROOT="$(pwd)"
TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RELEASE_TAG="${RELEASE_TAG:-dev-$TIMESTAMP}"
DRY_RUN="${DRY_RUN:-false}"

# --- CLI Flags ---
FORCE_REBUILD=false
VERBOSE=false
PARALLEL=false

while [[ $# -gt 0 ]]; do
    case "$1" in
        --force-rebuild)
            FORCE_REBUILD=true
            shift
            ;;
        --verbose)
            VERBOSE=true
            shift
            ;;
        --parallel)
            PARALLEL=true
            shift
            ;;
        *)
            shift
            ;;
    esac
done

log() {
    if [ "$VERBOSE" = true ]; then
        echo "$@"
    fi
}

echo "🚀 Starting DKI Hub Theme release build..."
echo "   Timestamp: $TIMESTAMP"
echo ""

# Prepare release directory
RELEASE_DIR="$ROOT/release"
rm -rf "$RELEASE_DIR"
mkdir -p "$RELEASE_DIR"

echo "📁 Copying entire theme structure to release directory..."
cp -r "$ROOT/"* "$RELEASE_DIR/" 2>/dev/null || true
rm -rf "$RELEASE_DIR/release"
echo "✅ Theme structure copied"
echo ""

# --- Block Selection Logic ---
BLOCKS_TO_BUILD=()
if [ "$FORCE_REBUILD" = true ]; then
    log "Force rebuild enabled: building all blocks."
    for block_dir in "$ROOT/blocks"/*/; do
        if [ -f "$block_dir/package.json" ]; then
            BLOCKS_TO_BUILD+=("$(basename "$block_dir")")
        fi
    done
elif [ -n "${CHANGED_BLOCKS:-}" ]; then
    log "Change detection enabled: using CHANGED_BLOCKS env."
    IFS=',' read -ra changed <<< "$CHANGED_BLOCKS"
    for block_name in "${changed[@]}"; do
        block_dir="$ROOT/blocks/$block_name"
        if [ -d "$block_dir" ] && [ -f "$block_dir/package.json" ]; then
            BLOCKS_TO_BUILD+=("$block_name")
        fi
    done
    if [ "${#BLOCKS_TO_BUILD[@]}" -eq 0 ]; then
        log "No changed blocks detected, falling back to all blocks."
        for block_dir in "$ROOT/blocks"/*/; do
            if [ -f "$block_dir/package.json" ]; then
                BLOCKS_TO_BUILD+=("$(basename "$block_dir")")
            fi
        done
    fi
else
    log "No change detection data, building all blocks."
    for block_dir in "$ROOT/blocks"/*/; do
        if [ -f "$block_dir/package.json" ]; then
            BLOCKS_TO_BUILD+=("$(basename "$block_dir")")
        fi
    done
fi

echo "📦 Building blocks..."
block_count=0
BUILD_START=$(date +%s)

build_block() {
    block_name="$1"
    block_dir="$ROOT/blocks/$block_name"
    log "  Building block: $block_name"
    cd "$block_dir"
    if [ -f "package-lock.json" ]; then
        npm ci
    else
        npm install
    fi
    npm run build
    cd "$ROOT"

    release_block_dir="$RELEASE_DIR/blocks/$block_name"
    rm -rf "$release_block_dir/src"
    rm -rf "$release_block_dir/node_modules"
    rm -f "$release_block_dir/package.json"
    rm -f "$release_block_dir/package-lock.json"
    rm -f "$release_block_dir/readme.txt"
    rm -rf "$release_block_dir/.editorconfig"
    rm -rf "$release_block_dir/.gitignore"

    if [ -d "$block_dir/build" ]; then
        mkdir -p "$release_block_dir/build"
        cp -r "$block_dir/build/." "$release_block_dir/build/"
    fi

    block_php_file="$block_name.php"
    if [ -f "$block_dir/$block_php_file" ]; then
        cp "$block_dir/$block_php_file" "$release_block_dir/"
    else
        echo "⚠️  Warning: $block_php_file not found for $block_name"
    fi

    block_count=$((block_count + 1))
    log "    ✅ $block_name built and structured"
}

if [ "$PARALLEL" = true ]; then
    log "Parallel build enabled."
    PIDS=()
    for block_name in "${BLOCKS_TO_BUILD[@]}"; do
        (build_block "$block_name") &
        PIDS+=($!)
    done
    for pid in "${PIDS[@]}"; do
        wait "$pid"
    done
else
    for block_name in "${BLOCKS_TO_BUILD[@]}"; do
        build_block "$block_name"
    done
fi

BUILD_END=$(date +%s)
BUILD_TIME=$((BUILD_END - BUILD_START))
echo "  📊 Built $block_count blocks in ${BUILD_TIME}s"
echo ""

echo "🎨 Compiling SCSS to CSS for main theme..."
if [ -d "./includes/scss" ]; then
    if command -v sass >/dev/null 2>&1; then
        sass --no-source-map ./includes/scss:./includes/css
        echo "Finished compiling SCSS to CSS"
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
        [[ "$pattern" =~ ^[[:space:]]*$ ]] && continue
        [[ "$pattern" =~ ^[[:space:]]*# ]] && continue

        if [ "$DRY_RUN" = "true" ]; then
            echo "Would remove: $pattern"
        else
            rm -rf $pattern 2>/dev/null || true
        fi
    done < "$ROOT/.releaseignore"
    cd "$ROOT"
else
    echo "No .releaseignore file found, skipping cleanup."
fi
echo ""

echo "🏁 Release build complete: $RELEASE_TAG"