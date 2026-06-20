/**
 * Clase base para el manejo del estado global (Store Pattern).
 * Permite suscribir componentes a cambios en el estado y persistir datos en localStorage.
 */
class Store {
    /**
     * @param {Object} initialState - Estado inicial de la aplicación.
     * @param {string|null} storageKey - Clave opcional para persistir el estado en localStorage.
     */
    constructor(initialState = {}, storageKey = null) {
        this.storageKey = storageKey;
        this.listeners = [];

        // Inicializar el estado (recuperándolo del disco si existe la clave)
        if (this.storageKey) {
            try {
                const savedState = localStorage.getItem(this.storageKey);
                this.state = savedState ? JSON.parse(savedState) : initialState;
            } catch (e) {
                console.error("Error al inicializar el Store desde localStorage:", e);
                this.state = initialState;
            }
        } else {
            this.state = initialState;
        }
    }

    /**
     * Retorna el estado actual.
     * @returns {Object}
     */
    getState() {
        return this.state;
    }

    /**
     * Actualiza una o más propiedades del estado y notifica a los suscriptores.
     * @param {Object} newState - Nuevas propiedades a fusionar con el estado actual.
     */
    setState(newState) {
        // Fusionar el estado antiguo con el nuevo
        this.state = { ...this.state, ...newState };

        // Guardar en el disco local si se definió una clave
        if (this.storageKey) {
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(this.state));
            } catch (e) {
                console.error("Error al guardar el estado en localStorage:", e);
            }
        }

        // Ejecutar todos los suscriptores interesados en los cambios
        this.listeners.forEach(listener => {
            try {
                listener(this.state);
            } catch (e) {
                console.error("Error en uno de los suscriptores del Store:", e);
            }
        });
    }

    /**
     * Registra una función para ejecutarse cada vez que cambie el estado.
     * @param {Function} listener - Callback que recibe el estado actualizado.
     * @returns {Function} Función para cancelar la suscripción (unsubscribe).
     */
    subscribe(listener) {
        if (typeof listener !== 'function') {
            throw new Error('El suscriptor (listener) debe ser una función.');
        }

        this.listeners.push(listener);

        // Retorna una función para limpiar la suscripción cuando ya no sea necesaria
        return () => {
            this.listeners = this.listeners.filter(l => l !== listener);
        };
    }
}

// Exponer en el objeto global del navegador
if (typeof window !== 'undefined') {
    window.Store = Store;
}

// Exportar para soporte de módulos ES6/Node en el futuro
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Store;
}
