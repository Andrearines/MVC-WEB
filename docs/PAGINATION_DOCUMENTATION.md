# 📄 PaginationModel - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Estructura de la Clase](#estructura-de-la-clase)
4. [Propiedades](#propiedades)
5. [Constructor](#constructor)
6. [Métodos Principales](#métodos-principales)
7. [Métodos de Navegación](#métodos-de-navegación)
8. [Métodos de Renderizado](#métodos-de-renderizado)
9. [Ejemplos de Uso](#ejemplos-de-uso)
10. [Integración con Base de Datos](#integración-con-base-de-datos)
11. [Personalización](#personalización)
12. [Buenas Prácticas](#buenas-prácticas)

---

## 🎯 Descripción General

La clase `PaginationModel` es un sistema completo de paginación para aplicaciones PHP que facilita la navegación a través de grandes conjuntos de datos. Proporciona una interfaz limpia para generar enlaces de paginación HTML con navegación intuitiva.

### Características Principales

- ✅ **Navegación completa**: Anterior, siguiente, y números de página
- ✅ **Cálculo automático**: Total de páginas y offset
- ✅ **HTML semántico**: Estructura accesible y estilizable
- ✅ **Configuración flexible**: Registros por página personalizables
- ✅ **Estado actual**: Resalta la página activa
- ✅ **Validación inteligente**: Evita enlaces inválidos

---

## 🏗️ Estructura de la Clase

### Ubicación

```
app/components/PaginationModel.php
```

### Namespace

```php
namespace models;
```

---

## 📊 Propiedades

### `$current_page`

Página actual (comienza en 1).

```php
public $current_page;
```

### `$records_per_page`

Número de registros por página.

```php
public $records_per_page;
```

### `$total_pages`

Total de registros disponibles.

```php
public $total_pages;
```

---

## 🏗️ Constructor

### `__construct($current_page = 1, $records_per_page = 6, $total_pages = 0)`

Inicializa una nueva instancia de paginación.

**Parámetros:**

- `$current_page` (int): Página actual, default 1
- `$records_per_page` (int): Registros por página, default 6
- `$total_pages` (int): Total de registros, default 0

**Ejemplo:**

```php
$pagination = new PaginationModel(
    $_GET['page'] ?? 1,  // Página actual desde URL
    10,                  // 10 registros por página
    $totalRegistros      // Total de registros de la consulta
);
```

---

## 📚 Métodos Principales

### `offset()`

Calcula el offset para la consulta SQL.

```php
public function offset()
{
    return $this->records_per_page * ($this->current_page - 1);
}
```

**Retorna:** int - Offset para LIMIT en SQL

**Uso en consulta:**

```php
$offset = $pagination->offset();
$limit = $pagination->records_per_page;

$query = "SELECT * FROM usuarios LIMIT $limit OFFSET $offset";
```

### `totalPages()`

Calcula el total de páginas necesarias.

```php
public function totalPages()
{
    return ceil($this->total_pages / $this->records_per_page);
}
```

**Retorna:** int - Número total de páginas

---

## 🔄 Métodos de Navegación

### `nextPage()`

Obtiene el número de la siguiente página.

```php
public function nextPage()
{
    if ($this->current_page < $this->totalPages()) {
        return $this->current_page + 1;
    }
    return false;
}
```

**Retorna:** int|false - Siguiente página o false si no hay

### `previousPage()`

Obtiene el número de la página anterior.

```php
public function previousPage()
{
    if ($this->current_page > 1) {
        return $this->current_page - 1;
    }
    return false;
}
```

**Retorna:** int|false - Página anterior o false si no hay

---

## 🎨 Métodos de Renderizado

### `previousLink()`

Genera el enlace HTML para la página anterior.

```php
public function previousLink()
{
    $html = "";
    if ($this->previousPage() !== false) {
        $html .= '<a class="pagination_link" href="?page=' . $this->previousPage() . '">previous</a>';
    }
    return $html;
}
```

**Retorna:** string - HTML del enlace anterior

### `nextLink()`

Genera el enlace HTML para la siguiente página.

```php
public function nextLink()
{
    $html = "";
    if ($this->nextPage() !== false) {
        $html .= '<a class="pagination_link" href="?page=' . $this->nextPage() . '">next</a>';
    }
    return $html;
}
```

**Retorna:** string - HTML del enlace siguiente

### `pageNumbers()`

Genera los enlaces HTML para los números de página.

```php
public function pageNumbers()
{
    $html = "";
    for ($i = 1; $i <= $this->totalPages(); $i++) {
        if ($i == $this->current_page) {
            $html .= "<span class='pagination_active'>" . $i . "</span>";
        } else {
            $html .= "<a class='pagination_link' href='?page=" . $i . "'>" . $i . "</a>";
        }
    }
    return $html;
}
```

**Retorna:** string - HTML de los números de página

### `pagination()`

Genera el HTML completo de la paginación.

```php
public function pagination()
{
    $html = "";
    if ($this->totalPages() > 1) {
        // Pagination logic would go here
        $html .= '<div class="pagination">';
        $html .= $this->previousLink();
        $html .= $this->pageNumbers();
        $html .= $this->nextLink();
        $html .= '</div>';
    }
    return $html;
}
```

**Retorna:** string - HTML completo de paginación

---

## 💡 Ejemplos de Uso

### 1. Uso Básico con Base de Datos

```php
<?php
class UserController
{
    public function list()
    {
        // Obtener total de registros
        $totalRegistros = User::count();

        // Crear paginación
        $pagination = new PaginationModel(
            $_GET['page'] ?? 1,
            10,
            $totalRegistros
        );

        // Obtener registros para la página actual
        $offset = $pagination->offset();
        $limit = $pagination->records_per_page;

        $usuarios = User::limit($limit, $offset)->get();

        // Renderizar vista con paginación
        return view('users/list', [
            'usuarios' => $usuarios,
            'pagination' => $pagination
        ]);
    }
}
```

### 2. Integración con Consultas Complejas

```php
<?php
class ProductController
{
    public function search($query)
    {
        // Consulta base con filtros
        $baseQuery = Product::where('nombre', 'LIKE', "%$query%")
                           ->where('activo', 1);

        // Total de registros
        $totalRegistros = $baseQuery->count();

        // Paginación
        $pagination = new PaginationModel(
            $_GET['page'] ?? 1,
            12, // 12 productos por página
            $totalRegistros
        );

        // Obtener productos paginados
        $productos = $baseQuery->limit($pagination->records_per_page)
                              ->offset($pagination->offset())
                              ->orderBy('nombre')
                              ->get();

        return view('products/search', [
            'productos' => $productos,
            'pagination' => $pagination,
            'query' => $query
        ]);
    }
}
```

### 3. Paginación con AJAX

```php
<?php
class APIController
{
    public function getUsers()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;

        $total = User::count();
        $pagination = new PaginationModel($page, $perPage, $total);

        $users = User::limit($pagination->records_per_page)
                    ->offset($pagination->offset())
                    ->get();

        return response()->json([
            'data' => $users,
            'pagination' => [
                'current_page' => $pagination->current_page,
                'total_pages' => $pagination->totalPages(),
                'records_per_page' => $pagination->records_per_page,
                'total_records' => $total,
                'has_next' => $pagination->nextPage() !== false,
                'has_previous' => $pagination->previousPage() !== false
            ]
        ]);
    }
}
```

### 4. Vista con Paginación

```php
<!-- views/users/list.php -->
<div class="users-container">
    <h1>Lista de Usuarios</h1>

    <div class="users-grid">
        <?php foreach ($usuarios as $usuario): ?>
            <div class="user-card">
                <h3><?= htmlspecialchars($usuario->nombre) ?></h3>
                <p><?= htmlspecialchars($usuario->email) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginación -->
    <div class="pagination-wrapper">
        <?= $pagination->pagination() ?>
    </div>
</div>
```

---

## 🗄️ Integración con Base de Datos

### Método Count en Modelo Base

```php
<?php
class Main
{
    public static function count($column = 'id')
    {
        $query = "SELECT COUNT($column) as total FROM " . static::$table;
        $result = self::$db->query($query);
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    public static function limit($limit, $offset = 0)
    {
        $query = "SELECT * FROM " . static::$table . " LIMIT $limit OFFSET $offset";
        $result = self::$db->query($query);

        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }
        return $array;
    }
}
```

### Consulta SQL Directa

```php
<?php
// Para consultas personalizadas
$totalQuery = "SELECT COUNT(*) as total FROM usuarios WHERE activo = 1";
$result = $db->query($totalQuery);
$total = $result->fetch_assoc()['total'];

$dataQuery = "SELECT * FROM usuarios WHERE activo = 1
              LIMIT {$pagination->records_per_page}
              OFFSET {$pagination->offset()}";
```

---

## 🎨 Personalización

### CSS para Estilos

```css
/* Estilos base de paginación */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin: 20px 0;
  flex-wrap: wrap;
}

.pagination_link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  height: 40px;
  padding: 0 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  color: #333;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.2s ease;
}

.pagination_link:hover {
  background: #f5f5f5;
  border-color: #999;
}

.pagination_active {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 40px;
  height: 40px;
  padding: 0 12px;
  border: 1px solid #007bff;
  border-radius: 4px;
  background: #007bff;
  color: white;
  font-weight: bold;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
  .pagination {
    gap: 5px;
  }

  .pagination_link,
  .pagination_active {
    min-width: 35px;
    height: 35px;
    font-size: 12px;
  }
}
```

### Personalizar Textos

```php
<?php
class CustomPaginationModel extends PaginationModel
{
    public function previousLink()
    {
        $html = "";
        if ($this->previousPage() !== false) {
            $html .= '<a class="pagination_link" href="?page=' . $this->previousPage() . '">';
            $html .= '<span>&laquo; Anterior</span>';
            $html .= '</a>';
        }
        return $html;
    }

    public function nextLink()
    {
        $html = "";
        if ($this->nextPage() !== false) {
            $html .= '<a class="pagination_link" href="?page=' . $this->nextPage() . '">';
            $html .= '<span>Siguiente &raquo;</span>';
            $html .= '</a>';
        }
        return $html;
    }
}
```

### Paginación con Rangos

```php
<?php
class AdvancedPaginationModel extends PaginationModel
{
    public function pageNumbers($range = 2)
    {
        $html = "";
        $total = $this->totalPages();
        $current = $this->current_page;

        // Calcular rango
        $start = max(1, $current - $range);
        $end = min($total, $current + $range);

        // Primera página
        if ($start > 1) {
            $html .= $this->pageLink(1);
            if ($start > 2) {
                $html .= '<span class="pagination_ellipsis">...</span>';
            }
        }

        // Páginas del rango
        for ($i = $start; $i <= $end; $i++) {
            $html .= $this->pageLink($i);
        }

        // Última página
        if ($end < $total) {
            if ($end < $total - 1) {
                $html .= '<span class="pagination_ellipsis">...</span>';
            }
            $html .= $this->pageLink($total);
        }

        return $html;
    }

    private function pageLink($page)
    {
        if ($page == $this->current_page) {
            return "<span class='pagination_active'>$page</span>";
        }
        return "<a class='pagination_link' href='?page=$page'>$page</a>";
    }
}
```

---

## 📊 Métricas y Optimización

### Información de Paginación

```php
<?php
class PaginationController
{
    public function getPaginationInfo()
    {
        $pagination = new PaginationModel(
            $_GET['page'] ?? 1,
            10,
            1000
        );

        return [
            'current_page' => $pagination->current_page,
            'total_pages' => $pagination->totalPages(),
            'records_per_page' => $pagination->records_per_page,
            'total_records' => $pagination->total_pages,
            'offset' => $pagination->offset(),
            'showing_from' => $pagination->offset() + 1,
            'showing_to' => min(
                $pagination->offset() + $pagination->records_per_page,
                $pagination->total_pages
            ),
            'has_previous' => $pagination->previousPage() !== false,
            'has_next' => $pagination->nextPage() !== false
        ];
    }
}
```

### Optimización para Grandes Volúmenes

```php
<?php
// Para tablas muy grandes, usar COUNT optimizado
$totalQuery = "SELECT TABLE_ROWS
              FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = 'database_name'
              AND TABLE_NAME = 'users'";

// O usar aproximación para mejor rendimiento
$estimated = User::raw('SELECT COUNT(*) FROM users LIMIT 1')->fetchColumn();
```

---

## 🛡️ Buenas Prácticas

### Seguridad

1. **Validar página actual**: Asegurar que sea un número válido
2. **Límites máximos**: Establecer máximo de registros por página
3. **Sanitizar parámetros**: Limpiar parámetros de entrada

### Rendimiento

1. **Índices adecuados**: Crear índices en columnas de ORDER BY
2. **COUNT optimizado**: Usar COUNT(\*) en lugar de COUNT(columna)
3. **Caché**: Cachear resultados de COUNT para consultas complejas

### Experiencia de Usuario

1. **Estado actual**: Mostrar siempre la página activa
2. **Información útil**: Mostrar rango de registros visibles
3. **Navegación intuitiva**: Enlaces claros de anterior/siguiente

### Ejemplo Completo

```php
<?php
class UserController
{
    public function index()
    {
        // Validar página
        $page = max(1, (int)($_GET['page'] ?? 1));

        // Limitar registros por página
        $perPage = min(50, max(5, (int)($_GET['per_page'] ?? 10)));

        // Obtener total (con caché si es posible)
        $cacheKey = 'users_count';
        $total = Cache::remember($cacheKey, 3600, function() {
            return User::count();
        });

        // Crear paginación
        $pagination = new PaginationModel($page, $perPage, $total);

        // Obtener datos
        $users = User::limit($pagination->records_per_page)
                    ->offset($pagination->offset())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('users/index', [
            'users' => $users,
            'pagination' => $pagination,
            'info' => [
                'showing_from' => $pagination->offset() + 1,
                'showing_to' => min(
                    $pagination->offset() + $pagination->records_per_page,
                    $total
                ),
                'total' => $total
            ]
        ]);
    }
}
```

---

## 📚 Referencia Rápida

### Resumen de Métodos

| Método           | Propósito                 | Parámetros                    | Retorna |
| ---------------- | ------------------------- | ----------------------------- | ------- | ----- |
| `__construct()`  | Inicializar paginación    | `$page`, `$perPage`, `$total` | -       |
| `offset()`       | Calcular offset SQL       | -                             | int     |
| `totalPages()`   | Calcular total páginas    | -                             | int     |
| `nextPage()`     | Obtener siguiente página  | -                             | int     | false |
| `previousPage()` | Obtener página anterior   | -                             | int     | false |
| `previousLink()` | Generar enlace anterior   | -                             | string  |
| `nextLink()`     | Generar enlace siguiente  | -                             | string  |
| `pageNumbers()`  | Generar números de página | -                             | string  |
| `pagination()`   | Generar HTML completo     | -                             | string  |

### Valores por Defecto

| Propiedad            | Valor por Defecto |
| -------------------- | ----------------- |
| Página actual        | 1                 |
| Registros por página | 6                 |
| Total de registros   | 0                 |

### Clases CSS Generadas

- `.pagination` - Contenedor principal
- `.pagination_link` - Enlaces de página
- `.pagination_active` - Página actual activa

---

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. Página fuera de rango

**Problema:** Usuario accede a página mayor que el total

**Solución:**

```php
$page = min($page, $pagination->totalPages());
```

#### 2. Offset negativo

**Problema:** Cálculo incorrecto cuando página = 0

**Solución:**

```php
$page = max(1, (int)($_GET['page'] ?? 1));
```

#### 3. Sin resultados

**Problema:** Paginación muestra cuando no hay datos

**Solución:**

```php
if ($total > 0) {
    echo $pagination->pagination();
}
```

### Debug

```php
// Debug de paginación
error_log("Page: " . $pagination->current_page);
error_log("Total Pages: " . $pagination->totalPages());
error_log("Offset: " . $pagination->offset());
error_log("Limit: " . $pagination->records_per_page);
```

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 8.0+
**Dependencias:** Ninguna

---

**Desarrollado con ❤️ para navegación eficiente de datos**
