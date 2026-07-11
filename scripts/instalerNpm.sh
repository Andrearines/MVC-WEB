#!/bin/bash
set -e

# ============================================================
#  MVC-WEB v9.0.0 — Instalador de dependencias Node.js / Vite
# ============================================================

CYAN='\033[0;36m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${CYAN}"
echo "╔══════════════════════════════════════════╗"
echo "║  MVC-WEB v9.0.0 — Node.js Installer      ║"
echo "╚══════════════════════════════════════════╝"
echo -e "${NC}"

# ─── Verificar Node.js ────────────────────────────────────────
if ! command -v node &> /dev/null; then
    echo -e "${RED}❌ Node.js no está instalado.${NC}"
    echo "   Instálalo desde: https://nodejs.org  (se recomienda v18+)"
    exit 1
fi

NODE_VERSION=$(node -v | sed 's/v//' | cut -d. -f1)
if [ "$NODE_VERSION" -lt 16 ]; then
    echo -e "${RED}❌ Node.js v${NODE_VERSION} es demasiado antiguo. Se requiere v16+.${NC}"
    exit 1
fi
echo -e "${GREEN}✔ Node.js $(node -v) detectado${NC}"

# ─── Verificar npm ────────────────────────────────────────────
if ! command -v npm &> /dev/null; then
    echo -e "${RED}❌ npm no está disponible. Reinstala Node.js.${NC}"
    exit 1
fi
echo -e "${GREEN}✔ npm $(npm -v) detectado${NC}"

# ─── Verificar package.json ───────────────────────────────────
if [ ! -f "package.json" ]; then
    echo -e "${RED}❌ No se encontró package.json en la raíz del proyecto.${NC}"
    echo "   Asegúrate de ejecutar este script desde la raíz de MVC-WEB."
    exit 1
fi
echo -e "${GREEN}✔ package.json encontrado${NC}"

# ─── Instalar dependencias ────────────────────────────────────
echo ""
echo -e "${CYAN}=== Instalando dependencias npm... ===${NC}"
npm install

echo ""
echo -e "${GREEN}✔ Dependencias instaladas correctamente${NC}"

# ─── Verificar vite.config.js ─────────────────────────────────
if [ ! -f "vite.config.js" ]; then
    echo -e "${YELLOW}⚠ No se encontró vite.config.js. El build de Vite puede fallar.${NC}"
else
    echo -e "${GREEN}✔ vite.config.js encontrado${NC}"
fi

# ─── Compilar assets para producción (opcional) ───────────────
echo ""
read -p "¿Deseas compilar los assets de Vite ahora? (producción) [s/N]: " BUILD_NOW
if [[ "$BUILD_NOW" =~ ^[sS]$ ]]; then
    echo -e "${CYAN}=== Compilando assets con Vite... ===${NC}"
    npm run build
    echo -e "${GREEN}✔ Assets compilados en public/build/${NC}"
else
    echo -e "${YELLOW}ℹ  Omitiendo build. Ejecuta 'npm run dev' para el servidor de desarrollo.${NC}"
fi

echo ""
echo -e "${GREEN}╔══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  ✅ Frontend listo para MVC-WEB v9.0.0   ║${NC}"
echo -e "${GREEN}╚══════════════════════════════════════════╝${NC}"
echo ""
echo "  Comandos disponibles:"
echo "    npm run dev    → Servidor de desarrollo Vite (HMR)"
echo "    npm run build  → Compilar assets para producción"
echo "    npm run preview → Previsualizar build de producción"
echo ""
