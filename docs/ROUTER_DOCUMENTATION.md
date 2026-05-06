# 🛣️ Documentación del Router y la Clase Request

El sistema de enrutamiento de este framework permite gestionar peticiones HTTP de forma sencilla, segura y extensible, soportando aplicaciones RESTful modernas.

## 1. El Router

El Router es el encargado de registrar las rutas y despachar la petición al controlador correspondiente.

### Registro de Rutas
Puedes registrar rutas para los métodos `GET`, `POST`, `PUT` y `DELETE`.

```php
use controllers\PagesController;
use MVC\Router;

$router = new Router();

// Rutas GET
$router->get('/usuario', [PagesController::class, 'index']);

// Rutas POST
$router->post('/usuario', [PagesController::class, 'crear']);

// Rutas PUT (REST o Spoofing)
$router->put('/usuario/actualizar', [PagesController::class, 'actualizar']);

// Rutas DELETE (REST o Spoofing)
$router->delete('/usuario/eliminar', [PagesController::class, 'eliminar']);

// Rutas con protección por roles
$router->get('/admin', [PagesController::class, 'admin'], ['admin']);

$router->Rutas();
```

---

## 2. La Clase Request

La clase `Request` representa la petición entrante y unifica el acceso a los datos.

### Métodos Disponibles

#### `getMethod()`
Retorna el método HTTP actual (siempre en mayúsculas). Soporta *method spoofing* (ver más abajo).

#### `getPath()`
Retorna la URL actual limpia de parámetros de consulta.

#### `getBody()`
Retorna un **arreglo asociativo** con todos los datos de la petición.
- Detecta automáticamente datos enviados como **JSON** (fetch/axios).
- Lee datos de formularios tradicionales (`$_POST`).
- Procesa datos de `PUT` y `DELETE` crudos.

---

## 3. Uso en Controladores

A partir de la v8.0.0, el Router inyecta automáticamente los objetos `$router` y `$request` en los métodos de tus controladores.

```php
namespace controllers;

use MVC\Router;
use MVC\Request;

class UsuarioController {
    public static function actualizar(Router $router, Request $request) {
        // Obtener datos unificados
        $datos = $request->getBody();
        
        $nombre = $datos['nombre'] ?? '';
        
        // Renderizar vista o responder JSON
        echo json_encode(['status' => 'success', 'nombre' => $nombre]);
    }
}
```

---

## 4. Method Spoofing (Formularios HTML)

Dado que los formularios HTML nativos solo soportan `GET` y `POST`, puedes usar otros métodos añadiendo un campo oculto llamado `_method`:

```html
<form action="/usuario/actualizar" method="POST">
    <!-- El Router tratará esta petición como PUT -->
    <input type="hidden" name="_method" value="PUT">
    
    <input type="text" name="nombre">
    <button type="submit">Actualizar</button>
</form>
```

---

## 5. Peticiones desde JavaScript (REST)

Cuando usas `fetch` o bibliotecas como `axios`, el sistema procesa el cuerpo JSON automáticamente:

```javascript
fetch('/usuario/actualizar', {
    method: 'PUT', // Método real
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        id: 1,
        nombre: 'Nuevo Nombre'
    })
});
```
En el controlador, usarás `$request->getBody()` y recibirás el arreglo con el `id` y el `nombre`.
