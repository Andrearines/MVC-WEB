<?php

namespace controllers\API;

use services\Logger;

class logs
{
    public static function getLogs()
    {
        // 1. Calculamos la ruta del archivo de log del día actual
        $date = date('Y-m-d');
        // logs.php está en app/controllers/API/logs, por tanto subimos de nivel:
        // logs -> API -> controllers -> app -> raíz -> logs
        $logFile = __DIR__ . '/../../../../logs/app-' . $date . '.log';

        $logsArray = [];

        // 2. Verificamos si el archivo de hoy existe
        if (file_exists($logFile)) {
            // 3. Leemos las líneas del archivo
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Añadimos cada línea textual al array
                // (Si usáramos JsonFormatter en Logger.php, podríamos hacer json_decode aquí)
                $logsArray[] = $line;
            }
        } else {
            Logger::warning("No se encontró el archivo de log del día " . $date);
            http_response_code(404);
            echo json_encode([
                "date" => $date,
                "total" => 0,
                "logs" => []
            ]);
            return;
        }

        // 4. Retornamos en formato JSON
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            "date" => $date,
            "total" => count($logsArray),
            "logs" => array_reverse($logsArray) // Los enviamos al revés para que los más nuevos salgan arriba.
        ]);
        return;
    }
}