#!/bin/bash
set -e

APP_DIR="/var/www/aureuserp"
cd "$APP_DIR"

log() { echo "[aureus-entrypoint] $(date '+%Y-%m-%d %H:%M:%S') $*"; }

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-aureus}"
DB_USERNAME="${DB_USERNAME:-aureus}"
DB_PASSWORD="${DB_PASSWORD:-aureus}"

use_internal_mysql() { [[ "$DB_HOST" == "127.0.0.1" || "$DB_HOST" == "localhost" ]]; }

if use_internal_mysql; then
    log "Mode: INTERNAL MySQL"
    export MYSQL_AUTOSTART=true
else
    log "Mode: EXTERNAL MySQL (${DB_HOST}:${DB_PORT})"
    export MYSQL_AUTOSTART=false
fi

sed_escape() { printf '%s' "$1" | sed -e 's/[\\&|]/\\&/g'; }

set_env() {
    local key="$1" val
    val=$(sed_escape "$2")
    sed -i "s|^${key}=.*|${key}=${val}|" .env
}

log "Applying runtime environment overrides..."
set_env DB_HOST     "$DB_HOST"
set_env DB_PORT     "$DB_PORT"
set_env DB_DATABASE "$DB_DATABASE"
set_env DB_USERNAME "$DB_USERNAME"
set_env DB_PASSWORD "$DB_PASSWORD"

set_env APP_ENV "${APP_ENV:-production}"
set_env APP_DEBUG "${APP_DEBUG:-false}"

[ -n "$APP_URL" ]      && set_env APP_URL      "$APP_URL"
[ -n "$APP_KEY" ]      && set_env APP_KEY      "$APP_KEY"
[ -n "$APP_NAME" ]     && set_env APP_NAME     "\"${APP_NAME}\""
[ -n "$APP_LOCALE" ]   && set_env APP_LOCALE   "$APP_LOCALE"
[ -n "$APP_CURRENCY" ] && set_env APP_CURRENCY "$APP_CURRENCY"
[ -n "$APP_TIMEZONE" ] && set_env APP_TIMEZONE "$APP_TIMEZONE"

if ! use_internal_mysql; then
    log "Waiting for external MySQL at ${DB_HOST}:${DB_PORT}..."
    for i in $(seq 1 60); do
        if php -r "try { new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}'); } catch (Throwable \$e) { exit(1); }" 2>/dev/null; then
            log "External MySQL is reachable."
            break
        fi
        if [ "$i" -eq 60 ]; then
            log "ERROR: cannot reach external MySQL at ${DB_HOST}:${DB_PORT} after 60s."
            exit 1
        fi
        sleep 1
    done
fi

log "Refreshing cached configuration..."

php artisan optimize:clear --no-interaction 2>/dev/null || true

log "Starting services via Supervisor..."

exec "$@"
