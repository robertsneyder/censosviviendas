#!/bin/bash
# Ejecutar UNA VEZ en el servidor Hostinger vía SSH (primer despliegue)
set -e

echo "=== Censo Viviendas - Configuración inicial Hostinger ==="

if [ ! -f .env ]; then
    echo "ERROR: Crea el archivo .env antes de ejecutar este script."
    echo "Copia .env.production.example a .env y completa los valores."
    exit 1
fi

php artisan key:generate --force
php artisan storage:link || true
php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo ""
echo "=== Configuración completada ==="
echo "Sitio: https://censosviviendas.robertsneyder.co"
echo "Admin: https://censosviviendas.robertsneyder.co/admin"
echo "Usuario: admin@censosviviendas.co"
