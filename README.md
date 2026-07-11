# 🚀 MVC-WEB Framework PHP v9.0.0

> [!IMPORTANT]
> **¡Nuevo en v9.0.0!** Arquitectura completamente modular con sistema de **Módulos**, **Plugins**, **Routes separadas** (web/api/admin), **Auth & Middleware** en capas, y **Vite** como sistema de build frontend.

> [!NOTE]
> **v8.1.0**: Sistema de Manejo de Estado Global (Store Pattern) para Vanilla JS. Aún disponible y compatible.

---

## 📋 Descripción del Proyecto

**MVC-WEB** es un framework PHP de desarrollo web basado en el patrón **Modelo-Vista-Controlador**. Diseñado para ser extensible, seguro y moderno, incluye un sistema completo de enrutamiento con middleware, autenticación JWT, módulos independientes, sistema de plugins y compilación de assets con Vite.

---

## 🏗️ Arquitectura v9.0.0

```
MVC-WEB/
│
├── app/
│   ├── Core/                       # Núcleo del framework
│   │   ├── Application.php         # Bootstrap principal
│   │   ├── Router.php              # Enrutador con middleware & grupos
│   │   ├── Request.php             # Objeto de petición HTTP
│   │   ├── Event.php               # Sistema de eventos
│   │   ├── Plugin.php              # Gestor de plugins
│   │   ├── Auth/
│   │   │   ├── Authentication.php  # ¿Quién eres?
│   │   │   ├── Authorization.php   # ¿Qué puedes hacer?
│   │   │   └── Permission.php      # Sistema de permisos granular
│   │   └── Middleware/
│   │       ├── Middleware.php      # Interfaz base
│   │       ├── AuthMiddleware.php  # Protección por autenticación
│   │       └── PermissionMiddleware.php  # Protección por permisos
│   │
│   ├── controllers/                # Controladores principales
│   │   ├── API/                    # Controladores de API
│   │   ├── LoginController.php
│   │   └── PagesController.php
│   │
│   ├── models/                     # Modelos de datos
│   │   ├── Main.php                # Modelo base con caché y CRUD
│   │   └── User.php
│   │
│   ├── services/                   # Servicios de la aplicación
│   │   ├── auth/
│   │   │   ├── JWTAuth.php
│   │   │   └── PHPAuth.php
│   │   ├── EmailModel.php
│   │   └── FileManagerModel.php
│   │
│   └── views/                      # Vistas de la aplicación
│       ├── layouts/
│       │   └── layout.php          # Layout principal (Vite assets)
│       ├── home/
│       └── includes/
│
├── routes/                         # Definición de rutas globales
│   ├── web.php                     # Rutas web (HTML, páginas)
│   ├── api.php                     # Rutas API REST (v1, v2, etc.)
│   └── admin.php                   # Rutas del panel de administración
│
├── modules/                        # Módulos independientes
│   └── Blog/
│       ├── routes.php              # Rutas propias del módulo
│       ├── controllers/
│       │   └── BlogController.php
│       └── views/
│           └── index.php
│
├── plugins/                        # Plugins del framework
│   └── AnalyticsPlugin/
│       └── AnalyticsPlugin.php
│
├── config/                         # Configuración global
│   ├── config.php
│   └── utilis.php                  # Helpers (incluye asset_vite())
│
├── public/                         # Document root del servidor web
│   ├── index.php                   # Punto de entrada único
│   └── build/                      # Assets compilados por Vite
│
├── src/                            # Código fuente frontend
│   ├── main.js                     # Entrada principal de Vite
│   └── Ui/                         # Componentes de UI por página
│
├── scripts/                        # Scripts de instalación
│   ├── install.sh                  # ★ Instalador maestro
│   ├── instalerComposer.sh         # Instalador PHP/Composer
│   ├── instalerNpm.sh              # Instalador Node.js/Vite
│   └── startEnv.sh                 # Configurador de .env
│
├── docs/                           # Documentación completa
├── vite.config.js                  # Configuración de Vite
├── composer.json                   # Dependencias PHP (PSR-4)
├── package.json                    # Dependencias Node.js
└── .env                            # Variables de entorno (no commitar)
```

