<?php

namespace MVC;

class Request
{
    /**
     * Obtiene el método HTTP de la petición, soportando spoofing de formularios (_method).
     * @return string
     */
    public function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }
        return strtoupper($method);
    }

    /**
     * Obtiene la URL actual (path).
     * @return string
     */
    public function getPath()
    {
        return $_SERVER['PATH_INFO'] ?? '/';
    }

    /**
     * Obtiene los datos de la petición unificados en un arreglo asociativo.
     * Soporta GET, POST, y lectura de php://input para JSON o Form Data (PUT/DELETE).
     * @return array
     */
    public function getBody()
    {
        $method = $this->getMethod();

        if ($method === 'GET') {
            return $_GET;
        }

        // Leer datos del flujo de entrada para métodos que no son GET
        $input = file_get_contents('php://input');

        // Intentar parsear como JSON (típico de fetch/axios)
        $data = json_decode($input, true);

        // Si no es un JSON válido, intentar parsear como query string (application/x-www-form-urlencoded)
        if (json_last_error() !== JSON_ERROR_NONE) {
            parse_str($input, $data);
        }

        // Combinar con $_POST por si es un formulario tradicional con spoofing o multipart/form-data
        return array_merge($_POST, is_array($data) ? $data : []);
    }
}
