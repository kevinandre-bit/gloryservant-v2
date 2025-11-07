#!/usr/bin/env bash
set -euo pipefail

# Scan auth views for inline scripts or inline event attributes.
# Exit non-zero if any are found to help maintain CSP.

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
TARGET_DIR="$ROOT/resources/views/auth"

if [ ! -d "$TARGET_DIR" ]; then
  echo "No auth views directory found at $TARGET_DIR; skipping."
  exit 0
fi

err=0

# 1) Inline <script> tags without src= and not JSON config blocks
if grep -RIn --exclude-dir=vendor --include='*.blade.php' '<script' "$TARGET_DIR" | grep -vE 'type="application/json"|src=' >/tmp/csp_inline_scripts.txt 2>/dev/null; then
  echo "Found inline <script> tags in auth views (not allowed):"
  cat /tmp/csp_inline_scripts.txt
  err=1
fi

# 2) Inline event handlers like onclick= on elements
if grep -RIn --exclude-dir=vendor --include='*.blade.php' -E 'on[a-zA-Z]+\s*=' "$TARGET_DIR" >/tmp/csp_inline_handlers.txt 2>/dev/null; then
  echo "Found inline event handlers in auth views (not allowed):"
  cat /tmp/csp_inline_handlers.txt
  err=1
fi

exit $err

