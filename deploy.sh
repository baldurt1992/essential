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
  # git pull en bare actualiza refs; NO rellena el work tree. Sin checkout, httpdocs queda stale.
  git --git-dir="$BARE_REPO" fetch origin "refs/heads/${BRANCH}:refs/heads/${BRANCH}"
  git --git-dir="$BARE_REPO" --work-tree="$WORK_TREE" checkout -f "$BRANCH"
  log "httpdocs sincronizado con commit: $(git --git-dir="$BARE_REPO" --work-tree="$WORK_TREE" rev-parse HEAD)"
fi

cd "$WORK_TREE"

rm -f public/hot

# Si este sha256 no coincide con tu máquina en el mismo commit, el checkout no está escribiendo en este árbol
# (otra ruta, permisos, caché de FS). Referencia main@8cb5a9c: ServicesPage.vue = fcbeedba57fda11d...
SP="$WORK_TREE/resources/js/components/pages/ServicesPage.vue"
if [ -f "$SP" ]; then
  log "sha256 ServicesPage.vue: $(sha256sum "$SP" | awk '{print $1}')"
fi

if [ -d "${WORK_TREE}/.git" ]; then
  if ! git -C "$WORK_TREE" diff-files --quiet -- resources/js resources/css vite.config.js 2>/dev/null; then
    log "WARN: archivos fuente distintos al índice Git (resources/js|css o vite.config):"
    git -C "$WORK_TREE" diff-files --stat -- resources/js resources/css vite.config.js | head -20 || true
  fi
else
  if ! git --git-dir="$BARE_REPO" --work-tree="$WORK_TREE" diff-files --quiet -- resources/js resources/css vite.config.js 2>/dev/null; then
    log "WARN: archivos fuente distintos al índice Git (resources/js|css o vite.config):"
    git --git-dir="$BARE_REPO" --work-tree="$WORK_TREE" diff-files --stat -- resources/js resources/css vite.config.js | head -20 || true
  fi
fi

log "composer install"
composer install --no-dev --optimize-autoloader --no-interaction

log "npm ci"
npm ci

rm -rf "$WORK_TREE/node_modules/.vite"

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
  log "Listo. httpdocs HEAD: $(git --git-dir="$BARE_REPO" --work-tree="$WORK_TREE" rev-parse HEAD) (bare refs/heads/$BRANCH alineado)"
fi
