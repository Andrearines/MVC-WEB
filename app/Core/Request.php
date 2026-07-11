<?php

namespace app\Core;

class Request
{
    public function getMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            return strtoupper($_POST['_method']);
        }
        return strtoupper($method);
    }

    public function getPath()
    {
        return $_SERVER['PATH_INFO'] ?? '/';
    }

    public function getBody()
    {
        $method = $this->getMethod();

        if ($method === 'GET') {
            return $_GET;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            parse_str($input, $data);
        }

        return array_merge($_POST, is_array($data) ? $data : []);
    }
}
