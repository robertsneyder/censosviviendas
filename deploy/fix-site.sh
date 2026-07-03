#!/bin/bash
# Recuperar sitio cuando public_html queda vacio o Git de hPanel lo rompe
set -e

DOMAIN="censosviviendas.robertsneyder.co"
BASE=~/domains/$DOMAIN
PHP=/opt/alt/php83/usr/bin/php
COMPOSER=$(command -v composer)

echo "=== Reparacion sitio $DOMAIN ==="
cd "$BASE"

# Restaurar public/ si esta vacio
if [ ! -f public/index.php ]; then
    echo "public/ vacio — descargando desde GitHub..."
    cd /tmp
    rm -rf censos-fix repo-fix.zip
    curl -fsSL -o repo-fix.zip "https://github.com/robertsneyder/censosviviendas/archive/refs/heads/main.zip"
    unzip -q repo-fix.zip
    mkdir -p "$BASE/public"
    cp -a censos-fix/public/. "$BASE/public/"
    rm -rf /tmp/censos-fix /tmp/repo-fix.zip
    cd "$BASE"
fi

# Reinstalar vendor si falta
if [ ! -d vendor ]; then
    echo "vendor/ no existe — instalando dependencias..."
    $PHP $COMPOSER install --no-dev --optimize-autoloader --no-interaction
fi

# Verificar .env
if [ ! -f .env ]; then
    echo "ERROR: No existe .env. Copia .env.production.example y configura DB_PASSWORD."
    exit 1
fi

# Sincronizar public_html y configurar Laravel
chmod +x deploy/post-deploy.sh
PHP_BIN=$PHP COMPOSER_BIN=$COMPOSER ./deploy/post-deploy.sh

# Permisos web
chmod 755 "$BASE/public_html"
chmod 644 "$BASE/public_html/index.php" "$BASE/public_html/.htaccess" 2>/dev/null || true
chmod -R 755 "$BASE/public_html/css" "$BASE/public_html/js" 2>/dev/null || true

echo ""
echo "=== Verificacion ==="
ls -la "$BASE/public_html/"
test -f "$BASE/public_html/index.php" && echo "OK: index.php presente" || echo "ERROR: falta index.php"
test ! -d "$BASE/public_html/app" && echo "OK: sin app en public_html" || echo "ERROR: app en public_html"
test ! -d "$BASE/public_html/vendor" && echo "OK: sin vendor en public_html" || echo "ERROR: vendor en public_html"

echo ""
echo "Sitio: https://$DOMAIN"
echo "Admin: https://$DOMAIN/admin"
