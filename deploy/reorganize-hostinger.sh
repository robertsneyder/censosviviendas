#!/bin/bash
# Reorganizar Laravel en Hostinger despues de que Git de hPanel rompe public_html
# Uso: cd ~/domains/censosviviendas.robertsneyder.co && bash deploy/reorganize-hostinger.sh
set -e

DOMAIN="censosviviendas.robertsneyder.co"
BASE=~/domains/$DOMAIN
PHP=/opt/alt/php83/usr/bin/php
COMPOSER=$(command -v composer)
REPO_ZIP="https://github.com/robertsneyder/censosviviendas/archive/refs/heads/main.zip"

echo "============================================"
echo " Reorganizar $DOMAIN"
echo "============================================"
cd "$BASE"

# --- 1. Descargar codigo limpio desde GitHub ---
echo ""
echo "[1/6] Descargando codigo desde GitHub..."
cd /tmp
rm -rf censosviviendas-main repo-reorg.zip
curl -fsSL -o repo-reorg.zip "$REPO_ZIP"
unzip -q repo-reorg.zip
SRC=/tmp/censosviviendas-main

# --- 2. Restaurar Laravel en la raiz del dominio (sin tocar .env) ---
echo "[2/6] Restaurando proyecto Laravel en raiz del dominio..."
cd "$BASE"

for item in app bootstrap config database deploy resources routes tests artisan composer.json composer.lock package.json package-lock.json phpunit.xml postcss.config.js tailwind.config.js vite.config.js README.md .editorconfig .gitattributes .gitignore; do
    if [ -e "$SRC/$item" ]; then
        rm -rf "$item" 2>/dev/null || true
        cp -a "$SRC/$item" .
    fi
done

# Restaurar public/ del proyecto
rm -rf public
mkdir -p public
cp -a "$SRC/public/." public/

# Preservar .env existente
if [ ! -f .env ]; then
    if [ -f .env.production.example ]; then
        cp .env.production.example .env
        echo "AVISO: Se creo .env desde plantilla. Edita DB_PASSWORD: nano .env"
    else
        echo "ERROR: No hay .env. Configuralo antes de continuar."
        exit 1
    fi
fi

# --- 3. Limpiar public_html por completo ---
echo "[3/6] Limpiando public_html (eliminando .git, app, vendor, etc.)..."
rm -rf public_html
mkdir -p public_html

# --- 4. Copiar SOLO archivos publicos a public_html ---
echo "[4/6] Copiando archivos publicos a public_html..."
for item in index.php robots.txt favicon.ico; do
    [ -f "public/$item" ] && cp -a "public/$item" public_html/
done
for dir in css js build; do
    [ -d "public/$dir" ] && cp -a "public/$dir" public_html/
done

cat > public_html/.htaccess << 'EOF'
AddHandler application/x-httpd-alt-php83___lsphp .php

<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

cd public_html
ln -sfn ../storage/app/public storage
chmod 755 .
chmod 644 index.php .htaccess 2>/dev/null || true

# --- 5. Composer y Laravel ---
echo "[5/6] Instalando dependencias y configurando Laravel..."
cd "$BASE"
$PHP $COMPOSER install --no-dev --optimize-autoloader --no-interaction --prefer-dist

$PHP artisan storage:link 2>/dev/null || true
$PHP artisan migrate --force 2>/dev/null || true
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# --- 6. Verificacion ---
echo ""
echo "[6/6] Verificacion..."
echo ""
echo "=== Raiz del dominio (debe tener app, vendor, NO ser web) ==="
ls -la "$BASE" | head -20

echo ""
echo "=== public_html (solo archivos web) ==="
ls -la "$BASE/public_html"

ERR=0
test -f "$BASE/public_html/index.php" && echo "OK: index.php en public_html" || { echo "ERROR: falta index.php"; ERR=1; }
test ! -d "$BASE/public_html/app" && echo "OK: sin app en public_html" || { echo "ERROR: app en public_html"; ERR=1; }
test ! -d "$BASE/public_html/vendor" && echo "OK: sin vendor en public_html" || { echo "ERROR: vendor en public_html"; ERR=1; }
test ! -d "$BASE/public_html/.git" && echo "OK: sin .git en public_html" || { echo "ERROR: .git en public_html"; ERR=1; }
test -d "$BASE/app" && echo "OK: app en raiz" || { echo "ERROR: falta app en raiz"; ERR=1; }
test -d "$BASE/vendor" && echo "OK: vendor en raiz" || { echo "ERROR: falta vendor en raiz"; ERR=1; }

rm -rf /tmp/censosviviendas-main /tmp/repo-reorg.zip

echo ""
if [ $ERR -eq 0 ]; then
    echo "============================================"
    echo " LISTO. Estructura correcta."
    echo " Sitio:  https://$DOMAIN"
    echo " Admin:  https://$DOMAIN/admin"
    echo ""
    echo " NO vuelvas a habilitar Git en hPanel."
    echo "============================================"
else
    echo "HAY ERRORES. Revisa los mensajes arriba."
    exit 1
fi
