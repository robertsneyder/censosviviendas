#!/bin/bash
# Ejecutar en el servidor después de git pull (GitHub Actions o Git hPanel)
set -e

PHP="${PHP_BIN:-/opt/alt/php83/usr/bin/php}"
COMPOSER="${COMPOSER_BIN:-$(command -v composer)}"

if [ ! -x "$PHP" ]; then
    echo "ERROR: PHP 8.3 no encontrado en $PHP"
    exit 1
fi

if [ -z "$COMPOSER" ] || [ ! -f "$COMPOSER" ]; then
    echo "ERROR: composer no encontrado"
    exit 1
fi

echo "=== Post-deploy Censo Viviendas ==="
echo "PHP: $PHP"

echo "📦 Instalando dependencias..."
"$PHP" "$COMPOSER" install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "🔧 Configurando Laravel..."
"$PHP" artisan storage:link || true
"$PHP" artisan migrate --force

"$PHP" artisan config:clear
"$PHP" artisan config:cache
"$PHP" artisan route:clear
"$PHP" artisan route:cache
"$PHP" artisan view:clear
"$PHP" artisan view:cache
"$PHP" artisan optimize:clear
"$PHP" artisan optimize
"$PHP" artisan cache:clear

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "🌐 Sincronizando public/ → public_html/..."
mkdir -p public_html

for item in index.php robots.txt favicon.ico; do
    if [ -f "public/$item" ]; then
        cp -a "public/$item" public_html/
    fi
done

for dir in css js build; do
    if [ -d "public/$dir" ]; then
        rm -rf "public_html/$dir"
        cp -a "public/$dir" public_html/
    fi
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
rm -f storage
ln -sfn ../storage/app/public storage

echo ""
echo "✅ Post-deploy completado"
echo "   Sitio:  https://censosviviendas.robertsneyder.co"
echo "   Admin:  https://censosviviendas.robertsneyder.co/admin"
