# 👤 Modelos de Usuario - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Modelos Disponibles](#modelos-disponibles)
3. [UserPHP - Modelo Principal](#userphp---modelo-principal)
4. [UserTokenModel - Autenticación JWT](#usertokenmodel---autenticación-jwt)
5. [Estructura de Base de Datos](#estructura-de-base-de-datos)
6. [Métodos de Validación](#métodos-de-validación)
7. [Autenticación y Seguridad](#autenticación-y-seguridad)
8. [Ejemplos Prácticos](#ejemplos-prácticos)
9. [Integración con Controladores](#integración-con-controladores)
10. [Buenas Prácticas](#buenas-prácticas)

---

## 🎯 Descripción General

El sistema de usuarios de MVC-WEB consta de dos modelos principales que trabajan juntos para proporcionar una gestión completa de usuarios con autenticación segura, validación de datos y control de acceso.

### Características Principales

- ✅ **Autenticación segura** con JWT y hashing Argon2ID
- ✅ **Validación completa** de datos de usuario
- ✅ **Gestión de tokens** para recuperación de contraseña
- ✅ **Control de acceso** basado en roles (admin/user)
- ✅ **Verificación de email** y confirmación de cuenta
- ✅ **Seguridad avanzada** contra ataques comunes
- ✅ **Integración con caché** del modelo Main

---

## 📚 Modelos Disponibles

### 1. UserPHP

**Ubicación:** `app/models/UserPHP.php`

**Propósito:** Modelo principal para gestión de usuarios del sistema.

**Características:**

- Hereda del modelo Main (con caché)
- Validaciones completas para registro, login, recuperación
- Manejo de contraseñas seguras
- Verificación de existencia de usuarios

### 2. UserTokenModel

**Ubicación:** `app/models/UserTokenModel.php`

**Propósito:** Gestión de tokens JWT para autenticación.

**Características:**

- Generación de tokens JWT
- Validación de tokens
- Refresco de tokens
- Manejo de expiración

---

## 👤 UserPHP - Modelo Principal

### Estructura de la Clase

```php
<?php
namespace models;

class UserPHP extends Main
{
    public static $table = "users";
    public static $columnDB = ["id", "nombre", "apellido", "email", "password", "confirmado", "token", "admin"];

    // Propiedades públicas
    public $id;
    public $admin;
    public $nombre;
    public $apellido;
    public $token;
    public $email;
    public $confirmado;
    public $password_c;
    public $password;
}
```

### Propiedades

| Propiedad    | Tipo   | Descripción                                 |
| ------------ | ------ | ------------------------------------------- |
| `id`         | int    | ID único del usuario                        |
| `nombre`     | string | Nombre del usuario                          |
| `apellido`   | string | Apellido del usuario                        |
| `email`      | string | Email único del usuario                     |
| `password`   | string | Contraseña hasheada                         |
| `password_c` | string | Confirmación de contraseña (temporal)       |
| `confirmado` | bool   | Estado de confirmación de email             |
| `token`      | string | Token para recuperación de contraseña       |
| `admin`      | bool   | Nivel de administrador (0=usuario, 1=admin) |

### Constructor

```php
public function __construct($args = [])
```

**Parámetros:**

- `$args`: Array asociativo con datos del usuario

**Ejemplo:**

```php
$user = new UserPHP([
    'nombre' => 'Juan',
    'apellido' => 'Pérez',
    'email' => 'juan@example.com',
    'password' => 'password123',
    'password_c' => 'password123'
]);
```

---

## 🔐 UserTokenModel - Autenticación JWT

### Métodos Principales

#### `generateToken($userId)`

Genera un token JWT para un usuario.

```php
$token = UserTokenModel::generateToken($userId);
```

#### `validateToken($token)`

Valida un token JWT y retorna el payload.

```php
$payload = UserTokenModel::validateToken($token);
if ($payload) {
    $userId = $payload['user_id'];
}
```

#### `refreshToken($token)`

Refresca un token existente.

```php
$newToken = UserTokenModel::refreshToken($oldToken);
```

---

## 🗄️ Estructura de Base de Datos

### Tabla `users`

```sql
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

### Índices Recomendados

```sql
-- Índice para búsqueda por email
CREATE INDEX idx_users_email ON users(email);

-- Índice para búsqueda por token
CREATE INDEX idx_users_token ON users(token);

-- Índice compuesto para usuarios confirmados
CREATE INDEX idx_users_confirmados ON users(confirmado, email);
```

---

## 🔍 Métodos de Validación

### 1. Validate_Register()

Valida datos para registro de nuevos usuarios.

```php
public function Validate_Register(): array
```

**Validaciones:**

- Nombre y apellido obligatorios
- Email válido y único
- Contraseña mínima 6 caracteres
- Confirmación de contraseña coincidente
- Email no registrado previamente

**Ejemplo:**

```php
$user = new UserPHP($_POST);
$errors = $user->Validate_Register();

if (!empty($errors['error'])) {
    foreach ($errors['error'] as $error) {
        echo "<p class='error'>$error</p>";
    }
} else {
    // Proceder con registro
}
```

### 2. Validate_Login()

Valida credenciales para inicio de sesión.

```php
public function Validate_Login(): array
```

**Validaciones:**

- Email obligatorio y válido
- Contraseña obligatoria y mínima 6 caracteres

### 3. Validate_Forget()

Valida email para recuperación de contraseña.

```php
public function Validate_Forget(): array
```

**Validaciones:**

- Email obligatorio y válido

### 4. Validate_reset()

Valida datos para reseteo de contraseña.

```php
public function Validate_reset(): array
```

**Validaciones:**

- Token válido
- Contraseña obligatoria y mínima 6 caracteres
- Confirmación de contraseña coincidente

---

## 🔐 Autenticación y Seguridad

### Métodos de Contraseña

#### `Password_hash($password = null)`

Hashea una contraseña usando Argon2ID.

```php
$user = new UserPHP();
$user->password = "miPasswordSeguro";
$user->Password_hash(); // Hashea la contraseña actual

// O hashea una contraseña específica
$user->Password_hash("otroPassword");
```

#### `Verify_password($hash, $password)`

Verifica una contraseña contra su hash.

```php
$user = new UserPHP();
$isValid = $user->Verify_password($hash, $password);
```

### Generación de Tokens

#### `create_token()`

Genera un token aleatorio para recuperación de contraseña.

```php
$token = $user->create_token(); // Ej: "a1b2c3d4e5"
```

### Verificación de Existencia

#### `ExisteUser()`

Verifica si un email ya está registrado.

```php
if ($user->ExisteUser()) {
    echo "El email ya está registrado";
} else {
    echo "Email disponible";
}
```

---

## 💡 Ejemplos Prácticos

### 1. Registro de Usuario

```php
<?php
// En el controlador de registro
public function register()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new UserPHP($_POST);

        // Validar datos
        $errors = $user->Validate_Register();

        if (empty($errors['error'])) {
            // Hashear contraseña
            $user->Password_hash();

            // Crear token de confirmación
            $user->create_token();

            // Guardar usuario
            if ($user->save()) {
                // Enviar email de confirmación
                $email = new EmailModel();
                $email->enviarBienvenida($user->email, $user->nombre);

                // Redirigir con mensaje de éxito
                header('Location: /login?success=registered');
                exit;
            }
        } else {
            // Mostrar errores
            return view('auth/register', [
                'errors' => $errors['error'],
                'old' => $_POST
            ]);
        }
    }

    return view('auth/register');
}
?>
```

### 2. Inicio de Sesión

```php
<?php
public function login()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new UserPHP($_POST);
        $errors = $user->Validate_Login();

        if (empty($errors['error'])) {
            // Buscar usuario por email
            $foundUser = UserPHP::findBy('email', $user->email);

            if ($foundUser && $user->Verify_password($foundUser->password, $user->password)) {
                // Generar token JWT
                $token = UserTokenModel::generateToken($foundUser->id);

                // Guardar token en sesión
                $_SESSION['token'] = $token;
                $_SESSION['user'] = $foundUser;

                // Redirigir al dashboard
                header('Location: /dashboard');
                exit;
            } else {
                $errors['error'][] = 'Credenciales incorrectas';
            }
        }

        return view('auth/login', [
            'errors' => $errors['error'] ?? [],
            'old' => $_POST
        ]);
    }

    return view('auth/login');
}
?>
```

### 3. Recuperación de Contraseña

```php
<?php
public function forgotPassword()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new UserPHP($_POST);
        $errors = $user->Validate_Forget();

        if (empty($errors['error'])) {
            // Buscar usuario
            $foundUser = UserPHP::findBy('email', $user->email);

            if ($foundUser) {
                // Generar token
                $foundUser->create_token();
                $foundUser->update($foundUser->id);

                // Enviar email de recuperación
                $email = new EmailModel();
                $email->enviarRecuperacionPassword(
                    $foundUser->email,
                    $foundUser->token,
                    $foundUser->nombre
                );

                return view('auth/forgot-success', [
                    'message' => 'Se ha enviado un email de recuperación'
                ]);
            }

            // Siempre mostrar éxito por seguridad
            return view('auth/forgot-success', [
                'message' => 'Si el email existe, se enviará un enlace de recuperación'
            ]);
        }

        return view('auth/forgot', [
            'errors' => $errors['error'],
            'old' => $_POST
        ]);
    }

    return view('auth/forgot');
}
?>
```

### 4. Reseteo de Contraseña

```php
<?php
public function resetPassword($token)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new UserPHP($_POST);
        $user->token = $token;
        $errors = $user->Validate_reset();

        if (empty($errors['error'])) {
            // Buscar usuario por token
            $foundUser = UserPHP::findBy('token', $token);

            if ($foundUser) {
                // Actualizar contraseña
                $foundUser->password = $user->password;
                $foundUser->Password_hash();
                $foundUser->token = null; // Limpiar token
                $foundUser->confirmado = 1; // Confirmar cuenta
                $foundUser->update($foundUser->id);

                return view('auth/reset-success', [
                    'message' => 'Contraseña actualizada correctamente'
                ]);
            }
        }

        return view('auth/reset', [
            'errors' => $errors['error'],
            'token' => $token,
            'old' => $_POST
        ]);
    }

    // Verificar que el token sea válido
    $user = UserPHP::findBy('token', $token);
    if (!$user) {
        return view('auth/invalid-token');
    }

    return view('auth/reset', ['token' => $token]);
}
?>
```

### 5. Middleware de Autenticación

```php
<?php
class AuthMiddleware
{
    public static function authenticate()
    {
        $token = $_SESSION['token'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($token)) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Acceso no autorizado');
        }

