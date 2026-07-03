# Censo Viviendas — Junta Niño Jesús

Plataforma web responsive para el censo de viviendas del Barrio Niño Jesús, con panel de administración, control de usuarios/permisos y división territorial.

**Producción:** https://censosviviendas.robertsneyder.co

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8 (producción) o SQLite (desarrollo local)

## Instalación local (XAMPP)

```bash
# Usar PHP 8.2
D:\php82\php.exe d:\xampp\php\composer.phar install
npm install && npm run build

# Configurar .env (ya incluye SQLite por defecto)
D:\php82\php.exe artisan migrate:fresh --seed
```

Acceso local: `http://localhost/censosviviendas/public`

## Credenciales iniciales

| Campo | Valor |
|-------|-------|
| Email | admin@censosviviendas.co |
| Contraseña | Admin2026! |

## Estructura principal

- `/` — Landing pública
- `/admin` — Panel Filament (gestión)
- `/censo/nuevo` — Formulario wizard responsive (8 pasos)

## Roles

- `super_admin` — Acceso total
- `administrador` — Gestión del barrio
- `coordinador` — Censos y reportes de su comuna
- `censista` — Crear/editar censos de su sector
- `consulta` — Solo lectura

## Despliegue Hostinger

1. Subir código vía Git o FTP
2. Apuntar el subdominio al directorio `/public`
3. Configurar `.env` con credenciales MySQL de Hostinger
4. Ejecutar: `php artisan migrate --seed` y `php artisan config:cache`

## Territorio precargado

Atlántico > Barranquilla > Comuna Metropolitana > Barrio Niño Jesús > Sectores:
Calle Real, Los Balcones, Calle de La Bombonera, Los Almendros, La Draga
