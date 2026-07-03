#!/bin/bash
# Despliegue sin GitHub auth - usa descarga ZIP (para terminal hPanel/SSH)
set -e

SITE_DIR="censosviviendas.robertsneyder.co"
cd ~/domains/$SITE_DIR

echo "=== Limpiando carpeta (excepto .htaccess) ==="
shopt -s dotglob
for item in *; do
  [ "$item" != ".htaccess" ] && rm -rf "$item" 2>/dev/null || true
done
shopt -u dotglob

echo "=== Descargando codigo desde GitHub (ZIP) ==="
curl -fsSL -o repo.zip "https://github.com/robertsneyder/censosviviendas/archive/refs/heads/main.zip"
unzip -q repo.zip
shopt -s dotglob
mv censosviviendas-main/* .
shopt -u dotglob
rm -rf censosviviendas-main repo.zip

echo "=== Configurando .env ==="
cp .env.production.example .env
# Editar DB_PASSWORD manualmente si es necesario:
# nano .env

if grep -q "CAMBIAR_PASSWORD" .env; then
  echo "AVISO: Edita .env y pon la contrasena real de MySQL:"
  echo "  nano .env"
  echo "  (busca DB_PASSWORD= y guarda con Ctrl+O, Enter, Ctrl+X)"
  exit 1
fi

echo "=== Instalando dependencias ==="
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== Configurando Laravel ==="
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
echo "https://censosviviendas.robertsneyder.co/admin"
