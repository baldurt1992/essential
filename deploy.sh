#!/usr/bin/env bash
# Despliegue en Plesk: sincroniza Git con httpdocs y reconstruye backend + frontend.
# public/build no debe estar en Git: un pull/checkout restauraría chunks viejos y pisaría Vite.
# Ajusta BARE_REPO si tu ruta real del bare diffiere (Panel → Git → ruta del repositorio).

set -euo pipefail

WORK_TREE="/var/www/vhosts/web.essentialinnovation.es/httpdocs"
BARE_REPO="/var/www/vhosts/web.essentialinnovation.es/git/essential.git"
BRANCH="main"

log() { printf '[deploy] %s\n' "$*"; }

if [ ! -d "$WORK_TREE" ]; then
  log "ERROR: WORK_TREE no existe: $WORK_TREE"
  exit 1
fi

if [ -d "${WORK_TREE}/.git" ]; then
  log "Modo: clone en httpdocs"
  cd "$WORK_TREE"
  git fetch origin "$BRANCH"
  git reset --hard "origin/${BRANCH}"
else
  if [ ! -d "$BARE_REPO" ]; then
    log "ERROR: httpdocs sin .git y no existe bare repo: $BARE_REPO"
    log "Corrige BARE_REPO en deploy.sh (ruta que muestra Plesk para el repo Git)."
    exit 1
  fi
  log "Modo: bare repo Plesk -> httpdocs"
  export GIT_DIR="$BARE_REPO"
  export GIT_WORK_TREE="$WORK_TREE"
  git pull origin "$BRANCH"
  unset GIT_DIR GIT_WORK_TREE
fi

cd "$WORK_TREE"

rm -f public/hot

log "composer install"
composer install --no-dev --optimize-autoloader --no-interaction

log "npm ci"
npm ci

log "npm run build"
npm run build

primary_js=$(ls -1 public/build/assets/app-*.js 2>/dev/null | head -1 || true)
if [ -n "$primary_js" ]; then
  log "Chunk JS principal en disco: $primary_js"
else
  log "WARN: no hay public/build/assets/app-*.js tras el build"
fi

log "migrate + optimize:clear"
php artisan migrate --force
php artisan optimize:clear

if [ -d "${WORK_TREE}/.git" ]; then
  log "Listo. HEAD httpdocs: $(cd "$WORK_TREE" && git rev-parse HEAD)"
else
  log "Listo. $BRANCH en bare: $(git --git-dir="$BARE_REPO" rev-parse "$BRANCH")"
fi
