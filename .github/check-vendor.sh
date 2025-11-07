#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"

# Read setting from config/assets.php by grepping the default; this is a best-effort check for CI.
USE_CDN_DEFAULT=true
if grep -q "'use_cdn'\s*=>\s*env\('ASSET_USE_CDN'" "$ROOT/config/assets.php"; then
  : # dynamic; rely on env
fi

if [ "${ASSET_USE_CDN:-}" = "false" ] || [ "${ASSET_USE_CDN:-}" = "0" ]; then
  echo "ASSET_USE_CDN=false; verifying local vendor files exist..."
  missing=0
  for p in \
    assets3/vendor/flatpickr/flatpickr.min.js \
    assets3/vendor/flatpickr/flatpickr.min.css \
    assets3/vendor/html5-qrcode/html5-qrcode.min.js \
    assets3/vendor/cropperjs/cropper.min.js \
    assets3/vendor/cropperjs/cropper.min.css
  do
    if [ ! -f "$ROOT/public/$p" ]; then
      echo "Missing: public/$p"
      missing=1
    fi
  done
  if [ $missing -ne 0 ]; then
    echo "One or more local vendor files are missing. Provide them or set ASSET_USE_CDN=true."
    exit 1
  fi
  echo "All required vendor files found."
else
  echo "ASSET_USE_CDN=true; skipping local vendor file check."
fi

