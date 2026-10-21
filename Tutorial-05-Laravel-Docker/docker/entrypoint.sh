#!/bin/sh
set -eu
cd /var/www/html
# Nunca generar una clave nueva si la aplicacion ya tiene datos cifrados.
if [ -z "${APP_KEY:-}" ]; then
  echo "ERROR: define APP_KEY (php artisan key:generate --show)." >&2
  exit 1
fi
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
exec "$@"
