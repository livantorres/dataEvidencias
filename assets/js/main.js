/**
 * dataEvidencias - JavaScript principal
 * Archivo: assets/js/main.js
 * Version: 1.0.0
 */

// ============================================
// CONFIGURACIÓN Y CONSTANTES
// ============================================
const APP_CONFIG = {
    appName: 'dataEvidencias',
    version: '1.0.0',
    apiBaseUrl: '', // URL base para llamadas AJAX
    debug: true, // Cambiar a false en producción
    defaultToastDuration: 3000,
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
};

// ============================================
// CLASES UTILITARIAS
// ============================================

/**
 * Clase para manejar notificaciones Toast
 */
class ToastManager {
    constructor() {
        this.containerId = 'toast-container';
        this.initContainer();
    }

    initContainer() {
        if (!document.getElementById(this.containerId)) {
            const container = document.createElement('div');
            container.id = this.containerId;
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.cssText = 'z-index: 9999; max-width: 350px;';
            document.body.appendChild(container);
        }
    }

    show(message, type = 'info', duration = APP_CONFIG.defaultToastDuration) {
        const toastId = 'toast-' + Date.now();
        const icon = this.getIconByType(type);
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${icon} me-2"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast"></button>
            </div>
        `;

        document.getElementById(this.containerId).appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: duration
        });
        
        bsToast.show();
        
        // Limpiar después de ocultar
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });

        return toastId;
    }

    getIconByType(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

/**
 * Clase para manejar validación de formularios
 */
class FormValidator {
    constructor() {
        this.patterns = {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            phone: /^[0-9\-\+\s\(\)]{7,15}$/,
            password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/,
            numbersOnly: /^[0-9]+$/,
            lettersOnly: /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/
        };
    }

    validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const name = field.name || field.id;
        const isRequired = field.required;
        const pattern = field.pattern || field.dataset.pattern;

        // Validar campo requerido
        if (isRequired && !value) {
            return {
                isValid: false,
                message: `El campo ${this.getFieldLabel(name)} es requerido`
            };
        }

        // Validar según tipo
        switch (type) {
            case 'email':
                if (value && !this.patterns.email.test(value)) {
                    return {
                        isValid: false,
                        message: 'Ingrese un correo electrónico válido'
                    };
                }
                break;

            case 'tel':
                if (value && !this.patterns.phone.test(value)) {
                    return {
                        isValid: false,
                        message: 'Ingrese un número de teléfono válido'
                    };
                }
                break;

            case 'password':
                if (value && !this.patterns.password.test(value)) {
                    return {
                        isValid: false,
                        message: 'La contraseña debe tener al menos 8 caracteres, una letra y un número'
                    };
                }
                break;
        }

        // Validar con patrón personalizado
        if (pattern && value) {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                const errorMsg = field.dataset.patternError || 'Formato inválido';
                return {
                    isValid: false,
                    message: errorMsg
                };
            }
        }

        // Validar longitud mínima/máxima
        if (field.minLength && value.length < field.minLength) {
            return {
                isValid: false,
                message: `Mínimo ${field.minLength} caracteres`
            };
        }

        if (field.maxLength && value.length > field.maxLength) {
            return {
                isValid: false,
                message: `Máximo ${field.maxLength} caracteres`
            };
        }

        return { isValid: true, message: '' };
    }

    getFieldLabel(name) {
        const labels = {
            'username': 'usuario',
            'password': 'contraseña',
            'email': 'correo electrónico',
            'nombre': 'nombre',
            'descripcion': 'descripción'
        };
        return labels[name] || name;
    }

    validateForm(form) {
        let isValid = true;
        const errors = [];

        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            const validation = this.validateField(field);
            
            if (!validation.isValid) {
                isValid = false;
                errors.push({
                    field: field.name || field.id,
                    message: validation.message
                });
                
                this.markFieldAsInvalid(field, validation.message);
            } else {
                this.markFieldAsValid(field);
            }
        });

        return { isValid, errors };
    }

    markFieldAsInvalid(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        // Agregar o actualizar mensaje de error
        let feedback = field.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.insertBefore(feedback, field.nextSibling);
        }
        feedback.textContent = message;
        
        // Enfocar el primer campo inválido
        if (!field.dataset.hasFocus) {
            setTimeout(() => field.focus(), 100);
            field.dataset.hasFocus = 'true';
        }
    }

    markFieldAsValid(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        // Remover mensaje de error si existe
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
        
        delete field.dataset.hasFocus;
    }

    resetForm(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.classList.remove('is-invalid', 'is-valid');
            
            const feedback = field.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.remove();
            }
            
            delete field.dataset.hasFocus;
        });
    }
}

/**
 * Clase para manejar operaciones con archivos
 */
class FileHandler {
    constructor() {
        this.maxSize = APP_CONFIG.maxFileSize;
        this.allowedTypes = APP_CONFIG.allowedFileTypes;
    }

    validateFile(file) {
        const errors = [];

        // Validar tamaño
        if (file.size > this.maxSize) {
            const maxSizeMB = this.maxSize / (1024 * 1024);
            errors.push(`El archivo excede el tamaño máximo de ${maxSizeMB}MB`);
        }

        // Validar tipo
        if (!this.allowedTypes.includes(file.type)) {
            const allowedExtensions = this.allowedTypes.map(t => t.split('/')[1]).join(', ');
            errors.push(`Tipo de archivo no permitido. Formatos aceptados: ${allowedExtensions}`);
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    previewImage(input, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';

        if (!input.files || input.files.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted p-4">
                    <i class="fas fa-image fa-3x mb-3"></i>
                    <p>No hay imágenes seleccionadas</p>
                </div>
            `;
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'row g-2';

        Array.from(input.files).forEach((file, index) => {
            const validation = this.validateFile(file);
            
            const col = document.createElement('div');
            col.className = 'col-6 col-md-4 col-lg-3';
            
            if (!validation.isValid) {
                col.innerHTML = `
                    <div class="card border-danger">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-exclamation-circle text-danger mb-2"></i>
                            <small class="d-block text-truncate">${file.name}</small>
                            <small class="text-danger">${validation.errors[0]}</small>
                        </div>
                    </div>
                `;
            } else {
                const reader = new FileReader();
                reader.onload = (e) => {
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" 
                                 class="card-img-top" 
                                 alt="Vista previa ${index + 1}"
                                 style="height: 120px; object-fit: cover;">
                            <div class="card-body p-2">
                                <small class="d-block text-truncate">${file.name}</small>
                                <small class="text-muted">${this.formatFileSize(file.size)}</small>
                                <button type="button" 
                                        class="btn btn-sm btn-danger btn-block mt-1 remove-image"
                                        data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
            
            grid.appendChild(col);
        });

        container.appendChild(grid);

        // Agregar event listeners para botones de eliminar
        container.querySelectorAll('.remove-image').forEach(button => {
            button.addEventListener('click', (e) => {
                const index = parseInt(e.target.closest('.remove-image').dataset.index);
                this.removeFileFromInput(input, index);
                this.previewImage(input, containerId);
            });
        });
    }

    removeFileFromInput(input, index) {
        const files = Array.from(input.files);
        files.splice(index, 1);
        
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    downloadFile(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// ============================================
// INSTANCIAS GLOBALES
// ============================================
const toast = new ToastManager();
const validator = new FormValidator();
const fileHandler = new FileHandler();

// ============================================
// FUNCIONES GLOBALES
// ============================================

/**
 * Inicializa la aplicación
 */
function initApp() {
    if (APP_CONFIG.debug) {
        console.log(`${APP_CONFIG.appName} v${APP_CONFIG.version} inicializado`);
    }

    initBootstrapComponents();
    initFormValidation();
    initFileUploads();
    initDatePickers();
    initSidebar();
    initAlerts();
    initConfirmButtons();
    initSearchForms();
    
    updateDateTime();
    setInterval(updateDateTime, 60000);
}

/**
 * Inicializa componentes de Bootstrap
 */
function initBootstrapComponents() {
    // Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });

    // Popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].forEach(popoverTriggerEl => {
        new bootstrap.Popover(popoverTriggerEl);
    });

    // Modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', () => {
            const focusElement = modal.querySelector('[autofocus]');
            if (focusElement) focusElement.focus();
        });
    });
}

