#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_DIR_NAME="print-my-blog"
OUTPUT_PATH="${1:-$ROOT_DIR/dist/${PLUGIN_DIR_NAME}.zip}"
OUTPUT_DIR="$(dirname "$OUTPUT_PATH")"
STAGING_DIR="$ROOT_DIR/dist/${PLUGIN_DIR_NAME}"

rm -rf "$STAGING_DIR"
mkdir -p "$STAGING_DIR" "$OUTPUT_DIR"

rsync -a \
  --delete \
  --exclude '.git/' \
  --exclude '.github/' \
  --exclude '.idea/' \
  --exclude '.vscode/' \
  --exclude '.env' \
  --exclude 'node_modules/' \
  --exclude 'dist/' \
  --exclude 'tests/' \
  --exclude 'playwright-report/' \
  --exclude 'test-results/' \
  "$ROOT_DIR/" "$STAGING_DIR/"

rm -f "$OUTPUT_PATH"
(
  cd "$ROOT_DIR/dist"
  zip -qr "$(basename "$OUTPUT_PATH")" "$PLUGIN_DIR_NAME"
)

printf 'Created %s\n' "$OUTPUT_PATH"