        // Quitar "Bearer " si existe
        $token = str_replace('Bearer ', '', $token);

        $payload = UserTokenModel::validateToken($token);

        if (!$payload) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Token inválido o expirado');
        }

        // Obtener usuario
        $user = UserPHP::find($payload['user_id']);

        if (!$user) {
            header('HTTP/1.0 401 Unauthorized');
            exit('Usuario no encontrado');
        }

        return $user;
    }

    public static function requireAdmin()
    {
        $user = self::authenticate();

        if (!$user->admin) {
            header('HTTP/1.0 403 Forbidden');
            exit('Acceso denegado. Se requieren privilegios de administrador');
        }

        return $user;
    }
}
?>
```

---

## 🔧 Integración con Controladores

### LoginController

```php
<?php
namespace controllers;

use models\UserPHP;
use models\UserTokenModel;
use models\EmailModel;

class LoginController
{
    public function index()
    {
        return view('auth/login');
    }

    public function authenticate()
    {
        // Implementación de login (ver ejemplo arriba)
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function register()
    {
        // Implementación de registro (ver ejemplo arriba)
    }

    public function forgot()
    {
        // Implementación de recuperación (ver ejemplo arriba)
    }

    public function reset($token)
    {
        // Implementación de reseteo (ver ejemplo arriba)
    }
}
?>
```

---

## 🎯 Buenas Prácticas

### 1. Seguridad

```php
// ✅ Siempre hashea contraseñas
$user->Password_hash();

// ✅ Usa tokens seguros
$token = bin2hex(random_bytes(32)); // Más seguro que 5 bytes

// ✅ Valida siempre la entrada
$email = filter_var($email, FILTER_VALIDATE_EMAIL);

// ✅ Usa prepared statements (heredado de Main)
$user = UserPHP::findBy('email', $email);
```

### 2. Manejo de Errores

```php
// ✅ Agrupa errores por tipo
$errors = $user->Validate_Register();
if (!empty($errors['error'])) {
    // Manejar errores
}

// ✅ Mensajes genéricos por seguridad
if (!$user) {
    echo "Credenciales incorrectas"; // No revelar si el email existe
}
```

### 3. Sesiones y Tokens

```php
// ✅ Regenera ID de sesión después del login
session_regenerate_id(true);

// ✅ Usa HTTPS para tokens
if ($_SERVER['HTTPS'] !== 'on') {
    // Redirigir a HTTPS
}

// ✅ Establece tiempo de expiración
ini_set('session.cookie_lifetime', 3600); // 1 hora
```

### 4. Validaciones Adicionales

```php
// ✅ Validación de fortaleza de contraseña
public function validatePasswordStrength($password)
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Mínimo 8 caracteres";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Al menos una mayúscula";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Al menos un número";
    }

    if (!preg_match('/[!@#$%^&*]/', $password)) {
        $errors[] = "Al menos un carácter especial";
    }

    return $errors;
}
```

---

## 🔍 Troubleshooting

### Problemas Comunes

#### 1. Contraseña no verifica

**Causa:** Hashing incorrecto o comparación errónea

**Solución:**

```php
// Verifica que estés usando el mismo método
$user->Password_hash(); // Argon2ID
$user->Verify_password($hash, $password); // Verificación correcta
```

#### 2. Token JWT inválido

**Causa:** Token expirado o clave incorrecta

**Solución:**

```php
// Verifica configuración de JWT
$payload = UserTokenModel::validateToken($token);
if (!$payload) {
    // Generar nuevo token
    $newToken = UserTokenModel::generateToken($userId);
}
```

#### 3. Email duplicado

**Causa:** Validación incorrecta o race condition

**Solución:**

```php
// Usa transacciones para evitar race conditions
Main::$db->begin_transaction();
try {
    if ($user->ExisteUser()) {
        throw new Exception("Email ya existe");
    }
    $user->save();
    Main::$db->commit();
} catch (Exception $e) {
    Main::$db->rollback();
    throw $e;
}
```

---

## 📈 Optimización y Rendimiento

### 1. Caché de Consultas

```php
// UserPHP hereda caché de Main
$users = UserPHP::all(['id', 'nombre', 'email']); // Con caché

