#!/usr/bin/env bash
# Build a ZIP for WordPress: Appearance → Themes → Install new theme → Upload.
# Archive root must be: outlier-collective/style.css
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="${1:-$ROOT/outlier-coaching-theme.zip}"

if [[ ! -f "$ROOT/wp-content/themes/outlier-collective/style.css" ]]; then
	echo "error: run from repo root; missing wp-content/themes/outlier-collective/style.css" >&2
	exit 1
fi

rm -f "$OUT"
( cd "$ROOT/wp-content/themes" && zip -rq "$OUT" outlier-collective )
echo "OK: $OUT ($(du -h "$OUT" | cut -f1))"
