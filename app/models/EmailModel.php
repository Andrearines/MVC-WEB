<?php

namespace models;

require_once __DIR__ . '/../../config/Environment.php';
\Environment::load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailModel
{

    private $mailer;
    private $config;

    public function __construct()
    {
        $this->config = [
            'host' => \Environment::get('MAIL_HOST', 'smtp.gmail.com'),
            'port' => \Environment::getInt('MAIL_PORT', 587),
            'username' => \Environment::get('MAIL_USERNAME', ''),
            'password' => \Environment::get('MAIL_PASSWORD', ''),
            'encryption' => \Environment::get('MAIL_ENCRYPTION', 'tls'),
            'from_name' => \Environment::get('APP_NAME', 'Web MVC'),
            'from_email' => \Environment::get('MAIL_USERNAME', '')
        ];

        if (\Environment::get('APP_DEBUG', false) === 'true' || \Environment::get('APP_DEBUG', false) === true) {
            $this->config['from_email'] = 'debug@mailtrap.io';
        }
        $this->inicializarMailer();
    }



    /**
     * Inicializa PHPMailer con la configuración del entorno
     */
    private function inicializarMailer()
    {
        try {
            $this->mailer = new PHPMailer(true);

            // Configuración del servidor
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['encryption'];
            $this->mailer->Port = $this->config['port'];

            // Configuración de caracteres
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

            // Configuración del remitente
            $this->mailer->setFrom($this->config['from_email'], $this->config['from_name']);
        } catch (\Exception $e) {
            throw new \Exception("Error al inicializar PHPMailer: " . $e->getMessage());
        }
    }

    /**
     * Envía un email simple
     */
    public function enviar($para, $asunto, $mensaje, $html = true)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($para);
            $this->mailer->Subject = $asunto;

            if ($html) {
                $this->mailer->isHTML(true);
                $this->mailer->Body = $mensaje;
                $this->mailer->AltBody = strip_tags($mensaje);
            } else {
                $this->mailer->isHTML(false);
                $this->mailer->Body = $mensaje;
            }

            return $this->mailer->send();
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email: " . $e->getMessage());
        }
    }

    /**
     * Envía un email con plantilla HTML
     */
    public function enviarConPlantilla($para, $asunto, $plantilla, $datos = [])
    {
        try {
            $html = $this->cargarPlantilla($plantilla, $datos);
            return $this->enviar($para, $asunto, $html, true);
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email con plantilla: " . $e->getMessage());
        }
    }

    /**
     * Envía un email con archivos adjuntos
     */
    public function enviarConAdjuntos($para, $asunto, $mensaje, $adjuntos = [], $html = true)
    {
        try {
            // Agregar archivos adjuntos
            foreach ($adjuntos as $adjunto) {
                if (file_exists($adjunto)) {
                    $this->mailer->addAttachment($adjunto);
                }
            }

            return $this->enviar($para, $asunto, $mensaje, $html);
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email con adjuntos: " . $e->getMessage());
        }
    }

    /**
     * Envía un email a múltiples destinatarios
     */
    public function enviarMultiple($destinatarios, $asunto, $mensaje, $html = true)
    {
        try {
            $this->mailer->clearAddresses();

            foreach ($destinatarios as $destinatario) {
                $this->mailer->addAddress($destinatario);
            }

            $this->mailer->Subject = $asunto;

            if ($html) {
                $this->mailer->isHTML(true);
                $this->mailer->Body = $mensaje;
                $this->mailer->AltBody = strip_tags($mensaje);
            } else {
                $this->mailer->isHTML(false);
                $this->mailer->Body = $mensaje;
            }

            return $this->mailer->send();
        } catch (\Exception $e) {
            throw new \Exception("Error al enviar email múltiple: " . $e->getMessage());
        }
    }

    /**
     * Carga una plantilla HTML y reemplaza variables
     */
    private function cargarPlantilla($plantilla, $datos = [])
    {
        $rutaPlantilla = __DIR__ . "/../views/emails/{$plantilla}.php";

        if (!file_exists($rutaPlantilla)) {
            throw new \Exception("Plantilla de email no encontrada: {$plantilla}");
        }

        // Extraer variables para usar en la plantilla
        extract($datos);

        ob_start();
        include $rutaPlantilla;
        return ob_get_clean();
    }

    /**
     * Verifica la configuración del email
     */
    public function verificarConfiguracion()
    {
        $errores = [];

        if (empty($this->config['username'])) {
            $errores[] = "MAIL_USERNAME no está configurado";
        }

        if (empty($this->config['password'])) {
            $errores[] = "MAIL_PASSWORD no está configurado";
        }

        if (empty($this->config['host'])) {
            $errores[] = "MAIL_HOST no está configurado";
        }

        return $errores;
    }

    /**
     * Obtiene información de configuración (sin contraseña)
     */
    public function getConfiguracion()
    {
        $config = $this->config;
        unset($config['password']); // No mostrar contraseña
        return $config;
    }

    /**
     * Envía email de bienvenida
     */
    public function enviarBienvenida($email, $nombre, $token)
    {
        $datos = [
            'nombre' => $nombre,
            'app_name' => \Environment::get('APP_NAME', 'Web MVC'),
            'app_url' => \Environment::get('APP_URL', 'http://localhost'),
            'token' => $token

        ];

        return $this->enviarConPlantilla($email, 'Bienvenido a ' . $datos['app_name'], 'bienvenida', $datos);
    }

    /**
     * Envía email de recuperación de contraseña
     */
    public function enviarRecuperacionPassword($email, $token, $nombre = '')
    {
        $datos = [
            'nombre' => $nombre,
            'token' => $token,
            'app_name' => \Environment::get('APP_NAME', 'Web MVC'),
            'app_url' => \Environment::get('APP_URL', 'http://localhost')
        ];

        return $this->enviarConPlantilla($email, 'Recuperación de contraseña', 'recuperacion_password', $datos);
    }

    /**
     * Envía email de notificación
     */
    public function enviarNotificacion($email, $titulo, $mensaje, $tipo = 'info')
    {
        $datos = [
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'app_name' => \Environment::get('APP_NAME', 'Web MVC')
        ];

        return $this->enviarConPlantilla($email, $titulo, 'notificacion', $datos);
    }
}