// Limpiar caché después de actualizaciones
$user->save(); // Limpia caché automáticamente
```

### 2. Consultas Optimizadas

```php
// ✅ Traer solo columnas necesarias
$users = UserPHP::all(['id', 'nombre', 'email']);

// ✅ Usar índices en búsquedas
$user = UserPHP::findBy('email', $email); // Usa índice idx_users_email
```

### 3. Batch Operations

```php
// Para múltiples operaciones
$users = UserPHP::findAllBy('confirmado', 0, ['id', 'email']);
foreach ($users as $user) {
    $user->confirmado = 1;
    $user->update($user->id);
}
```

---

## 🚀 Extensiones y Personalización

### 1. Campos Adicionales

```php
class UserPHP extends Main
{
    public static $columnDB = [
        "id", "nombre", "apellido", "email", "password",
        "confirmado", "token", "admin", "telefono", "avatar", "bio"
    ];

    public $telefono;
    public $avatar;
    public $bio;

    public function __construct($args = [])
    {
        parent::__construct($args);
        $this->telefono = $this::$db->real_escape_string($args["telefono"] ?? "");
        $this->avatar = $this::$db->real_escape_string($args["avatar"] ?? "");
        $this->bio = $this::$db->real_escape_string($args["bio"] ?? "");
    }
}
```

### 2. Roles Personalizados

```php
class UserPHP extends Main
{
    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;
    const ROLE_MODERATOR = 2;
    const ROLE_SUPER_ADMIN = 3;

