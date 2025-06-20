#!/usr/bin/env bash
set -euo pipefail

# —————————————————————————————————————————————————————————————
# Helpers
# —————————————————————————————————————————————————————————————
check_cmd() {
    command -v "$1" >/dev/null 2>&1 ||
        {
            echo >&2 "✗ ERROR: '$1' is required but not installed."
            exit 1
        }
}

# —————————————————————————————————————————————————————————————
# 1. Check prerequisites
# —————————————————————————————————————————————————————————————
echo "⏳ Checking prerequisites…"
check_cmd php
check_cmd composer
check_cmd python3

# —————————————————————————————————————————————————————————————
# 2. Install PHP dependencies (only if vendor/ is missing)
# —————————————————————————————————————————————————————————————
if [[ ! -d vendor ]]; then
    echo "⏳ Installing PHP dependencies via Composer…"
    composer install --no-interaction --optimize-autoloader
else
    echo "✔️  PHP dependencies already installed; skipping."
fi

# —————————————————————————————————————————————————————————————
# 3. Set up Python venv & ensure PuLP is installed
# —————————————————————————————————————————————————————————————
PY_DIR="pythonSolver"
VENV_DIR="$PY_DIR/venv"

if [[ -d "$PY_DIR" ]]; then
    # create venv if missing
    if [[ ! -d "$VENV_DIR" ]]; then
        echo "⏳ Creating Python virtual environment…"
        python3 -m venv "$VENV_DIR"
    else
        echo "✔️  Python venv already exists; skipping creation."
    fi

    # figure out where activate lives
    if [[ -f "$VENV_DIR/bin/activate" ]]; then
        ACTIVATE="$VENV_DIR/bin/activate"
    elif [[ -f "$VENV_DIR/Scripts/activate" ]]; then
        ACTIVATE="$VENV_DIR/Scripts/activate"
    else
        echo "✗ ERROR: Could not find activate script under '$VENV_DIR/bin' or '$VENV_DIR/Scripts'."
        read -p "Press [Enter] to exit…"
        exit 1
    fi

    echo "⏳ Activating venv ($ACTIVATE) and ensuring PuLP is installed…"
    # shellcheck disable=SC1090
    source "$ACTIVATE"

    if ! pip show pulp >/dev/null 2>&1; then
        pip install --upgrade pip
        echo "⏳ Installing PuLP…"
        pip install pulp
    else
        echo "✔️  PuLP already installed; skipping."
    fi

    deactivate
else
    echo "✗ ERROR: Directory '$PY_DIR' not found; please check your project layout."
    read -p "Press [Enter] to exit…"
    exit 1
fi

echo "✅ install.sh completed successfully!"
read -p "Press [Enter] to exit…"
