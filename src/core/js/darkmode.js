document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    
    // Seleccionar botones por clase (tal como están definidos en layout.php)
    const btnDark = document.querySelector(".dark-mode-btn");
    const btnLight = document.querySelector(".ligth-mode-btn"); // "ligth" con la misma ortografía que en layout.php

    // Verificar que los objetos globales de ThemeStore estén disponibles
    if (typeof window.ThemeStore === 'undefined' || typeof window.ThemeActions === 'undefined') {
        console.error("ThemeStore o ThemeActions no están cargados.");
        return;
    }

    // 1. Suscribirse al Store de Temas para reaccionar a cualquier cambio
    window.ThemeStore.subscribe((state) => {
        actualizarInterfaz(state.theme);
    });

    // 2. Sincronizar la interfaz por primera vez al cargar la página con el estado inicial
    actualizarInterfaz(window.ThemeStore.getState().theme);

    // Función auxiliar para actualizar las clases del DOM según el tema activo
    function actualizarInterfaz(theme) {
        if (theme === "dark") {
            body.classList.add("dark");
            if (btnLight) btnLight.classList.remove("disable");
            if (btnDark) btnDark.classList.add("disable");
        } else {
            body.classList.remove("dark");
            if (btnDark) btnDark.classList.remove("disable");
            if (btnLight) btnLight.classList.add("disable");
        }
    }

    // 3. Asignar los eventos de click a los botones para ejecutar las acciones del Store
    if (btnDark) {
        btnDark.addEventListener("click", () => {
            window.ThemeActions.setTheme("dark");
        });
    }

    if (btnLight) {
        btnLight.addEventListener("click", () => {
            window.ThemeActions.setTheme("light");
        });
    }
});