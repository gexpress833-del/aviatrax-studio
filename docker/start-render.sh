#!/bin/sh
set -eu

port="${PORT:-10000}"
external_url="${RENDER_EXTERNAL_URL:-http://localhost}"

case "${APP_KEY:-}" in
	base64:*) ;;
	*) export APP_KEY="base64:${APP_KEY:?APP_KEY must be configured}" ;;
esac

sed -i "s/^Listen 80$/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

export APP_URL="${APP_URL:-${external_url}}"
export ASSET_URL="${ASSET_URL:-${external_url}}"

php artisan config:cache
php artisan view:cache

exec apache2-foreground