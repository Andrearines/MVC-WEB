<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - <?php echo $app_name; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #c82333;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .token-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            font-family: monospace;
            font-size: 18px;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="title">Recuperación de Contraseña</h1>
        </div>
        
        <div class="content">
            <h2>Hola<?php echo $nombre ? ', ' . htmlspecialchars($nombre) : ''; ?>,</h2>
            
            <p>Has solicitado restablecer tu contraseña en <?php echo $app_name; ?>.</p>
            
            <p>Para completar el proceso de recuperación, utiliza el siguiente código de verificación:</p>
            
            <div class="token-box">
                <strong><?php echo htmlspecialchars($token); ?></strong>
            </div>
            
            <p>O haz clic en el siguiente enlace para restablecer tu contraseña directamente:</p>
            
            <div style="text-align: center;">
                <a href="<?php echo $app_url; ?>/reset-password?token=<?php echo urlencode($token); ?>" class="btn">Restablecer Contraseña</a>
            </div>
            
            <div class="warning">
                <strong>⚠️ Importante:</strong>
                <ul>
                    <li>Este enlace es válido por 24 horas</li>
                    <li>Si no solicitaste este cambio, puedes ignorar este email</li>
                    <li>Nunca compartas este código con nadie</li>
                </ul>
            </div>
            
            <p>Si tienes problemas para acceder a tu cuenta, contacta con nuestro equipo de soporte.</p>
        </div>
        
        <div class="footer">
            <p>Este email fue enviado desde <?php echo $app_name; ?></p>
            <p>Por seguridad, este enlace expirará en 24 horas.</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo $app_name; ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 