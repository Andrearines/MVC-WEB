#!/bin/bash
set -e

echo "=== Inicializando proyecto Composer ==="

if [ -f "composer.json" ]; then
    echo "✔ composer.json detectado. Instalando dependencias..."
    composer install
else
    echo "❌ No se encontró composer.json en la raíz."
    exit 1
fi

echo ""
echo "=== Generando Autoload ==="
composer dump-autoload
echo "✔ Autoload generado con éxito."
echo "🚀 Proyecto MVC v9.0.0 listo"
