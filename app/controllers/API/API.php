<?php
namespace controllers\API;
use MVC\Router;
class API{
    public static function servicios(Router $r){
        echo json_encode('hola mundo');
    }
     //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    public static function crearServicio(Router $r){
        echo json_encode('crear servicio');
    }
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    public static function actualizarServicio(Router $r){
        echo json_encode('actualizar servicio');
    }
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    //-------------------------------------------------------------------------
    public static function eliminarServicio(Router $r){
        echo json_encode('eliminar servicio');
    }

}