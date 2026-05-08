#!/usr/bin/env bash
# Build a ZIP WordPress accepts under Appearance → Themes → Install new theme.
# The archive root must contain the theme folder: outlier-collective/style.css
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
THEME_DIR="$ROOT/wp-content/themes/outlier-collective"
OUT="${1:-$ROOT/outlier-coaching-theme.zip}"

if [[ ! -f "$THEME_DIR/style.css" ]]; then
	echo "error: missing $THEME_DIR/style.css" >&2
	exit 1
fi

rm -f "$OUT"
( cd "$ROOT/wp-content/themes" && zip -rq "$OUT" outlier-collective )
echo "Wrote $OUT"
