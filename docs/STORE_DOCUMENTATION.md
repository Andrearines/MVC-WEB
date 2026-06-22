# 🗃️ Store — Manejo de Estado Global (Vanilla JS)

Documentación del sistema de estado reactivo incluido en MVC-WEB desde la versión **8.1.0**. Permite compartir datos entre scripts y páginas sin necesidad de frameworks como React o Vue.

---

## 📋 Índice

- [¿Qué es el Store?](#-qué-es-el-store)
- [Arquitectura](#-arquitectura)
- [Clase Base: Store](#-clase-base-store)
- [ThemeStore — Control de Temas](#-themestore--control-de-temas)
- [CartStore — Carrito de Compras](#-cartstore--carrito-de-compras)
- [Dark Mode — Estilos SCSS](#-dark-mode--estilos-scss)
- [Integración en el Layout PHP](#-integración-en-el-layout-php)
- [Flujo Completo](#-flujo-completo)

---

## 🧠 ¿Qué es el Store?

El **Store** es un contenedor de datos centralizado que:

1. **Guarda** el estado de la aplicación en memoria.
2. **Persiste** automáticamente ese estado en `localStorage` para que sobreviva al cambiar de página.
3. **Notifica** a todos los scripts suscritos cuando los datos cambian.

> Es el equivalente en Vanilla JS al `Context` de React, `Vuex` de Vue, o el `StateNotifierProvider` de Riverpod en Flutter.

---

## 🏗️ Arquitectura

```
src/core/provider/
└── Store.js               ← Clase base genérica y reutilizable

src/core/theme/
├── ThemeStore.js          ← Store específico del tema (Claro/Oscuro)
└── DarkMode/
    └── index.scss         ← Estilos SCSS del modo oscuro

src/core/js/theme/
└── darkmode.js            ← Suscriptor que conecta el Store con el DOM
```

---

## ⚙️ Clase Base: Store

**Archivo**: [`src/core/provider/Store.js`](../src/core/provider/Store.js)

```javascript
class Store {
    constructor(initialState = {}, storageKey = null)
    getState()
    setState(newState)
    subscribe(listener)
}
```

### Constructor

| Parámetro      | Tipo     | Descripción                                              |
| -------------- | -------- | -------------------------------------------------------- |
| `initialState` | `Object` | Estado inicial si no hay datos guardados en localStorage |
| `storageKey`   | `string` | Clave de localStorage para persistir el estado           |

### Métodos

#### `getState()`
Retorna el estado actual (sólo lectura).
```javascript
const estado = window.ThemeStore.getState();
console.log(estado.theme); // 'light' o 'dark'
```

#### `setState(newState)`
Actualiza el estado fusionando los nuevos valores con los existentes, guarda en localStorage y notifica a todos los suscriptores.
```javascript
window.ThemeStore.setState({ theme: 'dark' });
```

#### `subscribe(listener)`
Registra una función que se ejecutará automáticamente cada vez que cambie el estado. Retorna una función para cancelar la suscripción.
```javascript
const cancelar = window.ThemeStore.subscribe((state) => {
    console.log('Nuevo tema:', state.theme);
});

// Para dejar de escuchar:
cancelar();
```

---

## 🎨 ThemeStore — Control de Temas

**Archivo**: [`src/core/theme/ThemeStore.js`](../src/core/theme/ThemeStore.js)

Gestiona el tema visual (Claro/Oscuro) de toda la aplicación, con persistencia automática entre páginas.

### Estado

```javascript
{
    theme: 'light' // 'light' | 'dark'
}
```

### Acciones disponibles en `window.ThemeActions`

| Acción                        | Descripción                              |
| ----------------------------- | ---------------------------------------- |
| `ThemeActions.setTheme(theme)` | Cambia al tema especificado (`'light'` o `'dark'`) |
| `ThemeActions.toggleTheme()`   | Alterna entre claro y oscuro             |

### Ejemplo de uso

```javascript
// Cambiar a modo oscuro
window.ThemeActions.setTheme('dark');

// Alternar entre claro y oscuro
window.ThemeActions.toggleTheme();

// Leer el tema actual
const { theme } = window.ThemeStore.getState();
```

### Cómo suscribirse al tema en cualquier script

```javascript
window.ThemeStore.subscribe((state) => {
    if (state.theme === 'dark') {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }
});

// Sincronizar al cargar la página
const { theme } = window.ThemeStore.getState();
if (theme === 'dark') {
    document.body.classList.add('dark');
}
```

---

## 🛒 CartStore — Carrito de Compras

**Archivo**: [`src/core/provider/CartStore.js`](../src/core/provider/CartStore.js)

Gestiona el estado del carrito de compras persistido entre páginas.

### Estado

```javascript
{
    cart: [],       // Array de items: { id, nombre, precio, cantidad }
    cartOpen: false // Si el modal del carrito está visible
}
```

### Acciones disponibles en `window.CartActions`

| Acción                             | Descripción                                          |
| ---------------------------------- | ---------------------------------------------------- |
| `CartActions.agregarProducto(producto)` | Agrega un artículo o incrementa su cantidad si ya existe |
| `CartActions.quitarProducto(id)`    | Resta la cantidad de un artículo, elimínalo si llega a 0 |
| `CartActions.limpiarCarrito()`      | Vacía todo el carrito                                |
| `CartActions.toggleCarrito()`       | Muestra u oculta el modal del carrito                |

### Ejemplo de uso

```javascript
// Agregar un producto
window.CartActions.agregarProducto({
    id: 1,
    nombre: 'Pizza Margherita',
    precio: 12.99
});

// Escuchar cambios del carrito
window.CartStore.subscribe((state) => {
    const total = state.cart.reduce((acc, item) => acc + item.cantidad, 0);
    document.querySelector('#cart-counter').textContent = total;
});
```

---

## 🌙 Dark Mode — Estilos SCSS

**Archivo**: [`src/core/theme/DarkMode/index.scss`](../src/core/theme/DarkMode/index.scss)

Los estilos se activan añadiendo la clase `.dark` al elemento `<body>`. Utiliza **variables CSS** y diferenciación de tonos de fondo en lugar de bordes para separar los elementos.

### Paleta de colores

| Variable CSS        | Valor       | Uso                                      |
| ------------------- | ----------- | ---------------------------------------- |
| `--bg-primaryD`     | `#0a0c10`   | Fondo principal de la página             |
| `--bg-secondaryD`   | `#141822`   | Tarjetas, secciones, header, footer      |
| `--bg-tertiaryD`    | `#1e2330`   | Inputs, botones en reposo                |
| `--bg-hoverD`       | `#272f40`   | Hover de elementos interactivos          |
| `--text-primaryD`   | `#f3f4f6`   | Texto principal (casi blanco)            |
| `--text-secondaryD` | `#a1a8b9`   | Texto secundario, subtítulos             |
| `--text-mutedD`     | `#626a7f`   | Texto deshabilitado, placeholders        |
| `--accentD`         | `#6366f1`   | Color de acento — Botones, enlaces       |
| `--accent-hoverD`   | `#4f46e5`   | Acento en hover                          |

### Logo en modo oscuro

Los logotipos en SVG inline cambian de color usando la propiedad `fill` del CSS en `body.dark`:

```scss
body.dark .logo-sm path {
    fill: #ffffff; // Blanco en modo oscuro
}
```

> [!IMPORTANT]
> Para que funcione el cambio de color del logo, los elementos `<path>` del SVG en el HTML deben tener el atributo `fill` eliminado o usar `fill="currentColor"`. Si tienen `fill="white"` hardcodeado en el HTML, el CSS no puede sobreescribirlo sin `!important`.

---

## 🔗 Integración en el Layout PHP

**Archivo**: [`app/views/layouts/layout.php`](../app/views/layouts/layout.php)

Para usar el Store y el modo oscuro en todas las páginas, los scripts se cargan en el layout base en este orden específico:

```html
<!-- 1. Primero: la clase base Store (debe cargarse antes que cualquier Store específico) -->
<script src="/build/js/core/provider/Store.js"></script>

<!-- 2. Scripts específicos por página (ej. ThemeStore) -->
<?php foreach ($script as $s): ?>
    <script src="build/js/<?= $s ?>.js"></script>
<?php endforeach; ?>

<!-- 3. El suscriptor que conecta el Store con el DOM -->
<script src="/build/js/core/theme/ThemeStore.js"></script>
<script src="/build/js/core/theme/darkmode.js"></script>
```

### Registrar scripts por página en el Controlador

```php
// app/controllers/PagesController.php
public static function indexView(Router $router)
{
    $router->view('home/index.php', [
        'titulo' => 'Home',
        'inicio' => true,
        'script'  => [
            'core/theme/ThemeStore',   // Store de temas
            'core/js/theme/darkmode',  // Suscriptor del modo oscuro
        ]
    ]);
}
```

---

## 🔄 Flujo Completo

El siguiente diagrama muestra el ciclo de vida completo desde que el usuario presiona el botón hasta que la pantalla cambia:

```
[Usuario presiona el botón de Modo Oscuro]
        │
        ▼
[darkmode.js detecta el clic]
        │
        ▼
[Llama a window.ThemeActions.setTheme('dark')]
        │
        ▼
[ThemeStore.setState({ theme: 'dark' })]
        │
        ├──► Guarda en localStorage['app_theme']
        │
        └──► Notifica a todos los suscriptores
                  │
                  ▼
        [darkmode.js recibe la notificación]
                  │
                  ▼
        [body.classList.add('dark')]
                  │
                  ▼
        [CSS de DarkMode/index.scss entra en acción]
                  │
                  ▼
        [🌙 La UI cambia a modo oscuro]

--- El usuario navega a otra página ---

[Nueva página carga]
        │
        ▼
[ThemeStore lee localStorage['app_theme']]
        │
        ▼
[Estado inicial: { theme: 'dark' }]
        │
        ▼
[darkmode.js se suscribe y llama actualizarInterfaz('dark')]
        │
        ▼
[🌙 La página nueva también está en modo oscuro]
```

---

## ✅ Próximas Mejoras Planificadas

- [ ] Soporte para más temas (sepia, alto contraste)
- [ ] Sincronización con la preferencia del sistema (`prefers-color-scheme`)
- [ ] `UserStore` para persistir datos del usuario logueado entre páginas

---

**Versión**: 8.1.0  
**Fecha**: Junio 2026
