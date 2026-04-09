# 🎓 Lógica de Ingeniería: Routing & Request

En esta sección vamos a entender cómo el framework "escucha" al mundo exterior.

## 1. El Reto: ¿Qué nos están pidiendo?

Cuando un navegador nos visita, PHP nos lanza una montaña de datos desordenados en variables globales como `$_SERVER`, `$_POST` y `$_GET`. Además, PHP nativo tiene un punto débil: **no sabe manejar `PUT` o `DELETE` automáticamente**.

### La Solución: El Binomio Router-Request

Hemos separado el proceso en dos responsabilidades claras:
1.  **Request**: Se encarga de la **percepción**. Analiza la entrada de datos.
2.  **Router**: Se encarga de la **decisión**. Decide qué controlador debe actuar.

---

## 2. Ingeniería de la Clase `Request`

La clase `Request` es como un traductor. Su lógica más interesante está en el método `getBody()`.

### ¿Cómo resolvemos el problema de PUT/DELETE?
Como `$_PUT` no existe, usamos el flujo de entrada crudo: `php://input`.

```php
// Fragmento de la lógica técnica
$input = file_get_contents('php://input');
$data = json_decode($input, true); // ¿Es JSON?
if (json_last_error() !== JSON_ERROR_NONE) {
    parse_str($input, $data); // No es JSON, intenta como Form Data
}
```

**Nota técnica:**
Fíjate que usamos `json_decode` con el segundo parámetro en `true`. Esto asegura que siempre trabajemos con **Arreglos Asociativos** de PHP, evitando la confusión de trabajar con objetos de clase `stdClass`.

> [!TIP]
> **El dilema de los tipos de datos**
> 
> ¿Por qué preferimos arreglos asociativos en lugar de objetos `stdClass`?
> 1. **Consistencia**: PHP maneja `$_POST` y `$_GET` como arreglos. Usar arreglos para JSON también mantiene la sintaxis `$datos['clave']` en todo el proyecto.
> 2. **Compatibilidad**: La mayoría de las funciones de filtrado y manipulación de datos en PHP están optimizadas para arreglos.
> 
> **JSON vs FormData**:
> Mientras que `FormData` es el estándar de los formularios HTML (que PHP captura en `$_POST`), el `JSON` es el estándar de las aplicaciones modernas. El método `getBody()` unifica ambos mundos usando `parse_str()` para el texto plano y `json_decode()` para el JSON, para que tú no tengas que preocuparte por el origen.

### Soporte para "Method Spoofing"
Los formularios HTML solo permiten `GET` y `POST`. Para simular un `DELETE` en un formulario, el sistema detecta un campo oculto llamado `_method`:
`if ($metodo === 'POST' && isset($_POST['_method']))` -> ¡Magia! El sistema trata la petición como el método indicado.

---

## 3. La Inteligencia Dinámica del `Router`

El `Router` ha evolucionado para no tener que usar infinitos `if/else`. Usamos **Propiedades Dinámicas**.

### El Despacho (Dispatch)
En lugar de preguntar: "¿Es GET? ¿Es POST?", el Router hace esto:
```php
$propiedadRutas = "rutas" . $request->getMethod(); // Resulta en "rutasGET" o "rutasPUT"
$fn = $this->{$propiedadRutas}[$urlActual] ?? null;
```
Esto permite que el framework crezca. Si mañana quieres soportar el método `PATCH`, solo añades una propiedad `$rutasPATCH` y el sistema lo detectará automáticamente sin tocar la lógica central.

---

## 4. Inyección de Dependencias: Pasando el Testigo

Finalmente, el Router ejecuta el controlador, pero no lo deja solo. Le pasa las herramientas necesarias:
`call_user_func($fn, $this, $request);`

**Análisis de diseño:**
¿Por qué pasamos el objeto `$request`?
Porque el controlador no debería ir a buscar los datos a las globales de PHP. Al recibir el objeto `$request`, el controlador ya tiene los datos limpios y listos para ser usados, promoviendo un código mucho más testeable y fácil de leer.

---

### Resumen de Ingeniería
- **Separación de conceptos**: Uno escucha, otro decide.
- **Unificación**: No importa el origen (JSON o Form), los datos llegan igual.
- **Extensibilidad**: El sistema está listo para nuevos métodos HTTP con mínimo esfuerzo.
