#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_DIR_NAME="avpvh-print-website"
OUTPUT_PATH="${1:-$ROOT_DIR/dist/${PLUGIN_DIR_NAME}.zip}"
OUTPUT_DIR="$(dirname "$OUTPUT_PATH")"
STAGING_DIR="$ROOT_DIR/dist/${PLUGIN_DIR_NAME}"

rm -rf "$STAGING_DIR"
mkdir -p "$STAGING_DIR" "$OUTPUT_DIR"

# git archive honours export-ignore in .gitattributes: the same files as
# CI's build and the release ZIP (committed state only, so commit first).
git -C "$ROOT_DIR" archive HEAD | tar -x -C "$STAGING_DIR"

rm -f "$OUTPUT_PATH"
(
  cd "$ROOT_DIR/dist"
  zip -qr "$(basename "$OUTPUT_PATH")" "$PLUGIN_DIR_NAME"
)

printf 'Created %s\n' "$OUTPUT_PATH"
