<?php

namespace models;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileManagerModel
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
        }
    }

    public static function deleteImage($carpeta, $nombreArchivo)
    {
        $filePath = __DIR__ . '/../../public/updates/imgs/' . $carpeta . '/' . $nombreArchivo;
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
                self::$errors["error"][] = "Archivo de imagen no válido";
            }

            if ($img['size'] > 3 * 1024 * 1024) {
                self::$errors["error"][] = "El archivo es demasiado grande (máximo 3MB)";
            }

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($img['type'], $allowedTypes)) {
                self::$errors["error"][] = "Tipo de archivo no permitido";
            }

            $imageInfo = @getimagesize($img['tmp_name']);
            if ($imageInfo === false) {
                self::$errors["error"][] = "El archivo no es una imagen válida";
            }

            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                self::$errors["error"][] = "La imagen es demasiado grande (máximo 2000x2000 píxeles)";
            }

            $nombreArchivo = md5(uniqid(rand(), true)) . $tipo;
            $uploadDir = __DIR__ . '/../../public/updates/imgs/' . $carpeta . '/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                self::$errors["error"][] = "No hay permisos de escritura en el directorio";
            }

            $filePath = $uploadDir . $nombreArchivo;

            $manager = new ImageManager(new Driver());
            $image = $manager->read($img['tmp_name']);
            $image->scale(800, 600);
            $image->save($filePath);

            if (!file_exists($filePath)) {
                self::$errors["error"][] = "No se pudo guardar la imagen";
            }

            if (empty(self::$errors)) {
                return [$nombreArchivo];
            }
            return self::$errors;
        } catch (\Exception $e) {
            self::$errors["error"][] = "Error procesando imagen: " . $e->getMessage();
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
            $uploadDir = __DIR__ . '/../../public/updates/archivos/' . $carpeta . '/';

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
            ("Error procesando archivo: " . $e->getMessage());
            return false;
        }
    }
}
