# Despliegue en Hostinger — Censo Viviendas

Guía para publicar en **https://censosviviendas.robertsneyder.co**

---

## Resumen rápido

| Paso | Dónde | Acción |
|------|-------|--------|
| 1 | hPanel | Activar SSH y PHP 8.2 |
| 2 | hPanel | Crear base de datos MySQL |
| 3 | hPanel | Apuntar dominio a carpeta `public/` |
| 4 | SSH | Clonar repo y configurar `.env` |
| 5 | SSH | Ejecutar `deploy/first-setup.sh` |
| 6 | GitHub | Configurar secrets para deploy automático |

---

## Paso 1 — Configurar hPanel

### 1.1 Activar SSH
1. Entra a [hPanel Hostinger](https://hpanel.hostinger.com)
2. Ve a **Avanzado → SSH Access**
3. Activa SSH y anota:
   - **IP del servidor** (SSH Host)
   - **Puerto** (suele ser `65002` en shared hosting, o `22`)
   - **Usuario SSH**

### 1.2 Agregar clave SSH (recomendado)
En tu PC (PowerShell):
```powershell
ssh-keygen -t ed25519 -f "$env:USERPROFILE\.ssh\hostinger_censos" -N '""'
Get-Content "$env:USERPROFILE\.ssh\hostinger_censos.pub"
```
Copia la clave pública y pégala en hPanel → SSH Access → **Add SSH Key**.

### 1.3 PHP 8.2+
1. hPanel → **Sitios web** → selecciona `censosviviendas.robertsneyder.co`
2. **PHP Configuration** → versión **8.2** o superior
3. En **PHP Options**, verifica que `exec` **no** esté deshabilitado

### 1.4 Document Root → carpeta `public/`
1. hPanel → **Sitios web** → **Administrar**
2. En **Dominio** o **Configuración avanzada**, cambia el directorio raíz a:
   ```
   domains/censosviviendas.robertsneyder.co/public
   ```
   > Si no puedes cambiar el document root, contacta soporte Hostinger o usa el symlink descrito en la sección alternativa al final.

---

## Paso 2 — Crear base de datos MySQL

1. hPanel → **Bases de datos → MySQL**
2. Crea una base de datos, por ejemplo: `u123456789_censos`
3. Crea un usuario y asígnalo a la base de datos
4. Anota: **host** (normalmente `127.0.0.1`), **nombre BD**, **usuario**, **contraseña**

---

## Paso 3 — Primer despliegue vía SSH

Conéctate al servidor:
```bash
ssh -p 65002 u123456789@TU_IP_SERVIDOR
```

### 3.1 Clonar el repositorio
```bash
cd ~/domains/censosviviendas.robertsneyder.co

# Si la carpeta está vacía o es nueva:
git clone https://github.com/robertsneyder/censosviviendas.git .

# Si ya existe contenido de Hostinger, haz backup primero
```

### 3.2 Configurar `.env` de producción
```bash
cp .env.production.example .env
nano .env
```

Completa estos valores:
```env
APP_KEY=                          # se genera en el siguiente paso
APP_URL=https://censosviviendas.robertsneyder.co
DB_DATABASE=u123456789_censos
DB_USERNAME=u123456789_censos
DB_PASSWORD=tu_password_real
```

### 3.3 Instalar dependencias y configurar
```bash
composer install --no-dev --optimize-autoloader
chmod +x deploy/first-setup.sh
./deploy/first-setup.sh
```

### 3.4 Permisos de storage
```bash
chmod -R 775 storage bootstrap/cache
```

### 3.5 Verificar
Abre en el navegador:
- https://censosviviendas.robertsneyder.co
- https://censosviviendas.robertsneyder.co/admin

**Login inicial:**
- Email: `admin@censosviviendas.co`
- Contraseña: `Admin2026!` (cámbiala después del primer acceso)

---

## Paso 4 — Deploy automático con GitHub Actions

El workflow ya está en `.github/workflows/hostinger-deploy.yml`. Configura estos **Secrets** en GitHub:

Repositorio → **Settings → Secrets and variables → Actions → New repository secret**

| Secret | Valor |
|--------|-------|
| `SSH_HOST` | IP del servidor Hostinger |
| `SSH_PORT` | `65002` (o el puerto que indique hPanel) |
| `SSH_USERNAME` | Usuario SSH de Hostinger |
| `SSH_KEY` | Contenido completo de la clave privada (`hostinger_censos`) |
| `WEBSITE_FOLDER` | `censosviviendas.robertsneyder.co` |

Cada `git push` a `main` desplegará automáticamente.

### Alternativa: deploy manual desde tu PC
Agrega al `.env` local (no commitear):
```env
HOSTINGER_SSH_HOST=TU_IP
HOSTINGER_SSH_USERNAME=u123456789
HOSTINGER_SSH_PORT=65002
HOSTINGER_SITE_DIR=censosviviendas.robertsneyder.co
```

Luego ejecuta:
```bash
D:\php82\php.exe artisan hostinger:deploy
```

---

## Paso 5 — SSL (HTTPS)

Hostinger activa SSL gratuito (Let's Encrypt) automáticamente. Si no está activo:
1. hPanel → **SSL** → activar para `censosviviendas.robertsneyder.co`
2. Espera unos minutos y verifica `https://`

---

## Solución de problemas

| Error | Solución |
|-------|----------|
| 500 Internal Server Error | Revisa `storage/logs/laravel.log` vía SSH |
| Permiso denegado en storage | `chmod -R 775 storage bootstrap/cache` |
| APP_KEY no definida | `php artisan key:generate --force` |
| CSS/JS no cargan | Verifica que `public/build/` existe; en local: `npm run build` y commit |
| Git de Hostinger expone archivos | No uses Git de hPanel; usa SSH + carpeta `domains/` con root en `public/` |

### Alternativa si no puedes cambiar document root
```bash
# Dentro de public_html, crear symlink al public de Laravel
cd ~/domains/censosviviendas.robertsneyder.co
ln -sfn "$(pwd)/public" public_html
```

---

## Checklist post-despliegue

- [ ] Cambiar contraseña del admin
- [ ] Crear usuarios censistas con su sector asignado
- [ ] Probar formulario `/censo/nuevo` desde celular
- [ ] Verificar que `APP_DEBUG=false` en producción
