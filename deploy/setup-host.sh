#!/usr/bin/env bash
# =====================================================================
# AVSEC - Host setup helper
# Jalankan SEKALI saat pertama setup project di server.
# Memastikan:
#   - direktori storage/ ada dengan struktur lengkap
#   - bootstrap/cache writable
#   - owner = UID 33 (www-data di container) supaya php-fpm bisa
#     baca/tulis. UID 33 adalah default www-data di Debian/Ubuntu.
#   - private uploads (storage/app/private) di-chmod 750
#
# Usage:
#   sudo ./deploy/setup-host.sh
# =====================================================================
set -euo pipefail

# Resolve project root (parent dari direktori script ini)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"

WWW_UID="${WWW_UID:-33}"      # www-data di container (Debian)
WWW_GID="${WWW_GID:-33}"

echo "==> Project root: ${PROJECT_ROOT}"
echo "==> Target ownership: ${WWW_UID}:${WWW_GID}"

# Wajib root karena chown UID 33
if [ "$(id -u)" != "0" ]; then
    echo "ERROR: Jalankan dengan sudo (butuh chown ke UID 33)."
    exit 1
fi

cd "${PROJECT_ROOT}"

# ---------------------------------------------------------------------
# 1. Pastikan struktur storage lengkap
# ---------------------------------------------------------------------
echo "==> Membuat struktur direktori storage/..."
mkdir -p \
    storage/app/public \
    storage/app/private/private/documents \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache

# .gitignore di tiap subdirektori (Laravel standard)
for d in \
    storage/app \
    storage/app/public \
    storage/app/private \
    storage/framework \
    storage/framework/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache
do
    if [ ! -f "${d}/.gitignore" ]; then
        printf "*\n!.gitignore\n" > "${d}/.gitignore"
    fi
done

# ---------------------------------------------------------------------
# 2. Ownership & permissions
# ---------------------------------------------------------------------
echo "==> Setting ownership ${WWW_UID}:${WWW_GID} pada storage/ dan bootstrap/cache..."
chown -R "${WWW_UID}:${WWW_GID}" storage bootstrap/cache

echo "==> Setting permissions: 755 directories, 644 files (writable group)..."
find storage bootstrap/cache -type d -exec chmod 2775 {} \;   # setgid agar file baru inherit group
find storage bootstrap/cache -type f -exec chmod 664 {} \;

# Private uploads: lebih ketat (750 dir, 640 file)
if [ -d storage/app/private ]; then
    echo "==> Tightening permissions on storage/app/private (750 / 640)..."
    find storage/app/private -type d -exec chmod 750 {} \;
    find storage/app/private -type f -exec chmod 640 {} \;
fi

# ---------------------------------------------------------------------
# 3. .env.docker permission check
# ---------------------------------------------------------------------
if [ -f .env.docker ]; then
    echo "==> Securing .env.docker (chmod 600)..."
    chmod 600 .env.docker
    chown root:root .env.docker
else
    echo "WARNING: .env.docker belum ada. Salin dari .env.docker.example dan isi credential."
fi

# ---------------------------------------------------------------------
# 4. Project root permission check (anti world-readable)
# ---------------------------------------------------------------------
echo "==> Setting project root permission 750..."
chmod 750 "${PROJECT_ROOT}"

# ---------------------------------------------------------------------
# Done
# ---------------------------------------------------------------------
echo ""
echo "==> Setup selesai. Verifikasi:"
ls -ld "${PROJECT_ROOT}" storage storage/app/private storage/app/public bootstrap/cache 2>/dev/null || true
echo ""
echo "Langkah berikutnya:"
echo "  1. Pastikan .env.docker sudah diisi (DB_PASSWORD, APP_URL, dll)"
echo "  2. Jalankan: ./deploy/build.sh"
echo "  3. Jalankan: docker compose up -d"
