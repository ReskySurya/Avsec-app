#!/bin/sh
# =====================================================================
# AVSEC - container entrypoint
# Runs once at container start before exec'ing the main command (php-fpm,
# queue worker, or scheduler).
# Order of operations:
#   1. (root) Fix bind-mount ownership, then drop to www-data via gosu
#   2. Wait for MySQL (native on host, via host.docker.internal)
#   3. APP_KEY sanity check
#   4. storage:link (relative, idempotent)
#   5. Run migrations (configurable via RUN_MIGRATIONS)
#   6. Cache config / routes / views / events
#   7. exec the requested process
# =====================================================================
set -e

APP_PATH="${APP_PATH:-/var/www/html}"
DB_HOST="${DB_HOST:-host.docker.internal}"
DB_PORT="${DB_PORT:-3306}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-true}"
RUN_OPTIMIZE="${RUN_OPTIMIZE:-true}"
WAIT_FOR_DB="${WAIT_FOR_DB:-true}"

log() {
    printf '[entrypoint] %s\n' "$*"
}

cd "$APP_PATH"

# ---------------------------------------------------------------------
# 1. Bind-mount ownership fix + privilege drop
# Storage and public are bind-mounted from the host filesystem. The
# host directories may be owned by an arbitrary UID (e.g. the deploy
# user). We chown them to www-data:www-data inside the container so
# php-fpm can read/write. This is safe because the host UID for
# www-data is propagated by the bind mount to the host as well — so
# admins should `chown -R 33:33 storage` on the host (see setup-host.sh).
#
# Cron (scheduler) must run as root; set SKIP_DROP_PRIVILEGES=true to
# stay as root after this step.
# ---------------------------------------------------------------------
if [ "$(id -u)" = "0" ]; then
    log "running as root: ensuring writable storage / bootstrap-cache..."
    # Only chown directories we know we need; never touch /var/www/html
    # recursively (would defeat image caching of vendor/).
    chown -R www-data:www-data \
        "$APP_PATH/storage" \
        "$APP_PATH/bootstrap/cache" 2>/dev/null || true
    chmod -R ug+rwX \
        "$APP_PATH/storage" \
        "$APP_PATH/bootstrap/cache" 2>/dev/null || true

    # Tighten private uploads (read/exec only for owner+group)
    if [ -d "$APP_PATH/storage/app/private" ]; then
        chmod -R 750 "$APP_PATH/storage/app/private" 2>/dev/null || true
    fi

    if [ "${SKIP_DROP_PRIVILEGES:-false}" != "true" ]; then
        log "dropping privileges to www-data..."
        exec gosu www-data "$0" "$@"
    fi
fi

# ---------------------------------------------------------------------
# 2. Wait for MySQL (native on host)
# ---------------------------------------------------------------------
if [ "$WAIT_FOR_DB" = "true" ]; then
    log "waiting for native MySQL at ${DB_HOST}:${DB_PORT}..."
    i=0
    until mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" \
            -u"${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" --silent >/dev/null 2>&1; do
        i=$((i + 1))
        if [ "$i" -gt 60 ]; then
            log "ERROR: database not reachable after 60 attempts (~120s)."
            log "  - Pastikan MySQL native bind ke 172.17.0.1 atau 0.0.0.0"
            log "  - Pastikan user MySQL: 'avsec'@'172.%.%.%' atau '%'"
            log "  - Cek firewall: ufw allow from 172.17.0.0/16 to any port 3306"
            exit 1
        fi
        sleep 2
    done
    log "database is reachable."
fi

# ---------------------------------------------------------------------
# 3. APP_KEY sanity check
# ---------------------------------------------------------------------
if [ -z "${APP_KEY:-}" ] || [ "${APP_KEY}" = "base64:" ]; then
    log "WARNING: APP_KEY is empty in environment. Generate one with:"
    log "  docker compose exec app php artisan key:generate --show"
    log "Then put the value into .env.docker and restart."
fi

# ---------------------------------------------------------------------
# 4. storage:link (relative + force, idempotent)
# Use --relative so the symlink survives bind-mount paths differing
# between host and container.
# ---------------------------------------------------------------------
log "ensuring public/storage symlink (relative)..."
php artisan storage:link --relative --force >/dev/null 2>&1 || \
    php artisan storage:link --force >/dev/null 2>&1 || true

# ---------------------------------------------------------------------
# 5. Migrations
# ---------------------------------------------------------------------
if [ "$RUN_MIGRATIONS" = "true" ]; then
    log "running migrations..."
    php artisan migrate --force --no-interaction
fi

# ---------------------------------------------------------------------
# 6. Cache optimizations
# ---------------------------------------------------------------------
if [ "$RUN_OPTIMIZE" = "true" ]; then
    log "caching config / routes / views / events..."
    php artisan config:cache
    php artisan route:cache  || log "route:cache skipped (closures in routes?)"
    php artisan view:cache
    php artisan event:cache  || true
fi

# ---------------------------------------------------------------------
# 7. Hand off to the requested process
# ---------------------------------------------------------------------
log "starting: $*"
exec "$@"
