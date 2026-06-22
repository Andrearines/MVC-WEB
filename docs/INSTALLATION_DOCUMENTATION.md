# 🚀 Guía de Instalación - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Requisitos Previos](#requisitos-previos)
3. [Scripts de Instalación](#scripts-de-instalación)
4. [Instalación Automática](#instalación-automática)
5. [Instalación Manual](#instalación-manual)
6. [Configuración del Entorno](#configuración-del-entorno)
7. [Verificación de la Instalación](#verificación-de-la-instalación)
8. [Troubleshooting](#troubleshooting)
9. [Configuración Avanzada](#configuración-avanzada)
10. [Mantenimiento](#mantenimiento)

---

## 🎯 Descripción General

MVC-WEB incluye un sistema completo de scripts de instalación automatizada que facilitan la configuración del proyecto desde cero. Los scripts manejan la instalación de dependencias, configuración del entorno y puesta en marcha del servidor de desarrollo.

### Características de los Scripts

- ✅ **Instalación automática** de dependencias PHP y Node.js
- ✅ **Configuración interactiva** de variables de entorno
- ✅ **Generación de claves** seguras automáticamente
- ✅ **Verificación de requisitos** del sistema
- ✅ **Backup automático** de configuración
- ✅ **Inicio del servidor** de desarrollo
- ✅ **Manejo de errores** con mensajes claros

---

## 🔧 Requisitos Previos

### Sistema Operativo

- **Linux** (Ubuntu 18.04+, Debian 10+, CentOS 8+)
- **macOS** (10.15+)
- **Windows** (10+ con WSL2 recomendado)

### Software Requerido

| Software      | Versión Mínima | Verificación         |
| ------------- | -------------- | -------------------- |
| PHP           | 8.0+           | `php --version`      |
| Composer      | 2.0+           | `composer --version` |
| Node.js       | 16.0+          | `node --version`     |
| npm           | 8.0+           | `npm --version`      |
| MySQL/MariaDB | 5.7+           | `mysql --version`    |
| Git           | 2.0+           | `git --version`      |

### Extensiones PHP Requeridas

```bash
# Verificar extensiones instaladas
php -m | grep -E "(mysqli|pdo|mbstring|json|curl|gd|zip)"
```

Extensiones necesarias:

- `mysqli` o `pdo_mysql`
- `mbstring`
- `json`
- `curl`
- `gd` (para procesamiento de imágenes)
- `zip`
- `openssl`
- `tokenizer`

---

## 📁 Scripts de Instalación

### Estructura de Scripts

```
MVC-WEB/
├── start.sh                    # Instalación completa automatizada
├── startServer.sh              # Inicio del servidor de desarrollo
└── scripts/
    ├── instalerComposer.sh     # Instalación de dependencias PHP
    ├── instalerNpm.sh          # Instalación de dependencias frontend
    └── startEnv.sh             # Configuración de variables de entorno
```

### Permisos de Ejecución

```bash
# Dar permisos a todos los scripts
chmod +x start.sh
chmod +x startServer.sh
chmod +x scripts/*.sh

# Verificar permisos
ls -la *.sh scripts/*.sh
```

---

## 🚀 Instalación Automática

### Script Principal: start.sh

El script `start.sh` realiza la instalación completa del proyecto en un solo paso.

#### Uso Básico

```bash
# Clonar el proyecto
git clone <repositorio-url>
cd MVC-WEB

# Dar permisos y ejecutar
chmod +x start.sh
./start.sh
```

#### Proceso de Instalación

El script realiza los siguientes pasos automáticamente:

1. **Verificación de requisitos**
2. **Instalación de dependencias Composer**
3. **Instalación de dependencias NPM**
4. **Configuración interactiva del entorno**
5. **Generación de autoloader**
6. **Compilación de assets**
7. **Inicio del servidor de desarrollo**

#### Opciones del Script

```bash
# Ejecución con opciones específicas
./start.sh --help                    # Muestra ayuda
./start.sh --skip-npm               # Omite instalación NPM
./start.sh --dev                    # Modo desarrollo
./start.sh --prod                   # Modo producción
./start.sh --force                  # Fuerza reinstalación
```

---

## 📦 Scripts Individuales

### 1. instalerComposer.sh

Instala las dependencias PHP necesarias para el proyecto.

#### Ejecución

```bash
chmod +x scripts/instalerComposer.sh
./scripts/instalerComposer.sh
```

#### Funcionalidades

- **Verificación de Composer**: Comprueba si Composer está instalado
- **Instalación/Actualización**: Instala o actualiza dependencias
- **Generación de autoloader**: Crea el PSR-4 autoloader
- **Verificación de paquetes**: Confirma que los paquetes estén instalados

#### Dependencias Instaladas

```json
{
  "require": {
    "phpmailer/phpmailer": "^6.8",
    "firebase/php-jwt": "^6.0",
    "intervention/image": "^3.11"
  }
}
```

#### Salida Esperada

```
=== Instalación de Dependencias PHP ===
✓ Composer detectado: v2.5.5
✓ Instalando paquetes...
✓ phpmailer/phpmailer instalado
✓ firebase/php-jwt instalado
✓ intervention/image instalado
✓ Generando autoloader...
✓ Autoloader generado exitosamente
=== Instalación PHP completada ===
```

### 2. instalerNpm.sh

Instala las dependencias de frontend y compila los assets.

#### Ejecución

```bash
chmod +x scripts/instalerNpm.sh
./scripts/instalerNpm.sh
```

#### Funcionalidades

- **Verificación de Node.js y npm**
- **Instalación de paquetes NPM**
- **Compilación de assets CSS y JS**
- **Optimización de imágenes**
- **Generación de source maps**

#### Dependencias Instaladas

```json
{
  "devDependencies": {
    "cssnano": "^6.0.5",
    "gulp": "^4.0.2",
    "gulp-autoprefixer": "^8.0.0",
    "gulp-cache": "^1.1.3",
    "gulp-clean": "^0.4.0",
    "gulp-concat": "^2.6.1",
    "gulp-imagemin": "^8.0.0",
    "gulp-notify": "^4.0.0",
    "gulp-postcss": "^9.0.1",
    "gulp-rename": "^2.0.0",
    "gulp-sass": "^5.1.0",
    "gulp-sourcemaps": "^3.0.0",
    "gulp-terser-js": "^5.2.2",
    "gulp-webp": "^4.0.1",
    "sass": "^1.71.1",
    "terser": "^5.28.1"
  }
}
```

#### Salida Esperada

```
=== Instalación de Dependencias Frontend ===
✓ Node.js detectado: v18.17.0
✓ npm detectado: v9.6.7
✓ Instalando paquetes NPM...
✓ Paquetes instalados exitosamente
✓ Compilando assets...
✓ CSS compilado
✓ JavaScript minificado
✓ Imágenes optimizadas
=== Instalación Frontend completada ===
```

### 3. startEnv.sh

Configura las variables de entorno de forma interactiva.

#### Ejecución

```bash
chmod +x scripts/startEnv.sh
./scripts/startEnv.sh
```

#### Funcionalidades

- **Configuración interactiva** de base de datos
- **Generación automática** de clave JWT
- **Configuración de la aplicación**
- **Creación de backup** automático
- **Validación** de datos ingresados

#### Flujo de Configuración

```
=== CONFIGURACIÓN DE BASE DE DATOS ===
HOST [localhost]:
USUARIO [root]:
CONTRASEÑA: ********
NOMBRE DE LA BASE DE DATOS [mvc_web]:

=== CONFIGURACIÓN DE APLICACIÓN ===
CLAVE JWT: [generada_automáticamente]
NOMBRE DE LA APLICACIÓN [Web MVC]:
URL DE LA APLICACIÓN [http://localhost:3000]:

=== CONFIGURACIÓN DE EMAIL (OPCIONAL) ===
MAIL HOST [smtp.gmail.com]:
MAIL PORT [587]:
MAIL USERNAME:
MAIL PASSWORD:
MAIL ENCRYPTION [tls]:

=== CONFIGURACIÓN COMPLETADA ===
✓ Archivo .env creado
✓ Backup .env.backup creado
✓ Variables validadas
```

---

## ⚙️ Instalación Manual

### Paso 1: Clonar el Proyecto

```bash
git clone <repositorio-url>
cd MVC-WEB
```

### Paso 2: Instalar Dependencias PHP

```bash
# Instalar dependencias de Composer
composer install

# O manualmente
composer init
composer require phpmailer/phpmailer:^6.8
composer require firebase/php-jwt:^6.0
composer require intervention/image:^3.11

# Configurar autoloader
composer dump-autoload
```

### Paso 3: Instalar Dependencias Frontend

```bash
# Instalar paquetes NPM
npm install

# Compilar assets
npm run dev
# O manualmente con gulp
gulp
```

### Paso 4: Configurar Variables de Entorno

```bash
# Copiar archivo de ejemplo
cp env.ejemplo .env

# Editar archivo .env
nano .env
```

#### Configuración Básica de .env

```env
# Base de datos
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=mvc_web

# Aplicación
APP_NAME="Web MVC"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:3000

# JWT
JWT_KEY=tu_clave_secreta_jwt_aqui

# Email (opcional)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_password_email
MAIL_ENCRYPTION=tls
```

### Paso 5: Configurar Base de Datos

```sql
-- Crear base de datos
CREATE DATABASE mvc_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crear tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirmado TINYINT(1) DEFAULT 0,
    token VARCHAR(255) NULL,
    admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Paso 6: Iniciar Servidor de Desarrollo

```bash
# Usar el script
chmod +x startServer.sh
./startServer.sh

# O manualmente
php -S localhost:3000 -t public
```

---

## 🔍 Verificación de la Instalación

### Checklist de Verificación

#### 1. Verificar Dependencias PHP

```bash
# Verificar paquetes instalados
composer show

# Verificar autoloader
composer dump-autoload --optimize

# Probar carga de clases
php -r "require 'vendor/autoload.php'; echo 'Autoload OK\n';"
```

#### 2. Verificar Dependencias Frontend

```bash
# Verificar paquetes NPM
npm list

# Verificar compilación
ls -la public/build/

# Verificar archivos generados
ls -la public/build/css/
ls -la public/build/js/
```

#### 3. Verificar Configuración

```bash
# Verificar variables de entorno
cat .env

# Verificar conexión a base de datos
php -r "
require 'vendor/autoload.php';
require 'config/Environment.php';
Environment::load();
echo 'DB_HOST: ' . Environment::get('DB_HOST') . PHP_EOL;
echo 'DB_NAME: ' . Environment::get('DB_NAME') . PHP_EOL;
"
```

#### 4. Verificar Servidor

```bash
# Verificar que el servidor esté corriendo
curl -I http://localhost:3000

# Verificar respuesta del servidor
curl http://localhost:3000
```

### Tests de Funcionalidad

#### Test 1: Conexión a Base de Datos

```php
<?php
// test_db.php
require 'vendor/autoload.php';
require 'config/Environment.php';

Environment::load();

try {
    $mysqli = new mysqli(
        Environment::get('DB_HOST'),
        Environment::get('DB_USER'),
        Environment::get('DB_PASSWORD'),
        Environment::get('DB_NAME')
    );

    if ($mysqli->connect_error) {
        throw new Exception($mysqli->connect_error);
    }

    echo "✓ Conexión a base de datos exitosa\n";

    // Probar consulta simple
    $result = $mysqli->query("SELECT 1");
    if ($result) {
        echo "✓ Consulta de prueba exitosa\n";
    }

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
```

#### Test 2: Carga de Clases

```php
<?php
// test_classes.php
require 'vendor/autoload.php';

try {
    // Probar carga de modelos
    $user = new \models\User();
    echo "✓ Clase User cargada\n";

    $email = new \models\EmailModel();
    echo "✓ Clase EmailModel cargada\n";

    $fileManager = new \models\FileManagerModel();
    echo "✓ Clase FileManagerModel cargada\n";

} catch (Exception $e) {
    echo "✗ Error cargando clases: " . $e->getMessage() . "\n";
}
?>
```

#### Test 3: Funcionalidad JWT

```php
<?php
// test_jwt.php
require 'vendor/autoload.php';
require 'config/Environment.php';

Environment::load();

try {
    $token = \models\JWTAuth::generateToken(1);
    echo "✓ Token generado: " . substr($token, 0, 20) . "...\n";

    $payload = \models\JWTAuth::validateToken($token);
    if ($payload) {
        echo "✓ Token validado exitosamente\n";
        echo "✓ User ID: " . $payload['user_id'] . "\n";
    }

} catch (Exception $e) {
    echo "✗ Error JWT: " . $e->getMessage() . "\n";
}
?>
```

---

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. Permisos Denegados

**Error:** `Permission denied: ./start.sh`

**Solución:**

```bash
# Dar permisos de ejecución
chmod +x start.sh
chmod +x startServer.sh
chmod +x scripts/*.sh

# Verificar permisos
ls -la *.sh scripts/*.sh
```

#### 2. Composer No Encontrado

**Error:** `composer: command not found`

**Solución:**

```bash
# Instalar Composer globalmente
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# O usar localmente
php composer.phar install
```

#### 3. Node.js No Encontrado

**Error:** `node: command not found`

**Solución:**

```bash
# Instalar Node.js con nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 18
nvm use 18

# O instalar desde repositorios
sudo apt update
sudo apt install nodejs npm
```

#### 4. Error de Conexión a Base de Datos

**Error:** `Connection refused` o `Access denied`

**Solución:**

```bash
# Verificar que MySQL esté corriendo
sudo systemctl status mysql

# Iniciar MySQL si no está corriendo
sudo systemctl start mysql

# Verificar credenciales
mysql -u root -p

# Crear usuario si es necesario
CREATE USER 'mvc_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON mvc_web.* TO 'mvc_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 5. Error de Extensiones PHP

**Error:** `Call to undefined function` o extensiones faltantes

**Solución:**

```bash
# Ubuntu/Debian
sudo apt install php8.0-mysql php8.0-mbstring php8.0-json php8.0-curl php8.0-gd php8.0-zip

# CentOS/RHEL
sudo yum install php80-mysqlnd php80-mbstring php80-json php80-curl php80-gd php80-zip

# Verificar extensiones
php -m | grep -E "(mysqli|mbstring|json|curl|gd|zip)"
```

#### 6. Error de Compilación de Assets

**Error:** `gulp command not found` o errores de compilación

**Solución:**

```bash
# Instalar Gulp globalmente
npm install -g gulp-cli

# O usar npx
npx gulp

# Limpiar caché de npm
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Logs y Debug

#### Verbose Mode

```bash
# Ejecutar scripts con verbose
bash -x start.sh
bash -x scripts/instalerComposer.sh
bash -x scripts/startEnv.sh
```

#### Logs de Error

```bash
# Ver logs de PHP
php -l index.php

# Ver logs de Composer
composer install --verbose

# Ver logs de npm
npm install --verbose
```

#### Debug Mode

```bash
# Habilitar debug en .env
APP_DEBUG=true

# Ver errores de PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## ⚙️ Configuración Avanzada

### 1. Configuración de Producción

```bash
# Variables de entorno de producción
APP_ENV=production
APP_DEBUG=false

# Optimizar autoloader
composer dump-autoload --optimize --no-dev

# Compilar assets para producción
npm run build
# O gulp --production

# Configurar cache de OPcache
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
```

### 2. Configuración de Servidor Web

#### Apache (.htaccess)

```apache
# public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Seguridad
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Headers de seguridad
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

#### Nginx

```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/MVC-WEB/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Seguridad
    location ~ /\.env {
        deny all;
    }
}
```

### 3. Configuración de Docker

#### Dockerfile

```dockerfile
FROM php:8.0-apache

# Instalar extensiones
RUN docker-php-ext-install mysqli mbstring curl gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . /var/www/html/

# Instalar dependencias
WORKDIR /var/www/html/
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Configurar Apache
RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80
```

#### docker-compose.yml

```yaml
version: "3.8"

services:
  app:
    build: .
    ports:
      - "80:80"
    environment:
      - DB_HOST=db
      - DB_USER=root
      - DB_PASSWORD=password
      - DB_NAME=mvc_web
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=password
      - MYSQL_DATABASE=mvc_web
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

---

## 🔧 Mantenimiento

### 1. Actualización de Dependencias

```bash
# Actualizar Composer
composer update

# Actualizar npm
npm update

# Verificar actualizaciones seguras
composer audit
npm audit
```

### 2. Limpieza

```bash
# Limpiar caché de Composer
composer clear-cache

# Limpiar caché de npm
npm cache clean --force

# Limpiar assets compilados
rm -rf public/build/*
npm run build
```

### 3. Backup

```bash
# Backup del proyecto
tar -czf mvc-web-backup-$(date +%Y%m%d).tar.gz \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=.git \
    .

# Backup de base de datos
mysqldump -u root -p mvc_web > backup-$(date +%Y%m%d).sql
```

### 4. Monitoreo

```bash
# Verificar espacio en disco
df -h

# Verificar uso de memoria
free -h

# Verificar procesos
ps aux | grep php

# Verificar logs
tail -f /var/log/apache2/error.log
```

---

## 📝 Notas Importantes

### Seguridad

- **Nunca expongas** el archivo `.env` públicamente
- **Usa HTTPS** en producción
- **Mantén actualizadas** las dependencias
- **Configura firewall** apropiadamente
- **Usa claves seguras** y únicas

### Rendimiento

- **Habilita OPcache** en producción
- **Usa CDN** para assets estáticos
- **Configura caché** de base de datos
- **Optimiza imágenes** y assets
- **Monitorea recursos** del servidor

### Mantenimiento

- **Actualiza regularmente** Composer y npm
- **Revisa logs** de errores periódicamente
- **Haz backups** regulares
- **Monitorea seguridad** y vulnerabilidades
- **Documenta cambios** personalizados

---

## 🆘 Soporte

### Recursos Útiles

- [Documentación Composer](https://getcomposer.org/doc/)
- [Documentación npm](https://docs.npmjs.com/)
- [Documentación PHP](https://www.php.net/docs.php)
- [Documentación MySQL](https://dev.mysql.com/doc/)

### Comandos Útiles

```bash
# Verificar versión de PHP
php --version

# Verificar extensiones
php -m

# Verificar configuración
php --ini

# Debug de Composer
composer --verbose install

# Debug de npm
npm install --verbose
```

---

**Versión:** 2.0.0
**Compatibilidad:** PHP 8.0+, Node.js 16+, MySQL 5.7+
**Última Actualización:** Enero 5, 2026

---

**Documentación mantenida con ❤️ por el equipo MVC-WEB**
