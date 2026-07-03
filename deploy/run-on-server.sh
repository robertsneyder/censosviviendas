#!/bin/bash
# Ejecutar en la terminal SSH del hPanel (Avanzado → SSH Access → Open Terminal)
set -e

SITE_DIR="censosviviendas.robertsneyder.co"
REPO="https://github.com/robertsneyder/censosviviendas.git"

echo "=== Despliegue Censo Viviendas ==="
cd ~/domains/$SITE_DIR

# Limpiar carpeta si es primer despliegue (conserva .htaccess de Hostinger si existe)
if [ ! -d .git ]; then
  echo "Clonando repositorio..."
  shopt -s dotglob
  for item in *; do
    [ "$item" != ".htaccess" ] && rm -rf "$item"
  done
  shopt -u dotglob
  git clone $REPO .
fi

git fetch origin
git reset --hard origin/main

# Crear .env si no existe
if [ ! -f .env ]; then
  echo "Creando .env de produccion..."
  cp .env.production.example .env
  nano .env   # Completar DB_PASSWORD y guardar (Ctrl+O, Enter, Ctrl+X)
fi

echo "Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Configurando Laravel..."
php artisan key:generate --force
php artisan storage:link || true
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
chmod -R 775 storage bootstrap/cache

echo ""
echo "=== DESPLIEGUE COMPLETADO ==="
echo "Sitio:  https://censosviviendas.robertsneyder.co"
echo "Admin:  https://censosviviendas.robertsneyder.co/admin"
echo "Email:  admin@censosviviendas.co"
echo "Pass:   Admin2026!  (cambiar despues del primer acceso)"
