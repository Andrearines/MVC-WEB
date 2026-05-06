# DOCUMENTACIÓN DEL SISTEMA DE LOGS (MONOLOG)

El framework `MVC-WEB` integra **Monolog** para proveer un sistema avanzado, confiable e industrial de "Logging" (Registro de eventos o errores). 

Gracias al Wrapper `services\Logger`, podrás enviar mensajes y registrar acciones sin tener que configurar Monolog cada vez en tus controladores o modelos.

## Características de la Implementación
1. **Daily Log Rotation (Rotación Diaria)**: Por defecto, generará archivos bajo el formato `logs/app-YYYY-MM-DD.log`. Esto asegura que los archivos de texto no se volverán inmensamente pesados, facilitando buscar errores por día en concreto.
2. **Auto-limpieza**: Solo guardará el historial de los **últimos 30 días**. Eliminará lo demás automáticamente.
3. **Array Context Contextual**: Permitimos inyectar arreglos que en el archivo `.log` se verán formateados en formato JSON a la perfección.

## Niveles de Severidad
Están basados en la convención oficial **PSR-3**:
- `debug()`: Información de depuración minuciosa. (Generalmente se usa en desarrollo).
- `info()`: Sucesos normales interesantes pero no errores (Ej. "Usuario X inició sesión").
- `warning()`: Acontecimientos excepcionales que no son errores (Ej. "Se usarán credenciales viejas por fallback").
- `error()`: El flujo tuvo un error grave pero la app sigue funcionando. (Ej. "Fallo enviando correo al usuario").
- `critical()`: Errores tan graves que abortan transacciones o el funcionamiento correcto de un servicio (Ej. "Base de datos caída").

---

## 🚀 ¿Cómo usarlo en la Aplicación?

Solo debes importar el namespace correcto al principio del archivo y llamar los métodos estáticos del logger.

### Ejemplo dentro de un Controlador:

```php
<?php
namespace controllers;

use services\Logger; // <--- Importamos el servicio
use MVC\Router;

class MiControlador
{
    public static function crearUsuario(Router $router)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // ... Lógica para guardar ...
                
                // 1. Uso Básico (Info)
                Logger::info("Se ha creado exitosamente un usuario nuevo.");

            } catch (\Exception $e) {
                
                // 2. Uso con Contexto de Error (Error)
                // Pasamos el string del error al log en un Array (Contexto)
                Logger::error("Excepción al intentar crear el usuario", [
                    'mensaje' => $e->getMessage(),
                    'codigo' => $e->getCode()
                ]);
                
            }
        }
    }
}
```

### Ejemplo para Debugear Array o Objetos:

Es muy común querer ver el contenido de un post request, sin hacer `var_dump()` y ensuciar la pantalla.

```php
Logger::debug("Petición Inundante de POST", $_POST);
```

¡Boom! Puedes entrar a `logs/app-fecha.log` y verás tu array procesado en puro JSON directamente ahí, sin interrumpir tu sistema local.

## 📂 ¿Dónde se Guardan?
Todos los archivos se ubican en el interior del directorio `logs/` en la raíz de tu proyecto. El archivo `.gitignore` configurado allí protegerá que Github ignore estos archivos, evitando subidas públicas de información sensible que haya ocurrido en local o producción, preservando tu seguridad y limpieza de Commits.
