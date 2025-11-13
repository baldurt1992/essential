#!/bin/bash

# Script de deployment para Plesk
# Este script se ejecuta cuando Plesk recibe el webhook de GitHub Actions

set -e  # Salir si hay algún error

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Directorio del proyecto (ajusta según tu configuración en Plesk)
PROJECT_DIR="/var/www/vhosts/web.essentialinnovation.es/httpdocs"
BRANCH="main"

echo -e "${GREEN}🚀 Iniciando deployment...${NC}"

# Ir al directorio del proyecto
cd "$PROJECT_DIR" || {
    echo -e "${RED}❌ Error: No se pudo acceder al directorio $PROJECT_DIR${NC}"
    exit 1
}

echo -e "${YELLOW}📂 Directorio actual: $(pwd)${NC}"

# Verificar que estamos en un repositorio git
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ Error: No se encontró el directorio .git${NC}"
    exit 1
fi

# Mostrar información del repositorio
echo -e "${YELLOW}📊 Información del repositorio:${NC}"
echo -e "   Rama actual: $(git branch --show-current)"
echo -e "   Remote: $(git remote get-url origin 2>/dev/null || echo 'No configurado')"
echo -e "   Último commit: $(git log -1 --oneline 2>/dev/null || echo 'No hay commits')"

# Mostrar estado actual
echo -e "${YELLOW}📊 Estado actual del repositorio:${NC}"
git status --short || echo -e "${YELLOW}⚠️  No se pudo obtener el estado${NC}"

# Verificar que el remote esté configurado
if ! git remote get-url origin > /dev/null 2>&1; then
    echo -e "${RED}❌ Error: No hay remote configurado${NC}"
    echo -e "${YELLOW}💡 Configurando remote...${NC}"
    git remote add origin https://github.com/baldurt1992/essential.git || {
        echo -e "${RED}❌ Error al configurar remote${NC}"
        exit 1
    }
fi

# Hacer pull de la rama main
echo -e "${YELLOW}⬇️  Haciendo fetch de la rama $BRANCH...${NC}"
git fetch origin "$BRANCH" || {
    echo -e "${RED}❌ Error al hacer fetch${NC}"
    echo -e "${YELLOW}💡 Intentando con configuración alternativa...${NC}"
    git fetch origin || {
        echo -e "${RED}❌ Error al hacer fetch${NC}"
        exit 1
    }
}

# Verificar si hay cambios
LOCAL=$(git rev-parse HEAD 2>/dev/null || echo "")
REMOTE=$(git rev-parse "origin/$BRANCH" 2>/dev/null || echo "")

if [ -z "$LOCAL" ] || [ -z "$REMOTE" ]; then
    echo -e "${YELLOW}⚠️  No se pudo comparar commits, haciendo pull de todas formas...${NC}"
    git pull origin "$BRANCH" || {
        echo -e "${RED}❌ Error al hacer pull${NC}"
        exit 1
    }
    echo -e "${GREEN}✅ Pull completado${NC}"
elif [ "$LOCAL" = "$REMOTE" ]; then
    echo -e "${GREEN}✅ Ya estás actualizado con origin/$BRANCH${NC}"
    echo -e "   Commit: $LOCAL"
else
    echo -e "${YELLOW}🔄 Hay cambios nuevos, haciendo pull...${NC}"
    echo -e "   Local:  $LOCAL"
    echo -e "   Remote: $REMOTE"
    git pull origin "$BRANCH" || {
        echo -e "${RED}❌ Error al hacer pull${NC}"
        exit 1
    }
    echo -e "${GREEN}✅ Pull completado${NC}"
    echo -e "   Nuevo commit: $(git rev-parse HEAD)"
fi

# Limpiar caché de Laravel
echo -e "${YELLOW}🧹 Limpiando caché de Laravel...${NC}"
php artisan config:clear || echo -e "${YELLOW}⚠️  Advertencia: No se pudo limpiar config cache${NC}"
php artisan cache:clear || echo -e "${YELLOW}⚠️  Advertencia: No se pudo limpiar cache${NC}"
php artisan route:clear || echo -e "${YELLOW}⚠️  Advertencia: No se pudo limpiar route cache${NC}"
php artisan view:clear || echo -e "${YELLOW}⚠️  Advertencia: No se pudo limpiar view cache${NC}"

# Ejecutar migraciones si hay pendientes
echo -e "${YELLOW}🗄️  Verificando migraciones pendientes...${NC}"
php artisan migrate --force || {
    echo -e "${YELLOW}⚠️  Advertencia: Hubo un problema con las migraciones${NC}"
}

# Optimizar autoloader de Composer
echo -e "${YELLOW}📦 Optimizando autoloader de Composer...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction || {
    echo -e "${YELLOW}⚠️  Advertencia: Hubo un problema con composer install${NC}"
}

# Optimizar Laravel (opcional, solo si es necesario)
# php artisan config:cache || echo -e "${YELLOW}⚠️  Advertencia: No se pudo cachear config${NC}"
# php artisan route:cache || echo -e "${YELLOW}⚠️  Advertencia: No se pudo cachear routes${NC}"

echo -e "${GREEN}✅ Deployment completado exitosamente${NC}"

