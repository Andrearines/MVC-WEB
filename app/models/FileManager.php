<?php

namespace models;

class FileManager
{
    private static $errors = [];

    /**
     * Elimina un archivo de la carpeta pública
     */
    public static function deleteFile($carpeta, $nombreArchivo)
    {
        $filePath = __DIR__ . '/../../public/' . $carpeta . '/' . $nombreArchivo;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * Procesa imágenes (jpg, png, gif) con validaciones
     */
    public static function processImage($img, $carpeta, $tipo)
    {
        try {
            self::$errors = [];

            if (!isset($img['tmp_name']) || !file_exists($img['tmp_name'])) {
                throw new \Exception("Archivo de imagen no válido");
            }

            if ($img['size'] > 3 * 1024 * 1024) {
                throw new \Exception("El archivo es demasiado grande (máximo 3MB)");
            }

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($img['type'], $allowedTypes)) {
                throw new \Exception("Tipo de archivo no permitido");
            }

            $imageInfo = @getimagesize($img['tmp_name']);
            if ($imageInfo === false) {
                throw new \Exception("El archivo no es una imagen válida");
            }

            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                throw new \Exception("La imagen es demasiado grande (máximo 2000x2000 píxeles)");
            }

            $nombreArchivo = md5(uniqid(rand(), true)) . $tipo;
            $uploadDir = __DIR__ . '/../../public/imagenes/' . $carpeta . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                throw new \Exception("No hay permisos de escritura en el directorio");
            }

            $filePath = $uploadDir . $nombreArchivo;

            if (!move_uploaded_file($img['tmp_name'], $filePath)) {
                throw new \Exception("No se pudo guardar la imagen");
            }

            return [$nombreArchivo];
        } catch (\Exception $e) {
            error_log("Error procesando imagen: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Procesa archivos genéricos (PDF, DOCX, ZIP, etc.) evitando webshells
     */
    public static function processFile($file, $carpeta, $allowedExtensions = null, $maxBytes = 5 * 1024 * 1024)
    {
        try {
            self::$errors = [];

            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                throw new \Exception("Archivo no válido");
            }

            if ($file['size'] > $maxBytes) {
                throw new \Exception("El archivo es demasiado grande");
            }

            $origName = $file['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if ($allowedExtensions === null) {
                $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'txt'];
            }

            if (!in_array($ext, $allowedExtensions, true)) {
                throw new \Exception("Extensión no permitida");
            }

            // Validar MIME real
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            if ($mime === false) {
                throw new \Exception("No se pudo determinar MIME");
            }

            // Escaneo básico contra webshell
            $sample = file_get_contents($file['tmp_name'], false, null, 0, 16000);
            if (preg_match('/<\?php|eval\(|base64_decode|shell_exec|passthru|system/i', $sample)) {
                throw new \Exception("Contenido sospechoso detectado");
            }

            $nombreArchivo = md5(uniqid(rand(), true)) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/archivos/' . $carpeta . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                throw new \Exception("No hay permisos de escritura en el directorio");
            }

            $filePath = $uploadDir . $nombreArchivo;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new \Exception("No se pudo guardar el archivo");
            }

            @chmod($filePath, 0644);

            return [$nombreArchivo];
        } catch (\Exception $e) {
            error_log("Error procesando archivo: " . $e->getMessage());
            return false;
        }
    }
}