    public function hasRole($role)
    {
        return $this->admin >= $role;
    }

    public function isModerator()
    {
        return $this->hasRole(self::ROLE_MODERATOR);
    }
}
```

---

## 📝 Notas Importantes

### Seguridad

- **Nunca almacenes** contraseñas en texto plano
- **Usa siempre** HTTPS para autenticación
- **Implementa rate limiting** para intentos de login
- **Registra intentos fallidos** de autenticación

### Mantenimiento

- **Limpia tokens** expirados periódicamente
- **Actualiza algoritmos** de hashing cuando sea necesario
- **Monitorea actividades** sospechosas
- **Backup regular** de datos de usuarios

### Cumplimiento

- **GDPR**: Derecho al olvido y portabilidad de datos
- **CCPA**: Privacidad de datos de California
- **LGPD**: Ley de protección de datos Brasil

---

## 🆘 Soporte

### Recursos Útiles

- [Documentación Main Model](MAIN_MODEL_DOCUMENTATION.md)
- [Documentación JWT](JWT_DOCUMENTATION.md)
- [EmailModel Documentation](EMAIL_DOCUMENTATION.md)
- [Security Best Practices](../README.md#seguridad)

### Contacto

Para soporte técnico sobre los modelos de usuario, consulta la documentación o crea un issue en el repositorio.

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 7.4+, MySQL 5.7+, MVC-WEB Framework
**Última Actualización:** Enero 5, 2026

---

**Documentación mantenida con ❤️ por el equipo MVC-WEB**
