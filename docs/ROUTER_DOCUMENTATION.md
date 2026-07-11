# 🛣️ Router y Middleware — Documentación v9.0.0

El sistema de enrutamiento de MVC-WEB v9.0.0 soporta rutas simples, rutas agrupadas con prefijos de versión, pipelines de middleware y protección granular por permisos.

---

## Tabla de Contenidos

1. [Registro de Rutas Básicas](#1-registro-de-rutas-básicas)
2. [Grupos de Rutas y Prefijos](#2-grupos-de-rutas-y-prefijos)
3. [Prefijos de Versión en API](#3-prefijos-de-versión-en-api)
4. [Middleware](#4-middleware)
5. [La Clase Request](#5-la-clase-request)
6. [Uso en Controladores](#6-uso-en-controladores)
7. [Method Spoofing (Formularios HTML)](#7-method-spoofing-formularios-html)
8. [Peticiones desde JavaScript (REST)](#8-peticiones-desde-javascript-rest)
9. [Archivos de Rutas](#9-archivos-de-rutas)

---

## 1. Registro de Rutas Básicas

Registra rutas para los métodos HTTP `GET`, `POST`, `PUT` y `DELETE`.

```php
use app\Core\Router;
use controllers\PagesController;

$router = new Router();

$router->get('/usuarios',           [UserController::class, 'index']);
$router->post('/usuarios',          [UserController::class, 'store']);
$router->put('/usuarios/{id}',      [UserController::class, 'update']);
$router->delete('/usuarios/{id}',   [UserController::class, 'destroy']);

$router->dispatch();
```

---

## 2. Grupos de Rutas y Prefijos

Agrupa rutas bajo un prefijo común para mantener el código organizado:

```php
// Todas estas rutas tendrán el prefijo /blog
$router->group(['prefix' => '/blog'], function($router) {
    $router->get('/',       [BlogController::class, 'index']);
    $router->get('/{slug}', [BlogController::class, 'show']);
    $router->post('/new',   [BlogController::class, 'store']);
});
```

Los grupos pueden anidarse:

```php
$router->group(['prefix' => '/admin', 'middleware' => ['auth']], function($router) {

    $router->group(['prefix' => '/users'], function($router) {
        $router->get('/',    [AdminUserController::class, 'index']);
        $router->delete('/{id}', [AdminUserController::class, 'destroy']);
    });

    $router->group(['prefix' => '/posts'], function($router) {
        $router->get('/', [AdminPostController::class, 'index']);
    });
});
```

---

## 3. Prefijos de Versión en API

Las versiones de API se manejan como prefijos de grupo. Esto permite mantener múltiples versiones en paralelo sin conflictos:

```php
// ─── API v1 ───────────────────────────────────────────────────
$router->group(['prefix' => '/api/v1', 'middleware' => ['auth']], function($router) {

    $router->get('/users',        [UserController::class, 'index']);
    $router->get('/users/{id}',   [UserController::class, 'show']);
    $router->post('/users',       [UserController::class, 'store']);
    $router->put('/users/{id}',   [UserController::class, 'update']);
    $router->delete('/users/{id}',[UserController::class, 'destroy']);

    $router->get('/posts',        [PostController::class, 'index']);
});

// ─── API v2 (nuevas funcionalidades, v1 intacta) ──────────────
$router->group(['prefix' => '/api/v2', 'middleware' => ['auth']], function($router) {

    // Devuelve respuesta paginada (nueva en v2)
    $router->get('/users', [UserV2Controller::class, 'index']);
});

// ─── API pública (sin auth) ───────────────────────────────────
$router->group(['prefix' => '/api/v1/public'], function($router) {
    $router->get('/status', [StatusController::class, 'index']);
});
```

> **Convención de URLs generadas:**
> - `GET /api/v1/users` → Lista de usuarios (v1)
> - `GET /api/v2/users` → Lista paginada (v2)
> - `POST /api/v1/users` → Crear usuario

---

## 4. Middleware

### Middleware en rutas individuales

```php
// Middleware único
$router->get('/perfil', [UserController::class, 'profile'])
       ->middleware('auth');

// Múltiples middlewares (se ejecutan en orden)
$router->post('/posts', [PostController::class, 'store'])
       ->middleware(['auth', 'permission:create_posts']);
```

### Middleware en grupos

```php
$router->group(['middleware' => ['auth', 'permission:admin']], function($router) {
    $router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
    $router->get('/admin/users',     [AdminController::class, 'users']);
});
```

### Middlewares disponibles

| Middleware           | Descripción                                      |
|----------------------|--------------------------------------------------|
| `auth`               | Verifica que el usuario esté autenticado         |
| `permission:NOMBRE`  | Verifica que el usuario tenga el permiso dado    |

### Crear un Middleware personalizado

```php
// app/Core/Middleware/MiMiddleware.php
namespace app\Core\Middleware;

class MiMiddleware implements Middleware
{
    public function handle(array $params, callable $next): mixed
    {
        // Lógica antes de la ruta
        if (!condicion()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }

        // Continuar al siguiente middleware / controlador
        return $next($params);
    }
}
```

Registrarlo en el Router:

```php
$router->registerMiddleware('mi_guard', MiMiddleware::class);

// Uso
$router->get('/ruta', [Controller::class, 'method'])
       ->middleware('mi_guard');
```

---

## 5. La Clase Request

`app\Core\Request` unifica el acceso a todos los datos de una petición HTTP.

### Métodos disponibles

| Método           | Descripción                                                |
|------------------|------------------------------------------------------------|
| `getMethod()`    | Método HTTP real (soporta method spoofing)                 |
| `getPath()`      | URL limpia sin query string                                |
| `getBody()`      | Array con todos los datos (JSON, POST, PUT, DELETE)        |
| `get($key)`      | Obtener un campo específico del body                       |
| `isJson()`       | `true` si el Content-Type es `application/json`            |
| `bearerToken()`  | Extrae el token del header `Authorization: Bearer <token>` |

```php
public function show(Router $router, Request $request): void
{
    $id     = $request->get('id');
    $token  = $request->bearerToken();
    $body   = $request->getBody();
}
```

---

## 6. Uso en Controladores

El Router inyecta automáticamente `$router` y `$request` en todos los métodos de controladores:

```php
namespace controllers;

use app\Core\Router;
use app\Core\Request;

class UserController
{
    public static function store(Router $router, Request $request): void
    {
        $data = $request->getBody();

        // Validar
        if (empty($data['email'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Email requerido']);
            return;
        }

        // Guardar...
        echo json_encode(['status' => 'created', 'email' => $data['email']]);
    }
}
```

---

## 7. Method Spoofing (Formularios HTML)

Los formularios HTML solo soportan `GET` y `POST`. Para usar `PUT` o `DELETE` desde un formulario, añade el campo oculto `_method`:

```html
<form action="/usuarios/5" method="POST">
    <input type="hidden" name="_method" value="PUT">
    <input type="text" name="nombre" value="Carlos">
    <button type="submit">Actualizar</button>
</form>
```

El Router detecta `_method` y trata la petición como `PUT`.

---

## 8. Peticiones desde JavaScript (REST)

Cuando usas `fetch` o `axios`, el Router procesa el cuerpo JSON automáticamente:

```javascript
// Crear recurso
const res = await fetch('/api/v1/posts', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ title: 'Hola', body: 'Contenido...' })
});

// Actualizar recurso
const res = await fetch('/api/v1/posts/1', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    },
    body: JSON.stringify({ title: 'Nuevo título' })
});
```

En el controlador, `$request->getBody()` devuelve los datos como array PHP.

---

## 9. Archivos de Rutas

### Estructura de archivos

```
routes/
├── web.php     # Rutas HTML / páginas (cargado siempre)
├── api.php     # Rutas REST (prefijo /api/vX)
└── admin.php   # Rutas del panel (prefijo /admin)

modules/
└── Blog/
    └── routes.php  # Rutas propias del módulo Blog
```

### Orden de carga (en `Application.php`)

1. `routes/web.php`
2. `routes/api.php`
3. `routes/admin.php`
4. `modules/*/routes.php` (auto-descubiertos)

### Ejemplo: `routes/api.php`

```php
<?php
use app\Core\Router;

return function (Router $router): void {

    $router->group(['prefix' => '/api/v1', 'middleware' => ['auth']], function($router) {
        $router->get('/me', [UserController::class, 'me']);
    });

};
```

---

## Compatibilidad con v8.x

Para proyectos que usaban `MVC\Router` y `MVC\Request` (v8.x), los alias siguen funcionando:

```php
use MVC\Router;   // → app\Core\Router
use MVC\Request;  // → app\Core\Request
```

Definidos en `app/Core/Application.php` mediante `class_alias`.
