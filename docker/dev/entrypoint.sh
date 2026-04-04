#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -d node_modules ]; then
  npm install
fi

# Ensure writable runtime directories exist inside the mounted workspace.
mkdir -p storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache || true

# Prevent stale local config cache from overriding docker-compose env vars.
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php
php artisan config:clear >/dev/null 2>&1 || true

# Ensure APP_KEY exists for local/dev containers.
if grep -q '^APP_KEY=$' .env; then
  php artisan key:generate --force
fi

if [ "${DB_CONNECTION}" = "pgsql" ]; then
  echo "Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT}..."
  until php -r '
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "5432";
    $db = getenv("DB_DATABASE") ?: "monday_prayer";
    $user = getenv("DB_USERNAME") ?: "monday_user";
    $pass = getenv("DB_PASSWORD") ?: "monday_pass";
    try {
      new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
      exit(0);
    } catch (Throwable $e) {
      exit(1);
    }
  '; do
    sleep 2
  done
fi

php artisan migrate --force

exec "$@"
