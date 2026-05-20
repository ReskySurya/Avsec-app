#!/usr/bin/env bash
# =====================================================================
# AVSEC - Build helper
# Build asset Vite di HOST (bukan di image), lalu build docker image.
#
# Alasan asset di-build di host:
#   - public/ di-bind mount dari host ke container, jadi kalau asset
#     dibangun di image, mount akan menimpanya (B1 dari QA review).
#   - Server butuh Node 20+ dan npm/pnpm untuk menjalankan ini.
#
# Usage:
#   ./deploy/build.sh           # full: composer? skip, npm + docker
#   ./deploy/build.sh --skip-npm  # skip npm step
# =====================================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

cd "${PROJECT_ROOT}"

SKIP_NPM=false
for arg in "$@"; do
    case "$arg" in
        --skip-npm) SKIP_NPM=true ;;
        *) echo "Unknown arg: $arg"; exit 1 ;;
    esac
done

# ---------------------------------------------------------------------
# 1. Cek prasyarat
# ---------------------------------------------------------------------
if ! command -v docker >/dev/null 2>&1; then
    echo "ERROR: docker tidak ditemukan."
    exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
    echo "ERROR: docker compose plugin tidak ditemukan."
    exit 1
fi

# ---------------------------------------------------------------------
# 2. Build asset frontend (di host)
# ---------------------------------------------------------------------
if [ "${SKIP_NPM}" = "false" ]; then
    if ! command -v npm >/dev/null 2>&1; then
        echo "ERROR: npm tidak ditemukan. Install Node 20+ atau gunakan --skip-npm."
        exit 1
    fi

    echo "==> Installing npm dependencies..."
    npm ci --no-audit --no-fund

    echo "==> Building Vite assets..."
    npm run build

    if [ ! -d "public/build" ]; then
        echo "ERROR: public/build/ tidak ter-generate. Build gagal."
        exit 1
    fi

    echo "==> Asset Vite siap di public/build/"
fi

# ---------------------------------------------------------------------
# 3. Build docker image
# ---------------------------------------------------------------------
echo "==> Building docker image avsec-app:latest..."
docker compose build app

echo ""
echo "==> Build selesai."
echo ""
echo "Langkah berikutnya:"
echo "  docker compose up -d"
echo "  docker compose ps"
echo "  docker compose logs -f app"
