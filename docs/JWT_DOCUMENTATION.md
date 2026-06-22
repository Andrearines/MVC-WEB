# 🔐 Sistema de Autenticación JWT - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Instalación y Configuración](#instalación-y-configuración)
4. [JWTAuth - Clase Principal](#usertokenmodel---clase-principal)
5. [Métodos JWT](#métodos-jwt)
6. [Configuración de Entorno](#configuración-de-entorno)
7. [Flujo de Autenticación](#flujo-de-autenticación)
8. [Ejemplos Prácticos](#ejemplos-prácticos)
9. [Middleware de Autenticación](#middleware-de-autenticación)
10. [Seguridad y Buenas Prácticas](#seguridad-y-buenas-prácticas)

---

## 🎯 Descripción General

El sistema de autenticación JWT (JSON Web Tokens) de MVC-WEB proporciona un mecanismo seguro y eficiente para manejar la autenticación de usuarios en aplicaciones web. Utiliza tokens firmados digitalmente para verificar la identidad de los usuarios sin necesidad de mantener estado en el servidor.

### Características Principales

- ✅ **Sin Estado (Stateless)**: No requiere sesiones del servidor
- ✅ **Seguro**: Tokens firmados con clave secreta HMAC-SHA256
- ✅ **Escalable**: Ideal para aplicaciones distribuidas
- ✅ **Flexible**: Soporte para claims personalizados
- ✅ **Expiración**: Control automático de tiempo de vida
- ✅ **Refresco**: Sistema de renovación de tokens
- ✅ **Cross-Origin**: Funciona con APIs y SPAs

---

## ⚙️ Instalación y Configuración

### Dependencias Requeridas

```json
{
  "require": {
    "firebase/php-jwt": "^6.0"
  }
}
```

### Configuración en composer.json

```json
{
  "autoload": {
    "psr-4": {
      "models\\": "./app/models"
    }
  }
}
```

### Variables de Entorno

```env
# Configuración JWT
JWT_KEY=tu_clave_secreta_muy_larga_y_segura_aqui
JWT_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800
```

---

## 🏗️ JWTAuth - Clase Principal

### Ubicación

```
app/services/auth/JWTAuth.php
```

### Estructura de la Clase

```php
<?php
namespace models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth extends Main
{
    public static $table = "users";
    static $columnDB = ["id", "nombre", "email", "password", "confirmado", "token", "admin"];

    // Propiedades del usuario
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $confirmado;
    public $token;
    public $admin;

    // Clave JWT privada
    static private $key = null;

    // Métodos JWT principales
    public static function generateToken($userId, $expiresIn = null)
    public static function validateToken($token)
    public static function refreshToken($token)
    public static function decodeToken($token)
}
```

### Propiedades

| Propiedad    | Tipo   | Descripción            |
| ------------ | ------ | ---------------------- |
| `id`         | int    | ID único del usuario   |
| `nombre`     | string | Nombre del usuario     |
| `email`      | string | Email del usuario      |
| `password`   | string | Contraseña hasheada    |
| `confirmado` | bool   | Estado de confirmación |
| `token`      | string | Token de recuperación  |
| `admin`      | bool   | Nivel de administrador |

---

## 🔑 Métodos JWT

### 1. generateToken()

Genera un nuevo token JWT para un usuario.

```php
public static function generateToken($userId, $expiresIn = null): string
```

**Parámetros:**

- `$userId`: ID del usuario
- `$expiresIn`: Tiempo de expiración en segundos (opcional)

**Retorna:** String con el token JWT

**Ejemplo:**

```php
$token = JWTAuth::generateToken(123);
// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

**Payload del Token:**

```json
{
  "iss": "http://localhost",
  "aud": "http://localhost",
  "iat": 1640995200,
  "exp": 1640998800,
  "user_id": 123,
  "email": "user@example.com",
  "role": "user"
}
```

### 2. validateToken()

Valida un token JWT y retorna el payload.

```php
public static function validateToken($token): array|false
```

**Parámetros:**

- `$token`: Token JWT a validar

**Retorna:** Array con el payload o `false` si es inválido

**Ejemplo:**

```php
$payload = JWTAuth::validateToken($token);
if ($payload) {
    $userId = $payload['user_id'];
    $email = $payload['email'];
} else {
    echo "Token inválido o expirado";
}
```

### 3. refreshToken()

Refresca un token existente generando uno nuevo.

```php
public static function refreshToken($token): string|false
```

**Parámetros:**

- `$token`: Token JWT a refrescar

**Retorna:** Nuevo token JWT o `false` si el original es inválido

**Ejemplo:**

```php
$newToken = JWTAuth::refreshToken($oldToken);
if ($newToken) {
    $_SESSION['token'] = $newToken;
}
```

### 4. decodeToken()

Decodifica un token sin validar la expiración.

```php
public static function decodeToken($token): array|false
```

**Parámetros:**

- `$token`: Token JWT a decodificar

**Retorna:** Array con el payload o `false` si es inválido

---

## 🔧 Configuración de Entorno

### Clave JWT

La clave JWT se configura mediante variables de entorno:

```php
private static function getKey()
{
    if (self::$key === null) {
        self::$key = \Environment::get('JWT_KEY', '');
    }
    return self::$key;
}
```

### Generación de Clave Segura

```bash
# Generar clave segura de 256 bits
openssl rand -base64 32

# O usar comando PHP
php -r "echo bin2hex(random_bytes(32));"
```

### Configuración Completa

```env
# .env
JWT_KEY=mi_clave_secreta_muy_larga_y_segura_aqui_32_bytes_minimo
JWT_ALGORITHM=HS256
JWT_EXPIRE=3600
JWT_REFRESH_EXPIRE=604800
JWT_ISSUER=http://localhost
JWT_AUDIENCE=http://localhost
```

---

## 🔄 Flujo de Autenticación

### 1. Login del Usuario

```php
<?php
// 1. Verificar credenciales
$user = User::findBy('email', $email);
if ($user && $user->Verify_password($user->password, $password)) {

    // 2. Generar token
    $token = JWTAuth::generateToken($user->id);

    // 3. Almacenar token
    $_SESSION['token'] = $token;
    setcookie('jwt_token', $token, time() + 3600, '/', '', true, true);

    // 4. Redirigir
    header('Location: /dashboard');
}
?>
```

### 2. Verificación en Peticiones

```php
<?php
// Middleware de autenticación
function authenticate()
{
    $token = $_SESSION['token'] ?? $_COOKIE['jwt_token'] ??
             $_SERVER['HTTP_AUTHORIZATION'] ??
             $_SERVER['HTTP_X_AUTH_TOKEN'] ?? '';

    if (empty($token)) {
        header('HTTP/1.0 401 Unauthorized');
        exit('Token requerido');
    }

    // Quitar "Bearer " si existe
    $token = str_replace('Bearer ', '', $token);

    $payload = JWTAuth::validateToken($token);
    if (!$payload) {
        header('HTTP/1.0 401 Unauthorized');
        exit('Token inválido o expirado');
    }

    return $payload;
}
?>
```

### 3. Refresco Automático

```php
<?php
function refreshTokenIfNeeded($token)
{
    $payload = JWTAuth::decodeToken($token);

    if ($payload && $payload['exp'] - time() < 300) { // 5 minutos antes
        $newToken = JWTAuth::refreshToken($token);
        if ($newToken) {
            $_SESSION['token'] = $newToken;
            setcookie('jwt_token', $newToken, time() + 3600, '/', '', true, true);
            return $newToken;
        }
    }

    return $token;
}
?>
```

---

## 💡 Ejemplos Prácticos

### 1. Sistema de Login Completo

```php
<?php
class AuthController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validar entrada
            if (empty($email) || empty($password)) {
                return view('auth/login', [
                    'error' => 'Email y contraseña son requeridos'
                ]);
            }

            // Buscar usuario
            $user = User::findBy('email', $email);

            if ($user && $user->Verify_password($user->password, $password)) {
                // Generar token con claims personalizados
                $token = JWTAuth::generateToken($user->id);

                // Guardar en sesión y cookie
                $_SESSION['token'] = $token;
                $_SESSION['user'] = $user;
                setcookie('jwt_token', $token, time() + 3600, '/', '', true, true);

                // Log de acceso
                error_log("Login successful: $email");

                header('Location: /dashboard');
                exit;
            } else {
                // Log de intento fallido
                error_log("Login failed: $email");

                return view('auth/login', [
                    'error' => 'Credenciales incorrectas'
                ]);
            }
        }

        return view('auth/login');
    }

    public function logout()
    {
        // Limpiar sesión y cookies
        session_destroy();
        setcookie('jwt_token', '', time() - 3600, '/', '', true, true);

        header('Location: /login');
        exit;
    }
}
?>
```

### 2. Middleware de API

```php
<?php
class ApiMiddleware
{
    public static function authenticate()
    {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($token)) {
            self::jsonResponse(['error' => 'Token requerido'], 401);
            exit;
        }

        // Quitar "Bearer "
        $token = str_replace('Bearer ', '', $token);

        $payload = JWTAuth::validateToken($token);
        if (!$payload) {
            self::jsonResponse(['error' => 'Token inválido o expirado'], 401);
            exit;
        }

        // Agregar información del usuario a la request
        $_REQUEST['user_id'] = $payload['user_id'];
        $_REQUEST['user_email'] = $payload['email'];
        $_REQUEST['user_role'] = $payload['role'] ?? 'user';

        return $payload;
    }

    public static function requireAdmin()
    {
        $payload = self::authenticate();

        if (($payload['role'] ?? 'user') !== 'admin') {
            self::jsonResponse(['error' => 'Acceso denegado'], 403);
            exit;
        }

        return $payload;
    }

    private static function jsonResponse($data, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}
?>
```

### 3. API Endpoints Protegidos

```php
<?php
class UserController
{
    public function profile()
    {
        // Autenticar usuario
        $payload = ApiMiddleware::authenticate();

        // Obtener información completa del usuario
        $user = User::find($payload['user_id']);

        if (!$user) {
            ApiMiddleware::jsonResponse(['error' => 'Usuario no encontrado'], 404);
            return;
        }

        // Retornar perfil (sin contraseña)
        unset($user->password);
        ApiMiddleware::jsonResponse([
            'user' => $user,
            'token_expires' => $payload['exp']
        ]);
    }

    public function updateProfile()
    {
        $payload = ApiMiddleware::authenticate();

        $data = json_decode(file_get_contents('php://input'), true);

        $user = User::find($payload['user_id']);
        if (!$user) {
            ApiMiddleware::jsonResponse(['error' => 'Usuario no encontrado'], 404);
            return;
        }

        // Actualizar campos permitidos
        $allowedFields = ['nombre', 'apellido', 'email'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $user->$field = $data[$field];
            }
        }

        if ($user->update($user->id)) {
            ApiMiddleware::jsonResponse([
                'message' => 'Perfil actualizado',
                'user' => $user
            ]);
        } else {
            ApiMiddleware::jsonResponse(['error' => 'Error al actualizar'], 500);
        }
    }

    public function adminOnly()
    {
        // Solo administradores
        $payload = ApiMiddleware::requireAdmin();

        ApiMiddleware::jsonResponse([
            'message' => 'Acceso de administrador',
            'admin_data' => 'Datos sensibles aquí'
        ]);
    }
}
?>
```

### 4. Frontend JavaScript

```javascript
// Cliente JavaScript para API con JWT
class ApiClient {
  constructor() {
    this.token =
      localStorage.getItem("jwt_token") || this.getCookie("jwt_token") || "";
  }

  getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
    return null;
  }

  async request(endpoint, options = {}) {
    const url = `/api/${endpoint}`;
    const config = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    if (this.token) {
      config.headers["Authorization"] = `Bearer ${this.token}`;
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      // Si el token expiró, intentar refrescar
      if (response.status === 401 && data.error?.includes("expirado")) {
        const refreshed = await this.refreshToken();
        if (refreshed) {
          config.headers["Authorization"] = `Bearer ${this.token}`;
          const retryResponse = await fetch(url, config);
          return await retryResponse.json();
        }
      }

      return data;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  async refreshToken() {
    try {
      const response = await fetch("/api/auth/refresh", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${this.token}`,
        },
      });

      if (response.ok) {
        const data = await response.json();
        this.token = data.token;
        localStorage.setItem("jwt_token", this.token);
        return true;
      }
    } catch (error) {
      console.error("Token refresh failed:", error);
    }

    return false;
  }

  login(email, password) {
    return this.request("auth/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    });
  }

  getProfile() {
    return this.request("user/profile");
  }

  logout() {
    this.token = "";
    localStorage.removeItem("jwt_token");
    document.cookie =
      "jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    return this.request("auth/logout", { method: "POST" });
  }
}

// Uso
const api = new ApiClient();

// Login
api.login("user@example.com", "password").then((data) => {
  if (data.token) {
    api.token = data.token;
    localStorage.setItem("jwt_token", data.token);
    window.location.href = "/dashboard";
  }
});

// Obtener perfil
api.getProfile().then((data) => {
  console.log("User profile:", data.user);
});
```

---

## 🛡️ Middleware de Autenticación

### 1. Middleware Básico

```php
<?php
class AuthMiddleware
{
    public static function handle()
    {
        $token = self::getTokenFromRequest();

        if (empty($token)) {
            self::unauthorized('Token requerido');
        }

        $payload = JWTAuth::validateToken($token);

        if (!$payload) {
            self::unauthorized('Token inválido o expirado');
        }

        return $payload;
    }

    private static function getTokenFromRequest()
    {
        // Prioridad: Header > Cookie > Session
        $token = $_SERVER['HTTP_AUTHORIZATION'] ??
                 $_SERVER['HTTP_X_AUTH_TOKEN'] ??
                 $_COOKIE['jwt_token'] ??
                 $_SESSION['token'] ?? '';

        return str_replace('Bearer ', '', $token);
    }

    private static function unauthorized($message)
    {
        if (self::isApiRequest()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => $message]);
        } else {
            header('Location: /login?error=' . urlencode($message));
        }
        exit;
    }

    private static function isApiRequest()
    {
        return strpos($_SERVER['REQUEST_URI'], '/api/') === 0 ||
               isset($_SERVER['HTTP_ACCEPT']) &&
               strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }
}
?>
```

### 2. Middleware de Roles

```php
<?php
class RoleMiddleware
{
    public static function requireRole($requiredRole)
    {
        $payload = AuthMiddleware::handle();

        $userRole = $payload['role'] ?? 'user';

        if ($userRole !== $requiredRole && $userRole !== 'admin') {
            self::forbidden('Acceso denegado');
        }

        return $payload;
    }

    public static function requireAdmin()
    {
        return self::requireRole('admin');
    }

    public static function requireModerator()
    {
        $payload = AuthMiddleware::handle();

        $userRole = $payload['role'] ?? 'user';

        if (!in_array($userRole, ['admin', 'moderator'])) {
            self::forbidden('Se requieren privilegios de moderador');
        }

        return $payload;
    }

    private static function forbidden($message)
    {
        if (AuthMiddleware::isApiRequest()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => $message]);
        } else {
            http_response_code(403);
            echo $message;
        }
        exit;
    }
}
?>
```

---

## 🔒 Seguridad y Buenas Prácticas

### 1. Configuración Segura

```php
// ✅ Usar HTTPS obligatoriamente
if ($_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}

// ✅ Configurar cookies seguras
setcookie('jwt_token', $token, [
    'expires' => time() + 3600,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,      // Solo HTTPS
    'httponly' => true,    // No accesible por JavaScript
    'samesite' => 'Strict' // Protección CSRF
]);
```

### 2. Manejo de Errores

```php
// ✅ Mensajes genéricos por seguridad
public static function validateToken($token)
{
    try {
        return JWT::decode($token, new Key(self::getKey(), 'HS256'));
    } catch (\Firebase\JWT\ExpiredException $e) {
        error_log("Token expired: " . $e->getMessage());
        return false;
    } catch (\Firebase\JWT\BeforeValidException $e) {
        error_log("Token not valid yet: " . $e->getMessage());
        return false;
    } catch (\Firebase\JWT\SignatureInvalidException $e) {
        error_log("Invalid token signature: " . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log("Token validation error: " . $e->getMessage());
        return false;
    }
}
```

### 3. Rate Limiting

```php
class RateLimitMiddleware
{
    private static $attempts = [];

    public static function check($identifier, $maxAttempts = 5, $window = 300)
    {
        $now = time();
        $key = $identifier . '_' . floor($now / $window);

        if (!isset(self::$attempts[$key])) {
            self::$attempts[$key] = 0;
        }

        self::$attempts[$key]++;

        if (self::$attempts[$key] > $maxAttempts) {
            header('HTTP/1.0 429 Too Many Requests');
            header('Retry-After: ' . $window);
            exit('Too many attempts');
        }
    }
}

// Uso en login
RateLimitMiddleware::check($_SERVER['REMOTE_ADDR']);
```

### 4. Logs de Auditoría

```php
class AuditLogger
{
    public static function logAuth($action, $userId, $email, $ip)
    {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'user_id' => $userId,
            'email' => $email,
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        error_log("AUTH_AUDIT: " . json_encode($log));

        // También guardar en base de datos si es necesario
        // AuditLog::create($log);
    }
}

// Uso
AuditLogger::logAuth('LOGIN_SUCCESS', $user->id, $user->email, $_SERVER['REMOTE_ADDR']);
```

---

## 🔍 Troubleshooting

### Problemas Comunes

#### 1. Token inválido

**Causa:** Clave JWT incorrecta o token malformado

**Solución:**

```php
// Verificar clave
$key = \Environment::get('JWT_KEY');
if (empty($key)) {
    error_log("JWT_KEY no configurada");
}

// Validar formato
$parts = explode('.', $token);
if (count($parts) !== 3) {
    error_log("Token malformado");
}
```

#### 2. Token expirado

**Causa:** Tiempo de expiración superado

**Solución:**

```php
// Implementar refresco automático
$payload = JWTAuth::decodeToken($token);
if ($payload && $payload['exp'] - time() < 300) {
    $newToken = JWTAuth::refreshToken($token);
}
```

#### 3. Problemas con cookies

**Causa:** Configuración incorrecta de cookies

**Solución:**

```php
// Verificar configuración de cookies
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
```

---

## 📈 Optimización y Rendimiento

### 1. Caché de Validación

```php
class CachedTokenValidator
{
    private static $cache = [];

    public static function validate($token)
    {
        $hash = md5($token);

        if (!isset(self::$cache[$hash])) {
            self::$cache[$hash] = JWTAuth::validateToken($token);
        }

        return self::$cache[$hash];
    }
}
```

### 2. Tokens Cortos para APIs

```php
// Para APIs móviles, usar tokens más cortos
$apiToken = JWTAuth::generateToken($userId, 900); // 15 minutos

// Para web, tokens más largos
$webToken = JWTAuth::generateToken($userId, 3600); // 1 hora
```

---

## 🚀 Extensiones y Personalización

### 1. Claims Personalizados

```php
public static function generateToken($userId, $expiresIn = null, $customClaims = [])
{
    $payload = array_merge([
        'iss' => $_SERVER['HTTP_HOST'],
        'aud' => $_SERVER['HTTP_HOST'],
        'iat' => time(),
        'exp' => time() + ($expiresIn ?? 3600),
        'user_id' => $userId,
        'role' => 'user',
        'permissions' => []
    ], $customClaims);

    return JWT::encode($payload, self::getKey(), 'HS256');
}
```

### 2. Tokens por Tipo

```php
class TokenType
{
    const ACCESS = 'access';
    const REFRESH = 'refresh';
    const RESET = 'reset';
    const EMAIL_VERIFY = 'email_verify';
}

public static function generateTokenByType($userId, $type, $expiresIn = null)
{
    $expirations = [
        TokenType::ACCESS => 3600,
        TokenType::REFRESH => 604800,
        TokenType::RESET => 1800,
        TokenType::EMAIL_VERIFY => 86400
    ];

    $expires = $expiresIn ?? $expirations[$type] ?? 3600;

    return self::generateToken($userId, $expires, ['type' => $type]);
}
```

---

## 📝 Notas Importantes

### Seguridad

- **Nunca expongas** la clave JWT en el frontend
- **Usa siempre** HTTPS en producción
- **Implementa rate limiting** para prevenir ataques
- **Registra intentos fallidos** de autenticación
- **Usa cookies HttpOnly** cuando sea posible

### Mantenimiento

- **Rota claves** periódicamente
- **Monitorea tokens** anómalos
- **Limpia tokens** expirados
- **Actualiza librerías** JWT regularmente

### Cumplimiento

- **GDPR**: Considera los tokens como datos personales
- **CCPA**: Derecho a eliminar tokens
- **SOX**: Logs de auditoría para accesos

---

## 🆘 Soporte

### Recursos Útiles

- [JWT.io](https://jwt.io/) - Debugging de tokens
- [Firebase JWT PHP](https://github.com/firebase/php-jwt) - Documentación oficial
- [OWASP Authentication](https://owasp.org/www-project-application-security-verification-standard/) - Guías de seguridad

### Herramientas

```bash
# Decodificar token para debugging
echo "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..." | cut -d. -f2 | base64 -d

# Generar clave segura
openssl rand -base64 32
```

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 7.4+, Firebase JWT 6.0+, MVC-WEB Framework
**Última Actualización:** Enero 5, 2026

---

**Documentación mantenida con ❤️ por el equipo MVC-WEB**
