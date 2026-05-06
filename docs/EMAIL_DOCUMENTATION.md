# 📧 Sistema de Email con PHPMailer - Documentación Completa

## 📋 Tabla de Contenidos

1. [Configuración Inicial](#configuración-inicial)
2. [Instalación de Dependencias](#instalación-de-dependencias)
3. [Configuración del Entorno](#configuración-del-entorno)
4. [Uso Básico](#uso-básico)
5. [Métodos Disponibles](#métodos-disponibles)
6. [Plantillas de Email](#plantillas-de-email)
7. [Ejemplos Prácticos](#ejemplos-prácticos)
8. [Manejo de Errores](#manejo-de-errores)
9. [Configuración Avanzada](#configuración-avanzada)
10. [Troubleshooting](#troubleshooting)

---

## 🚀 Configuración Inicial

### Requisitos Previos

- PHP 7.4 o superior
- Composer instalado
- Servidor SMTP configurado (Gmail, Outlook, etc.)

### Estructura de Archivos

```
app/
├── models/
│   └── email.php              # Clase principal de email
└── views/
    └── emails/                # Plantillas de email
        ├── bienvenida.php
        ├── recuperacion_password.php
        └── notificacion.php

config/
├── Environment.php            # Gestión de variables de entorno
└── app.php                   # Configuración principal

.env                          # Variables de entorno (crear desde env.ejemplo)
```

## ⚙️ Configuración del Entorno

### 1. Crear Archivo .env

```bash
cp env.ejemplo .env
```

### 2. Configurar Variables de Email

Edita el archivo `.env` con tus credenciales:

```env
# Configuración de Email
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_password_de_aplicacion
MAIL_ENCRYPTION=tls

# Configuración de la Aplicación
APP_NAME="Mi Aplicación Web"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost3000
```

### 3. Configuración para Gmail

Para usar Gmail, necesitas:

1. **Habilitar 2FA** en tu cuenta de Google
2. **Generar contraseña de aplicación**:

   - Ve a Configuración de Google
   - Seguridad
   - Verificación en 2 pasos
   - Contraseñas de aplicación
   - Genera una nueva contraseña

3. **Usar la contraseña generada** en `MAIL_PASSWORD`

---

## 🎯 Uso Básico

### 1. Instanciar la Clase

```php
<?php
use services\Email;

// Crear instancia
$email = new email();
```

### 2. Enviar Email Simple

```php
try {
    $email = new email();
    $resultado = $email->enviar(
        'destinatario@ejemplo.com',
        'Asunto del Email',
        'Contenido del mensaje'
    );

    if ($resultado) {
        echo "Email enviado correctamente";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 3. Enviar Email HTML

```php
$html = '<h1>Hola</h1><p>Este es un <strong>email HTML</strong>.</p>';
$email->enviar('destinatario@ejemplo.com', 'Email HTML', $html, true);
```

---

## 📚 Métodos Disponibles

### Métodos Principales

#### `enviar($para, $asunto, $mensaje, $html = true)`

Envía un email simple.

**Parámetros:**

- `$para`: Email del destinatario
- `$asunto`: Asunto del email
- `$mensaje`: Contenido del mensaje
- `$html`: Si es HTML (true) o texto plano (false)

**Retorna:** `bool` - true si se envió correctamente

#### `enviarConPlantilla($para, $asunto, $plantilla, $datos = [])`

Envía un email usando una plantilla HTML.

**Parámetros:**

- `$para`: Email del destinatario
- `$asunto`: Asunto del email
- `$plantilla`: Nombre de la plantilla (sin .php)
- `$datos`: Array con variables para la plantilla

#### `enviarConAdjuntos($para, $asunto, $mensaje, $adjuntos = [], $html = true)`

Envía un email con archivos adjuntos.

**Parámetros:**

- `$adjuntos`: Array con rutas de archivos a adjuntar

#### `enviarMultiple($destinatarios, $asunto, $mensaje, $html = true)`

Envía un email a múltiples destinatarios.

**Parámetros:**

- `$destinatarios`: Array con emails de destinatarios

### Métodos Especializados

#### `enviarBienvenida($email, $nombre)`

Envía email de bienvenida usando la plantilla `bienvenida.php`.

#### `enviarRecuperacionPassword($email, $token, $nombre = '')`

Envía email de recuperación de contraseña usando la plantilla `recuperacion_password.php`.

#### `enviarNotificacion($email, $titulo, $mensaje, $tipo = 'info')`

Envía email de notificación usando la plantilla `notificacion.php`.

**Tipos disponibles:**

- `info`: Azul
- `success`: Verde
- `warning`: Amarillo
- `error`: Rojo

### Métodos de Utilidad

#### `verificarConfiguracion()`

Verifica que todas las variables de entorno estén configuradas.

**Retorna:** Array con errores encontrados

#### `getConfiguracion()`

Obtiene la configuración actual (sin contraseña).

---

## 🎨 Plantillas de Email

### Estructura de Plantillas

Las plantillas se encuentran en `app/views/emails/` y usan PHP para variables dinámicas.

### Variables Disponibles

Todas las plantillas tienen acceso a:

- `$app_name`: Nombre de la aplicación
- `$app_url`: URL de la aplicación
- Variables específicas según el tipo de email

### Plantillas Incluidas

#### 1. `bienvenida.php`

Email de bienvenida para nuevos usuarios.

**Variables:**

- `$nombre`: Nombre del usuario

#### 2. `recuperacion_password.php`

Email para recuperación de contraseña.

**Variables:**

- `$nombre`: Nombre del usuario (opcional)
- `$token`: Token de recuperación

#### 3. `notificacion.php`

Email de notificaciones generales.

**Variables:**

- `$titulo`: Título de la notificación
- `$mensaje`: Mensaje de la notificación
- `$tipo`: Tipo de notificación (info, success, warning, error)

### Crear Nueva Plantilla

1. Crea un archivo PHP en `app/views/emails/`
2. Usa HTML con estilos CSS inline
3. Incluye las variables PHP necesarias
4. Llama al método correspondiente

---

## 💡 Ejemplos Prácticos

### 1. Email de Bienvenida

```php
<?php
use services\Email;

try {
    $email = new email();
    $email->enviarBienvenida('usuario@ejemplo.com', 'Juan Pérez');
    echo "Email de bienvenida enviado";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### 2. Recuperación de Contraseña

```php
<?php
use services\Email;

$email = new email();
$email->enviarRecuperacionPassword($usuario, $token, $nombre);
```

### 3. Notificación Personalizada

```php
<?php
use services\Email;

$email = new email();
$email->enviarNotificacion(
    'admin@ejemplo.com',
    'Nuevo Usuario Registrado',
    'Se ha registrado un nuevo usuario en el sistema.',
    'success'
);
```

### 4. Email con Adjuntos

```php
<?php
use services\Email;

$adjuntos = [
    '/ruta/al/archivo.pdf',
    '/ruta/a/otro/archivo.jpg'
];

$email = new email();
$email->enviarConAdjuntos(
    'destinatario@ejemplo.com',
    'Documentos Adjuntos',
    'Aquí tienes los documentos solicitados.',
    $adjuntos
);
```

### 5. Email Múltiple

```php
<?php
use services\Email;

$destinatarios = [
    'usuario1@ejemplo.com',
    'usuario2@ejemplo.com',
    'usuario3@ejemplo.com'
];

$email = new email();
$email->enviarMultiple(
    $destinatarios,
    'Anuncio Importante',
    'Tenemos un anuncio importante para todos los usuarios.'
);
```

---

## 🔧 Manejo de Errores

### Estructura Try-Catch

```php
try {
    $email = new email();
    $resultado = $email->enviar('destinatario@ejemplo.com', 'Asunto', 'Mensaje');
} catch (Exception $e) {
    // Log del error
    error_log("Error de email: " . $e->getMessage());

    // Mostrar mensaje al usuario
    echo "No se pudo enviar el email. Inténtalo más tarde.";
}
```

### Verificar Configuración

```php
$email = new email();
$errores = $email->verificarConfiguracion();

if (!empty($errores)) {
    foreach ($errores as $error) {
        echo "Error de configuración: $error\n";
    }
}
```

### Errores Comunes

1. **"SMTP connect() failed"**

   - Verificar credenciales SMTP
   - Comprobar puerto y encriptación
   - Verificar firewall

2. **"Authentication failed"**

   - Usar contraseña de aplicación (Gmail)
   - Verificar usuario y contraseña

3. **"Could not instantiate mail function"**
   - Verificar extensión PHP mail
   - Comprobar configuración del servidor

---

## ⚙️ Configuración Avanzada

### Configuración de Debug

En modo desarrollo, puedes habilitar debug SMTP:

```php
// En el constructor de la clase email
if (Environment::getBool('APP_DEBUG', false)) {
    $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
}
```

### Configuración de Timeout

```php
// En el método inicializarMailer()
$this->mailer->Timeout = 30; // 30 segundos
$this->mailer->SMTPKeepAlive = true;
```

### Configuración de Caracteres

```php
$this->mailer->CharSet = 'UTF-8';
$this->mailer->Encoding = 'base64';
```

### Configuración de Seguridad

```php
$this->mailer->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
```

---

## 🔍 Troubleshooting

### Problemas Comunes

#### 1. Email no se envía

- Verificar configuración SMTP
- Comprobar credenciales
- Revisar logs del servidor

#### 2. Email llega a spam

- Configurar SPF, DKIM, DMARC
- Usar servidor SMTP confiable
- Evitar palabras spam en asunto/contenido

#### 3. Caracteres especiales mal mostrados

- Verificar encoding UTF-8
- Usar `htmlspecialchars()` en contenido
- Configurar charset correcto

#### 4. Plantilla no se carga

- Verificar ruta de plantilla
- Comprobar permisos de archivo
- Revisar sintaxis PHP en plantilla

### Logs y Debug

```php
// Habilitar debug completo
$this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
$this->mailer->Debugoutput = 'error_log';
```

### Verificación de Configuración

```php
$email = new email();
$config = $email->getConfiguracion();
print_r($config);
```

---

## 📝 Notas Importantes

### Seguridad

- Nunca incluyas contraseñas en el código
- Usa variables de entorno para credenciales
- Valida emails de entrada
- Sanitiza contenido HTML

### Rendimiento

- Usa colas para emails masivos
- Implementa rate limiting
- Considera servicios de email transaccional

### Mantenimiento

- Actualiza PHPMailer regularmente
- Monitorea logs de email
- Verifica configuración periódicamente

---

## 🆘 Soporte

### Recursos Útiles

- [Documentación PHPMailer](https://github.com/PHPMailer/PHPMailer)
- [Configuración Gmail SMTP](https://support.google.com/mail/answer/7126229)
- [Configuración Outlook SMTP](https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-8361e398-8af4-4e97-b147-6c6c4ac95353)

### Contacto

Para soporte técnico o preguntas sobre esta implementación, consulta la documentación o crea un issue en el repositorio.

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 7.4+, PHPMailer 6.8+
