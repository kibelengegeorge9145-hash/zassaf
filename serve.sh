#!/usr/bin/env bash
# Starts the Laravel dev server with the local SQLite PDO driver enabled.
# (pdo_sqlite is not installed system-wide; we load it from .php/sqlite/)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEFAULT_SCAN="$(php --ini 2>/dev/null | grep -oP 'additional .ini files parsed \K/.*(?=:$)' | head -1)"
export PHP_INI_SCAN_DIR="${DEFAULT_SCAN}:${SCRIPT_DIR}/.php/sqlite/conf"
exec php artisan serve "$@"
