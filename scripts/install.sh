#!/bin/bash

# ============================================================
#  MVC-WEB v9.0.0 — Script de Instalación Maestro
#  Orquesta todo el proceso de setup: env → composer → npm
# ============================================================

set -e

CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

print_banner() {
echo -e "${CYAN}${BOLD}"
cat << 'EOF'
  __  ____     _____ ______        ________ ____
 |  \/  \ \   / / __|  _ \ \      / / ____| __ )
 | |\/| |\ \ / / |  | |_) \ \ /\ / /|  _| |  _ \
 | |  | | \ V /| |__|  _ < \ V  V / | |___| |_) |
 |_|  |_|  \_/  \____|_| \_\ \_/\_/  |_____|____/

          Framework PHP v9.0.0 — Installer
EOF
echo -e "${NC}"
}

step() {
    echo -e "${CYAN}${BOLD}▶ $1${NC}"
}

ok() {
    echo -e "${GREEN}✔ $1${NC}"
}

warn() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

fail() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

# ─── Banner ──────────────────────────────────────────────────
print_banner

# ─── Verificar que estamos en la raíz del proyecto ───────────
cd "$ROOT_DIR"

if [ ! -f "composer.json" ] || [ ! -f "package.json" ]; then
    fail "No se encontró composer.json o package.json. Ejecuta este script desde la raíz de MVC-WEB."
fi

ok "Directorio de proyecto detectado: $ROOT_DIR"
echo ""

# ─── PASO 1: Variables de entorno ─────────────────────────────
step "PASO 1/3 — Configurando variables de entorno..."
echo ""

if [ -f ".env" ]; then
    warn ".env ya existe. ¿Deseas reconfigurarlo?"
    read -p "  [s/N]: " RECONFIG_ENV
    if [[ "$RECONFIG_ENV" =~ ^[sS]$ ]]; then
        bash "$SCRIPT_DIR/startEnv.sh"
    else
        ok ".env existente conservado."
    fi
else
    bash "$SCRIPT_DIR/startEnv.sh"
fi

echo ""

# ─── PASO 2: Dependencias PHP (Composer) ──────────────────────
step "PASO 2/3 — Instalando dependencias PHP con Composer..."
echo ""
bash "$SCRIPT_DIR/instalerComposer.sh"
echo ""

# ─── PASO 3: Dependencias Node.js (Vite) ──────────────────────
step "PASO 3/3 — Instalando dependencias Node.js / Vite..."
echo ""
bash "$SCRIPT_DIR/instalerNpm.sh"
echo ""

# ─── Resumen final ────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}╔══════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}${BOLD}║   🚀 MVC-WEB v9.0.0 instalado correctamente          ║${NC}"
echo -e "${GREEN}${BOLD}╚══════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}Próximos pasos:${NC}"
echo "    1. Configura tu servidor web (Apache/Nginx) apuntando a public/"
echo "    2. Importa el schema SQL desde db/ en tu base de datos"
echo "    3. Ejecuta 'npm run dev' para el servidor de desarrollo Vite"
echo "    4. Accede a tu aplicación en la URL configurada en APP_URL"
echo ""
echo -e "  ${BOLD}Comandos útiles:${NC}"
echo "    npm run dev         → Vite dev server con HMR"
echo "    npm run build       → Build de producción"
echo "    php composer dump-autoload → Regenerar autoload"
echo ""
