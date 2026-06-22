<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - <?php echo $app_name; ?></title>
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
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
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
        .notification-box {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .notification-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .notification-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .notification-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .notification-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn-info {
            background-color: #17a2b8;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 class="title"><?php echo htmlspecialchars($titulo); ?></h1>
        </div>
        
        <div class="content">
            <div class="notification-box notification-<?php echo $tipo; ?>">
                <p><?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
            </div>
            
            <p>Esta notificación fue enviada desde <?php echo $app_name; ?>.</p>
            
            <p>Si tienes alguna pregunta sobre esta notificación, no dudes en contactarnos.</p>
        </div>
        
        <div class="footer">
            <p>Este email fue enviado desde <?php echo $app_name; ?></p>
            <p>Puedes gestionar tus notificaciones desde tu panel de usuario.</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo $app_name; ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 