/**
 * Inicializa validación de formularios
 */
function initFormValidation() {
    document.querySelectorAll('form.needs-validation').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const validation = validator.validateForm(this);
            
            if (validation.isValid) {
                // Deshabilitar botón de submit para evitar doble envío
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Procesando...
                    `;
                    
                    // Re-habilitar después de 10 segundos por si hay error
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 10000);
                }
                
                this.submit();
            } else {
                // Mostrar primer error
                if (validation.errors.length > 0) {
                    toast.error(validation.errors[0].message);
                }
            }
        }, false);
    });
}

/**
 * Inicializa carga de archivos
 */
function initFileUploads() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        const previewId = input.dataset.preview;
        
        input.addEventListener('change', function() {
            fileHandler.previewImage(this, previewId);
        });
        
        // Inicializar vista previa si ya hay archivos
        if (input.files && input.files.length > 0) {
            fileHandler.previewImage(input, previewId);
        }
    });
}

/**
 * Inicializa selectores de fecha
 */
function initDatePickers() {
    document.querySelectorAll('input[type="date"]').forEach(input => {
        // Establecer fecha máxima como hoy (para fechas pasadas)
        if (input.hasAttribute('data-max-today')) {
            input.max = new Date().toISOString().split('T')[0];
        }
        
        // Establecer fecha mínima como hoy (para fechas futuras)
        if (input.hasAttribute('data-min-today')) {
            input.min = new Date().toISOString().split('T')[0];
        }
        
        // Establecer valor por defecto como hoy
        if (input.hasAttribute('data-default-today') && !input.value) {
            input.value = new Date().toISOString().split('T')[0];
        }
    });
}

/**
 * Inicializa el sidebar
 */
function initSidebar() {
    const sidebarToggle = document.querySelector('[data-bs-toggle="sidebar"]');
    const sidebar = document.getElementById('sidebarMenu');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
        
        // Cerrar sidebar al hacer clic fuera en móviles
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('show') &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
    
    // Marcar elemento activo en el sidebar
    const currentPage = window.location.pathname + window.location.search;
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
        if (link.href && link.href.includes(currentPage)) {
            link.classList.add('active');
        }
    });
}

/**
 * Inicializa alerts auto-ocultables
 */
function initAlerts() {
    document.querySelectorAll('.alert.auto-hide').forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Cerrar alert al hacer clic
    document.querySelectorAll('.alert .btn-close').forEach(btn => {
        btn.addEventListener('click', function() {
            const alert = this.closest('.alert');
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    });
}

/**
 * Inicializa botones de confirmación
 */
function initConfirmButtons() {
    document.querySelectorAll('[data-confirm]').forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || '¿Está seguro de realizar esta acción?';
            const form = this.closest('form');
            const href = this.getAttribute('href');
            
            e.preventDefault();
            
            Swal.fire({
                title: 'Confirmar',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form) {
                        form.submit();
                    } else if (href) {
                        window.location.href = href;
                    }
                }
            });
        });
    });
}

/**
 * Inicializa formularios de búsqueda
 */
function initSearchForms() {
    document.querySelectorAll('form[role="search"]').forEach(form => {
        const input = form.querySelector('input[type="search"]');
        const clearBtn = form.querySelector('.search-clear');
        
        if (input && clearBtn) {
            // Mostrar/ocultar botón de limpiar
            input.addEventListener('input', function() {
                clearBtn.style.display = this.value ? 'block' : 'none';
            });
            
            // Limpiar búsqueda
            clearBtn.addEventListener('click', function() {
                input.value = '';
                input.focus();
                this.style.display = 'none';
                form.submit();
            });
            
            // Inicializar visibilidad del botón
            clearBtn.style.display = input.value ? 'block' : 'none';
        }
        
        // Auto-enfocar en búsqueda
        if (input && input.hasAttribute('data-autofocus')) {
            setTimeout(() => input.focus(), 100);
        }
    });
}

/**
 * Actualiza fecha y hora en elementos .time-display
 */
function updateDateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    try {
        const dateTimeString = now.toLocaleDateString('es-ES', options);
        const timeElements = document.querySelectorAll('.time-display');
        
        timeElements.forEach(el => {
            if (el) {
                // Solo actualizar si el contenido es diferente
                if (el.textContent !== dateTimeString) {
                    el.textContent = dateTimeString;
                }
            }
        });
    } catch (error) {
        if (APP_CONFIG.debug) {
            console.warn('Error al actualizar hora:', error);
        }
    }
}

/**
 * Formatear fecha
 */
function formatDate(dateString, format = 'es-ES') {
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString(format, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return dateString;
    }
}

/**
 * Formatear fecha y hora
 */
function formatDateTime(dateString, format = 'es-ES') {
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString(format, {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return dateString;
    }
}

/**
 * Copiar texto al portapapeles
 */
function copyToClipboard(text, showNotification = true) {
    navigator.clipboard.writeText(text).then(() => {
        if (showNotification) {
            toast.success('Texto copiado al portapapeles');
        }
    }).catch(err => {
        console.error('Error al copiar:', err);
        if (showNotification) {
            toast.error('Error al copiar texto');
        }
    });
}

/**
 * Confirmar eliminación
 */
function confirmDelete(message = '¿Está seguro de eliminar este registro?') {
    return Swal.fire({
        title: 'Confirmar eliminación',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });
}

/**
 * Confirmar acción
 */
function confirmAction(message = '¿Está seguro de realizar esta acción?', title = 'Confirmar') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    });
}

/**
 * Mostrar modal de éxito
 */
function showSuccess(message, title = 'Éxito') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#4e73df',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

/**
 * Mostrar modal de error
 */
function showError(message, title = 'Error') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#e74a3b'
    });
}

/**
 * Cargar más datos (paginación infinita)
 */
function loadMore(button, url, containerId) {
    const btn = button;
    const container = document.getElementById(containerId);
    
    if (!btn || !container) return;
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
    
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.text();
        })
        .then(html => {
            container.insertAdjacentHTML('beforeend', html);
            btn.style.display = 'none';
            toast.success('Más datos cargados');
        })
        .catch(error => {
            console.error('Error:', error);
            toast.error('Error al cargar más datos');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

/**
 * Alternar visibilidad de contraseña
 */
function togglePasswordVisibility(inputId, toggleBtn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    // Cambiar icono
    const icon = toggleBtn.querySelector('i');
    if (icon) {
        icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    }
}

/**
 * Función para exportar datos a Excel (simulada)
 */
function exportToExcel(tableId, filename = 'export.xlsx') {
    const table = document.getElementById(tableId);
    if (!table) {
        toast.error('No se encontró la tabla para exportar');
        return;
    }
    
    // En una implementación real, aquí se usaría una librería como SheetJS
    toast.info('Función de exportación en desarrollo');
    
    // Simulación de exportación
    setTimeout(() => {
        toast.success('Datos exportados correctamente');
    }, 1000);
}

/**
 * Función para imprimir contenido específico
 */
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) {
        toast.error('Elemento no encontrado');
        return;
    }
    
    const originalContent = document.body.innerHTML;
    const printContent = element.innerHTML;
    
    document.body.innerHTML = `
        <html>
            <head>
                <title>Imprimir - ${APP_CONFIG.appName}</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
                <style>
                    @media print {
                        .no-print { display: none !important; }
                        body { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                ${printContent}
                <div class="no-print text-center mt-4">
                    <button class="btn btn-primary" onclick="window.location.reload()">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </button>
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                    };
                <\/script>
            </body>
        </html>
    `;
}

// ============================================
// EVENTOS GLOBALES
// ============================================

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initApp);

// Manejar errores no capturados
window.addEventListener('error', function(e) {
    if (APP_CONFIG.debug) {
        console.error('Error no capturado:', e.error);
    }
    
    // Mostrar error amigable al usuario
    toast.error('Ocurrió un error inesperado. Por favor intente nuevamente.');
});

// Manejar promesas no capturadas
window.addEventListener('unhandledrejection', function(e) {
    if (APP_CONFIG.debug) {
        console.error('Promesa no capturada:', e.reason);
    }
    
    toast.error('Error en la operación. Por favor verifique los datos.');
});

// ============================================
// EXPORTACIÓN PARA USO GLOBAL
// ============================================

// Hacer funciones disponibles globalmente
window.dataEvidencias = {
    // Utilidades
    toast,
    validator,
    fileHandler,
    
    // Funciones
    initApp,
    formatDate,
    formatDateTime,
    copyToClipboard,
    confirmDelete,
    confirmAction,
    showSuccess,
    showError,
    loadMore,
    togglePasswordVisibility,
    exportToExcel,
    printElement,
    
    // Configuración
    config: APP_CONFIG
};

// Atajo global (opcional)
window.app = window.dataEvidencias;

// ============================================
// POLYFILLS (para compatibilidad)
// ============================================

// Polyfill para String.includes (IE)
if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
        if (typeof start !== 'number') {
            start = 0;
        }
        if (start + search.length > this.length) {
            return false;
        }
        return this.indexOf(search, start) !== -1;
    };
}

// Polyfill para Element.closest (IE)
if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

// Polyfill para Element.matches (IE)
if (!Element.prototype.matches) {
    Element.prototype.matches = 
        Element.prototype.matchesSelector || 
        Element.prototype.mozMatchesSelector ||
        Element.prototype.msMatchesSelector || 
        Element.prototype.oMatchesSelector || 
        Element.prototype.webkitMatchesSelector ||
        function(s) {
            var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                i = matches.length;
            while (--i >= 0 && matches.item(i) !== this) {}
            return i > -1;
        };
}