---

## ⚡ Inicio Rápido

### Instalación en un solo comando

```bash
# Clonar el repositorio
git clone https://github.com/tu-usuario/MVC-WEB.git
cd MVC-WEB

# Instalar todo con el script maestro
bash scripts/install.sh
```

El instalador maestro ejecuta automáticamente:
1. **Configuración del `.env`** — DB, JWT, APP_URL, etc.
2. **Composer install** — Dependencias PHP + autoload PSR-4
3. **npm install** — Dependencias Node.js + Vite

---

## 🛣️ Sistema de Rutas

### Rutas Web (`routes/web.php`)

```php
use app\Core\Router;
use controllers\PagesController;

$router->get('/', [PagesController::class, 'index']);
$router->get('/about', [PagesController::class, 'about']);
```

### Rutas API con prefijo de versión (`routes/api.php`)

```php
// Grupo API v1
$router->group(['prefix' => '/api/v1', 'middleware' => ['auth']], function($router) {
    $router->get('/users',    [UserController::class, 'index']);
    $router->post('/users',   [UserController::class, 'store']);
    $router->get('/users/{id}', [UserController::class, 'show']);
});

// Grupo API v2 (en paralelo, sin romper v1)
$router->group(['prefix' => '/api/v2', 'middleware' => ['auth']], function($router) {
    $router->get('/users', [UserV2Controller::class, 'index']);
});
```

### Rutas de Módulos (`modules/Blog/routes.php`)

```php
// Cargado automáticamente desde Application.php
$router->group(['prefix' => '/blog'], function($router) {
    $router->get('/',         [BlogController::class, 'index']);
    $router->get('/{slug}',   [BlogController::class, 'show']);
    $router->post('/create',  [BlogController::class, 'store'])
           ->middleware('auth');
});
```

### Rutas Admin con protección (`routes/admin.php`)

```php
$router->group(['prefix' => '/admin', 'middleware' => ['auth', 'permission:admin']], function($router) {
    $router->get('/dashboard', [AdminController::class, 'dashboard']);
});
```

---

## 🔐 Sistema Auth & Middleware

### Autenticación

```php
use app\Core\Auth\Authentication;

// Login
Authentication::login($userId, $userData);

// Verificar sesión
if (Authentication::check()) {
    $user = Authentication::user();
}

// Logout
Authentication::logout();
```

### Autorización y Permisos

```php
use app\Core\Auth\Authorization;
use app\Core\Auth\Permission;

// Verificar rol
if (Authorization::hasRole('admin')) {
    // ...
}

// Verificar permiso específico
if (Permission::can('edit_posts')) {
    // ...
}
```

### Middleware en Rutas

```php
// Middleware individual
$router->get('/perfil', [UserController::class, 'profile'])
       ->middleware('auth');

// Múltiples middlewares
$router->post('/posts', [PostController::class, 'store'])
       ->middleware(['auth', 'permission:create_posts']);

// Middleware en grupo
$router->group(['middleware' => ['auth']], function($router) {
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->get('/settings',  [SettingsController::class, 'index']);
});
```

---

## 🧩 Módulos

Cada módulo es un directorio autocontenido bajo `modules/`. El framework carga automáticamente su archivo `routes.php`.

```bash
# Estructura de un módulo
modules/
└── MiModulo/
    ├── routes.php              # Rutas del módulo
    ├── controllers/
    │   └── MiModuloController.php
    ├── models/
    │   └── MiModuloModel.php
    └── views/
        └── index.php
```

Para registrar un módulo, agrégalo en `app/Core/Application.php`:

```php
protected array $modules = [
    'Blog',
    'Events',
    'Donations',
    'MiModulo',   // ← Agregar aquí
];
```

---

## 🔌 Plugins

Los plugins extienden el framework sin modificar el núcleo:

```php
// plugins/MiPlugin/MiPlugin.php
namespace plugins\MiPlugin;

use app\Core\Plugin;

class MiPlugin extends Plugin
{
    public function boot(): void
    {
        // Se ejecuta en cada petición
        $this->on('request.before', function($event) {
            // Lógica del plugin
        });
    }
}
```

