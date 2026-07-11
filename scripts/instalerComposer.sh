#!/bin/bash
set -e

# ============================================================
#  MVC-WEB v9.0.0 — Instalador de dependencias Composer / PHP
# ============================================================

CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${CYAN}"
echo "╔══════════════════════════════════════════╗"
echo "║  MVC-WEB v9.0.0 — Composer Installer     ║"
echo "╚══════════════════════════════════════════╝"
echo -e "${NC}"

# ─── Detectar Composer (global o local) ───────────────────────
COMPOSER_CMD=""
if command -v composer &> /dev/null; then
    COMPOSER_CMD="composer"
elif [ -f "./composer" ]; then
    COMPOSER_CMD="php ./composer"
elif [ -f "./composer.phar" ]; then
    COMPOSER_CMD="php ./composer.phar"
else
    echo -e "${YELLOW}⚠ Composer no encontrado. Intentando descarga local...${NC}"
    if command -v php &> /dev/null && command -v curl &> /dev/null; then
        curl -sS https://getcomposer.org/installer | php -- --filename=composer
        COMPOSER_CMD="php ./composer"
        echo -e "${GREEN}✔ Composer descargado localmente${NC}"
    else
        echo -e "${RED}❌ No se puede instalar Composer automáticamente.${NC}"
        echo "   Instálalo manualmente: https://getcomposer.org/download/"
        exit 1
    fi
fi

echo -e "${GREEN}✔ Composer detectado: $(${COMPOSER_CMD} --version 2>&1 | head -1)${NC}"

# ─── Verificar PHP ────────────────────────────────────────────
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP no está instalado.${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo -e "${GREEN}✔ PHP ${PHP_VERSION} detectado${NC}"

# ─── Verificar composer.json ─────────────────────────────────
if [ ! -f "composer.json" ]; then
    echo -e "${RED}❌ No se encontró composer.json en la raíz del proyecto.${NC}"
    echo "   Asegúrate de ejecutar este script desde la raíz de MVC-WEB."
    exit 1
fi
echo -e "${GREEN}✔ composer.json encontrado${NC}"

# ─── Instalar dependencias ────────────────────────────────────
echo ""
echo -e "${CYAN}=== Instalando dependencias PHP... ===${NC}"
${COMPOSER_CMD} install --no-interaction --prefer-dist

echo ""
echo -e "${CYAN}=== Generando Autoload optimizado (PSR-4)... ===${NC}"
${COMPOSER_CMD} dump-autoload --optimize

echo ""
echo -e "${GREEN}✔ Autoload generado para los siguientes namespaces:${NC}"
echo "    • app\\Core\\        → app/Core/"
echo "    • app\\controllers\\ → app/controllers/"
echo "    • app\\models\\      → app/models/"
echo "    • app\\services\\    → app/services/"
echo "    • modules\\         → modules/"
echo "    • plugins\\         → plugins/"

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  ✅ Backend listo para MVC-WEB v9.0.0    ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""
