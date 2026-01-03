#!/bin/bash
set -e

echo "=== Inicializando proyecto Composer ==="

# Inicializar Composer
composer init -n
echo "✔ Composer init completado"

echo ""
echo "=== Instalando dependencias ==="

composer require phpmailer/phpmailer
echo "✔ PHPMailer instalado"

composer require firebase/php-jwt
echo "✔ Firebase JWT instalado"

composer require intervention/image
echo "✔ Intervention Image instalado"

echo ""
echo "=== Actualizando dependencias ==="
composer update
echo "✔ Dependencias actualizadas"

echo ""
echo "=== Verificando instalación ==="
composer show

echo ""
echo "=== Proyecto Composer listo para usar ==="

echo ""
echo "=== Autoload PSR-4 recomendado ==="
cat <<'EOF'
{
  "autoload": {
    "psr-4": {
      "Models\\": "./app/models/",
      "MVC\\": "./router/",
      "Controllers\\API\\": "./app/controllers/API/",
      "Controllers\\": "./app/controllers/"
    }
  }
}
EOF

echo ""
echo "✔ Configuración de autoload mostrada"
echo "✔ Recuerda crear las carpetas correspondientes"
echo "✔ Ejecuta: composer dump-autoload"
echo ""
echo "🚀 Proyecto MVC listo para desarrollo"


