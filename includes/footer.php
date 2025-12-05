        </main>
    </div>

    <!-- Toast Container (para notificaciones dinámicas) -->
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <!-- Toasts se generarán aquí dinámicamente -->
    </div>

      <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js (opcional para gráficos futuros) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
    // ========== FUNCIONES GLOBALES ==========
    
    // Toggle sidebar en móviles
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebarMenu');
        sidebar.classList.toggle('show');
        
        // Cerrar sidebar al hacer clic fuera en móviles
        if (sidebar.classList.contains('show')) {
            document.addEventListener('click', closeSidebarOnClickOutside);
        } else {
            document.removeEventListener('click', closeSidebarOnClickOutside);
        }
    }
    
    function closeSidebarOnClickOutside(event) {
        const sidebar = document.getElementById('sidebarMenu');
        const toggleBtn = document.querySelector('.navbar-toggler');
        
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('show');
            document.removeEventListener('click', closeSidebarOnClickOutside);
        }
    }
    
    // Función para actualizar fecha y hora
    function updateDateTime() {
        const now = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        try {
            const dateTimeString = now.toLocaleDateString('es-ES', options);
            const timeElements = document.querySelectorAll('.time-display');
            
            if (timeElements && timeElements.length > 0) {
                timeElements.forEach(el => {
                    if (el) el.textContent = dateTimeString;
                });
            }
        } catch (error) {
            console.warn('Error al actualizar hora:', error);
        }
    }
    
    // Inicializar actualización de hora solo si hay elementos
    document.addEventListener('DOMContentLoaded', function() {
        const timeElements = document.querySelectorAll('.time-display');
        if (timeElements.length > 0) {
            setInterval(updateDateTime, 1000);
            updateDateTime();
        }
    });
    
    // ========== FUNCIONES DE CONFIRMACIÓN ==========
    
    // Confirmar eliminación con SweetAlert2
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
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        });
    }
    
    // Confirmar acción genérica
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
    
    // ========== FUNCIONES DE NOTIFICACIÓN ==========
    
    // Mostrar toast notification
    function showToast(message, type = 'info', duration = 3000) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;
        
        const toastId = 'toast-' + Date.now();
        const icon = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        }[type] || 'info-circle';
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.id = toastId;
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: duration
        });
        bsToast.show();
        
        // Eliminar del DOM después de ocultar
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
    
    // Mostrar SweetAlert de éxito
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
    
    // Mostrar SweetAlert de error
    function showError(message, title = 'Error') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonColor: '#e74a3b'
        });
    }
    
    // ========== FUNCIONES DE FORMULARIO ==========
    
    // Manejar formularios con confirmación
    document.addEventListener('DOMContentLoaded', function() {
        // Formularios con confirmación
        document.querySelectorAll('form.confirm-submit').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const action = this.getAttribute('data-action') || 'realizar esta acción';
                
                confirmAction(`¿Está seguro de ${action}?`, 'Confirmar').then(result => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
        
        // Formularios con validación Bootstrap
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        // Auto-focus en primer campo inválido
        document.querySelectorAll('.is-invalid').forEach((input, index) => {
            if (index === 0) {
                setTimeout(() => input.focus(), 100);
            }
        });
    });
    
    // ========== FUNCIONES UTILITARIAS ==========
    
    // Copiar texto al portapapeles
    function copyToClipboard(text, showNotification = true) {
        navigator.clipboard.writeText(text).then(() => {
            if (showNotification) {
                showToast('Texto copiado al portapapeles', 'success');
            }
        }).catch(err => {
            console.error('Error al copiar:', err);
            if (showNotification) {
                showToast('Error al copiar texto', 'error');
            }
        });
    }
    
    // Formatear fecha
    function formatDate(dateString, format = 'es-ES') {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString(format, {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (error) {
            return dateString;
        }
    }
    
    // Formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0 || !bytes) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Cargar más datos (para paginación infinita)
    function loadMoreData(button, url, containerId) {
        const btn = button;
        const container = document.getElementById(containerId);
        
        if (!btn || !container) return;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cargando...';
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                container.innerHTML += html;
                btn.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al cargar más datos', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Cargar más';
            });
    }
    
    // Inicializar tooltips y popovers
    function initBootstrapComponents() {
        // Tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }
    
    // Ejecutar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initBootstrapComponents();
        
        // Auto-hide alerts después de 5 segundos
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Prevenir doble envío de formularios
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
                    
                    // Re-enable después de 10 segundos por si hay error
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Enviar';
                    }, 10000);
                }
            });
        });
        
        // Guardar texto original de botones de submit
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.setAttribute('data-original-text', btn.innerHTML);
        });
    });
    
    // Manejar errores globales
    window.addEventListener('error', function(e) {
        console.error('Error global:', e.error);
        // Mostrar error amigable al usuario si es crítico
        if (e.error.message && e.error.message.includes('crítico')) {
            showToast('Ocurrió un error inesperado. Por favor recargue la página.', 'error');
        }
    });
    
    // ========== FUNCIONES ESPECÍFICAS DEL SISTEMA ==========
    
    // Mostrar modal para subir evidencia
    function showEvidenceModal(institucionId, institucionNombre) {
        // Esta función se implementará en el módulo de evidencias
        console.log('Modal para institución:', institucionId, institucionNombre);
        showToast('Función en desarrollo', 'info');
    }
    
    // Vista previa de imágenes antes de subir
    function previewImages(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview) return;
        
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-md-3 mb-2';
                    div.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" 
                                 style="height: 100px; object-fit: cover;"
                                 alt="Vista previa ${i + 1}">
                            <div class="card-body p-2">
                                <small class="text-muted text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${formatFileSize(file.size)}</small>
                            </div>
                        </div>
                    `;
                    preview.appendChild(div);
                };
                
                reader.readAsDataURL(file);
            }
        }
    }
    </script>
</body>
</html>