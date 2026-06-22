<?php

namespace services;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\JsonFormatter;

/**
 * Clase Logger
 * Wrapper sobre Monolog para proveer un sistema avanzado de logging
 * centralizado para todo el proyecto.
 */
class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Inicializa o devuelve la instancia del logger estática.
     */
    public static function getLogger(): MonologLogger
    {
        if (self::$instance === null) {
            // Se le asigna un nombre al canal (el nombre del framework por ejemplo)
            self::$instance = new MonologLogger('mvc_web');

            $logPath = __DIR__ . '/../../logs/app.log';

            // Formato de línea para los logs
            // [Fecha-Hora] Canal.NIVEL: Mensaje {"contexto": "array"} {"extra": "array"}
            $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $formatter = new LineFormatter($output, "Y-m-d H:i:s");

            // Handler Principal: RotatingFileHandler
            // Esto creará un archivo nuevo cada día: app-YYYY-MM-DD.log
            // Manteniendo los archivos de los últimos 30 días para evitar saturar el disco.
            $rotatingHandler = new RotatingFileHandler(
                $logPath, 
                30, 
                MonologLogger::DEBUG // nivel mínimo esperado
            );
            $rotatingHandler->setFormatter($formatter);

            // Añadir el handler a la instancia de Monolog
            self::$instance->pushHandler($rotatingHandler);
        }

        return self::$instance;
    }

    /**
     * Registra un mensaje informativo. Útil para flujo feliz.
     */
    public static function info(string $message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }

    /**
     * Registra un mensaje de debug. Muy útil en etapa de desarrollo local.
     */
    public static function debug(string $message, array $context = []): void
    {
        self::getLogger()->debug($message, $context);
    }

    /**
     * Registra un aviso (anormalidad pero sin ser error grave).
     */
    public static function warning(string $message, array $context = []): void
    {
        self::getLogger()->warning($message, $context);
    }

    /**
     * Registra un error grave (excepción capturada, fallo BD).
     */
    public static function error(string $message, array $context = []): void
    {
        self::getLogger()->error($message, $context);
    }

    /**
     * Registra eventos críticos (el sistema no puede iniciar, falta archivo de config vital).
     */
    public static function critical(string $message, array $context = []): void
    {
        self::getLogger()->critical($message, $context);
    }
}
