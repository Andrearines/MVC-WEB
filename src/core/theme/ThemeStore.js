/**
 * Implementación del control de Temas (Claro/Oscuro) usando la clase global Store.
 * Este script inicializa el estado del tema y expone sus acciones globalmente.
 */
(function () {
    if (typeof window.Store === 'undefined') {
        console.error('La clase Store no está cargada. Asegúrate de incluir Store.js antes de este archivo.');
        return;
    }

    // Inicializamos el Store con el tema claro por defecto y persistido bajo 'app_theme'
    const ThemeStore = new window.Store({
        theme: 'light' // Puede ser 'light' o 'dark'
    }, 'app_theme');

    // Acciones específicas para el control del tema
    const ThemeActions = {
        /**
         * Cambia el tema a un valor específico.
         * @param {string} theme - 'light' o 'dark'
         */
        setTheme(theme) {
            if (theme !== 'light' && theme !== 'dark') return;
            ThemeStore.setState({ theme });
        },

        /**
         * Alterna el tema actual entre claro y oscuro.
         */
        toggleTheme() {
            const { theme } = ThemeStore.getState();
            const nuevoTema = theme === 'light' ? 'dark' : 'light';
            ThemeStore.setState({ theme: nuevoTema });
        }
    };

    // Exponer globalmente en window para que cualquier script pueda importarlos/usarlos
    window.ThemeStore = ThemeStore;
    window.ThemeActions = ThemeActions;
})();
