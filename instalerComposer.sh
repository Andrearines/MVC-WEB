
#!/bin/bash
echo "Inicializando proyecto Composer..."
composer init -n
echo "Código de salida: $?"
echo "Instalando dependencias de Composer..."

composer require phpmailer/phpmailer
echo "Código de salida: $?"
composer require firebase/php-jwt
echo "Código de salida: $?"
composer require intervention/image
echo "Código de salida: $?"

echo "Instalación completada!"
echo "Código de salida final: $?"
echo "Script completado con éxito"
echo "Proyecto Composer listo para usar"
composer update
echo "Actualización de dependencias completada!"
echo "Verificando instalación..."
composer show
echo "Instalación y configuración completadas exitosamente!"
echo "¡Todo listo! Puedes comenzar a desarrollar tu aplicación MVC con Composer."



