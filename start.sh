#!/bin/bash
echo "Iniciando aplicación..."

echo "Instalando dependencias con Composer..."
chmod +x ./scripts/instalerComposer.sh
./scripts/instalerComposer.sh

echo "Instalando dependencias con NPM por miestras se configura Composer..."
chmod +x ./scripts/instalerNpm.sh
./scripts/instalerNpm.sh

echo "Configurando variables de entorno..."

chmod +x ./scripts/startEnv.sh
./scripts/startEnv.sh

if [ $? -eq 0 ]; then
    echo "Variables de entorno configuradas correctamente"
else
    echo "Error al configurar variables de entorno"
    exit 1
fi

echo "usa npm run dev para iniciar la compilación"
echo "Actualizando dependencias de Composer..."
echo "Actualización de Composer completada."
composer dump-autoload
echo "Autoload generado exitosamente."
echo "Iniciando servidor..."
chmod +x startServer.sh
./startServer.sh