Registrar en `app/Core/Application.php`:

```php
protected array $plugins = [
    \plugins\AnalyticsPlugin\AnalyticsPlugin::class,
    \plugins\MiPlugin\MiPlugin::class,
];
```

---

## 🎨 Frontend con Vite

### Servidor de Desarrollo

```bash
npm run dev       # Inicia Vite con HMR en http://localhost:5173
```

### Build de Producción

```bash
npm run build     # Genera assets en public/build/
```

### Helper en PHP

```php
// En tus vistas — carga el asset correcto según entorno
<link rel="stylesheet" href="<?= asset_vite('src/main.css') ?>">
<script type="module" src="<?= asset_vite('src/main.js') ?>"></script>
```

---

## 🔧 Requisitos

| Software      | Versión Mínima |
|---------------|----------------|
| PHP           | 8.0+           |
| Composer      | 2.0+           |
| Node.js       | 16.0+          |
| npm           | 8.0+           |
| MySQL/MariaDB | 5.7+           |

### Extensiones PHP requeridas

```bash
php -m | grep -E "(mysqli|pdo|mbstring|json|curl|gd|zip)"
```

---

## 🌱 Variables de Entorno

Copia `env.ejemplo` a `.env` y configura:

```env
# Base de datos
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=mvc_web_db

# Aplicación
APP_NAME="Mi Aplicación"
APP_URL=http://localhost

# Seguridad
JWT_KEY=clave_super_secreta_generada_automáticamente

# Entorno: development | production
APP_ENV=development
```

---

## 📚 Documentación

| Documento | Descripción |
|-----------|-------------|
| [Instalación](docs/INSTALLATION_DOCUMENTATION.md) | Guía completa de instalación y configuración |
| [Router & Middleware](docs/ROUTER_DOCUMENTATION.md) | Sistema de rutas, grupos, middleware y prefijos |
| [Modelo Principal](docs/MAIN_MODEL_DOCUMENTATION.md) | ORM base, consultas, caché, CRUD |
| [Autenticación JWT](docs/JWT_DOCUMENTATION.md) | Generación, verificación y renovación de tokens |
| [Usuarios](docs/USER_DOCUMENTATION.md) | Manejo de usuarios, roles y permisos |
| [Email](docs/EMAIL_DOCUMENTATION.md) | Envío de emails con plantillas |
| [Componentes](docs/COMPONENT_MANAGER_DOCUMENTATION.md) | Sistema de componentes reutilizables |
| [Paginación](docs/PAGINATION_DOCUMENTATION.md) | Paginación automática de resultados |
| [Archivos](docs/FILE_MANAGER_DOCUMENTATION.md) | Gestión y subida de archivos |
| [Logger](docs/LOGGER_DOCUMENTATION.md) | Sistema de logging |
| [SweetAlert2](docs/SWEETALERT2_DOCUMENTATION.md) | Alertas y notificaciones UI |

---

## 🗂️ Changelog

### v9.0.0 — Arquitectura Modular
- ✅ Sistema de módulos independientes (`modules/`)
- ✅ Sistema de plugins extensible (`plugins/`)
- ✅ Router con grupos, prefijos y middleware pipeline
- ✅ Routes separadas: `web.php`, `api.php`, `admin.php`
- ✅ Prefijos de versión en API (`/api/v1`, `/api/v2`)
- ✅ Sistema de Auth completo: Authentication, Authorization, Permission
- ✅ Middleware: AuthMiddleware, PermissionMiddleware
- ✅ Migración de Gulp → **Vite** (HMR, build optimizado)
- ✅ PSR-4 completo para Core, modules y plugins
- ✅ Scripts de instalación mejorados con instalador maestro

### v8.1.0 — Estado Global
- ✅ Store Pattern para Vanilla JS (ThemeStore, CartStore)
- ✅ Persistencia automática entre páginas

### v6.0.0 — Docker
- ✅ Soporte completo para Docker y MySQL

---

## 📄 Licencia

[Ver licencia](LICENSE) — Uso libre para proyectos personales y comerciales.
