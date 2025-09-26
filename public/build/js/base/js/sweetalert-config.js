// SweetAlert2 centralized configuration and helpers for MVC-WEB
// Requires SweetAlert2 CDN loaded before this script
// <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

(function () {
  if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 no está cargado. Asegúrate de incluir el CDN antes de este archivo.');
    return;
  }

  // Configuración centralizada
  const SweetAlertConfig = {
    defaults: {
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      allowOutsideClick: false,
      allowEscapeKey: false
    },
    colors: {
      primary: '#3085d6',
      danger: '#d33',
      warning: '#f39c12',
      success: '#28a745',
      info: '#17a2b8'
    },
    utils: {
      isValidEmail: (email) => {
        const re = /^(?:[a-zA-Z0-9_'^&\/+-])+(?:\.(?:[a-zA-Z0-9_'^&\/+-])+)*@(?:(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,})$/;
        return re.test(String(email).toLowerCase());
      },
      validateForm: (campos) => {
        const errores = [];
        campos.forEach(campo => {
          const el = document.getElementById(campo.id);
          if (!el) {
            errores.push(`Campo no encontrado: ${campo.id}`);
            return;
          }
          const valor = (el.value || '').toString().trim();
          if (campo.requerido && !valor) {
            errores.push(`El campo ${campo.nombre} es requerido`);
          }
          if (campo.tipo === 'email' && valor && !SweetAlertConfig.utils.isValidEmail(valor)) {
            errores.push(`El email ${valor} no es válido`);
          }
          if (campo.minLength && valor.length < campo.minLength) {
            errores.push(`${campo.nombre} debe tener al menos ${campo.minLength} caracteres`);
          }
        });

        if (errores.length > 0) {
          Swal.fire({
            title: 'Errores de Validación',
            html: errores.map(e => `<p>• ${e}</p>`).join(''),
            icon: 'warning',
            confirmButtonColor: SweetAlertConfig.colors.warning
          });
          return false;
        }
        return true;
      },
      handleError: (error, titulo = 'Error') => {
        console.error('Error:', error);
        let mensaje = 'Ha ocurrido un error inesperado';
        if (error && typeof error === 'object') {
          if (error.response && (error.response.data || error.response.statusText)) {
            mensaje = error.response.data?.message || error.response.statusText || mensaje;
          } else if (error.message) {
            mensaje = error.message;
          }
        }
        Swal.fire({
          title: titulo,
          text: mensaje,
          icon: 'error',
          confirmButtonColor: SweetAlertConfig.colors.danger
        });
      }
    }
  };

  // Aplicar defaults globales
  if (typeof Swal.setDefaults === 'function') {
    Swal.setDefaults(SweetAlertConfig.defaults);
  }

  // Toast preconfigurado
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  // Helpers de estado
  function showLoading(texto = 'Cargando...') {
    return Swal.fire({
      title: texto,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
  }

  function showSuccess(titulo, mensaje, onClose) {
    return Swal.fire({
      title: titulo,
      text: mensaje,
      icon: 'success',
      confirmButtonColor: SweetAlertConfig.colors.success
    }).then(() => {
      if (typeof onClose === 'function') onClose();
    });
  }

  function showError(titulo, mensaje, onClose) {
    return Swal.fire({
      title: titulo,
      text: mensaje,
      icon: 'error',
      confirmButtonColor: SweetAlertConfig.colors.danger
    }).then(() => {
      if (typeof onClose === 'function') onClose();
    });
  }

  function showWarning(titulo, mensaje, onClose) {
    return Swal.fire({
      title: titulo,
      text: mensaje,
      icon: 'warning',
      confirmButtonColor: SweetAlertConfig.colors.warning
    }).then(() => {
      if (typeof onClose === 'function') onClose();
    });
  }

  function showInfo(titulo, mensaje, onClose) {
    return Swal.fire({
      title: titulo,
      text: mensaje,
      icon: 'info',
      confirmButtonColor: SweetAlertConfig.colors.primary
    }).then(() => {
      if (typeof onClose === 'function') onClose();
    });
  }

  // Toast helper
  function notify(titulo, mensaje = '', tipo = 'info', duracion = 3000) {
    return Toast.fire({
      icon: tipo,
      title: titulo,
      text: mensaje,
      timer: duracion
    });
  }

  // Confirmaciones
  function confirm(titulo = '¿Estás seguro?', texto = 'Esta acción no se puede deshacer', opciones = {}) {
    return Swal.fire({
      title: titulo,
      text: texto,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: SweetAlertConfig.colors.primary,
      cancelButtonColor: SweetAlertConfig.colors.danger,
      confirmButtonText: opciones.confirmButtonText || 'Sí',
      cancelButtonText: opciones.cancelButtonText || 'Cancelar',
      reverseButtons: true,
      ...opciones
    });
  }

  function confirmDelete(tipo = 'registro', onConfirm) {
    return confirm('¿Eliminar?', `¿Estás seguro de que quieres eliminar este ${tipo}?`, {
      confirmButtonText: 'Sí, eliminar'
    }).then(result => {
      if (result.isConfirmed && typeof onConfirm === 'function') {
        return onConfirm();
      }
    });
  }

  // Formularios dinámicos
  function showForm(titulo, campos = [], onConfirm) {
    const buildField = (c) => {
      const common = `id="${c.id}" class="swal2-input" placeholder="${c.placeholder || ''}"`;
      switch ((c.tipo || 'text').toLowerCase()) {
        case 'textarea':
          return `<textarea ${common.replace('swal2-input', 'swal2-textarea')}></textarea>`;
        case 'email':
        case 'password':
        case 'tel':
        case 'number':
        case 'text':
        default:
          return `<input type="${c.tipo || 'text'}" ${common}>`;
      }
    };

    const html = `
      <form id="form-swal">
        ${campos.map(buildField).join('')}
      </form>
    `;

    return Swal.fire({
      title: titulo,
      html,
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      focusConfirm: false,
      preConfirm: () => {
        // Validación básica según la definición de campos
        const ok = SweetAlertConfig.utils.validateForm(campos);
        if (!ok) return false;
        const data = {};
        campos.forEach(c => {
          const el = document.getElementById(c.id);
          data[c.id] = el ? el.value : null;
        });
        return data;
      }
    }).then(result => {
      if (result.isConfirmed && typeof onConfirm === 'function') {
        return onConfirm(result.value);
      }
    });
  }

  // Exponer globalmente
  window.SweetAlertConfig = SweetAlertConfig;
  window.showLoading = showLoading;
  window.showSuccess = showSuccess;
  window.showError = showError;
  window.showWarning = showWarning;
  window.showInfo = showInfo;
  window.notify = notify;
  window.confirm = confirm;
  window.confirmDelete = confirmDelete;
  window.showForm = showForm;
})();
