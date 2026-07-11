# 🚀 Guía de Instalación — MVC-WEB v9.0.0

## Tabla de Contenidos

1. [Descripción General](#1-descripción-general)
2. [Requisitos Previos](#2-requisitos-previos)
3. [Instalación Automática (Recomendada)](#3-instalación-automática-recomendada)
4. [Instalación Manual Paso a Paso](#4-instalación-manual-paso-a-paso)
5. [Configuración del Entorno (.env)](#5-configuración-del-entorno-env)
6. [Configuración del Servidor Web](#6-configuración-del-servidor-web)
7. [Frontend con Vite](#7-frontend-con-vite)
8. [Verificación de la Instalación](#8-verificación-de-la-instalación)
9. [Troubleshooting](#9-troubleshooting)
10. [Actualización desde v8.x](#10-actualización-desde-v8x)

---

## 1. Descripción General

MVC-WEB v9.0.0 incluye un sistema de scripts de instalación automatizada que cubre:

- ✅ Configuración interactiva de variables de entorno
- ✅ Instalación de dependencias PHP con Composer (PSR-4 autoloading)
- ✅ Instalación de dependencias Node.js y compilación con Vite
- ✅ Detección automática de Composer (global, local o phar)
- ✅ Generación automática de claves JWT seguras
- ✅ Backup del `.env` antes de modificaciones

### Scripts disponibles

| Script | Descripción |
|--------|-------------|
| `scripts/install.sh` | ★ **Instalador maestro** — ejecuta todo en orden |
| `scripts/startEnv.sh` | Configura interactivamente el archivo `.env` |
| `scripts/instalerComposer.sh` | Instala dependencias PHP y genera autoload |
| `scripts/instalerNpm.sh` | Instala dependencias Node.js y compila con Vite |

---

## 2. Requisitos Previos

### Sistema Operativo

- **Linux** (Ubuntu 20.04+, Debian 11+, CentOS 8+)
- **macOS** (12+)
- **Windows 11** con WSL2 (recomendado)

### Software requerido

| Software      | Versión Mínima | Verificar con          |
|---------------|----------------|------------------------|
| PHP           | 8.0+           | `php --version`        |
| Composer      | 2.0+           | `composer --version`   |
| Node.js       | 16.0+          | `node --version`       |
| npm           | 8.0+           | `npm --version`        |
| MySQL/MariaDB | 5.7+           | `mysql --version`      |
| Git           | 2.0+           | `git --version`        |

### Extensiones PHP requeridas

```bash
# Verificar todas a la vez
php -m | grep -E "(mysqli|pdo|pdo_mysql|mbstring|json|curl|gd|zip|openssl)"
```

Las extensiones necesarias son:
- `mysqli` / `pdo_mysql` — Conexión a base de datos
- `mbstring` — Manejo de cadenas multibyte
- `json` — Soporte JSON (JWT)
- `curl` — Peticiones HTTP externas
- `gd` / `imagick` — Procesamiento de imágenes
- `zip` — Compresión de archivos
- `openssl` — Generación de claves seguras

---

## 3. Instalación Automática (Recomendada)

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/MVC-WEB.git
cd MVC-WEB

# 2. Dar permisos de ejecución
chmod +x scripts/*.sh

# 3. Ejecutar el instalador maestro
bash scripts/install.sh
```

El instalador te guiará por **3 pasos**:

```
▶ PASO 1/3 — Configurando variables de entorno...
  → Configura DB, JWT, APP_NAME, APP_URL interactivamente

▶ PASO 2/3 — Instalando dependencias PHP con Composer...
  → Detecta Composer (global/local/phar)
  → Instala paquetes de composer.json
  → Genera autoload PSR-4 optimizado

▶ PASO 3/3 — Instalando dependencias Node.js / Vite...
  → npm install
  → Opción de compilar assets para producción
```

---

## 4. Instalación Manual Paso a Paso

Si prefieres control total sobre cada paso:

### 4.1 Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/MVC-WEB.git
cd MVC-WEB
```

### 4.2 Configurar variables de entorno

```bash
# Opción A: Script interactivo
bash scripts/startEnv.sh

# Opción B: Manual
cp env.ejemplo .env
nano .env   # o tu editor favorito
```

### 4.3 Instalar dependencias PHP

```bash
# Si tienes Composer instalado globalmente
composer install
composer dump-autoload --optimize

# Si no tienes Composer, el script lo descarga
bash scripts/instalerComposer.sh
```

### 4.4 Instalar dependencias Node.js

```bash
npm install
```

### 4.5 Compilar assets con Vite

```bash
# Desarrollo (con HMR en localhost:5173)
npm run dev

# Producción (genera public/build/)
npm run build
```

### 4.6 Importar base de datos

```bash
mysql -u root -p tu_base_de_datos < db/schema.sql
```

### 4.7 Configurar el servidor web

Apunta el `DocumentRoot` a la carpeta `public/`. Ver [sección 6](#6-configuración-del-servidor-web).

---

## 5. Configuración del Entorno (.env)

El archivo `.env` controla toda la configuración sensible. Nunca lo commitas al repositorio.

```env
# ────────────────────────────────────────
#  Base de Datos
# ────────────────────────────────────────
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_contraseña_segura
DB_NAME=mvc_web_db

# ────────────────────────────────────────
#  Aplicación
# ────────────────────────────────────────
APP_NAME="Mi Aplicación"
APP_URL=http://localhost
APP_ENV=development   # development | production

# ────────────────────────────────────────
#  Seguridad
# ────────────────────────────────────────
JWT_KEY=clave_aleatoria_segura_de_al_menos_32_chars

# ────────────────────────────────────────
#  Email (opcional)
# ────────────────────────────────────────
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=tu@email.com
MAIL_PASS=app_password
MAIL_FROM=noreply@tudominio.com
```

### Generar JWT_KEY manualmente

```bash
openssl rand -base64 32
```

---

## 6. Configuración del Servidor Web

> [!IMPORTANT]
> El `DocumentRoot` del servidor web **debe apuntar a la carpeta `public/`**, no a la raíz del proyecto. Esto protege todos los archivos del framework.

### Apache

Crear o editar el VirtualHost:

```apache
<VirtualHost *:80>
    ServerName mvc-web.local
    DocumentRoot /ruta/a/MVC-WEB/public

    <Directory /ruta/a/MVC-WEB/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/mvc-web-error.log
    CustomLog ${APACHE_LOG_DIR}/mvc-web-access.log combined
</VirtualHost>
```

Asegúrate de tener el módulo `mod_rewrite` habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

El archivo `.htaccess` en `public/` ya está configurado para redirigir todo al `index.php`.

### Nginx

```nginx
server {
    listen 80;
    server_name mvc-web.local;
    root /ruta/a/MVC-WEB/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

### PHP Built-in Server (solo desarrollo)

```bash
php -S localhost:8000 -t public/
```

---

## 7. Frontend con Vite

### Desarrollo con HMR (Hot Module Replacement)

```bash
npm run dev
# → Vite server en http://localhost:5173
# → PHP en http://localhost:8000 (o tu servidor web)
```

### Build para producción

```bash
npm run build
# → Genera public/build/manifest.json y assets con hash
```

### Usar assets en vistas PHP

El helper `asset_vite()` detecta automáticamente si está en modo dev o producción:

```php
<?php
// En tu layout principal (app/views/layouts/layout.php)
?>
<!DOCTYPE html>
<html>
<head>
    <!-- En dev: apunta a localhost:5173 -->
    <!-- En prod: usa el archivo con hash de public/build/ -->
    <link rel="stylesheet" href="<?= asset_vite('src/main.css') ?>">
</head>
<body>
    <?= $contenido ?>
    <script type="module" src="<?= asset_vite('src/main.js') ?>"></script>
</body>
</html>
```

La función `asset_vite()` está definida en `config/utilis.php`.

---

## 8. Verificación de la Instalación

### Verificar autoloading PHP

```bash
php -r "require 'vendor/autoload.php'; echo class_exists('app\\Core\\Router') ? 'OK' : 'FAIL';"
# → OK
```

### Verificar estructura de archivos clave

```bash
# Deben existir estos archivos
ls public/index.php           # Punto de entrada
ls routes/web.php             # Rutas web
ls routes/api.php             # Rutas API
ls app/Core/Application.php   # Bootstrap
ls app/Core/Router.php        # Enrutador
ls vite.config.js             # Config Vite
```

### Verificar variables de entorno

```bash
php -r "
require 'vendor/autoload.php';
\$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
\$dotenv->load();
echo 'DB_HOST: ' . \$_ENV['DB_HOST'] . PHP_EOL;
echo 'APP_URL: ' . \$_ENV['APP_URL'] . PHP_EOL;
"
```

### Test rápido de conexión a BD

```bash
php -r "
require 'vendor/autoload.php';
\$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
\$dotenv->load();
\$pdo = new PDO('mysql:host='.\$_ENV['DB_HOST'].';dbname='.\$_ENV['DB_NAME'], \$_ENV['DB_USER'], \$_ENV['DB_PASSWORD']);
echo \$pdo ? 'Conexión BD: OK' : 'ERROR';
"
```

---

## 9. Troubleshooting

### ❌ Error: `Class not found` / `namespace not found`

```bash
# Regenerar el autoload
php composer dump-autoload --optimize
# o
composer dump-autoload --optimize
```

Verifica que el namespace coincida con la estructura de carpetas (PSR-4):
- `app\Core\Router` → `app/Core/Router.php`
- `modules\Blog\BlogController` → `modules/Blog/BlogController.php`

---

### ❌ Error: Rutas no funcionan (404 en todo)

1. Verifica que `DocumentRoot` apunte a `public/`
2. Verifica que `mod_rewrite` esté habilitado (Apache)
3. Verifica el `.htaccess` en `public/`:

```apache
Options -Indexes
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

---

### ❌ Error: Assets de Vite no cargan

```bash
# En desarrollo: asegúrate de que Vite esté corriendo
npm run dev

# En producción: asegúrate de haber hecho el build
npm run build
ls public/build/   # debe contener manifest.json y los assets
```

---

### ❌ Error: `composer: command not found`

El script `instalerComposer.sh` descarga Composer automáticamente. Pero si quieres hacerlo manualmente:

```bash
# Descargar y usar localmente
curl -sS https://getcomposer.org/installer | php -- --filename=composer
php ./composer install
```

---

### ❌ Permisos denegados en Linux

```bash
# Dar permisos a carpetas de escritura
chmod -R 775 logs/
chmod -R 775 public/build/
chown -R www-data:www-data .  # si usas Apache
```

---

## 10. Actualización desde v8.x

> [!WARNING]
> La v9.0.0 introduce cambios en los namespaces. Los alias de compatibilidad están activos pero se recomienda migrar.

### Cambios de namespace

| v8.x               | v9.0.0                  |
|--------------------|-------------------------|
| `MVC\Router`       | `app\Core\Router`       |
| `MVC\Request`      | `app\Core\Request`      |
| `router/Router.php`| `app/Core/Router.php`   |

### Alias de compatibilidad

Los siguientes alias están activos en `app/Core/Application.php`:

```php
class_alias(\app\Core\Router::class,  'MVC\Router');
class_alias(\app\Core\Request::class, 'MVC\Request');
```

Esto permite que el código existente siga funcionando sin cambios. Migra gradualmente usando los nuevos namespaces.

### Migrar rutas del `router/` antiguo

```php
// Antes (v8.x) — en public/index.php
require_once '../router/Router.php';
$router = new MVC\Router();
$router->get('/', ...);
$router->Rutas();

// Ahora (v9.0.0) — en routes/web.php
use app\Core\Router;
return function(Router $router): void {
    $router->get('/', ...);
};
// Application.php carga y despacha todo automáticamente
```
