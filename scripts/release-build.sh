#!/usr/bin/env bash
set -euo pipefail

# DKI Hub Theme Release Build Script (Worktree-based, dev parity)
# 1. Creates a temporary git worktree for release branch
# 2. Copies main branch contents into worktree
# 3. Builds blocks in-place in worktree
# 4. Compiles SCSS to CSS
# 5. Removes files/dirs listed in .releaseignore
# 6. Global cleanup: removes dev/source files and empty dirs
# 7. Commits and pushes to release branch

ROOT="$(pwd)"
RELEASE_BRANCH="release"
MAIN_BRANCH="main"
TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RELEASE_TAG="${RELEASE_TAG:-dev-$TIMESTAMP}"
WORKTREE_DIR="$ROOT/.release-worktree"
DRY_RUN="${DRY_RUN:-false}"

echo "🚀 Starting DKI Hub Theme release build (worktree)..."
echo "   Release branch: $RELEASE_BRANCH"
echo "   Main branch: $MAIN_BRANCH"
echo "   Timestamp: $TIMESTAMP"

# Clean up previous worktree if exists
if [ -d "$WORKTREE_DIR" ]; then
    echo "🧹 Removing previous worktree..."
    rm -rf "$WORKTREE_DIR"
    git worktree prune
fi

echo "🔀 Creating worktree for $RELEASE_BRANCH..."
git fetch origin
if ! git show-ref --verify --quiet "refs/heads/$RELEASE_BRANCH"; then
    git branch "$RELEASE_BRANCH" "$MAIN_BRANCH"
fi
git worktree add "$WORKTREE_DIR" "$RELEASE_BRANCH"

echo "🔄 Resetting worktree to main branch contents..."
cd "$WORKTREE_DIR"
git reset --hard "origin/$MAIN_BRANCH"

echo ""
echo "📦 Building blocks in worktree blocks/..."
block_count=0
for block_dir in "$WORKTREE_DIR/blocks"/*/; do
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
        cd "$WORKTREE_DIR"
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built successfully"
    fi
done
echo "  📊 Built $block_count blocks"

echo ""
echo "🎨 Compiling SCSS to CSS for main theme..."
if [ -d "./includes/scss" ]; then
    if command -v sass >/dev/null 2>&1; then
        sass --no-source-map ./includes/scss:./includes/css
        echo "Finished compiling SCSS to CSS"
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
            find . -path "./$pattern" -exec rm -rf {} +
        fi
    done < "$ROOT/.releaseignore"
    echo "Finished removing files from .releaseignore"
fi

echo ""
echo "🔒 Committing and pushing to $RELEASE_BRANCH..."
git add .
git commit -m "Release build: $RELEASE_TAG"
git push origin "$RELEASE_BRANCH"

echo ""
echo "🧹 Cleaning up worktree..."
cd "$ROOT"
git worktree remove "$WORKTREE_DIR" --force
git worktree prune

echo ""
echo "✅ Release branch updated and pushed!"
echo "🎉 Ready for deployment!"