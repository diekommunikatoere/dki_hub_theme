#!/usr/bin/env bash
set -euo pipefail

# DKI Hub Theme Release Build Script (Worktree-based)
# 1. Creates a temporary git worktree for release branch
# 2. Copies main branch contents into worktree
# 3. Builds blocks in-place in worktree
# 4. Prunes dev/source files
# 5. Commits and pushes to release branch

ROOT="$(pwd)"
RELEASE_BRANCH="release"
MAIN_BRANCH="main"
TIMESTAMP=$(date -u +%Y%m%dT%H%M%SZ)
RELEASE_TAG="${RELEASE_TAG:-dev-$TIMESTAMP}"
WORKTREE_DIR="$ROOT/.release-worktree"

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
            npm ci --silent
        else
            npm install --silent
        fi
        npm run build --silent
        cd "$WORKTREE_DIR"
        block_count=$((block_count + 1))
        echo "    ✅ $block_name built successfully"
    fi
done
echo "  📊 Built $block_count blocks"

echo ""
echo "🧹 Pruning dev/source files from blocks..."
for block_dir in "$WORKTREE_DIR/blocks"/*/; do
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