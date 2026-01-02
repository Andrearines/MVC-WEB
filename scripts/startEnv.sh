#!/bin/bash
echo "=== Configuración de Variables de Entorno ==="
echo ""

# Verificar si existe .env, si no, crear desde el ejemplo
if [ ! -f .env ]; then
    if [ -f env.ejemplo ]; then
        echo "Creando .env desde env.ejemplo..."
        cp env.ejemplo .env
    else
        echo "Error: No se encuentra env.ejemplo"
        exit 1
    fi
fi

# Función para solicitar entrada del usuario
solicitar_valor() {
    local nombre_var=$1
    local valor_actual=$2
    local descripcion=$3

    echo ""
    echo "--- $descripcion ---"
    if [ -n "$valor_actual" ]; then
        echo "Valor actual: [$valor_actual]"
    else
        echo "Valor actual: [vacío]"
    fi
    echo -n "Nuevo valor: "
    read -r nuevo_valor

    if [ -n "$nuevo_valor" ]; then
        echo "✓ Se usará: $nuevo_valor"
        echo "$nuevo_valor"
    else
        if [ -n "$valor_actual" ]; then
            echo "✓ Se mantiene: $valor_actual"
        else
            echo "⚠ Campo vacío - deberás configurarlo manualmente"
        fi
        echo "$valor_actual"
    fi
}

# Obtener valores actuales desde .env
DB_HOST_ACTUAL=$(grep "DB_HOST=" .env | cut -d'=' -f2)
DB_USER_ACTUAL=$(grep "DB_USER=" .env | cut -d'=' -f2)
DB_PASSWORD_ACTUAL=$(grep "DB_PASSWORD=" .env | cut -d'=' -f2)
DB_NAME_ACTUAL=$(grep "DB_NAME=" .env | cut -d'=' -f2)
JWT_KEY_ACTUAL=$(grep "JWT_KEY=" .env | cut -d'=' -f2)
APP_NAME_ACTUAL=$(grep "APP_NAME=" .env | cut -d'=' -f2 | tr -d '"')
APP_URL_ACTUAL=$(grep "APP_URL=" .env | cut -d'=' -f2 | tr -d '"')

# Solicitar nuevos valores
echo "Por favor, ingresa los valores para las variables de entorno:"
echo ""

echo "=== CONFIGURACIÓN DE BASE DE DATOS ==="
echo ""

DB_HOST_NUEVO=$(solicitar_valor "DB_HOST" "$DB_HOST_ACTUAL" "HOST:")
DB_USER_NUEVO=$(solicitar_valor "DB_USER" "$DB_USER_ACTUAL" "USUARIO:")
DB_PASSWORD_NUEVO=$(solicitar_valor "DB_PASSWORD" "$DB_PASSWORD_ACTUAL" "CONTRASEÑA:")
DB_NAME_NUEVO=$(solicitar_valor "DB_NAME" "$DB_NAME_ACTUAL" "NOMBRE DE LA BASE DE DATOS:")

echo ""
echo "=== CONFIGURACIÓN DE APLICACIÓN ==="

# Para JWT_KEY, generar una nueva si está vacía
if [ -z "$JWT_KEY_ACTUAL" ] || [ "$JWT_KEY_ACTUAL" = "mi_clave_secreta_super_segura_1234567890" ]; then
    echo ""
    echo "CLAVE JWT (generando automáticamente por seguridad)..."
    JWT_KEY_NUEVO=$(openssl rand -base64 32 2>/dev/null || echo "clave_secreta_generada_$(date +%s)")
    echo "Nueva clave generada: ${JWT_KEY_NUEVO:0:10}..."
else
    echo ""
    JWT_KEY_NUEVO=$(solicitar_valor "JWT_KEY" "$JWT_KEY_ACTUAL" "CLAVE JWT (deja vacío para generar nueva):")
    if [ -z "$JWT_KEY_NUEVO" ]; then
        echo "Generando nueva clave JWT..."
        JWT_KEY_NUEVO=$(openssl rand -base64 32 2>/dev/null || echo "clave_secreta_generada_$(date +%s)")
        echo "Nueva clave generada: ${JWT_KEY_NUEVO:0:10}..."
    fi
fi

echo ""
APP_NAME_NUEVO=$(solicitar_valor "APP_NAME" "$APP_NAME_ACTUAL" "NOMBRE DE LA APLICACIÓN:")
echo ""
APP_URL_NUEVO=$(solicitar_valor "APP_URL" "$APP_URL_ACTUAL" "URL DE LA APLICACIÓN:")

# Actualizar archivo .env
echo ""
echo "Actualizando archivo .env..."

# Backup del archivo original
cp .env .env.backup

# Actualizar variables
sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST_NUEVO|" .env
sed -i "s|^DB_USER=.*|DB_USER=$DB_USER_NUEVO|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD_NUEVO|" .env
sed -i "s|^DB_NAME=.*|DB_NAME=$DB_NAME_NUEVO|" .env
sed -i "s|^JWT_KEY=.*|JWT_KEY=$JWT_KEY_NUEVO|" .env
sed -i "s|^APP_NAME=.*|APP_NAME=\"$APP_NAME_NUEVO\"|" .env
sed -i "s|^APP_URL=.*|APP_URL=$APP_URL_NUEVO|" .env

echo " Variables de entorno actualizadas correctamente"
echo ""

# Cargar y verificar variables
set -a
source .env
set +a

echo " Resumen de la configuración:"
echo "   DB_HOST: $DB_HOST"
echo "   DB_USER: $DB_USER"
echo "   DB_NAME: $DB_NAME"
echo "   APP_NAME: $APP_NAME"
echo "   APP_URL: $APP_URL"
echo "   JWT_KEY: ${JWT_KEY:0:10}... (longitud: ${#JWT_KEY})"
echo ""

# Verificación final
if [ -z "$DB_HOST" ] || [ -z "$DB_USER" ] || [ -z "$DB_NAME" ] || [ -z "$JWT_KEY" ]; then
    echo " Error: Alguna variable de entorno importante no se configuró correctamente"
    echo " Restaurando backup..."
    mv .env.backup .env
    exit 1
fi

echo " Configuración completada exitosamente!"
echo " Backup guardado como .env.backup"
echo " Entorno listo para usar"
echo ""
