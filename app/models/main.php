<?php

namespace models;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
class main
{
    public static $table;
    public static $db;
    static $columnDB = [];
    
    public static $errors = [];
    
    // Cache simple para mejorar rendimiento
    private static $cache = [];
    private static $cacheEnabled = true;

    public $id;
    public $img;
    
    public function __construct($data = [])
    {
       
    }
    
    public static function setDb($database)
    {
        self::$db = $database;
    }

    public function validate()
    {
       static::$errors = [];

        return static::$errors;
       
    }

    public function createError($type, $msg){
        static::$errors[$type][] = $msg;
    }

    public function sicronizar($data){
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $cleanValue = self::$db->real_escape_string($value);
                $this->$key = $cleanValue;
            }
        }
    }

    public static function SQL($query){
        $result = self::$db->query($query);
        $array = [];

        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }
        return $array;   
    }
 
    public static function all($columns = ['*']){
        // Optimización: permitir seleccionar columnas específicas
        $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;
        $query = "SELECT $columnsStr FROM " . static::$table;
        $result = self::$db->query($query);
        $array = [];

        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }
        return $array;
    }

    

    public static function find($id){
        // Cache para consultas frecuentes
        if (self::$cacheEnabled) {
            $cacheKey = static::$table . '_find_' . $id;
            if (isset(self::$cache[$cacheKey])) {
                return self::$cache[$cacheKey];
            }
        }
        
        $id = self::$db->real_escape_string($id); 
        $query = "SELECT * FROM " . static::$table . " WHERE id = $id LIMIT 1";
        $result = self::$db->query($query);

        if ($row = $result->fetch_assoc()) {
            $object = static::create($row);
            
            // Guardar en cache
            if (self::$cacheEnabled) {
                self::$cache[$cacheKey] = $object;
            }
            
            return $object;
        }
        return null;
    }

    public static function findBy($column, $value){
        // Validación básica de columnas para seguridad
        if (!in_array($column, static::$columnDB) && $column !== 'id') {
            return null;
        }
        
        $value = self::$db->real_escape_string($value);
        $query = "SELECT * FROM " . static::$table . " WHERE $column = '$value' LIMIT 1";
        $result = self::$db->query($query);

        if ($row = $result->fetch_assoc()) {
            return static::create($row);
        }
        return null;
    }

    public static function create($data){
        $object = new static;
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $cleanValue = self::$db->real_escape_string($value);
                $object->$key = $cleanValue;
            }
        }
        return $object;
    }

    public function update($id){
        $id = self::$db->real_escape_string($id);
        $query = "UPDATE " . static::$table . " SET ";
        $updates = [];
        
        foreach (static::$columnDB as $column) {
            // Excluir siempre el campo 'id' en las actualizaciones
            if ($column !== 'id' && property_exists($this, $column)) {
                $value = self::$db->real_escape_string($this->$column);
                $updates[] = "`$column` = '$value'";
            }
        }
        
        $query .= implode(', ', $updates) . " WHERE id = '$id'";
        $result = self::$db->query($query);
        
        // Limpiar cache después de actualizar
        if (self::$cacheEnabled) {
            $cacheKey = static::$table . '_find_' . $id;
            unset(self::$cache[$cacheKey]);
        }
        
        return $result;
    }

    public function delete(){
        $id = self::$db->real_escape_string($this->id);
        $query = "DELETE FROM " . static::$table . " WHERE id = '$id'";
        $result = self::$db->query($query);
        
        // Limpiar cache después de eliminar
        if (self::$cacheEnabled) {
            $cacheKey = static::$table . '_find_' . $id;
            unset(self::$cache[$cacheKey]);
        }
        
        return $result;
    }

   
    public static function findAllBy($column, $value, $columns = ['*']){
        // Validación básica de columnas para seguridad
        if (!in_array($column, static::$columnDB) && $column !== 'id') {
            return [];
        }
        
        $value = self::$db->real_escape_string($value);
        $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;

        $query = "SELECT $columnsStr FROM " . static::$table . " WHERE $column = '$value'";
        $result = self::$db->query($query);
        $array = [];

        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }
        return $array;
    }


    
    public function save($img){
        try {
           
            if($img == true){
                // Procesar imagen con manejo de memoria
                $processedImage = $this->processImage($this->img,"users",".png");
                if ($processedImage === false) {
                    throw new \Exception("Error al procesar la imagen");
                }
                $this->img = $processedImage;
                
                // Liberar memoria después del procesamiento
                gc_collect_cycles();
            }
            if(empty(self::$errors)){
            $columns = [];
            $values = [];
            
            foreach (static::$columnDB as $column) {
                // Excluir siempre el campo 'id'
                if ($column !== 'id' && property_exists($this, $column)) {
                    $value = $this->$column;
                    $value = self::$db->real_escape_string($value);
                    $columns[] = "`$column`";
                    $values[] = "'$value'";
                }
            }
            
            $columnsStr = implode(', ', $columns);
            $valuesStr = implode(', ', $values);
            
            $query = "INSERT INTO " . static::$table . " ($columnsStr) VALUES ($valuesStr)";
            $result = self::$db->query($query);
            
            // Limpiar cache después de insertar
            if (self::$cacheEnabled && $result) {
                self::clearCache();
            }
            
            return true;
        }else{
            
            return self::$errors;
        }
            
        } catch (\Exception $e) {
            error_log("Error en save: " . $e->getMessage());
            return false;
        }
    }

    private function processImage($img, $carpeta, $tipo){ 
        try {
            self::$errors=[];
            // Verificar que el archivo existe y es válido
            if (!isset($img['tmp_name']) || !file_exists($img['tmp_name'])) {
                throw new \Exception("Archivo de imagen no válido");
                self::$errors["error"][]="Archivo de imagen no válido";
            }
            
            // Verificar el tamaño del archivo (máximo 3MB para evitar problemas de memoria)
            if ($img['size'] > 3 * 1024 * 1024) {
                throw new \Exception("El archivo es demasiado grande (máximo 1MB)");
                self::$errors["error"][]="Archivo es demasiado";
            }
            
            // Verificar el tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($img['type'], $allowedTypes)) {
                throw new \Exception("Tipo de archivo no permitido");
                self::$errors["error"][]="Archivo de imagen no válido";
            }
            
            // Verificar que el archivo es realmente una imagen válida
            $imageInfo = @getimagesize($img['tmp_name']);
            if ($imageInfo === false) {
                throw new \Exception("El archivo no es una imagen válida");
                self::$errors["error"][]="Archivo de imagen no válido";
            }
            
            // Verificar dimensiones máximas (máximo 2000x2000 para evitar problemas de memoria)
            if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
                throw new \Exception("La imagen es demasiado grande (máximo 2000x2000 píxeles)");
                self::$errors["error"][]="es demasiado grande (maximo 2000x2000)";
            }
            
            $nombre_img = md5(uniqid(rand(), true)) . $tipo;
            $uploadDir = __DIR__ . '/../../public/imagenes/' . $carpeta . '/';
            
            // Crear directorio si no existe
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new \Exception("No se pudo crear el directorio de destino");
                    
                }
            }
            
            // Verificar permisos de escritura
            if (!is_writable($uploadDir)) {
                throw new \Exception("No hay permisos de escritura en el directorio");
            }
            
            // Simplemente copiar el archivo sin procesar para evitar problemas de memoria
            $filePath = $uploadDir . $nombre_img;
            
            if (!copy($img['tmp_name'], $filePath)) {
                throw new \Exception("No se pudo copiar la imagen");
            }
            
            // Verificar que el archivo se copió correctamente
            if (!file_exists($filePath)) {
                throw new \Exception("No se pudo guardar la imagen");
                self::$errors["error"][]="error con el guardado de la imagen";
            }
            
            return [$nombre_img];
            
        } catch (\Exception $e) {
            error_log("Error procesando imagen: " . $e->getMessage());
            return false;
        }
    }
    
    // Métodos para gestionar el cache
    public static function clearCache() {
        self::$cache = [];
    }
    
    public static function disableCache() {
        self::$cacheEnabled = false;
    }
    
    public static function enableCache() {
        self::$cacheEnabled = true;
    }
    
    public static function getCacheStats() {
        return [
            'enabled' => self::$cacheEnabled,
            'items' => count(self::$cache),
            'keys' => array_keys(self::$cache)
        ];
    }
}

  
       
    

