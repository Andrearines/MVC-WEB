# 🧩 ComponentManager - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Estructura de Componentes](#estructura-de-componentes)
4. [Instalación y Configuración](#instalación-y-configuración)
5. [Métodos Disponibles](#métodos-disponibles)
6. [Creación de Componentes](#creación-de-componentes)
7. [Ejemplos Prácticos](#ejemplos-prácticos)
8. [Componentes Incluidos](#componentes-incluidos)
9. [Buenas Prácticas](#buenas-prácticas)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 Descripción General

El `ComponentManager` es un sistema de gestión de componentes reutilizables para aplicaciones MVC-PHP. Permite crear, renderizar y manejar componentes de UI de manera modular y eficiente, promoviendo la reutilización de código y manteniendo una estructura limpia y organizada.

### Características Principales

- ✅ **Renderizado Dinámico**: Componentes con datos variables
- ✅ **Estructura Modular**: Organización por carpetas temáticas
- ✅ **Reutilización**: Componentes usables en múltiples vistas
- ✅ **Manejo de Errores**: Gestión elegante de componentes no encontrados
- ✅ **Aislamiento**: Cada componente es independiente
- ✅ **Flexibilidad**: Soporta cualquier tipo de componente UI

---

## 🏗️ Estructura de Componentes

### Ubicación

```
app/
├── components/
│   ├── ComponentManager.php     # Clase principal
│   └── views/                   # Vistas de componentes
│       ├── inputs/              # Componentes de formulario
│       │   ├── input-file.php
│       │   ├── input-text.php
│       │   └── input-select.php
│       ├── cards/              # Componentes de tarjeta
│       │   ├── card.php
│       │   └── card-profile.php
│       ├── lists/              # Componentes de lista
│       │   ├── list.php
│       │   └── list-item.php
│       ├── tables/             # Componentes de tabla
│       │   ├── table.php
│       │   └── table-row.php
│       ├── modals/             # Componentes modales
│       │   ├── modal.php
│       │   └── modal-confirm.php
│       └── layouts/            # Componentes de layout
│           ├── header.php
│           ├── footer.php
│           └── sidebar.php
```

### Namespace

```php
namespace components;
```

---

## ⚙️ Instalación y Configuración

### Requisitos Previos

- PHP 7.4 o superior
- Estructura MVC existente
- Sistema de vistas compatible

### Configuración Básica

El ComponentManager no requiere configuración especial. Solo necesita:

1. **Ubicación correcta**: `app/components/ComponentManager.php`
2. **Carpeta de vistas**: `app/components/views/`
3. **Permisos de lectura**: Acceso a los archivos de componentes

---

## 📚 Métodos Disponibles

### Constructor

```php
public function __construct(string $componentPath, array $data = [])
```

**Parámetros:**

- `$componentPath`: Ruta relativa al componente (ej: `"inputs/input-file"`)
- `$data`: Array de datos para pasar al componente

### Método Principal

#### `render(): string`

Renderiza el componente y retorna el HTML generado.

**Retorna:** String con el HTML del componente

**Comportamiento:**

- Busca el archivo en `app/components/views/{$componentPath}.php`
- Extrae los datos para hacerlos disponibles en el componente
- Captura el output y lo retorna como string
- Si no encuentra el componente, retorna un comentario HTML

---

## 🎨 Creación de Componentes

### 1. Estructura Básica de un Componente

```php
<?php
// app/components/views/cards/card.php

// Variables disponibles (extraídas del array $data)
// $title, $content, $class, $id, etc.
?>

<div class="card <?= $class ?? '' ?>" <?= isset($id) ? "id='$id'" : '' ?>>
    <?php if (isset($title)): ?>
        <div class="card-header">
            <h3><?= htmlspecialchars($title) ?></h3>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?= $content ?? '' ?>
    </div>

    <?php if (isset($footer)): ?>
        <div class="card-footer">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
```

### 2. Componente con Lógica

```php
<?php
// app/components/views/lists/user-list.php

// $users: Array de usuarios
// $showActions: Boolean para mostrar acciones
// $class: Clases CSS adicionales
?>

<div class="user-list <?= $class ?? '' ?>">
    <?php if (empty($users)): ?>
        <p class="no-users">No hay usuarios para mostrar</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($users as $user): ?>
                <li class="list-group-item">
                    <div class="user-info">
                        <strong><?= htmlspecialchars($user['name']) ?></strong>
                        <span class="email"><?= htmlspecialchars($user['email']) ?></span>
                    </div>

                    <?php if ($showActions ?? false): ?>
                        <div class="user-actions">
                            <button class="btn btn-sm btn-primary"
                                    onclick="editUser(<?= $user['id'] ?>)">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger"
                                    onclick="deleteUser(<?= $user['id'] ?>)">
                                Eliminar
                            </button>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
```

### 3. Componente de Formulario

```php
<?php
// app/components/views/inputs/input-file.php

// $name: Nombre del input
// $label: Etiqueta del campo
// $accept: Tipos de archivo aceptados
// $required: Campo requerido
// $error: Mensaje de error
// $class: Clases CSS adicionales
?>

<div class="form-group input-file-group <?= $class ?? '' ?>">
    <?php if (isset($label)): ?>
        <label for="<?= $name ?>" class="form-label">
            <?= htmlspecialchars($label) ?>
            <?php if ($required ?? false): ?>
                <span class="required">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>

    <input type="file"
           name="<?= $name ?>"
           id="<?= $name ?>"
           class="form-control <?= isset($error) ? 'is-invalid' : '' ?>"
           <?= isset($accept) ? "accept='$accept'" : '' ?>
           <?= ($required ?? false) ? 'required' : '' ?>>

    <?php if (isset($error)): ?>
        <div class="invalid-feedback">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="file-preview" id="<?= $name ?>-preview"></div>
</div>
```

---

## 💡 Ejemplos Prácticos

### 1. Uso Básico

```php
<?php
use components\ComponentManager;

// Crear componente simple
$component = new ComponentManager('cards/card', [
    'title' => 'Bienvenido',
    'content' => 'Este es el contenido de la tarjeta',
    'class' => 'mb-3'
]);

echo $component->render();
?>
```

### 2. Componente con Datos Dinámicos

```php
<?php
// En un controlador
public function showUsers()
{
    $users = User::all();

    $userListComponent = new ComponentManager('lists/user-list', [
        'users' => $users,
        'showActions' => true,
        'class' => 'mt-4'
    ]);

    return view('users/index', [
        'userList' => $userListComponent->render()
    ]);
}
?>
```

### 3. Componente de Formulario con Validación

```php
<?php
// En una vista de formulario
$fileInput = new ComponentManager('inputs/input-file', [
    'name' => 'avatar',
    'label' => 'Foto de Perfil',
    'accept' => 'image/jpeg,image/png',
    'required' => true,
    'error' => $_SESSION['errors']['avatar'] ?? null,
    'class' => 'mb-3'
]);

echo $fileInput->render();
?>
```

### 4. Componentes Anidados

```php
<?php
// Componente principal
$modal = new ComponentManager('modals/modal', [
    'id' => 'userModal',
    'title' => 'Editar Usuario',
    'content' => (new ComponentManager('forms/user-form', [
        'user' => $userData,
        'mode' => 'edit'
    ]))->render(),
    'footer' => (new ComponentManager('buttons/modal-actions', [
        'save' => true,
        'cancel' => true
    ]))->render()
]);

echo $modal->render();
?>
```

### 5. Componente en un Loop

```php
<?php
// Generar múltiples tarjetas
$products = Product::all();
$cards = '';

foreach ($products as $product) {
    $cardComponent = new ComponentManager('cards/product-card', [
        'product' => $product,
        'showPrice' => true,
        'showButton' => true
    ]);

    $cards .= $cardComponent->render();
}

echo "<div class='product-grid'>$cards</div>";
?>
```

---

## 📦 Componentes Incluidos

### Input File Component

**Ubicación:** `app/components/views/inputs/input-file.php`

**Uso:**

```php
$fileInput = new ComponentManager('inputs/input-file', [
    'name' => 'document',
    'label' => 'Subir Documento',
    'accept' => '.pdf,.doc,.docx',
    'required' => true
]);
```

**Características:**

- Vista previa de archivos
- Validación de tipos
- Manejo de errores
- Diseño responsive

---

## 🎯 Buenas Prácticas

### 1. Organización

- **Nombres descriptivos**: Usa nombres claros y específicos
- **Carpetas temáticas**: Agrupa componentes por función
- **Consistencia**: Mantén un estilo uniforme

### 2. Datos y Seguridad

```php
// ✅ Bueno: Siempre sanitiza la salida
<div class="title"><?= htmlspecialchars($title) ?></div>

// ❌ Malo: Salida directa sin sanitizar
<div class="title"><?= $title ?></div>
```

### 3. Manejo de Variables Opcionales

```php
// ✅ Bueno: Usa el operador null coalescing
$class = $class ?? '';
$required = $required ?? false;

// ✅ Alternativa: Verifica con isset
<?php if (isset($title)): ?>
    <h3><?= htmlspecialchars($title) ?></h3>
<?php endif; ?>
```

### 4. Componentes Atómicos

```php
// ✅ Bueno: Componentes pequeños y reutilizables
- button.php
- input-text.php
- card.php

// ❌ Evita: Componentes monolíticos
- complete-user-form-with-validation-and-styling.php
```

### 5. Documentación de Componentes

```php
<?php
/**
 * Component: User Card
 *
 * @param array $user Datos del usuario
 * @param bool $showActions Mostrar botones de acción
 * @param string $class Clases CSS adicionales
 *
 * @example
 * $component = new ComponentManager('cards/user-card', [
 *     'user' => ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
 *     'showActions' => true,
 *     'class' => 'mb-3'
 * ]);
 */
?>
```

---

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. Componente no encontrado

**Error:** Componente no encontrado

**Causa:** Ruta incorrecta o archivo no existe

**Solución:**

```php
// Verifica la ruta
$componentPath = 'inputs/input-file'; // Sin .php
$filePath = __DIR__ . "/views/{$componentPath}.php";

// Debug
if (!file_exists($filePath)) {
    error_log("Componente no encontrado: $filePath");
}
```

#### 2. Variables no disponibles

**Error:** Variables undefined en el componente

**Causa:** Olvidar pasar datos o nombres incorrectos

**Solución:**

```php
// ✅ Verifica los datos que pasas
$data = [
    'title' => 'Mi Título',    // ← Correcto
    'content' => 'Contenido'   // ← Correcto
];

// ❌ Error común
$data = [
    'titulo' => 'Mi Título',   // ← Nombre diferente
    'contenido' => 'Contenido' // ← Nombre diferente
];
```

#### 3. Problemas de rendimiento

**Síntomas:** Carga lenta con muchos componentes

**Soluciones:**

- Implementa caché de componentes
- Usa lazy loading para componentes pesados
- Optimiza las consultas a base de datos

#### 4. Conflictos CSS/JS

**Síntomas:** Estilos o scripts no funcionan

**Soluciones:**

- Usa nombres de clases específicas
- Encapsula estilos con BEM
- Carga scripts solo cuando necesites el componente

### Debug y Logging

```php
// Para debuggear componentes
$component = new ComponentManager('test/component', $data);
$html = $component->render();

// Log del resultado
error_log("Component HTML length: " . strlen($html));
error_log("Component data: " . json_encode($data));

// Verificar si el componente existe
$componentPath = __DIR__ . "/views/{$componentPath}.php";
if (!file_exists($componentPath)) {
    error_log("Component file not found: $componentPath");
}
```

---

## 📈 Optimización y Rendimiento

### 1. Caché de Componentes

```php
class CachedComponentManager extends ComponentManager
{
    private static $cache = [];

    public function render(): string
    {
        $cacheKey = md5($this->componentPath . serialize($this->data));

        if (!isset(self::$cache[$cacheKey])) {
            self::$cache[$cacheKey] = parent::render();
        }

        return self::$cache[$cacheKey];
    }
}
```

### 2. Lazy Loading

```php
// Cargar componente solo cuando se necesite
function loadComponent($name, $data = null) {
    static $components = [];

    if (!isset($components[$name])) {
        $components[$name] = new ComponentManager($name, $data);
    }

    return $components[$name]->render();
}
```

---

## 🚀 Extensión y Personalización

### 1. ComponentManager Personalizado

```php
class AdvancedComponentManager extends ComponentManager
{
    private $theme;
    private $language;

    public function __construct(string $componentPath, array $data = [], string $theme = 'default')
    {
        $this->theme = $theme;
        $data['theme'] = $theme;
        parent::__construct($componentPath, $data);
    }

    protected function getComponentPath(): string
    {
        return __DIR__ . "/views/{$this->theme}/{$this->componentPath}.php";
    }

    public function renderWithWrapper(): string
    {
        $content = $this->render();
        return "<div class='component-wrapper'>{$content}</div>";
    }
}
```

### 2. Sistema de Plugins

```php
interface ComponentPlugin
{
    public function beforeRender(string $componentPath, array $data): array;
    public function afterRender(string $html, string $componentPath): string;
}

class ComponentManagerWithPlugins extends ComponentManager
{
    private array $plugins = [];

    public function addPlugin(ComponentPlugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function render(): string
    {
        // Aplicar plugins beforeRender
        foreach ($this->plugins as $plugin) {
            $this->data = $plugin->beforeRender($this->componentPath, $this->data);
        }

        $html = parent::render();

        // Aplicar plugins afterRender
        foreach ($this->plugins as $plugin) {
            $html = $plugin->afterRender($html, $this->componentPath);
        }

        return $html;
    }
}
```

---

## 📝 Notas Importantes

### Seguridad

- **Siempre sanitiza** la salida con `htmlspecialchars()`
- **Valida datos** antes de pasarlos a componentes
- **Usa nombres seguros** para archivos de componentes

### Mantenimiento

- **Documenta** cada componente con su propósito y parámetros
- **Mantén consistencia** en la estructura de carpetas
- **Versiona** los componentes cuando hagas cambios breaking

### Rendimiento

- **Evita componentes demasiado complejos**
- **Usa caché** para componentes estáticos
- **Optimiza consultas** a base de datos en componentes

---

## 🆘 Soporte

### Recursos Útiles

- [Documentación MVC-WEB](README.md)
- [Ejemplos de Componentes](../app/components/views/)
- [Guía de Buenas Prácticas](#buenas-prácticas)

### Contacto

Para soporte técnico o preguntas sobre el ComponentManager, consulta la documentación o crea un issue en el repositorio.

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 7.4+, MVC-WEB Framework
**Última Actualización:** Enero 5, 2026

---

**Documentación mantenida con ❤️ por el equipo MVC-WEB**
