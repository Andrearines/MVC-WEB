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


    public function save($arg=[]){
            $columns = [];
            $values = [];
            
            foreach (static::$columnDB as $column) {
                // Excluir siempre el campo 'id'
                if ($column !== 'id' && property_exists($this, $column)) {
                    $value = self::$db->real_escape_string($this->$column);
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
            
            return $result;
    }
    

    private function img($img,$carpeta,$tipo){ 
        $nombre_img=md5(uniqid(rand(),true )).$tipo;
        
        $manager = new ImageManager(new Driver());
        $imagen = $manager->read($img['tmp_name']);
        
        // Optimización: solo redimensionar si es necesario
        $originalSize = $imagen->size();
        if ($originalSize->width() > 900 || $originalSize->height() > 900) {
            $imagen = $imagen->cover(900, 900);
        }
        
        $imagen->save(__DIR__ . '/../public/imagenes/'.$carpeta."/".$nombre_img);
        return  $nombre_img;
    }

    public function getErrors($type = null){
        if($type){
            return static::$errors[$type] ?? null;
        }
        return static::$errors;
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

  
       
    

