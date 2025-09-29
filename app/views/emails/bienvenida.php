<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a <?php echo $app_name; ?></title>
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .welcome-title {
            color: #007bff;
            font-size: 28px;
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
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="welcome-title">¡Bienvenido a <?php echo $app_name; ?>!</h1>
        </div>
        
        <div class="content">
            <h2>Hola <?php echo htmlspecialchars($nombre); ?>,</h2>
            
            <p>Nos complace darte la bienvenida a <?php echo $app_name; ?>. Tu cuenta ha sido creada exitosamente.</p>
            
           
            
            <p>Para comenzar a usar tu cuenta, haz clic en el siguiente botón:</p>
            
            <div style="text-align: center;">
                <a href="<?php echo $app_url; ?>?token=<?php echo $token; ?>" class="btn">Acceder a mi cuenta</a>
            </div>
            
            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
        </div>
        
        <div class="footer">
            <p>Este email fue enviado desde <?php echo $app_name; ?></p>
            <p>Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo $app_name; ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 