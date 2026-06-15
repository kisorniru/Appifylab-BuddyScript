#!/usr/bin/env bash
# Unified environment starter for buddyscript.

set -euo pipefail

MODE="local"
REBUILD_ARGS=()
ENV_EXAMPLE="source/.env.example"
RUN_FRESH_MIGRATIONS=true
COMPOSER_INSTALL_FLAGS=()
COMPOSER_DUMP_FLAGS=()

usage() {
  cat <<'USAGE'
Usage: ./start_server.sh [local|dev|prod] [--rebuild] [--fresh]

Modes:
  local  Uses source/.env.example and starts the frontend dev server.
  dev    Uses source/.env.dev.example and starts the frontend dev server.
  prod   Uses source/.env.prod.example and runs production-safe migrations.

Options:
  --rebuild  Rebuild Docker images before starting containers.
  --fresh    Remove containers, volumes, and orphans before starting.
USAGE
}

if [ "$#" -gt 0 ] && [ "${1#-}" = "$1" ]; then
  MODE="$1"
  shift
fi

while [ "$#" -gt 0 ]; do
  case "$1" in
    --rebuild)
      REBUILD_ARGS=(--build)
      ;;
    --fresh)
      echo "Stopping containers and removing volumes..."
      docker compose down --volumes --remove-orphans
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Unknown option: $1"
      usage
      exit 1
      ;;
  esac
  shift
done

case "$MODE" in
  local)
    ENV_EXAMPLE="source/.env.example"
    ;;
  dev)
    ENV_EXAMPLE="source/.env.dev.example"
    ;;
  prod|production)
    MODE="prod"
    ENV_EXAMPLE="source/.env.prod.example"
    RUN_FRESH_MIGRATIONS=false
    COMPOSER_INSTALL_FLAGS=(--no-dev --optimize-autoloader)
    COMPOSER_DUMP_FLAGS=(-o)
    ;;
  *)
    echo "Unknown mode: $MODE"
    usage
    exit 1
    ;;
esac

copy_env_file() {
  if [ -f "$ENV_EXAMPLE" ]; then
    echo "Using $ENV_EXAMPLE as source/.env..."
    cp "$ENV_EXAMPLE" source/.env
  elif [ ! -f source/.env ]; then
    echo "No $ENV_EXAMPLE found and source/.env does not exist."
    exit 1
  else
    echo "Keeping existing source/.env because $ENV_EXAMPLE was not found."
  fi
}

read_env_value() {
  local key="$1"
  local file="$2"

  if [ ! -f "$file" ]; then
    return 0
  fi

  grep -E "^${key}=" "$file" | tail -n 1 | cut -d '=' -f 2- | sed -e 's/^"//' -e 's/"$//'
}

wait_for_container_running() {
  local container_name="$1"
  local retries=30
  local count=0

  echo "Waiting for $container_name to start..."
  while [ "$count" -lt "$retries" ]; do
    if [ "$(docker inspect --format='{{.State.Running}}' "$container_name" 2>/dev/null || echo false)" = "true" ]; then
      echo "$container_name is running."
      return 0
    fi

    sleep 2
    count=$((count + 1))
  done

  echo "Timed out waiting for $container_name."
  return 1
}

wait_for_redis() {
  echo "Waiting for Redis..."
  until docker exec buddyscript_php php -r "
try {
    \$redis = new Redis();
    \$redis->connect('redis', 6379);
    \$redis->ping();
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    echo "Redis is not ready yet; retrying..."
    sleep 2
  done
}

run_artisan_setup() {
  echo "Installing Composer dependencies..."
  docker exec buddyscript_php composer install "${COMPOSER_INSTALL_FLAGS[@]}"

  echo "Preparing Laravel..."
  docker exec buddyscript_php php artisan key:generate --force
  docker exec buddyscript_php php artisan optimize

  if [ "$RUN_FRESH_MIGRATIONS" = true ]; then
    echo "Resetting and seeding the database..."
    docker exec buddyscript_php php artisan migrate:fresh --seed --force
  else
    echo "Running production-safe migrations..."
    docker exec buddyscript_php php artisan migrate --force
  fi

  echo "Fixing Laravel writable directories..."

  sudo mkdir -p source/storage source/bootstrap/cache
  sudo chmod -R 777 source/storage source/bootstrap/cache

  docker exec buddyscript_php sh -c "mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache &&chmod -R 777 storage bootstrap/cache"

  wait_for_redis

  echo "Restarting PHP services and queue worker..."
  docker compose restart php
  docker exec buddyscript_php php artisan queue:restart
  docker exec buddyscript_php php artisan storage:link
  docker exec buddyscript_php php artisan optimize:clear
  docker exec buddyscript_php composer dump-autoload "${COMPOSER_DUMP_FLAGS[@]}"
}

echo "Starting buddyscript in $MODE mode..."
copy_env_file

export VITE_API_BASE_URL="${VITE_API_BASE_URL:-$(read_env_value FRONTEND_API_URL "$ENV_EXAMPLE")}"
export VITE_API_BASE_URL="${VITE_API_BASE_URL:-$(read_env_value API_URL "$ENV_EXAMPLE")}"
export VITE_API_BASE_URL="${VITE_API_BASE_URL:-$(read_env_value APP_URL "$ENV_EXAMPLE")}"
export VITE_ADMIN_DEV_SERVER_URL="${VITE_ADMIN_DEV_SERVER_URL:-$(read_env_value VITE_DEV_SERVER_URL "$ENV_EXAMPLE")}"
export VITE_ADMIN_DEV_SERVER_URL="${VITE_ADMIN_DEV_SERVER_URL:-http://localhost:5174}"

# Frontend expects the versioned API base URL. Local Laravel runs through nginx at :8080.
if [ -n "${VITE_API_BASE_URL:-}" ] && [[ "$VITE_API_BASE_URL" != */api/v1 ]]; then
  VITE_API_BASE_URL="${VITE_API_BASE_URL%/}/api/v1"
fi

mkdir -p source/storage/framework/{cache,sessions,views} source/storage/logs source/bootstrap/cache

echo "Starting Docker containers..."
docker compose up -d "${REBUILD_ARGS[@]}"

wait_for_container_running buddyscript_php
run_artisan_setup

echo "buddyscript setup complete."
echo "Public React frontend: http://localhost"
echo "Laravel/Inertia app: http://localhost:8080"
echo "Laravel API base URL: ${VITE_API_BASE_URL}"
echo "API documentation: http://localhost:8080/testform/"
echo "Admin Vite HMR only: ${VITE_ADMIN_DEV_SERVER_URL} (Laravel/Inertia assets only)"
