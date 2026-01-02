# 📁 FileManagerModel - Documentación Completa

## 📋 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Instalación y Dependencias](#instalación-y-dependencias)
4. [Métodos Disponibles](#métodos-disponibles)
5. [Procesamiento de Imágenes](#procesamiento-de-imágenes)
6. [Procesamiento de Archivos](#procesamiento-de-archivos)
7. [Seguridad](#seguridad)
8. [Ejemplos Prácticos](#ejemplos-prácticos)
9. [Manejo de Errores](#manejo-de-errores)
10. [Configuración Avanzada](#configuración-avanzada)

---

## 🎯 Descripción General

La clase `FileManagerModel` es un sistema completo para la gestión segura de archivos e imágenes en aplicaciones PHP. Proporciona funcionalidades para subir, procesar, validar y eliminar archivos con múltiples capas de seguridad y optimización.

### Características Principales

- ✅ **Procesamiento de imágenes** con Intervention Image
- ✅ **Redimensionamiento automático** (800x600px)
- ✅ **Validación de seguridad** contra webshells
- ✅ **Soporte múltiple** de formatos (JPEG, PNG, GIF, PDF, DOCX, etc.)
- ✅ **Nombres aleatorios** para evitar colisiones
- ✅ **Control de tamaño** configurable
- ✅ **Creación automática** de directorios
- ✅ **Eliminación segura** de archivos

---

## ⚙️ Instalación y Dependencias

### Dependencias Requeridas

```json
{
  "require": {
    "intervention/image": "^3.0"
  }
}
```

### Importaciones en la Clase

```php
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
```

### Estructura de Directorios

```
public/
├── imagenes/                   # Imágenes procesadas
│   ├── perfil/               # Imágenes de perfil
│   ├── productos/            # Imágenes de productos
│   └── galeria/              # Galería de imágenes
└── archivos/                  # Documentos genéricos
    ├── documentos/           # PDFs, DOCX, etc.
    ├── reportes/             # Reportes generados
    └── backups/             # Archivos de backup
```

---

## 📚 Métodos Disponibles

### Métodos de Procesamiento

#### `processImage($img, $carpeta, $tipo)`

Procesa imágenes con redimensionamiento automático y validaciones.

**Parámetros:**

- `$img`: Array `$_FILES` de la imagen
- `$carpeta`: Nombre de la carpeta de destino
- `$tipo`: Extensión del archivo (ej: '.jpg', '.png')

**Retorna:** Array con nombre del archivo o array de errores

**Validaciones:**

- Tamaño máximo: 3MB
- Dimensiones máximas: 2000x2000px
- Formatos permitidos: JPEG, JPG, PNG, GIF
- Redimensionamiento automático a: 800x600px

#### `processFile($file, $carpeta, $allowedExtensions = null, $maxBytes = 5MB)`

Procesa archivos genéricos con validaciones de seguridad.

**Parámetros:**

- `$file`: Array `$_FILES` del archivo
- `$carpeta`: Nombre de la carpeta de destino
- `$allowedExtensions`: Array de extensiones permitidas (opcional)
- `$maxBytes`: Tamaño máximo en bytes (default: 5MB)

**Extensiones permitidas por defecto:**

```php
['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'txt']
```

### Métodos de Eliminación

#### `deleteImage($carpeta, $nombreArchivo)`

Elimina una imagen del directorio de imágenes.

**Parámetros:**

- `$carpeta`: Nombre de la carpeta
- `$nombreArchivo`: Nombre del archivo a eliminar

#### `deleteFile($carpeta, $nombreArchivo)`

Elimina un archivo del directorio de archivos.

**Parámetros:**

- `$carpeta`: Nombre de la carpeta
- `$nombreArchivo`: Nombre del archivo a eliminar

---

## 🖼️ Procesamiento de Imágenes

### Flujo de Procesamiento

1. **Validación inicial**: Verifica que el archivo sea una imagen válida
2. **Validación de tamaño**: Comprueba tamaño y dimensiones
3. **Generación de nombre**: Crea nombre único con MD5
4. **Creación de directorio**: Crea la carpeta si no existe
5. **Procesamiento con Intervention**: Redimensiona y optimiza
6. **Guardado**: Guarda la imagen procesada

### Ejemplo de Uso

```php
<?php
use models\FileManagerModel;

// Procesar avatar de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $resultado = FileManagerModel::processImage(
        $_FILES['avatar'],
        'perfil',
        '.jpg'
    );

    if (is_array($resultado) && !isset($resultado['error'])) {
        $nombreImagen = $resultado[0];
        // Guardar en base de datos
        $usuario->imagen = $nombreImagen;
        $usuario->update($usuario->id);

        echo "Imagen subida exitosamente: $nombreImagen";
    } else {
        // Manejar errores
        $errores = $resultado;
        foreach ($errores['error'] as $error) {
            echo "Error: $error\n";
        }
    }
}
```

### Configuración de Procesamiento

```php
// La imagen se redimensiona automáticamente a 800x600px
$manager = new ImageManager(new Driver());
$image = $manager->read($img['tmp_name']);
$image->scale(800, 600);  // Mantener proporción
$image->save($filePath);
```

---

## 📄 Procesamiento de Archivos

### Flujo de Procesamiento

1. **Validación de upload**: Verifica que sea un archivo subido válido
2. **Validación de tamaño**: Comprueba el tamaño máximo
3. **Validación de extensión**: Verifica extensión permitida
4. **Validación MIME**: Comprueba el tipo real del archivo
5. **Escaneo de seguridad**: Busca contenido malicioso
6. **Generación de nombre**: Crea nombre único
7. **Guardado seguro**: Guarda con permisos restrictivos

### Ejemplo de Uso

```php
<?php
use models\FileManagerModel;

// Procesar documento PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $resultado = FileManagerModel::processFile(
        $_FILES['documento'],
        'documentos',
        ['pdf', 'docx'],  // Solo PDF y DOCX
        10 * 1024 * 1024 // 10MB máximo
    );

    if (is_array($resultado) && !isset($resultado['error'])) {
        $nombreArchivo = $resultado[0];
        echo "Documento subido exitosamente: $nombreArchivo";
    } else {
        echo "Error al subir documento";
    }
}
```

### Extensiones Soportadas

| Tipo             | Extensiones         | Uso Común           |
| ---------------- | ------------------- | ------------------- |
| Documentos       | pdf, doc, docx, txt | Contratos, informes |
| Hojas de cálculo | xls, xlsx, csv      | Datos, reportes     |
| Presentaciones   | ppt, pptx           | Diapositivas        |
| Archivos         | zip, rar, 7z        | Compresión          |
| Imágenes         | jpg, jpeg, png, gif | Fotos, gráficos     |

---

## 🔒 Seguridad

### Validaciones Implementadas

#### 1. Validación de Upload

```php
if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    throw new \Exception("Archivo no válido");
}
```

#### 2. Validación MIME Real

```php
$finfo = new \finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if ($mime === false) {
    throw new \Exception("No se pudo determinar MIME");
}
```

#### 3. Escaneo Anti-Webshell

```php
$sample = file_get_contents($file['tmp_name'], false, null, 0, 16000);
if (preg_match('/<\?php|eval\(|base64_decode|shell_exec|passthru|system/i', $sample)) {
    throw new \Exception("Contenido sospechoso detectado");
}
```

#### 4. Permisos Seguros

```php
@chmod($filePath, 0644);  // Lectura/escritura para owner, solo lectura para otros
```

### Patrones Detectados

- `<?php` - Etiquetas PHP
- `eval(` - Función eval peligrosa
- `base64_decode` - Decodificación base64
- `shell_exec` - Ejecución de comandos
- `passthru` - Ejecución de comandos
- `system` - Función system

---

## 💡 Ejemplos Prácticos

### 1. Sistema de Avatar de Usuario

```php
<?php
class UserController
{
    public function updateAvatar()
    {
        if (!isset($_FILES['avatar'])) {
            return ['error' => 'No se seleccionó ningún archivo'];
        }

        $resultado = FileManagerModel::processImage(
            $_FILES['avatar'],
            'perfil',
            '.jpg'
        );

        if (is_array($resultado) && !isset($resultado['error'])) {
            // Eliminar avatar anterior si existe
            if ($this->usuario->imagen) {
                FileManagerModel::deleteImage('perfil', $this->usuario->imagen);
            }

            // Actualizar base de datos
            $this->usuario->imagen = $resultado[0];
            $this->usuario->update($this->usuario->id);

            return ['success' => 'Avatar actualizado'];
        }

        return $resultado;
    }
}
```

### 2. Galería de Imágenes

```php
<?php
class GalleryController
{
    public function uploadMultiple()
    {
        $imagenesSubidas = [];
        $errores = [];

        if (isset($_FILES['imagenes'])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                $file = [
                    'name' => $_FILES['imagenes']['name'][$key],
                    'type' => $_FILES['imagenes']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['imagenes']['error'][$key],
                    'size' => $_FILES['imagenes']['size'][$key]
                ];

                $resultado = FileManagerModel::processImage($file, 'galeria', '.jpg');

                if (is_array($resultado) && !isset($resultado['error'])) {
                    $imagenesSubidas[] = $resultado[0];
                } else {
                    $errores[] = $resultado['error'][0] ?? 'Error desconocido';
                }
            }
        }

        return [
            'subidas' => $imagenesSubidas,
            'errores' => $errores
        ];
    }
}
```

### 3. Gestor de Documentos

```php
<?php
class DocumentController
{
    public function uploadDocument()
    {
        $allowedTypes = ['pdf', 'docx', 'xlsx'];
        $maxSize = 20 * 1024 * 1024; // 20MB

        $resultado = FileManagerModel::processFile(
            $_FILES['documento'],
            'documentos',
            $allowedTypes,
            $maxSize
        );

        if (is_array($resultado) && !isset($resultado['error'])) {
            // Guardar metadata en base de datos
            $documento = new Document();
            $documento->nombre = $_FILES['documento']['name'];
            $documento->archivo = $resultado[0];
            $documento->tipo = $_FILES['documento']['type'];
            $documento->tamano = $_FILES['documento']['size'];
            $documento->save();

            return ['success' => 'Documento subido'];
        }

        return $resultado;
    }
}
```

### 4. Limpieza de Archivos

```php
<?php
class CleanupController
{
    public function cleanupUnusedFiles()
    {
        // Obtener archivos en uso desde la base de datos
        $archivosEnUso = $this->getFilesInUse();

        // Escanear directorios
        $directorios = ['perfil', 'productos', 'documentos'];

        foreach ($directorios as $directorio) {
            $ruta = __DIR__ . "/../../public/imagenes/$directorio";
            if (is_dir($ruta)) {
                $archivos = scandir($ruta);

                foreach ($archivos as $archivo) {
                    if ($archivo !== '.' && $archivo !== '..') {
                        if (!in_array($archivo, $archivosEnUso)) {
                            // Eliminar archivo no utilizado
                            FileManagerModel::deleteImage($directorio, $archivo);
                            echo "Eliminado: $directorio/$archivo\n";
                        }
                    }
                }
            }
        }
    }
}
```

---

## ⚠️ Manejo de Errores

### Estructura de Errores

```php
// Errores de procesamiento de imagen
self::$errors["error"][] = "Archivo de imagen no válido";
self::$errors["error"][] = "El archivo es demasiado grande (máximo 3MB)";
self::$errors["error"][] = "Tipo de archivo no permitido";
self::$errors["error"][] = "El archivo no es una imagen válida";
self::$errors["error"][] = "La imagen es demasiado grande (máximo 2000x2000 píxeles)";
self::$errors["error"][] = "No hay permisos de escritura en el directorio";
self::$errors["error"][] = "No se pudo guardar la imagen";

// Errores de procesamiento de archivos
throw new \Exception("Archivo no válido");
throw new \Exception("El archivo es demasiado grande");
throw new \Exception("Extensión no permitida");
throw new \Exception("No se pudo determinar MIME");
throw new \Exception("Contenido sospechoso detectado");
throw new \Exception("No hay permisos de escritura en el directorio");
throw new \Exception("No se pudo guardar el archivo");
```

### Manejo de Errores en Controladores

```php
<?php
try {
    $resultado = FileManagerModel::processImage($_FILES['imagen'], 'perfil', '.jpg');

    if (is_array($resultado)) {
        if (isset($resultado['error'])) {
            // Errores de validación
            return ['success' => false, 'errors' => $resultado['error']];
        } else {
            // Éxito
            return ['success' => true, 'filename' => $resultado[0]];
        }
    } else {
        // Error general
        return ['success' => false, 'error' => 'Error procesando imagen'];
    }
} catch (\Exception $e) {
    // Error excepcional
    error_log("Error en FileManager: " . $e->getMessage());
    return ['success' => false, 'error' => 'Error del servidor'];
}
```

---

## ⚙️ Configuración Avanzada

### Personalizar Validaciones

```php
class CustomFileManager extends FileManagerModel
{
    public static function processImageCustom($img, $carpeta, $tipo, $options = [])
    {
        // Opciones por defecto
        $defaultOptions = [
            'max_size' => 3 * 1024 * 1024,
            'max_width' => 2000,
            'max_height' => 2000,
            'resize_width' => 800,
            'resize_height' => 600,
            'quality' => 85
        ];

        $options = array_merge($defaultOptions, $options);

        // Implementar validaciones personalizadas...
    }
}
```

### Configuración de Directorios

```php
// En config/app.php
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('IMAGE_PATH', __DIR__ . '/../public/imagenes/');
define('DOCUMENT_PATH', __DIR__ . '/../public/archivos/');

// Asegurar permisos correctos
mkdir(UPLOAD_PATH, 0755, true);
mkdir(IMAGE_PATH, 0755, true);
mkdir(DOCUMENT_PATH, 0755, true);
```

### Integración con Base de Datos

```php
<?php
class File extends Main
{
    public static $table = 'archivos';
    static $columnDB = ['nombre_original', 'nombre_archivo', 'tipo', 'tamano', 'carpeta'];

    public function uploadAndSave($file, $carpeta, $allowedTypes = null)
    {
        $resultado = FileManagerModel::processFile($file, $carpeta, $allowedTypes);

        if (is_array($resultado) && !isset($resultado['error'])) {
            $this->nombre_original = $file['name'];
            $this->nombre_archivo = $resultado[0];
            $this->tipo = $file['type'];
            $this->tamano = $file['size'];
            $this->carpeta = $carpeta;

            return $this->save();
        }

        return false;
    }

    public function deleteFileAndRecord()
    {
        // Eliminar archivo físico
        FileManagerModel::deleteFile($this->carpeta, $this->nombre_archivo);

        // Eliminar registro de base de datos
        return $this->delete();
    }
}
```

---

## 📊 Métricas y Monitoreo

### Estadísticas de Uso

```php
<?php
class FileStats
{
    public static function getStorageUsage()
    {
        $stats = [];

        // Escanear directorios
        $directorios = ['perfil', 'productos', 'documentos'];

        foreach ($directorios as $directorio) {
            $ruta = __DIR__ . "/../../public/imagenes/$directorio";
            $stats[$directorio] = [
                'files' => 0,
                'size' => 0
            ];

            if (is_dir($ruta)) {
                $archivos = glob($ruta . '*');
                foreach ($archivos as $archivo) {
                    if (is_file($archivo)) {
                        $stats[$directorio]['files']++;
                        $stats[$directorio]['size'] += filesize($archivo);
                    }
                }
            }
        }

        return $stats;
    }

    public static function getRecentUploads($limit = 10)
    {
        // Consultar uploads recientes desde la base de datos
        return File::orderBy('created_at', 'desc')
                  ->limit($limit)
                  ->get();
    }
}
```

---

## 🔧 Buenas Prácticas

### Seguridad

1. **Validar siempre** el tipo y tamaño de archivos
2. **Usar nombres aleatorios** para evitar sobreescritura
3. **Escaneo de contenido** para detectar malware
4. **Permisos restrictivos** en archivos guardados
5. **Separar directorios** por tipo de contenido

### Rendimiento

1. **Redimensionar imágenes** para ahorrar espacio
2. **Implementar caché** para archivos frecuentes
3. **Usar CDN** para archivos estáticos
4. **Comprimir archivos** cuando sea posible
5. **Limpiar archivos** no utilizados periódicamente

### Mantenimiento

1. **Monitorear espacio** en disco
2. **Hacer backups** de archivos importantes
3. **Implementar logging** de operaciones
4. **Documentar políticas** de retención
5. **Auditar periódicamente** archivos existentes

---

## 🆘 Troubleshooting

### Problemas Comunes

#### 1. "No hay permisos de escritura"

**Solución:**

```bash
# Verificar permisos
ls -la public/imagenes/

# Corregir permisos
chmod 755 public/imagenes/
chmod 755 public/imagenes/perfil/
chown www-data:www-data public/imagenes/
```

#### 2. "Archivo demasiado grande"

**Solución:**

- Verificar `upload_max_filesize` en php.ini
- Ajustar límites en el método
- Implementar upload por chunks para archivos grandes

#### 3. "Contenido sospechoso detectado"

**Solución:**

- Revisar el archivo escaneado
- Ajustar patrones de detección
- Implementar cuarentena de archivos

#### 4. "No se pudo guardar la imagen"

**Solución:**

- Verificar espacio en disco
- Comprobar extensión GD de PHP
- Revisar permisos del directorio

### Debug y Logging

```php
// Habilitar logging detallado
error_log("FileManager: Procesando archivo " . $file['name']);
error_log("FileManager: Tamaño " . $file['size']);
error_log("FileManager: Tipo " . $file['type']);

// Log de errores
if (!empty(self::$errors)) {
    error_log("FileManager Errors: " . json_encode(self::$errors));
}
```

---

## 📚 Referencia Rápida

### Resumen de Métodos

| Método           | Propósito                                | Retorna                    |
| ---------------- | ---------------------------------------- | -------------------------- |
| `processImage()` | Procesar imágenes con redimensionamiento | Array con nombre o errores |
| `processFile()`  | Procesar archivos genéricos              | Array con nombre o errores |
| `deleteImage()`  | Eliminar imagen específica               | void                       |
| `deleteFile()`   | Eliminar archivo específico              | void                       |

### Límites por Defecto

| Tipo               | Límite           |
| ------------------ | ---------------- |
| Imágenes           | 3MB, 2000x2000px |
| Archivos           | 5MB              |
| Redimensionamiento | 800x600px        |

### Extensiones Soportadas

**Imágenes:** jpg, jpeg, png, gif
**Documentos:** pdf, doc, docx, xls, xlsx, ppt, pptx, zip, txt

---

**Versión:** 1.0.0
**Compatibilidad:** PHP 8.0+, Intervention Image 3.0+
**Dependencias:** GD Library, fileinfo extension

---

**Desarrollado con ❤️ para gestión segura de archivos**
