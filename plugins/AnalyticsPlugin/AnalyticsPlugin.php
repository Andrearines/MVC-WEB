<?php

namespace plugins\AnalyticsPlugin;

use app\Core\Plugin;
use app\Core\Event;

class AnalyticsPlugin extends Plugin
{
    public function init()
    {
        // Register an action hook on app.boot
        Event::on('app.boot', function($app) {
            $logDir = $app->basePath . 'logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . '/analytics.log';
            $time = date('Y-m-d H:i:s');
            $path = $_SERVER['PATH_INFO'] ?? '/';
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            
            $message = "[$time] [Plugin: Analytics] Request: $method $path\n";
            file_put_contents($logFile, $message, FILE_APPEND);
        });
    }
}
