# 🎓 Lógica de Ingeniería: Smart Cache Engine

En esta sección vamos a explorar el sistema de **Caché Inteligente**.

## 1. El Concepto: Memoria de Corto Plazo

Imagina que preguntas por el usuario con ID 1. La base de datos responde. Si un segundo después vuelves a preguntar por el mismo usuario, ¿por qué molestar a la base de datos? 

Nuestro **Main Model** utiliza un sistema de almacenamiento en memoria para guardar estos resultados temporalmente durante el ciclo de vida de la petición.

---

## 2. Cómo funciona el Smart Cache

La lógica reside en la clase `Main.php`. Se basa en tres pilares:

### A. La Llave Maestras (Key Management)
Cada consulta genera una "llave" única basada en los parámetros (nombre de tabla, IDs, columnas). 
`$cacheKey = $tableName . "_" . $id . "_" . implode(',', $columns);`

### B. El Almacén Seguro
Usamos una propiedad estática privada:
`private static $cachedData = [];`
Al ser estática, se mantiene viva mientras dure la ejecución de la petición actual, pero es privada para que nadie pueda corromper los datos desde afuera.

### C. La Lógica de Intercepción
Cuando llamas a un método como `find($id)`, el sistema hace esto:
1.  **Busca**: ¿Tengo la llave `$cacheKey` en mi almacén?
2.  **Entrega**: Si la tiene, la devuelve inmediatamente. ¡Tiempo de respuesta: ~1ms!
3.  **Consulta**: Si no la tiene, va a la DB, guarda el resultado en el almacén y luego lo entrega.

---

## 3. El Problema de la Coherencia (Invalidez de Caché)

El mayor reto del caché es: **¿Qué pasa cuando los datos cambian?** Si actualizas un usuario pero el caché sigue entregando la versión vieja, tenemos un problema.

### La Solución: Limpieza Reactiva
Hemos diseñado el framework para que sea "consciente" de los cambios. En cuanto ejecutas un método que modifica la DB (`save()`, `update()`, `delete()`), el framework dispara automáticamente:
`self::clearCache();`

**Nota técnica:**
Esta es la forma más segura de manejar caché.
 Preferimos borrar todo el caché y asegurar que el siguiente dato sea fresco, a arriesgarnos a entregar información obsoleta. Es un balance entre **Rendimiento** e **Integridad**.

---

## 4. Control Total para el Desarrollador

A veces, no quieres caché (por ejemplo, en reportes en tiempo real). El framework te da el control:

- `disableCache()`: Apaga el sistema temporalmente.
- `enableCache()`: Lo enciende de nuevo.
- `getCacheStats()`: Te dice cuántos elementos hay guardados y si el sistema está activo.

### ¿Por qué esta arquitectura?
Porque logramos una **mejora del 99%** en la velocidad de consultas repetidas sin añadir complejidad al código del controlador. El desarrollador solo usa `User::find(1)` y el framework se encarga del resto.

---

### Resumen de Ingeniería
- **Transparencia**: El desarrollador no tiene que escribir lógica de caché.
- **Seguridad**: Limpieza automática ante cualquier cambio (POST/PUT/DELETE).
- **Eficiencia**: Reduce drásticamente la latencia y la carga en el servidor SQL.
