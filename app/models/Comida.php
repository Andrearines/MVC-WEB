<?php

namespace models;

class Comida extends Main
{
    // Nombre de la tabla en la base de datos
    public static $table = "comida";

    // Columnas de la tabla (sin incluir el id, manejado por la clase base Main)
    public static $columnDB = ["nombre", "precio", "imagen", "descripcion"];

    // Atributos correspondientes a las columnas
    public $id;
    public $nombre;
    public $precio;
    public $imagen;
    public $descripcion;

    /**
     * Constructor del modelo
     * @param array $data - Datos iniciales para rellenar el modelo
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
    }
}
