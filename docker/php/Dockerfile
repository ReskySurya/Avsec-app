# syntax=docker/dockerfile:1.7
# =====================================================================
# AVSEC Production Image
# Multi-stage build:
#   1. vendor   -> install PHP dependencies via Composer
#   2. runtime  -> php-fpm + Chromium + Node + Puppeteer (for Browsershot)
#
# CATATAN: Asset Vite (public/build/) DIBANGUN DI HOST sebelum
# `docker compose build`, lalu di-bind mount ke container via
# ./public:/var/www/html/public (lihat deploy/build.sh).
# =====================================================================

# ---------------------------------------------------------------------
# Stage 1: PHP vendor (Composer)
# ---------------------------------------------------------------------
FROM docker.io/library/php:8.3-cli-bookworm AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip curl \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libzip-dev libicu-dev libonig-dev libxml2-dev libgmp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath gd zip exif intl pcntl opcache gmp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=docker.io/library/composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress

# Copy the rest of the application source for autoload generation
COPY . .

# Generate optimized, authoritative classmap
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative


# ---------------------------------------------------------------------
# Stage 2: Runtime (PHP-FPM + Chromium + Puppeteer for Browsershot)
# ---------------------------------------------------------------------
FROM docker.io/library/php:8.3-fpm-bookworm AS runtime

# Tell Puppeteer to use the system-installed Chromium instead of
# downloading its own copy (saves ~200MB and avoids sandbox issues).
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium \
    NODE_PATH=/usr/lib/node_modules \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    DEBIAN_FRONTEND=noninteractive

# ---- System packages -------------------------------------------------
# - chromium + fonts: Browsershot/Puppeteer rendering
# - cron: scheduler
# - default-mysql-client: mysqladmin ping in entrypoint
# - procps: pgrep for healthchecks
# - gosu: drop privileges from root to www-data in entrypoint
# - build deps for PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates curl gnupg git unzip \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libzip-dev libicu-dev libonig-dev libxml2-dev libgmp-dev \
        chromium fonts-liberation fonts-noto fonts-noto-cjk fonts-noto-color-emoji \
        cron default-mysql-client procps gosu \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath gd zip exif intl pcntl opcache gmp \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/* /tmp/* /var/tmp/*

# Install Puppeteer globally (required by Spatie Browsershot at runtime)
# Pinned to v24 to match project's package.json.
RUN npm install -g puppeteer@24 \
    && npm cache clean --force

# Composer binary for runtime tasks (e.g. troubleshooting in container)
COPY --from=docker.io/library/composer:2 /usr/bin/composer /usr/bin/composer

# ---- Application code -----------------------------------------------
WORKDIR /var/www/html

# Copy installed vendor + source from vendor stage
COPY --from=vendor --chown=www-data:www-data /app /var/www/html

# Run package:discover so bootstrap/cache/packages.php is pre-warmed
# (composer install used --no-scripts so this didn't happen yet).
RUN php artisan package:discover --ansi || true

# ---- PHP / FPM configuration ----------------------------------------
COPY docker/php/php.ini       /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/opcache.ini   /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/www.conf      /usr/local/etc/php-fpm.d/zz-www.conf

# ---- Entrypoint -----------------------------------------------------
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ---- Permissions ----------------------------------------------------
# Storage and bootstrap/cache must be writable by www-data.
# Bind mounts will REPLACE storage/ and public/ at runtime, but we
# still seed correct ownership for bootstrap/cache and for the
# fallback case where bind mounts are not used.
RUN mkdir -p \
        storage/app/public \
        storage/app/private \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/testing \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

# Container starts as root; entrypoint will drop privileges to www-data
# via gosu (except for the scheduler which needs root to run cron).
EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]
