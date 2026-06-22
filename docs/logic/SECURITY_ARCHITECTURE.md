# 🎓 Lógica de Ingeniería: Security Architecture

En esta sección aprenderás cómo el framework protege los datos.

## 1. Defensa contra Inyección SQL: La Primera Muralla

El error más común en PHP es concatenar variables directamente en las consultas SQL. 

### ¿Cómo lo evitamos?
En nuestro framework, el usuario nunca escribe SQL crudo en los controladores. Usamos **Sentencias Preparadas** y **Mapeo de Atributos**.

**La Lógica Técnica:**
Cuando usas `User::findBy('email', $value)`, el sistema no envía `$value` directamente. Internamente:
1.  Busca las columnas permitidas en la clase.
2.  Prepara la consulta: `SELECT * FROM table WHERE email = ?`
3.  Asocia (bind) el valor de forma segura.

---

## 2. Sanitización Automática de Entrada

Los ataques XSS (Cross-Site Scripting) ocurren cuando alguien envía código malicioso (como `<script>`) en un formulario.

### El Filtro Permanente
Cada vez que creamos un objeto con datos externos:
```php
public function __construct($args = []) {
    foreach($args as $key => $value) {
        if(property_exists($this, $key)) {
            $this->$key = s($value); // La función s() sanitiza el HTML
        }
    }
}
```
**Nota técnica:**
Fíjate en la función `s()`. Es una utilidad global que aplica `htmlspecialchars` a cada dato que entra.
 Esto significa que los datos están "limpios" desde el momento en que nacen dentro de la aplicación.

---

## 3. Gestión Segura de Archivos (FileManager)

Subir archivos es la parte más peligrosa de una web. Alguien podría subir un script PHP (webshell) y tomar control del servidor.

### Tres Niveles de Protección:
1.  **Validación MIME Real**: No confiamos en la extensión (ej. `.jpg`). Leemos el contenido binario del archivo para asegurar que sea realmente lo que dice ser.
2.  **Renombrado Aleatorio**: Convertimos `foto.jpg` en algo como `a8e9...2b1.jpg`. Esto evita colisiones y hace imposible que un atacante "adivine" la ruta de sus archivos.
3.  **Aislamiento de Ejecución**: Las carpetas de imágenes no deben permitir la ejecución de scripts.

---

## 4. Autenticación JWT vs Sesiones

El framework soporta ambos, pero el **JWT (JSON Web Tokens)** es nuestra joya de la corona para APIs.

- **Tokens Firmados**: Usamos una `JWT_KEY` secreta. Si alguien intenta cambiar su ID en el token, la firma se rompe y el sistema rechaza la petición.
- **Cookies HttpOnly**: Los tokens se guardan en cookies que JavaScript no puede leer, protegiéndolos contra ataques de robo de identidad.

---

### Resumen de Ingeniería
- **Privilegio Mínimo**: Solo pedimos y permitimos los datos necesarios.
- **Inmutabilidad**: Las validaciones se ejecutan siempre antes de tocar la base de datos.
- **Opacidad**: Ocultamos los nombres reales de archivos y IDs internos mediante hashing.
