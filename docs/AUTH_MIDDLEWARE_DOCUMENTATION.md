# 🔐 Auth & Middleware — Documentación v9.0.0

El sistema de autenticación y autorización de MVC-WEB v9.0.0 está compuesto por tres capas independientes que responden a preguntas concretas.

---

## Tabla de Contenidos

1. [Arquitectura del Sistema](#1-arquitectura-del-sistema)
2. [Authentication — ¿Quién eres?](#2-authentication--quién-eres)
3. [Authorization — ¿Qué puedes hacer?](#3-authorization--qué-puedes-hacer)
4. [Permission — ¿Tienes permiso específico?](#4-permission--tienes-permiso-específico)
5. [AuthMiddleware](#5-authmiddleware)
6. [PermissionMiddleware](#6-permissionmiddleware)
7. [Flujo completo de una petición protegida](#7-flujo-completo-de-una-petición-protegida)
8. [Integración con JWT](#8-integración-con-jwt)

---

## 1. Arquitectura del Sistema

```
app/Core/
├── Auth/
│   ├── Authentication.php   # Maneja identidad (sesión / JWT)
│   ├── Authorization.php    # Verifica roles del usuario
│   └── Permission.php       # Permisos granulares por acción
│
└── Middleware/
    ├── Middleware.php            # Interfaz base
    ├── AuthMiddleware.php        # Guard: requiere autenticación
    └── PermissionMiddleware.php  # Guard: requiere permiso específico
```

### Separación de responsabilidades

| Clase             | Pregunta que responde       | Ejemplo                            |
|-------------------|-----------------------------|------------------------------------|
| `Authentication`  | ¿Quién eres?                | ¿Hay un usuario logueado?          |
| `Authorization`   | ¿Qué puedes hacer?          | ¿Eres administrador o editor?      |
| `Permission`      | ¿Tienes permiso específico? | ¿Puedes `edit_posts`?              |

---

## 2. Authentication — ¿Quién eres?

### Clase: `app\Core\Auth\Authentication`

Gestiona el ciclo de vida de la sesión del usuario.

```php
use app\Core\Auth\Authentication;

// ─── Login ────────────────────────────────────────────────────
Authentication::login(
    userId: 42,
    userData: [
        'name'  => 'Carlos López',
        'email' => 'carlos@ejemplo.com',
        'role'  => 'editor',
        'permissions' => ['edit_posts', 'view_stats']
    ]
);

// ─── Verificar si hay sesión activa ───────────────────────────
if (Authentication::check()) {
    $user = Authentication::user();
    echo $user['name']; // Carlos López
}

// ─── Obtener ID del usuario ────────────────────────────────────
$userId = Authentication::id(); // 42

// ─── Logout ───────────────────────────────────────────────────
Authentication::logout();
```

### Métodos de `Authentication`

| Método               | Descripción                                              |
|----------------------|----------------------------------------------------------|
| `login(id, data)`    | Inicia sesión guardando id y datos del usuario           |
| `logout()`           | Destruye la sesión activa                                |
| `check()`            | `true` si hay un usuario autenticado                     |
| `user()`             | Devuelve el array con datos del usuario                  |
| `id()`               | Devuelve el ID del usuario autenticado                   |

---

## 3. Authorization — ¿Qué puedes hacer?

### Clase: `app\Core\Auth\Authorization`

Verifica roles del usuario autenticado.

```php
use app\Core\Auth\Authorization;

// ─── Verificar un rol ─────────────────────────────────────────
if (Authorization::hasRole('admin')) {
    // Solo admins
}

// ─── Verificar cualquiera de varios roles ─────────────────────
if (Authorization::hasAnyRole(['admin', 'editor'])) {
    // Admins o editores
}

// ─── Obtener el rol actual ────────────────────────────────────
$rol = Authorization::role(); // "editor"

// ─── Redirigir si no tiene rol ────────────────────────────────
Authorization::requireRole('admin', redirectTo: '/403');
```

### Métodos de `Authorization`

| Método                       | Descripción                                          |
|------------------------------|------------------------------------------------------|
| `hasRole(string $role)`      | `true` si el usuario tiene ese rol                   |
| `hasAnyRole(array $roles)`   | `true` si tiene al menos uno de los roles dados      |
| `role()`                     | Devuelve el rol actual como string                   |
| `requireRole($role, $url)`   | Redirige si no cumple el rol                         |

---

## 4. Permission — ¿Tienes permiso específico?

### Clase: `app\Core\Auth\Permission`

Maneja permisos granulares más allá del sistema de roles.

```php
use app\Core\Auth\Permission;

// ─── Verificar un permiso ─────────────────────────────────────
if (Permission::can('edit_posts')) {
    // Solo usuarios con permiso edit_posts
}

// ─── Verificar varios permisos (requiere TODOS) ───────────────
if (Permission::canAll(['edit_posts', 'publish_posts'])) {
    // Tiene ambos permisos
}

// ─── Verificar al menos uno ───────────────────────────────────
if (Permission::canAny(['edit_posts', 'view_stats'])) {
    // Tiene al menos uno
}

// ─── Abortar si no tiene permiso ─────────────────────────────
Permission::require('delete_users'); // HTTP 403 si no lo tiene
```

### Cómo se almacenan los permisos

Los permisos se guardan como un array en la sesión del usuario durante el login:

```php
Authentication::login(userId: $user->id, userData: [
    'name'        => $user->name,
    'role'        => $user->role,
    'permissions' => $user->getPermissions(), // ['edit_posts', 'view_stats']
]);
```

---

## 5. AuthMiddleware

### Clase: `app\Core\Middleware\AuthMiddleware`

Guard que verifica si el usuario está autenticado antes de dar acceso a la ruta. Si no lo está, redirige o devuelve `401 Unauthorized`.

### Comportamiento

- Para **rutas web**: redirige a `/login`
- Para **rutas API**: devuelve JSON `{"error": "Unauthorized"}` con status 401

### Cómo se aplica

```php
// En archivo de rutas
$router->get('/perfil', [UserController::class, 'profile'])
       ->middleware('auth');

// En grupo
$router->group(['prefix' => '/dashboard', 'middleware' => ['auth']], function($router) {
    $router->get('/', [DashboardController::class, 'index']);
});
```

### Implementación interna

```php
// app/Core/Middleware/AuthMiddleware.php
namespace app\Core\Middleware;

use app\Core\Auth\Authentication;

class AuthMiddleware implements Middleware
{
    public function handle(array $params, callable $next): mixed
    {
        if (!Authentication::check()) {
            // Detectar si es petición API
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            // Redirigir a login para rutas web
            header('Location: /login');
            exit;
        }

        return $next($params);
    }
}
```

---

## 6. PermissionMiddleware

### Clase: `app\Core\Middleware\PermissionMiddleware`

Guard que verifica que el usuario tenga un permiso o rol específico.

### Sintaxis de uso

```php
// Verificar permiso
->middleware('permission:edit_posts')

// Verificar rol
->middleware('permission:admin')

// En grupo
$router->group(['middleware' => ['auth', 'permission:admin']], function($router) {
    $router->get('/admin/users', [AdminController::class, 'users']);
});
```

### Implementación interna

```php
// app/Core/Middleware/PermissionMiddleware.php
namespace app\Core\Middleware;

use app\Core\Auth\Permission;
use app\Core\Auth\Authorization;

class PermissionMiddleware implements Middleware
{
    public function __construct(private string $permission) {}

    public function handle(array $params, callable $next): mixed
    {
        $hasAccess = Permission::can($this->permission)
                  || Authorization::hasRole($this->permission);

        if (!$hasAccess) {
            http_response_code(403);
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                echo json_encode(['error' => 'Forbidden']);
            } else {
                require_once __DIR__ . '/../../views/errors/403.php';
            }
            exit;
        }

        return $next($params);
    }
}
```

---

## 7. Flujo completo de una petición protegida

```
Petición HTTP
     │
     ▼
┌─────────────────┐
│  public/index.php│  ← Punto de entrada
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Application    │  ← Carga rutas, módulos y plugins
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│     Router      │  ← Encuentra la ruta que coincide
└────────┬────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│           Pipeline de Middleware         │
│                                         │
│  [AuthMiddleware]                        │
│    └─ ¿Authentication::check() = true?  │
│       ├─ No → 401 / redirect /login     │
│       └─ Sí ↓                           │
│                                         │
│  [PermissionMiddleware('admin')]         │
│    └─ ¿Permission::can('admin')?        │
│       ├─ No → 403 Forbidden             │
│       └─ Sí ↓                           │
│                                         │
│  [Controlador::metodo]                  │
│    └─ Lógica de negocio → Respuesta     │
└─────────────────────────────────────────┘
```

---

## 8. Integración con JWT

Para APIs sin estado (stateless), puedes combinar el sistema de Auth con JWT:

```php
// app/Core/Middleware/JwtMiddleware.php
namespace app\Core\Middleware;

use app\services\auth\JWTAuth;
use app\Core\Auth\Authentication;

class JwtMiddleware implements Middleware
{
    public function handle(array $params, callable $next): mixed
    {
        $token = $this->extractToken();

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Token requerido']);
            exit;
        }

        $payload = JWTAuth::verify($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Token inválido o expirado']);
            exit;
        }

        // Hidratar el sistema de Auth con los datos del token
        Authentication::login($payload['sub'], $payload['user']);

        return $next($params);
    }

    private function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
```

Registrar y usar:

```php
$router->registerMiddleware('jwt', JwtMiddleware::class);

$router->group(['prefix' => '/api/v1', 'middleware' => ['jwt']], function($router) {
    $router->get('/me', [UserController::class, 'me']);
});
```

---

## Resumen de guards disponibles

| Middleware Key         | Clase                   | Protege cuando...                        |
|------------------------|-------------------------|------------------------------------------|
| `auth`                 | `AuthMiddleware`        | No hay sesión activa                     |
| `permission:NOMBRE`    | `PermissionMiddleware`  | No tiene el permiso o rol especificado   |
| `jwt` *(custom)*       | `JwtMiddleware`         | Token JWT inválido, expirado o ausente   |
