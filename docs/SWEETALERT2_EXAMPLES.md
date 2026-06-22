# SweetAlert2 - Ejemplos Prácticos para MVC

## 🚀 Ejemplos de Uso Inmediato

### 1. Notificaciones Básicas

```javascript
// Éxito
showSuccess('¡Perfecto!', 'Los datos se guardaron correctamente');

// Error
showError('Error', 'No se pudo completar la operación');

// Advertencia
showWarning('Atención', 'Algunos campos están incompletos');

// Información
showInfo('Información', 'Tu sesión expirará en 5 minutos');
```

### 2. Confirmaciones

```javascript
// Confirmación simple
confirm('¿Continuar?', '¿Estás seguro de que quieres proceder?')
.then((result) => {
    if (result.isConfirmed) {
        // Acción de confirmación
        console.log('Usuario confirmó');
    }
});

// Confirmación de eliminación
confirmDelete('usuario', () => {
    // Lógica de eliminación
    eliminarUsuario(id);
});
```

### 3. Formularios Dinámicos

```javascript
// Formulario de usuario
const campos = [
    { id: 'nombre', nombre: 'Nombre', placeholder: 'Ingresa tu nombre', requerido: true },
    { id: 'email', nombre: 'Email', tipo: 'email', placeholder: 'tu@email.com', requerido: true },
    { id: 'telefono', nombre: 'Teléfono', tipo: 'tel', placeholder: '123-456-7890' },
    { id: 'comentarios', nombre: 'Comentarios', tipo: 'textarea', placeholder: 'Escribe aquí...' }
];

showForm('Crear Usuario', campos, (datos) => {
    console.log('Datos del formulario:', datos);
    guardarUsuario(datos);
});
```

### 4. Integración con AJAX

```javascript
// Función para enviar formulario
function enviarFormulario(formData) {
    showLoading('Enviando datos...');
    
    fetch('/api/usuarios/guardar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        Swal.close(); // Cerrar loader
        
        if (data.success) {
            showSuccess('¡Éxito!', data.message, () => {
                window.location.href = '/usuarios';
            });
        } else {
            showError('Error', data.message);
        }
    })
    .catch(error => {
        Swal.close();
        SweetAlertConfig.utils.handleError(error, 'Error de Conexión');
    });
}
```

### 5. Validaciones de Formulario

```javascript
// Validar formulario antes de enviar
function validarYEnviar() {
    const campos = [
        { id: 'nombre', nombre: 'Nombre', requerido: true, minLength: 3 },
        { id: 'email', nombre: 'Email', tipo: 'email', requerido: true },
        { id: 'password', nombre: 'Contraseña', requerido: true, minLength: 6 }
    ];
    
    if (SweetAlertConfig.utils.validateForm(campos)) {
        // Enviar formulario
        enviarFormulario();
    }
}
```

### 6. Notificaciones Toast

```javascript
// Notificación rápida
notify('Actualizado', 'Los datos se actualizaron', 'success', 2000);

// Notificación de error
notify('Error', 'No se pudo conectar', 'error', 4000);
```

---

## 🔧 Ejemplos Avanzados

### 1. CRUD Completo con SweetAlert2

```javascript
// Clase para manejar CRUD con alertas
class CRUDManager {
    constructor(endpoint) {
        this.endpoint = endpoint;
    }
    
    // Crear
    crear(datos) {
        showLoading('Creando registro...');
        
        return fetch(`${this.endpoint}/crear`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                showSuccess('Creado', 'Registro creado exitosamente');
                return data;
            } else {
                showError('Error', data.message);
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.close();
            SweetAlertConfig.utils.handleError(error);
        });
    }
    
    // Actualizar
    actualizar(id, datos) {
        showLoading('Actualizando...');
        
        return fetch(`${this.endpoint}/actualizar/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                showSuccess('Actualizado', 'Registro actualizado correctamente');
                return data;
            } else {
                showError('Error', data.message);
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.close();
            SweetAlertConfig.utils.handleError(error);
        });
    }
    
    // Eliminar
    eliminar(id, tipo = 'registro') {
        return confirmDelete(tipo, () => {
            showLoading('Eliminando...');
            
            fetch(`${this.endpoint}/eliminar/${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    showSuccess('Eliminado', 'Registro eliminado correctamente', () => {
                        location.reload();
                    });
                } else {
                    showError('Error', data.message);
                }
            })
            .catch(error => {
                Swal.close();
                SweetAlertConfig.utils.handleError(error);
            });
        });
    }
    
    // Obtener
    obtener(id) {
        showLoading('Cargando...');
        
        return fetch(`${this.endpoint}/obtener/${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                return data.data;
            } else {
                showError('Error', data.message);
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.close();
            SweetAlertConfig.utils.handleError(error);
        });
    }
}

// Uso
const usuarioManager = new CRUDManager('/api/usuarios');

// Crear usuario
usuarioManager.crear({ nombre: 'Juan', email: 'juan@email.com' });

// Eliminar usuario
usuarioManager.eliminar(123, 'usuario');
```

### 2. Formulario de Login con Validaciones

```javascript
function mostrarLogin() {
    const campos = [
        { id: 'email', nombre: 'Email', tipo: 'email', placeholder: 'tu@email.com', requerido: true },
        { id: 'password', nombre: 'Contraseña', tipo: 'password', placeholder: 'Tu contraseña', requerido: true }
    ];
    
    showForm('Iniciar Sesión', campos, (datos) => {
        // Validar email
        if (!SweetAlertConfig.utils.isValidEmail(datos.email)) {
            showError('Email Inválido', 'Por favor ingresa un email válido');
            return;
        }
        
        // Enviar datos de login
        login(datos);
    });
}

function login(datos) {
    showLoading('Iniciando sesión...');
    
    fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            showSuccess('¡Bienvenido!', 'Sesión iniciada correctamente', () => {
                window.location.href = '/dashboard';
            });
        } else {
            showError('Error de Login', data.message);
        }
    })
    .catch(error => {
        Swal.close();
        SweetAlertConfig.utils.handleError(error, 'Error de Conexión');
    });
}
```

### 3. Upload de Archivos con Progress

```javascript
function subirArchivo(archivo) {
    const formData = new FormData();
    formData.append('archivo', archivo);
    
    // Mostrar progreso
    Swal.fire({
        title: 'Subiendo archivo...',
        html: `
            <div class="progress">
                <div class="progress-bar" id="progress-bar" style="width: 0%"></div>
            </div>
            <p id="progress-text">0%</p>
        `,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            document.getElementById('progress-bar').style.width = percentComplete + '%';
            document.getElementById('progress-text').textContent = Math.round(percentComplete) + '%';
        }
    });
    
    xhr.addEventListener('load', () => {
        const response = JSON.parse(xhr.responseText);
        Swal.close();
        
        if (response.success) {
            showSuccess('¡Archivo subido!', 'El archivo se subió correctamente');
        } else {
            showError('Error', response.message);
        }
    });
    
    xhr.addEventListener('error', () => {
        Swal.close();
        showError('Error', 'No se pudo subir el archivo');
    });
    
    xhr.open('POST', '/api/archivos/subir');
    xhr.send(formData);
}
```

### 4. Sistema de Notificaciones en Tiempo Real

```javascript
// Sistema de notificaciones
class NotificationSystem {
    constructor() {
        this.notifications = [];
    }
    
