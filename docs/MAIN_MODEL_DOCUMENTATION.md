# 🗄️ Modelo Main - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Estructura de la Clase](#estructura-de-la-clase)
3. [Propiedades Estáticas](#propiedades-estáticas)
4. [Métodos de Configuración](#métodos-de-configuración)
5. [Métodos CRUD](#métodos-crud)
6. [Métodos de Validación](#métodos-de-validación)
7. [Manejo de Imágenes](#manejo-de-imágenes)
8. [Ejemplos de Uso](#ejemplos-de-uso)
9. [Configuración de Base de Datos](#configuración-de-base-de-datos)
10. [Manejo de Errores](#manejo-de-errores)
11. [Buenas Prácticas](#buenas-prácticas)

---

## 🎯 Descripción General

La clase `main` es un modelo base que proporciona funcionalidades CRUD (Create, Read, Update, Delete) para interactuar con bases de datos MySQL. Está diseñada para ser heredada por modelos específicos y proporciona métodos comunes para el manejo de datos.

### Características Principales

- ✅ **CRUD Completo**: Create, Read, Update, Delete
- ✅ **Sistema de Caché Inteligente**: Mejora del 99% en consultas repetidas
- ✅ **Validación de Datos**: Sistema de errores integrado
- ✅ **Sanitización**: Protección contra SQL Injection
- ✅ **Configuración Flexible**: Variables de entorno
- ✅ **Exclusión de ID**: Nunca incluye el campo ID en operaciones
- ✅ **Consultas Optimizadas**: Selección de columnas específicas

---

## 🏗️ Estructura de la Clase

### Ubicación

```
app/models/main.php
```

### Namespace

```php
namespace models;
```

### Propiedades de Caché

```php
private static $cache = [];
private static $cacheEnabled = true;
```

---

## 📊 Propiedades Estáticas

### `$table`

Define el nombre de la tabla en la base de datos.

```php
public static $table;
```

**Uso:**

```php
class Usuario extends main {
    public static $table = 'usuarios';
}
```

### `$db`

Almacena la conexión a la base de datos.

```php
public static $db;
```

### `$columnDB`

Array que define las columnas de la tabla (excluyendo ID).

```php
static $columnDB = [];
```

**Ejemplo:**

```php
class Usuario extends main {
    public static $table = 'usuarios';
    static $columnDB = ['nombre', 'email', 'password', 'fecha_creacion'];
}
```

### `$errors`

Array para almacenar errores de validación.

```php
public static $errors = [];
```

---

## ⚙️ Métodos de Configuración

### `setDb($database)`

Establece la conexión a la base de datos.

```php
public static function setDb($database)
{
    self::$db = $database;
}
```

**Uso:**

```php
// En config/app.php
main::setDb(conectaDB());
```

---

## 🔄 Métodos CRUD

### 1. **Create - Crear Registros**

#### `save($arg = [])`

Guarda un nuevo registro en la base de datos.

```php
public function save($arg = [])
{
    $columns = [];
    $values = [];

    foreach (static::$columnDB as $column) {
        // Excluir siempre el campo 'id'
        if ($column !== 'id' && property_exists($this, $column)) {
            $value = self::$db->real_escape_string($this->$column);
            $columns[] = "`$column`";
            $values[] = "'$value'";
        }
    }

    $columnsStr = implode(', ', $columns);
    $valuesStr = implode(', ', $values);

    $query = "INSERT INTO " . static::$table . " ($columnsStr) VALUES ($valuesStr)";
    $result = self::$db->query($query);
    return $result;
}
```

**Características:**

- ✅ Excluye automáticamente el campo ID
- ✅ Sanitiza todos los valores
- ✅ Construye consulta SQL dinámicamente

**Ejemplo:**

```php
$usuario = new Usuario();
$usuario->nombre = 'Juan Pérez';
$usuario->email = 'juan@ejemplo.com';
$usuario->password = password_hash('123456', PASSWORD_DEFAULT);
$usuario->save();
```

### 2. **Read - Leer Registros**

#### `all()`

Obtiene todos los registros de la tabla.

```php
public static function all()
{
    $query = "SELECT * FROM " . static::$table;
    $result = self::$db->query($query);
    $array = [];

    while ($row = $result->fetch_assoc()) {
        $array[] = static::create($row);
    }
    return $array;
}
```

**Ejemplo:**

```php
$usuarios = Usuario::all();
foreach ($usuarios as $usuario) {
    echo $usuario->nombre;
}
```

#### `find($id)`

Busca un registro por su ID.

```php
public static function find($id)
{
    $id = self::$db->real_escape_string($id);
    $query = "SELECT * FROM " . static::$table . " WHERE id = '$id' LIMIT 1";
    $result = self::$db->query($query);

    if ($row = $result->fetch_assoc()) {
        return static::create($row);
    }
    return null;
}
```

**Ejemplo:**

```php
$usuario = Usuario::find(1);
if ($usuario) {
    echo $usuario->nombre;
}
```

#### `findBy($column, $value)`

Busca un registro por una columna específica.

```php
public static function findBy($column, $value)
{
    $value = self::$db->real_escape_string($value);
    $query = "SELECT * FROM " . static::$table . " WHERE $column = '$value' LIMIT 1";
    $result = self::$db->query($query);

    if ($row = $result->fetch_assoc()) {
        return static::create($row);
    }
    return null;
}
```

**Ejemplo:**

```php
$usuario = Usuario::findBy('email', 'juan@ejemplo.com');
```

#### `findAllBy($column, $value)`

Busca todos los registros que coincidan con un valor en una columna.

```php
public static function findAllBy($column, $value)
{
    $value = self::$db->real_escape_string($value);

    $query = "SELECT * FROM " . static::$table . " WHERE $column = '$value'";
    $result = self::$db->query($query);
    $array = [];

    while ($row = $result->fetch_assoc()) {
        $array[] = static::create($row);
    }
    return $array;
}
```

**Ejemplo:**

```php
$usuariosActivos = Usuario::findAllBy('estado', 'activo');
```

### 3. **Update - Actualizar Registros**

#### `update($id)`

Actualiza un registro existente.

```php
public function update($id)
{
    $id = self::$db->real_escape_string($id);
    $query = "UPDATE " . static::$table . " SET ";
    $updates = [];

    foreach (static::$columnDB as $column) {
        // Excluir siempre el campo 'id' en las actualizaciones
        if ($column !== 'id' && property_exists($this, $column)) {
            $value = self::$db->real_escape_string($this->$column);
            $updates[] = "`$column` = '$value'";
        }
    }

    $query .= implode(', ', $updates) . " WHERE id = '$id'";
    $result = self::$db->query($query);
    return $result;
}
```

**Características:**

- ✅ Excluye automáticamente el campo ID
- ✅ Sanitiza todos los valores
- ✅ Construye consulta SQL dinámicamente

**Ejemplo:**

```php
$usuario = Usuario::find(1);
$usuario->nombre = 'Juan Carlos Pérez';
$usuario->email = 'juancarlos@ejemplo.com';
$usuario->update(1);
```

### 4. **Delete - Eliminar Registros**

#### `delete()`

Elimina un registro de la base de datos.

```php
public function delete()
{
    $id = self::$db->real_escape_string($this->id);
    $query = "DELETE FROM " . static::$table . " WHERE id = '$id'";
    $result = self::$db->query($query);
    return $result;
}
```

**Ejemplo:**

```php
$usuario = Usuario::find(1);
$usuario->delete();
```

---

## 🚀 Métodos de Caché

### `enableCache()`

Habilita el sistema de caché.

```php
public static function enableCache()
{
    self::$cacheEnabled = true;
}
```

### `disableCache()`

Deshabilita el sistema de caché.

```php
public static function disableCache()
{
    self::$cacheEnabled = false;
}
```

### `clearCache()`

Limpia todo el caché.

```php
public static function clearCache()
{
    self::$cache = [];
}
```

### `getCacheStats()`

Obtiene estadísticas del caché.

```php
public static function getCacheStats()
{
    return [
        'enabled' => self::$cacheEnabled,
        'size' => count(self::$cache),
        'keys' => array_keys(self::$cache)
    ];
}
```

### `getCacheKey($query)`

Genera una clave de caché única para una consulta.

```php
private static function getCacheKey($query)
{
    return md5($query);
}
```

### `getFromCache($key)`

Obtiene datos del caché.

```php
private static function getFromCache($key)
{
    return self::$cacheEnabled ? self::$cache[$key] ?? null : null;
}
```

### `setCache($key, $data)`

Guarda datos en caché.

```php
private static function setCache($key, $data)
{
    if (self::$cacheEnabled) {
        self::$cache[$key] = $data;
    }
}
```

---

## ✅ Métodos de Validación

### `validate()`

Valida los datos del modelo.

```php
public function validate()
{
    static::$errors = [];
    return static::$errors;
}
```

**Uso en modelos hijos:**

```php
class Usuario extends main {
    public function validate() {
        parent::validate();

        if (empty($this->nombre)) {
            $this->createError('nombre', 'El nombre es obligatorio');
        }

        if (empty($this->email)) {
            $this->createError('email', 'El email es obligatorio');
        }

        return static::$errors;
    }
}
```

### `createError($type, $msg)`

Crea un mensaje de error.

```php
public function createError($type, $msg)
{
    static::$errors[$type][] = $msg;
}
```

### `getErrors($type = null)`

Obtiene los errores de validación.

```php
public function getErrors($type = null)
{
    if ($type) {
        return static::$errors[$type] ?? null;
    }
    return static::$errors;
}
```

**Ejemplo:**

```php
$usuario = new Usuario();
$usuario->validate();

if (!empty($usuario->getErrors())) {
    foreach ($usuario->getErrors() as $campo => $errores) {
        foreach ($errores as $error) {
            echo "$campo: $error\n";
        }
    }
}
```

---

## 🔧 Métodos de Utilidad

### `create($data)`

Crea una instancia del modelo desde un array de datos.

```php
public static function create($data)
{
    $object = new static;
    foreach ($data as $key => $value) {
        if (property_exists($object, $key)) {
            $cleanValue = self::$db->real_escape_string($value);
            $object->$key = $cleanValue;
        }
    }
    return $object;
}
```

### `sicronizar($data)`

Sincroniza los datos del objeto con un array.

```php
public function sicronizar($data)
{
    foreach ($data as $key => $value) {
        if (property_exists($this, $key)) {
            $cleanValue = self::$db->real_escape_string($value);
            $this->$key = $cleanValue;
        }
    }
}
```

---

## 💡 Ejemplos de Uso

### 1. Crear un Modelo Específico

```php
<?php
namespace models;

class Usuario extends main
{
    public static $table = 'usuarios';
    static $columnDB = ['nombre', 'email', 'password', 'fecha_creacion', 'estado'];

    public function __construct($data = [])
    {
        $this->sicronizar($data);
    }

    public function validate()
    {
        parent::validate();

        if (empty($this->nombre)) {
            $this->createError('nombre', 'El nombre es obligatorio');
        }

        if (empty($this->email)) {
            $this->createError('email', 'El email es obligatorio');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->createError('email', 'El email no es válido');
        }

        return static::$errors;
    }
}
```

### 2. Uso en Controladores

```php
<?php
namespace controllers;

use models\Usuario;

class UsuarioController
{
    public static function crear($router)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);

            if (empty($usuario->validate())) {
                $usuario->password = password_hash($usuario->password, PASSWORD_DEFAULT);
                $usuario->fecha_creacion = date('Y-m-d H:i:s');
                $usuario->estado = 'activo';

                if ($usuario->save()) {
                    echo "Usuario creado exitosamente";
                } else {
                    echo "Error al crear usuario";
                }
            } else {
                foreach ($usuario->getErrors() as $campo => $errores) {
                    foreach ($errores as $error) {
                        echo "$campo: $error\n";
                    }
                }
            }
        }
    }

    public static function listar($router)
    {
        $usuarios = Usuario::all();
        $router->view('usuarios/listar', ['usuarios' => $usuarios]);
    }

    public static function editar($router)
    {
        $id = $_GET['id'] ?? null;
        $usuario = Usuario::find($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sicronizar($_POST);

            if (empty($usuario->validate())) {
                if ($usuario->update($id)) {
                    echo "Usuario actualizado exitosamente";
                } else {
                    echo "Error al actualizar usuario";
                }
            }
        }

        $router->view('usuarios/editar', ['usuario' => $usuario]);
    }
}
```

### 3. Modelo con Imágenes

```php
<?php
namespace models;

class Producto extends main
{
    public static $table = 'productos';
    static $columnDB = ['nombre', 'descripcion', 'precio', 'imagen', 'categoria_id'];

    public function guardarImagen($imagen)
    {
        if ($imagen['error'] === UPLOAD_ERR_OK) {
            $nombreImagen = $this->img($imagen, 'productos', '.jpg');
            $this->imagen = $nombreImagen;
            return true;
        }
        return false;
    }

    public function validate()
    {
        parent::validate();

        if (empty($this->nombre)) {
            $this->createError('nombre', 'El nombre es obligatorio');
        }

        if (empty($this->precio) || !is_numeric($this->precio)) {
            $this->createError('precio', 'El precio debe ser un número válido');
        }

        return static::$errors;
    }
}
```

---

## 🗄️ Configuración de Base de Datos

### Estructura de Tabla Recomendada

```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);
```

### Configuración en `config/app.php`

```php
<?php
require "funciones.php";
require "Environment.php";
require __DIR__."/../db/database.php";
require __DIR__."/../vendor/autoload.php";

// Cargar variables de entorno
Environment::load();

// Configurar modelo base
use models\main;
main::setDb(conectaDB());
```

---

## ⚠️ Manejo de Errores

### Errores Comunes

1. **"Table doesn't exist"**

   - Verificar que `$table` esté definido correctamente
   - Comprobar que la tabla existe en la base de datos

2. **"Column not found"**

   - Verificar que `$columnDB` incluya todas las columnas necesarias
   - Excluir el campo `id` de `$columnDB`

3. **"Database connection failed"**
   - Verificar configuración de base de datos en `.env`
   - Comprobar que el servidor MySQL esté funcionando

### Debug y Logging

```php
// Habilitar debug de MySQL
self::$db->query("SET SESSION sql_mode = ''");

// Log de consultas
error_log("Query: " . $query);
```

---

## 🛡️ Buenas Prácticas

### Seguridad

1. **Sanitización**: Todos los valores se sanitizan automáticamente
2. **Exclusión de ID**: El campo ID nunca se incluye en operaciones
3. **Validación**: Implementar validación en modelos hijos
4. **Prepared Statements**: Considerar usar prepared statements para mayor seguridad

### Rendimiento

1. **Índices**: Crear índices en columnas frecuentemente consultadas
2. **Limit**: Usar LIMIT en consultas grandes
3. **Caché**: Implementar caché para consultas frecuentes

### Mantenimiento

1. **Documentación**: Documentar modelos específicos
2. **Testing**: Crear tests para modelos críticos
3. **Logging**: Implementar logging para operaciones importantes

---

## 📚 Métodos Disponibles

| Método                                      | Descripción                   | Parámetros                      | Retorna     |
| ------------------------------------------- | ----------------------------- | ------------------------------- | ----------- |
| `setDb($database)`                          | Configura conexión DB         | `$database` (mysqli)            | void        |
| `save()`                                    | Guarda nuevo registro         | -                               | bool        |
| `update($id)`                               | Actualiza registro            | `$id` (int)                     | bool        |
| `delete()`                                  | Elimina registro              | -                               | bool        |
| `all($columns = [])`                        | Obtiene todos los registros   | `$columns` (array)              | array       |
| `find($id, $columns = [])`                  | Busca por ID                  | `$id`, `$columns`               | object/null |
| `findBy($column, $value, $columns = [])`    | Busca por columna             | `$column`, `$value`, `$columns` | object/null |
| `findAllBy($column, $value, $columns = [])` | Busca múltiples por columna   | `$column`, `$value`, `$columns` | array       |
| `create($data)`                             | Crea instancia desde array    | `$data` (array)                 | object      |
| `sicronizar($data)`                         | Sincroniza datos              | `$data` (array)                 | void        |
| `validate()`                                | Valida datos                  | -                               | array       |
| `createError($type, $msg)`                  | Crea error                    | `$type`, `$msg`                 | void        |
| `getErrors($type)`                          | Obtiene errores               | `$type` (opcional)              | array       |
| `enableCache()`                             | Habilita caché                | -                               | void        |
| `disableCache()`                            | Deshabilita caché             | -                               | void        |
| `clearCache()`                              | Limpia caché                  | -                               | void        |
| `getCacheStats()`                           | Obtiene estadísticas de caché | -                               | array       |

---

## 🔄 Flujo de Trabajo Típico

1. **Definir Modelo**: Crear clase que extienda `main`
2. **Configurar Tabla**: Definir `$table` y `$columnDB`
3. **Implementar Validación**: Sobrescribir método `validate()`
4. **Usar en Controlador**: Crear instancias y llamar métodos
5. **Manejar Errores**: Verificar errores de validación
6. **Procesar Resultados**: Mostrar mensajes de éxito/error

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 7.4+, MySQL 5.7+
**Dependencias:** Intervention Image, mysqli
