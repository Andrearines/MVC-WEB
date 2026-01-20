<?php

namespace models;

class Main
{
    public static $table;
    public static $db;
    static $columnDB = [];

    public static $errors = [];

    // Cache simple
    private static $cache = [];
    private static $cacheEnabled = true;

    public $id;
    public $img;

    public function __construct($data = []) {}

    public static function setDb($database)
    {
        self::$db = $database;
    }

    public function createError($type, $msg)
    {

        static::$errors[$type][] = $msg;
    }

    public function sicronizar($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /* ===================== SQL GENÉRICO ===================== */

    // Ejecución de query seguro (SELECT multiple)
    public static function SQL($query, $params = [], $types = "")
    {
        $stmt = self::$db->prepare($query);
        if (!$stmt) throw new \Exception("Error en prepare: " . self::$db->error);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $array = [];

        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }

        $stmt->close();
        return $array;
    }

    // Ejecución directa (INSERT, UPDATE, DELETE genéricos)
    public function exec($query, $params = [], $types = "")
    {
        $stmt = self::$db->prepare($query);
        if (!$stmt) throw new \Exception("Error en prepare: " . self::$db->error);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }

    public static function create($data)
    {
        $object = new static;
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }
        }
        return $object;
    }


    public static function findAllOrden($column, $orden)
    {
        $query = "SELECT * FROM " . static::$table . " ORDER BY ? ?";
        $stmt = self::$db->prepare($query);
        if (!$stmt) throw new \Exception("Error en prepare: " . self::$db->error);
        $stmt->bind_param("ss", $column, $orden);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = static::create($row);
        }
        $stmt->close();
        return $array;
    }

    public static function findBy($column, $value)
    {
        if (!in_array($column, static::$columnDB) && $column !== 'id') {
            return null;
        }

        $query = "SELECT * FROM " . static::$table . " WHERE $column = ? LIMIT 1";
        $result = self::SQL($query, [$value], "s");
        return $result ? $result[0] : null;
    }

    public static function findAllBy($column, $value, $columns = ['*'])
    {
        if (!in_array($column, static::$columnDB) && $column !== 'id') {
            return [];
        }

        $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;
        $query = "SELECT $columnsStr FROM " . static::$table . " WHERE $column = ?";
        return self::SQL($query, [$value], "s");
    }

    public static function find($id)
    {
        if (self::$cacheEnabled) {
            $cacheKey = static::$table . '_find_' . $id;
            if (isset(self::$cache[$cacheKey])) {
                return self::$cache[$cacheKey];
            }
        }

        $query = "SELECT * FROM " . static::$table . " WHERE id = ? LIMIT 1";
        $result = self::SQL($query, [$id], "i");

        if ($result) {
            $object = $result[0];
            if (self::$cacheEnabled) {
                self::$cache[$cacheKey] = $object;
            }
            return $object;
        }
        return null;
    }

    /* ===================== CRUD ===================== */

    public static function all($columns = ['*'])
    {
        $columnsStr = is_array($columns) ? implode(', ', $columns) : $columns;
        $query = "SELECT $columnsStr FROM " . static::$table;
        return self::SQL($query);
    }



    public function save($exclude = [])
    {
        try {
            if (!empty(self::$errors)) {
                return self::$errors;
            }

            $columns = [];
            $placeholders = [];
            $values = [];

            foreach (static::$columnDB as $column) {
                if ($column !== 'id' && property_exists($this, $column) && !in_array($column, $exclude)) {
                    $columns[] = "`$column`";
                    $placeholders[] = "?";
                    $values[] = $this->$column;
                }
            }

            $columnsStr = implode(", ", $columns);
            $placeholdersStr = implode(", ", $placeholders);

            $query = "INSERT INTO " . static::$table . " ($columnsStr) VALUES ($placeholdersStr)";
            $stmt = self::$db->prepare($query);

            if (!$stmt) {
                throw new \Exception("Error en prepare: " . self::$db->error);
            }

            $types = "";
            foreach ($values as $value) {
                if (is_int($value)) $types .= "i";
                elseif (is_float($value)) $types .= "d";
                elseif (is_null($value)) $types .= "s";
                else $types .= "s";
            }

            $stmt->bind_param($types, ...$values);
            $result = $stmt->execute();

            if ($result) {
                if (self::$cacheEnabled) self::clearCache();
                $this->id = self::$db->insert_id;
                $stmt->close();
                return $this->id;
            } else {
                throw new \Exception("Error en execute: " . $stmt->error);
            }
        } catch (\Exception $e) {
            error_log("Error en save: " . $e->getMessage());
            return false;
        }
    }

    public function update($id = null, $exclude = [])
    {
        try {
            if (!empty(self::$errors)) return self::$errors;

            $id = $id ?? $this->id;
            if (empty($id)) throw new \Exception("ID requerido para actualizar");

            $updates = [];
            $values = [];

            foreach (static::$columnDB as $column) {
                if ($column !== 'id' && property_exists($this, $column) && !in_array($column, $exclude)) {
                    $updates[] = "`$column` = ?";
                    $values[] = $this->$column;
                }
            }

            if (empty($updates)) throw new \Exception("No hay columnas para actualizar");

            $updatesStr = implode(", ", $updates);
            $query = "UPDATE " . static::$table . " SET $updatesStr WHERE id = ?";

            $stmt = self::$db->prepare($query);
            if (!$stmt) throw new \Exception("Error en prepare: " . self::$db->error);

            $types = "";
            foreach ($values as $value) {
                if (is_int($value)) $types .= "i";
                elseif (is_float($value)) $types .= "d";
                elseif (is_null($value)) $types .= "s";
                else $types .= "s";
            }

            $types .= "i";
            $values[] = $id;

            $stmt->bind_param($types, ...$values);
            $result = $stmt->execute();

            if ($result) {
                if (self::$cacheEnabled) {
                    $cacheKey = static::$table . '_find_' . $id;
                    unset(self::$cache[$cacheKey]);
                }
                $stmt->close();
                return true;
            } else {
                throw new \Exception("Error en execute: " . $stmt->error);
            }
        } catch (\Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id = null)
    {
        try {
            $id = $id ?? $this->id;
            if (empty($id)) throw new \Exception("ID requerido para eliminar");

            $query = "DELETE FROM " . static::$table . " WHERE id = ?";
            $stmt = self::$db->prepare($query);
            if (!$stmt) throw new \Exception("Error en prepare: " . self::$db->error);

            $stmt->bind_param("i", $id);
            $result = $stmt->execute();

            if ($result) {
                if (self::$cacheEnabled) {
                    $cacheKey = static::$table . '_find_' . $id;
                    unset(self::$cache[$cacheKey]);
                }
                $stmt->close();
                return true;
            } else {
                throw new \Exception("Error en execute: " . $stmt->error);
            }
        } catch (\Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }

    /* ===================== CACHE ===================== */
    public static function clearCache()
    {
        self::$cache = [];
    }

    public static function disableCache()
    {
        self::$cacheEnabled = false;
    }

    public static function enableCache()
    {
        self::$cacheEnabled = true;
    }

    public static function getCacheStats()
    {
        return [
            'enabled' => self::$cacheEnabled,
            'items' => count(self::$cache),
            'keys' => array_keys(self::$cache)
        ];
    }
}