    // Agregar notificación
    add(message, type = 'info', duration = 5000) {
        const notification = {
            id: Date.now(),
            message,
            type,
            duration
        };
        
        this.notifications.push(notification);
        this.show(notification);
    }
    
    // Mostrar notificación
    show(notification) {
        notify(notification.message, '', notification.type, notification.duration);
        
        // Remover después del tiempo especificado
        setTimeout(() => {
            this.remove(notification.id);
        }, notification.duration);
    }
    
    // Remover notificación
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
    
    // Notificaciones predefinidas
    success(message) {
        this.add(message, 'success', 3000);
    }
    
    error(message) {
        this.add(message, 'error', 5000);
    }
    
    warning(message) {
        this.add(message, 'warning', 4000);
    }
    
    info(message) {
        this.add(message, 'info', 3000);
    }
}

// Uso
const notifications = new NotificationSystem();

// En diferentes partes de tu aplicación
notifications.success('Usuario creado exitosamente');
notifications.error('Error al conectar con el servidor');
notifications.warning('Tu sesión expirará pronto');
notifications.info('Nuevo mensaje recibido');
```

---

## 🎨 Personalización Avanzada

### 1. Tema Personalizado

```javascript
// Aplicar tema personalizado
const temaPersonalizado = {
    background: '#2d3748',
    color: '#ffffff',
    confirmButtonColor: '#4299e1',
    cancelButtonColor: '#e53e3e',
    popup: {
        background: '#2d3748',
        color: '#ffffff'
    }
};

// Aplicar tema
Swal.fire({
    title: 'Tema Personalizado',
    text: 'Esta alerta usa un tema personalizado',
    background: temaPersonalizado.background,
    color: temaPersonalizado.color,
    confirmButtonColor: temaPersonalizado.confirmButtonColor
});
```

### 2. Animaciones Personalizadas

```javascript
// Alerta con animación personalizada
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

## 📱 Uso en Diferentes Contextos

### 1. En Formularios HTML

```html
<form id="miFormulario" onsubmit="validarFormulario(event)">
    <input type="text" id="nombre" placeholder="Nombre" required>
    <input type="email" id="email" placeholder="Email" required>
    <button type="submit">Enviar</button>
</form>

<script>
function validarFormulario(event) {
    event.preventDefault();
    
    const campos = [
        { id: 'nombre', nombre: 'Nombre', requerido: true },
        { id: 'email', nombre: 'Email', tipo: 'email', requerido: true }
    ];
    
    if (SweetAlertConfig.utils.validateForm(campos)) {
        // Enviar formulario
        enviarFormulario();
    }
}
</script>
```

### 2. En Botones de Acción

```html
<button onclick="confirmarAccion()">Eliminar Usuario</button>

<script>
function confirmarAccion() {
    confirmDelete('usuario', () => {
        // Lógica de eliminación
        eliminarUsuario(123);
    });
}
</script>
```

### 3. En Eventos de Página

```javascript
// Mostrar mensaje al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    if (nuevoUsuario) {
        showSuccess('¡Bienvenido!', 'Tu cuenta se creó exitosamente');
    }
});

// Mostrar mensaje antes de salir
window.addEventListener('beforeunload', (e) => {
    if (formularioModificado) {
        e.preventDefault();
        e.returnValue = '';
    }
});
```

---

**Nota**: Todos estos ejemplos están optimizados para tu proyecto MVC y utilizan la configuración centralizada que creamos en `sweetalert-config.js`. 