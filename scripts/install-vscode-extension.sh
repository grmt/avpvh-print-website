#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
EXT_SRC="$ROOT_DIR/vscode-secret-storage"
EXT_NAME="printmyblog-vscode-secret-storage"

install_target() {
  local target_base="$1"
  local target_dir="$target_base/$EXT_NAME"

  mkdir -p "$target_base"
  rm -rf "$target_dir"
  cp -R "$EXT_SRC" "$target_dir"
  printf 'Installed local VS Code extension to %s\n' "$target_dir"
}

choose_primary_target_dir() {
  if [[ -n "${VSCODE_EXTENSIONS_DIR:-}" ]]; then
    printf '%s\n' "$VSCODE_EXTENSIONS_DIR"
    return
  fi

  if [[ -d "$HOME/.vscode-server/extensions" ]]; then
    printf '%s\n' "$HOME/.vscode-server/extensions"
    return
  fi

  if [[ -d "$HOME/.vscode/extensions" ]]; then
    printf '%s\n' "$HOME/.vscode/extensions"
    return
  fi

  printf '%s\n' "$HOME/.vscode/extensions"
}

find_windows_extensions_dir() {
  if [[ -n "${WINDOWS_VSCODE_EXTENSIONS_DIR:-}" ]]; then
    printf '%s\n' "$WINDOWS_VSCODE_EXTENSIONS_DIR"
    return
  fi

  if [[ ! -d /mnt/c/Users ]]; then
    return
  fi

  local detected_dir
  detected_dir="$(find /mnt/c/Users -maxdepth 3 -type d -path '*/.vscode/extensions' 2>/dev/null | head -n 1 || true)"
  if [[ -n "$detected_dir" ]]; then
    printf '%s\n' "$detected_dir"
  fi
}

PRIMARY_TARGET_BASE="$(choose_primary_target_dir)"
install_target "$PRIMARY_TARGET_BASE"

WINDOWS_TARGET_BASE="$(find_windows_extensions_dir)"
if [[ -n "$WINDOWS_TARGET_BASE" && "$WINDOWS_TARGET_BASE" != "$PRIMARY_TARGET_BASE" ]]; then
  install_target "$WINDOWS_TARGET_BASE"
fi

printf 'Reload VS Code, then run: Print My Blog: Save WP Admin Password\n'
