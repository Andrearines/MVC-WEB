# SweetAlert2 Documentation - MVC Web Project

## 📋 Índice
- [Introducción](#introducción)
- [Instalación](#instalación)
- [Configuración Básica](#configuración-básica)
- [Tipos de Alertas](#tipos-de-alertas)
- [Ejemplos Prácticos](#ejemplos-prácticos)
- [Integración con PHP](#integración-con-php)
- [Mejores Prácticas](#mejores-prácticas)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Introducción

SweetAlert2 es una biblioteca JavaScript moderna que reemplaza las alertas nativas del navegador con alertas hermosas, personalizables y responsivas. En este proyecto MVC, se utiliza para mejorar la experiencia del usuario con notificaciones elegantes.

### Características Principales
- ✅ **Responsive**: Se adapta a cualquier dispositivo
- ✅ **Personalizable**: Temas y estilos personalizables
- ✅ **Sin dependencias**: No requiere jQuery
- ✅ **Promesas**: Soporte nativo para async/await
- ✅ **Animaciones**: Transiciones suaves y profesionales

---

## 📦 Instalación

### CDN (Ya implementado en tu proyecto)
```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### NPM (Alternativa)
```bash
npm install sweetalert2
```

---

## ⚙️ Configuración Básica

### Configuración Global
```javascript
// Configuración global de SweetAlert2
Swal.mixin({
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
    reverseButtons: true,
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
});
```

### Configuración por Defecto
```javascript
// Establecer configuración por defecto
Swal.setDefaults({
    allowOutsideClick: false,
    allowEscapeKey: false,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33'
});
```

---

## 🎨 Tipos de Alertas

### 1. Alerta Básica
```javascript
// Alerta simple
Swal.fire('¡Hola!', 'Este es un mensaje de ejemplo', 'info');

// Con icono personalizado
Swal.fire({
    title: '¡Éxito!',
    text: 'Operación completada correctamente',
    icon: 'success',
    confirmButtonText: 'Entendido'
});
```

### 2. Alerta de Confirmación
```javascript
// Confirmación básica
Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción no se puede deshacer",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
}).then((result) => {
    if (result.isConfirmed) {
        // Acción de confirmación
        eliminarElemento();
    }
});
```

### 3. Alerta con Input
```javascript
// Alerta con campo de texto
Swal.fire({
    title: 'Ingresa tu nombre',
    input: 'text',
    inputPlaceholder: 'Escribe tu nombre aquí...',
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    inputValidator: (value) => {
        if (!value) {
            return 'Debes escribir algo!'
        }
    }
}).then((result) => {
    if (result.isConfirmed) {
        console.log('Nombre ingresado:', result.value);
    }
});
```

### 4. Alerta de Carga
```javascript
// Mostrar loader
Swal.fire({
    title: 'Procesando...',
    text: 'Por favor espera',
    allowOutsideClick: false,
    didOpen: () => {
        Swal.showLoading();
    }
});

// Ocultar loader
Swal.close();
```

---

## 🔧 Ejemplos Prácticos para tu Proyecto MVC

### 1. Eliminación de Registros
```javascript
function confirmarEliminacion(id, tipo) {
    Swal.fire({
        title: '¿Eliminar registro?',
        text: `¿Estás seguro de que quieres eliminar este ${tipo}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Realizar petición AJAX
            fetch(`/api/${tipo}/eliminar/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Eliminado!',
                        'El registro ha sido eliminado correctamente.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error',
                        'No se pudo eliminar el registro.',
                        'error'
                    );
                }
            });
        }
    });
}
```

### 2. Formularios Dinámicos
```javascript
function mostrarFormulario(tipo, datos = {}) {
    Swal.fire({
        title: tipo === 'crear' ? 'Crear Nuevo' : 'Editar',
        html: `
            <form id="formulario">
                <input id="nombre" class="swal2-input" placeholder="Nombre" value="${datos.nombre || ''}">
                <input id="email" class="swal2-input" placeholder="Email" value="${datos.email || ''}">
                <textarea id="descripcion" class="swal2-textarea" placeholder="Descripción">${datos.descripcion || ''}</textarea>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nombre = document.getElementById('nombre').value;
            const email = document.getElementById('email').value;
            const descripcion = document.getElementById('descripcion').value;
            
            if (!nombre || !email) {
                Swal.showValidationMessage('Por favor completa todos los campos requeridos');
                return false;
            }
            
            return { nombre, email, descripcion };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarDatos(result.value, tipo);
        }
    });
}
```

### 3. Notificaciones de Estado
```javascript
// Función para mostrar notificaciones
function mostrarNotificacion(titulo, mensaje, tipo = 'info') {
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: tipo, // 'success', 'error', 'warning', 'info', 'question'
        timer: tipo === 'success' ? 2000 : 4000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Uso en diferentes situaciones
function procesarFormulario() {
    mostrarNotificacion('Procesando...', 'Enviando datos...', 'info');
    
    // Simular petición AJAX
    setTimeout(() => {
        mostrarNotificacion('¡Éxito!', 'Datos guardados correctamente', 'success');
    }, 2000);
}
```

---

## 🔗 Integración con PHP

### 1. Respuestas del Servidor
```php
// En tu controlador PHP
public function guardarUsuario() {
    try {
        // Lógica de guardado
        $usuario = new Usuario();
        $usuario->nombre = $_POST['nombre'];
        $usuario->email = $_POST['email'];
        $usuario->save();
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => 'Usuario guardado correctamente',
            'data' => $usuario
        ]);
        
    } catch (Exception $e) {
        // Respuesta de error
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar usuario: ' . $e->getMessage()
        ]);
    }
}
```

### 2. Manejo de Respuestas en JavaScript
```javascript
function enviarFormulario(formData) {
    fetch('/api/usuarios/guardar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Continuar'
            }).then(() => {
                // Redirigir o actualizar página
                window.location.href = '/usuarios';
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor',
            icon: 'error'
        });
    });
}
```

---

## 🎯 Mejores Prácticas

### 1. Configuración Centralizada
```javascript
// Crear archivo: public/build/js/sweetalert-config.js
const SweetAlertConfig = {
    // Configuración global
    defaults: {
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        allowOutsideClick: false,
        allowEscapeKey: false
    },
    
    // Colores del tema
    colors: {
        primary: '#3085d6',
        danger: '#d33',
        warning: '#f39c12',
        success: '#28a745'
    },
    
    // Funciones de utilidad
    showLoading: (texto = 'Cargando...') => {
        Swal.fire({
            title: texto,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },
    
    showSuccess: (titulo, mensaje) => {
        return Swal.fire({
            title: titulo,
            text: mensaje,
            icon: 'success',
            confirmButtonColor: SweetAlertConfig.colors.success
        });
    },
    
    showError: (titulo, mensaje) => {
        return Swal.fire({
            title: titulo,
            text: mensaje,
            icon: 'error',
            confirmButtonColor: SweetAlertConfig.colors.danger
        });
    }
};

// Aplicar configuración global
Swal.setDefaults(SweetAlertConfig.defaults);
```

### 2. Manejo de Errores
```javascript
// Función para manejar errores de forma consistente
function manejarError(error, titulo = 'Error') {
    console.error('Error:', error);
    
    let mensaje = 'Ha ocurrido un error inesperado';
    
    if (error.response) {
        // Error de respuesta del servidor
        mensaje = error.response.data?.message || mensaje;
    } else if (error.request) {
        // Error de conexión
        mensaje = 'No se pudo conectar con el servidor';
    } else {
        // Error de JavaScript
        mensaje = error.message || mensaje;
    }
    
    Swal.fire({
        title: titulo,
        text: mensaje,
        icon: 'error',
        confirmButtonColor: SweetAlertConfig.colors.danger
    });
}
```

### 3. Validaciones
```javascript
// Función para validar formularios
function validarFormulario(campos) {
    const errores = [];
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        const valor = elemento.value.trim();
        
        if (campo.requerido && !valor) {
            errores.push(`El campo ${campo.nombre} es requerido`);
        }
        
        if (campo.tipo === 'email' && valor && !validarEmail(valor)) {
            errores.push(`El email ${valor} no es válido`);
        }
        
        if (campo.minLength && valor.length < campo.minLength) {
            errores.push(`${campo.nombre} debe tener al menos ${campo.minLength} caracteres`);
        }
    });
    
    if (errores.length > 0) {
        Swal.fire({
            title: 'Errores de Validación',
            html: errores.map(error => `<p>• ${error}</p>`).join(''),
            icon: 'warning',
            confirmButtonColor: SweetAlertConfig.colors.warning
        });
        return false;
    }
    
    return true;
}
```

---

## 🔧 Troubleshooting

### Problemas Comunes

#### 1. SweetAlert2 no se carga
```html
<!-- Verificar que el script esté cargado -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Verificar en consola -->
<script>
console.log('SweetAlert2 cargado:', typeof Swal !== 'undefined');
</script>
```

#### 2. Alertas no aparecen
```javascript
// Verificar que no haya errores de JavaScript
try {
    Swal.fire('Test');
} catch (error) {
    console.error('Error con SweetAlert2:', error);
}
```

#### 3. Estilos no se aplican
```css
/* Asegurar que los estilos de SweetAlert2 se carguen */
.swal2-popup {
    font-family: inherit;
}
```

#### 4. Conflictos con otros scripts
```javascript
// Usar namespace para evitar conflictos
window.MiApp = window.MiApp || {};
MiApp.SweetAlert = Swal;
```

---

## 📚 Recursos Adicionales

- [Documentación Oficial](https://sweetalert2.github.io/)
- [Ejemplos Interactivos](https://sweetalert2.github.io/#examples)
- [GitHub Repository](https://github.com/sweetalert2/sweetalert2)

---

## 🎨 Personalización Avanzada

### Temas Personalizados
```javascript
// Tema oscuro
const temaOscuro = {
    background: '#2d3748',
    color: '#ffffff',
    confirmButtonColor: '#4299e1',
    cancelButtonColor: '#e53e3e'
};

// Aplicar tema
Swal.fire({
    title: 'Tema Oscuro',
    text: 'Esta alerta usa un tema personalizado',
    background: temaOscuro.background,
    color: temaOscuro.color,
    confirmButtonColor: temaOscuro.confirmButtonColor
});
```

### Animaciones Personalizadas
```javascript
Swal.fire({
    title: 'Animación Personalizada',
    showClass: {
        popup: 'animate__animated animate__bounceIn'
    },
    hideClass: {
        popup: 'animate__animated animate__bounceOut'
    }
});
```

---

**Nota**: Esta documentación está optimizada para tu proyecto MVC y incluye ejemplos prácticos que puedes implementar directamente en tu código. 