#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
TARGET_DIR="$ROOT/resources/views"

echo "Scanning all Blade views for CSP issues (report-only)..."

err=0

# Inline <script> without src/type=json/nonce
if grep -RIn --exclude-dir=vendor --include='*.blade.php' '<script' "$TARGET_DIR" | grep -vE 'src=|type="application/json"|nonce=' >/tmp/csp_all_inline.txt 2>/dev/null; then
  echo "[CSP] Inline scripts without nonce/src/type=json:" && cat /tmp/csp_all_inline.txt || true
  err=1
fi

# Inline handlers like onclick=
if grep -RIn --exclude-dir=vendor --include='*.blade.php' -E 'on[a-zA-Z]+\s*=' "$TARGET_DIR" >/tmp/csp_all_handlers.txt 2>/dev/null; then
  echo "[CSP] Inline event handlers detected:" && cat /tmp/csp_all_handlers.txt || true
  err=1
fi

exit $err

