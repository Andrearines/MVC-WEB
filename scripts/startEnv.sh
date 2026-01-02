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

# ===============================
# FUNCIÓN PARA SOLICITAR VALORES
# ===============================
solicitar_valor() {
    local nombre_var=$1
    local valor_actual=$2
    local descripcion=$3

    echo "" >&2
    echo "--- $descripcion ---" >&2

    if [ -n "$valor_actual" ]; then
        echo "Valor actual: [$valor_actual]" >&2
    else
        echo "Valor actual: [vacío]" >&2
    fi

    printf "Nuevo valor: " >&2
    read -r nuevo_valor

    if [ -n "$nuevo_valor" ]; then
        echo "✓ Se usará: $nuevo_valor" >&2
        echo "$nuevo_valor"
    else
        if [ -n "$valor_actual" ]; then
            echo "✓ Se mantiene: $valor_actual" >&2
        else
            echo "⚠ Campo vacío - deberás configurarlo manualmente" >&2
        fi
        echo "$valor_actual"
    fi
}

# ===============================
# LEER VALORES ACTUALES
# ===============================
DB_HOST_ACTUAL=$(grep "^DB_HOST=" .env | cut -d'=' -f2)
DB_USER_ACTUAL=$(grep "^DB_USER=" .env | cut -d'=' -f2)
DB_PASSWORD_ACTUAL=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)
DB_NAME_ACTUAL=$(grep "^DB_NAME=" .env | cut -d'=' -f2)
JWT_KEY_ACTUAL=$(grep "^JWT_KEY=" .env | cut -d'=' -f2)
APP_NAME_ACTUAL=$(grep "^APP_NAME=" .env | cut -d'=' -f2 | tr -d '"')
APP_URL_ACTUAL=$(grep "^APP_URL=" .env | cut -d'=' -f2 | tr -d '"')

echo "Por favor, ingresa los valores para las variables de entorno:"
echo ""

# ===============================
# BASE DE DATOS
# ===============================
echo "=== CONFIGURACIÓN DE BASE DE DATOS ==="

DB_HOST_NUEVO=$(solicitar_valor "DB_HOST" "$DB_HOST_ACTUAL" "HOST")
DB_USER_NUEVO=$(solicitar_valor "DB_USER" "$DB_USER_ACTUAL" "USUARIO")
DB_PASSWORD_NUEVO=$(solicitar_valor "DB_PASSWORD" "$DB_PASSWORD_ACTUAL" "CONTRASEÑA")
DB_NAME_NUEVO=$(solicitar_valor "DB_NAME" "$DB_NAME_ACTUAL" "NOMBRE DE LA BASE DE DATOS")

# ===============================
# APLICACIÓN
# ===============================
echo ""
echo "=== CONFIGURACIÓN DE APLICACIÓN ==="

if [ -z "$JWT_KEY_ACTUAL" ] || [ "$JWT_KEY_ACTUAL" = "mi_clave_secreta_super_segura_1234567890" ]; then
    echo "" >&2
    echo "Generando clave JWT automáticamente..." >&2
    JWT_KEY_NUEVO=$(openssl rand -base64 32 2>/dev/null || echo "jwt_$(date +%s)")
else
    JWT_KEY_NUEVO=$(solicitar_valor "JWT_KEY" "$JWT_KEY_ACTUAL" "CLAVE JWT (vacío = regenerar)")
    if [ -z "$JWT_KEY_NUEVO" ]; then
        echo "Regenerando clave JWT..." >&2
        JWT_KEY_NUEVO=$(openssl rand -base64 32 2>/dev/null || echo "jwt_$(date +%s)")
    fi
fi

APP_NAME_NUEVO=$(solicitar_valor "APP_NAME" "$APP_NAME_ACTUAL" "NOMBRE DE LA APLICACIÓN")
APP_URL_NUEVO=$(solicitar_valor "APP_URL" "$APP_URL_ACTUAL" "URL DE LA APLICACIÓN")

# ===============================
# ACTUALIZAR .env
# ===============================
echo ""
echo "Actualizando archivo .env..."

cp .env .env.backup

sed -i "s|^DB_HOST=.*|DB_HOST=$DB_HOST_NUEVO|" .env
sed -i "s|^DB_USER=.*|DB_USER=$DB_USER_NUEVO|" .env
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD_NUEVO|" .env
sed -i "s|^DB_NAME=.*|DB_NAME=$DB_NAME_NUEVO|" .env
sed -i "s|^JWT_KEY=.*|JWT_KEY=$JWT_KEY_NUEVO|" .env
sed -i "s|^APP_NAME=.*|APP_NAME=\"$APP_NAME_NUEVO\"|" .env
sed -i "s|^APP_URL=.*|APP_URL=$APP_URL_NUEVO|" .env

# ===============================
# VALIDACIÓN FINAL
# ===============================
set -a
source .env
set +a

echo ""
echo "Resumen de configuración:"
echo "  DB_HOST: $DB_HOST"
echo "  DB_USER: $DB_USER"
echo "  DB_NAME: $DB_NAME"
echo "  APP_NAME: $APP_NAME"
echo "  APP_URL: $APP_URL"
echo "  JWT_KEY: ${JWT_KEY:0:10}... (len=${#JWT_KEY})"

if [ -z "$DB_HOST" ] || [ -z "$DB_USER" ] || [ -z "$DB_NAME" ] || [ -z "$JWT_KEY" ]; then
    echo "Error crítico: variables obligatorias incompletas"
    echo "Restaurando backup..."
    mv .env.backup .env
    exit 1
fi

echo ""
echo "Configuración completada exitosamente"
echo "Backup guardado en .env.backup"
echo "Entorno listo para producción"
echo ""
