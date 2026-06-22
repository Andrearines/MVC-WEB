<?php

namespace services;

use Intervention\Image\ImageManager;
use services\Logger;
use Intervention\Image\Drivers\Gd\Driver;

class FileManager
{
    private static $errors = [];

    /**
     * Elimina un archivo de la carpeta pública
     */
    public static function deleteFile($carpeta, $nombreArchivo)
    {
        $filePath = __DIR__ . '/../../public/updates/imgs/' . $carpeta . '/' . $nombreArchivo;
        if (file_exists($filePath)) {
            unlink($filePath);
            Logger::info("Archivo eliminado: " . $filePath);
        }
    }

    public static function deleteImage($carpeta, $nombreArchivo)
    {
        $filePath = __DIR__ . '/../../public/updates/imgs/' . $carpeta . '/' . $nombreArchivo;
        if (file_exists($filePath)) {
            unlink($filePath);
            Logger::info("Imagen eliminada: " . $filePath);
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
                self::$errors["error"][] = "Archivo de imagen no válido";
                Logger::error("Archivo de imagen no válido: " . $img['tmp_name']);
            }

            if ($img['size'] > 3 * 1024 * 1024) {
                self::$errors["error"][] = "El archivo es demasiado grande (máximo 3MB)";
                Logger::error("El archivo es demasiado grande (máximo 3MB): " . $img['size']);
            }

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($img['type'], $allowedTypes)) {
                self::$errors["error"][] = "Tipo de archivo no permitido";
                Logger::error("Tipo de archivo no permitido: " . $img['type']);
            }

            $imageInfo = @getimagesize($img['tmp_name']);
            if ($imageInfo === false) {
                self::$errors["error"][] = "El archivo no es una imagen válida";
                Logger::error("El archivo no es una imagen válida: " . $img['tmp_name']);
            }

            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                self::$errors["error"][] = "La imagen es demasiado grande (máximo 2000x2000 píxeles)";
                Logger::error("La imagen es demasiado grande (máximo 2000x2000 píxeles): " . $imageInfo[0] . "x" . $imageInfo[1]);
            }

            $nombreArchivo = md5(uniqid(rand(), true)) . $tipo;
            $uploadDir = __DIR__ . '/../../public/updates/imgs/' . $carpeta . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                self::$errors["error"][] = "No hay permisos de escritura en el directorio";
                Logger::error("No hay permisos de escritura en el directorio: " . $uploadDir);
            }

            $filePath = $uploadDir . $nombreArchivo;

            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($img['tmp_name']);
                $image->scale(800, 600);
                $image->save($filePath);
                Logger::info("Imagen guardada: " . $filePath);
            } catch (\Exception $e) {
                self::$errors["error"][] = "Error procesando imagen: " . $e->getMessage();
                Logger::error("Error procesando imagen: " . $e->getMessage());
            }

            if (!file_exists($filePath)) {
                self::$errors["error"][] = "No se pudo guardar la imagen";
                Logger::error("No se pudo guardar la imagen: " . $filePath);
            }

            if (empty(self::$errors)) {
                return [$nombreArchivo];
                Logger::info("Imagen guardada exitosamente: " . $nombreArchivo);
            }
            return self::$errors;
        } catch (\Exception $e) {
            self::$errors["error"][] = "Error procesando imagen: " . $e->getMessage();
            Logger::error("Error procesando imagen: " . $e->getMessage());
            return self::$errors;
        }
    }

    /**
     * Procesa archivos genéricos (PDF, DOCX, ZIP, etc.) evitando webshells
     */
    public static function processFile($file, $carpeta, $allowedExtensions = null, $maxBytes = 5 * 1024 * 1024)
    {
        try {
            self::$errors = [];
            Logger::info("Procesando archivo: " . $file['name']);

            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                Logger::error("Archivo no válido: " . $file['tmp_name']);
            }

            if ($file['size'] > $maxBytes) {
                Logger::error("El archivo es demasiado grande: " . $file['size']);
            }

            $origName = $file['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            Logger::info("Extensión del archivo: " . $ext);

            if ($allowedExtensions === null) {
                $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'txt'];
            }

            if (!in_array($ext, $allowedExtensions, true)) {
                Logger::error("Extensión no permitida: " . $ext);
            }

            // Validar MIME real
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            Logger::info("MIME del archivo: " . $mime);
            if ($mime === false) {
                Logger::error("No se pudo determinar MIME: " . $mime);
            }

            // Escaneo básico contra webshell
            $sample = file_get_contents($file['tmp_name'], false, null, 0, 16000);
            if (preg_match('/<\?php|eval\(|base64_decode|shell_exec|passthru|system/i', $sample)) {
                Logger::critical("Contenido sospechoso detectado: " . $sample);
            }

            $nombreArchivo = md5(uniqid(rand(), true)) . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/updates/archivos/' . $carpeta . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                Logger::error("No hay permisos de escritura en el directorio: " . $uploadDir);
            }

            $filePath = $uploadDir . $nombreArchivo;

            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                Logger::error("No se pudo guardar el archivo: " . $filePath);
            }

            @chmod($filePath, 0644);

            Logger::info("Archivo guardado exitosamente: " . $nombreArchivo);
            return [$nombreArchivo];
        } catch (\Exception $e) {
            Logger::error("Error procesando archivo: " . $e->getMessage());
            return false;
        }
    }
}
