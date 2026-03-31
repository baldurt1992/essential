#!/usr/bin/env bash
# Ejecutar en el servidor por SSH desde GitHub Actions (ver deploy.yml).
# Fases: PHASE=1 solo Git; PHASE=2 composer/artisan (tras rsync de public/build desde CI).
set -euo pipefail

log() { printf '[ci-remote] %s\n' "$*"; }

PHASE="${PHASE:?}"
DEPLOY_PATH="${DEPLOY_PATH:?}"
TARGET_SHA="${TARGET_SHA:?}"
# Vacío o solo espacios = modo clone (httpdocs con .git). No pongas DEPLOY_BARE_REPO en GitHub si no usas bare real.
DEPLOY_BARE_REPO="${DEPLOY_BARE_REPO:-}"
DEPLOY_BARE_REPO="${DEPLOY_BARE_REPO#"${DEPLOY_BARE_REPO%%[![:space:]]*}"}"
DEPLOY_BARE_REPO="${DEPLOY_BARE_REPO%"${DEPLOY_BARE_REPO##*[![:space:]]}"}"

cd "$DEPLOY_PATH"
rm -f public/hot

if [ "$PHASE" = "1" ]; then
  if [ -n "$DEPLOY_BARE_REPO" ]; then
    if [ ! -d "$DEPLOY_BARE_REPO" ]; then
      log "ERROR: DEPLOY_BARE_REPO no existe: $DEPLOY_BARE_REPO"
      exit 1
    fi
    git --git-dir="$DEPLOY_BARE_REPO" fetch origin --prune
    git --git-dir="$DEPLOY_BARE_REPO" --work-tree="$DEPLOY_PATH" checkout -f "$TARGET_SHA"
  else
    git fetch origin
    git checkout -f "$TARGET_SHA"
  fi
  if [ -n "$DEPLOY_BARE_REPO" ]; then
    log "Git OK en $(git --git-dir="$DEPLOY_BARE_REPO" --work-tree="$DEPLOY_PATH" rev-parse HEAD)"
  else
    log "Git OK en $(git -C "$DEPLOY_PATH" rev-parse HEAD)"
  fi
  exit 0
fi

if [ "$PHASE" != "2" ]; then
  log "ERROR: PHASE debe ser 1 o 2"
  exit 1
fi

SKIP_NPM_BUILD="${SKIP_NPM_BUILD:-0}"
RUN_DB_SEED_CLASS="${RUN_DB_SEED_CLASS:-}"

composer install --no-dev --optimize-autoloader --no-interaction

if [ "$SKIP_NPM_BUILD" != "1" ]; then
  npm ci
  rm -rf node_modules/.vite
  npm run build
else
  log "SKIP_NPM_BUILD=1 — public/build viene de CI (rsync)"
fi

php artisan migrate --force

php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan route:cache || log "WARN: route:cache falló (rutas no cacheables); sigue sin caché de rutas"

php artisan queue:restart || log "WARN: queue:restart falló (¿sin cola configurada?)"

if [ -n "$RUN_DB_SEED_CLASS" ]; then
  log "Ejecutando seeder: $RUN_DB_SEED_CLASS"
  php artisan db:seed --force --class="$RUN_DB_SEED_CLASS"
fi

log "Listo PHASE=2"
