<?php
// Definir título de página
$page_title = "Gestión de Ciclos";
?>

<style>
.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>

<div class="container-fluid">
    <!-- Header con título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calendar-alt me-2"></i> Ciclos
        </h1>
        <button type="button" class="btn btn-primary" onclick="openCreateCicloModal()">
            <i class="fas fa-plus me-2"></i> Nuevo Ciclo
        </button>
    </div>

    <!-- Card principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Ciclos</h5>
            <div class="text-muted small">
                Ciclo activo actual: 
                <span class="badge bg-success">
                    <?php 
                    $ciclo_actual = $cicloActual ?? null;
                    echo $ciclo_actual ? htmlspecialchars($ciclo_actual['descripcion']) : 'No definido';
                    ?>
                </span>
            </div>
        </div>
        
        <div class="card-body">
            <?php 
            // Verificar si $ciclos está definido y es un PDOStatement
            if (isset($ciclos) && $ciclos instanceof PDOStatement): 
                $rowCount = $ciclos->rowCount();
                if ($rowCount > 0): 
            ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Evidencias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Reiniciar el cursor del resultado
                        $ciclos->execute(); // Re-ejecutar para asegurar que el cursor esté al inicio
                        while ($ciclo = $ciclos->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <?php
                        // Obtener número de evidencias para este ciclo
                        $total_evidencias = 0;
                        if (isset($db)) {
                            try {
                                $query_evidencias = "SELECT COUNT(*) as total FROM evidencias WHERE ciclo_id = ?";
                                $stmt_ev = $db->prepare($query_evidencias);
                                $stmt_ev->execute([$ciclo['id']]);
                                $result = $stmt_ev->fetch(PDO::FETCH_ASSOC);
                                $total_evidencias = $result['total'] ?? 0;
                            } catch (Exception $e) {
                                error_log("Error al contar evidencias: " . $e->getMessage());
                                $total_evidencias = 0;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ciclo['id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($ciclo['descripcion']); ?></strong>
                            </td>
                            <td>
                                <?php if ($ciclo['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $total_evidencias; ?></span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary" 
                                            onclick="openEditCicloModal(<?php echo $ciclo['id']; ?>)" 
                                            title="Editar ciclo">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <?php if ($ciclo['activo'] == 1): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success" 
                                            disabled
                                            title="Ciclo activo actual">
                                        <i class="fas fa-check-circle"></i> Activo
                                    </button>
                                    <?php else: ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning set-active" 
                                            data-id="<?php echo $ciclo['id']; ?>"
                                            data-descripcion="<?php echo htmlspecialchars($ciclo['descripcion']); ?>"
                                            title="Establecer como ciclo activo">
                                        <i class="fas fa-star"></i> Activar
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($total_evidencias == 0): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger delete-ciclo" 
                                            data-id="<?php echo $ciclo['id']; ?>"
                                            data-descripcion="<?php echo htmlspecialchars($ciclo['descripcion']); ?>"
                                            title="Eliminar ciclo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No hay ciclos registrados</h4>
                <p class="text-muted mb-4">
                    Los ciclos representan períodos académicos (ej: 2024, 2025)
                </p>
                <button type="button" class="btn btn-primary" onclick="openCreateCicloModal()">
                    <i class="fas fa-plus me-2"></i> Agregar Primer Ciclo
                </button>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-5">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Error al cargar los ciclos</h4>
                    <p>No se pudo obtener la lista de ciclos.</p>
                    <p class="small text-muted">Verifique la conexión a la base de datos y que la tabla 'ciclos' exista.</p>
                    <button type="button" class="btn btn-primary mt-2" onclick="window.location.reload()">
                        <i class="fas fa-redo me-2"></i> Recargar
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card-footer">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Solo un ciclo puede estar activo a la vez. 
                El ciclo activo se usará como predeterminado para nuevas evidencias.
            </div>
        </div>
    </div>
</div>


<!-- Modal para activar ciclo -->
<div class="modal fade" id="activateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activar Ciclo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="activateForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ciclo_id" id="modalCicloId">
                    
                    <p id="activateModalMessage"></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Al activar este ciclo, el ciclo actual se desactivará automáticamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Activar Ciclo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar ciclo -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Ciclo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ciclo_id" id="deleteCicloId">
                    
                    <p id="deleteModalMessage"></p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta acción no se puede deshacer.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Ciclo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA CREAR/EDITAR CICLO -->
<div class="modal fade" id="cicloModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cicloModalTitle">Nuevo Ciclo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cicloForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="cicloId">
                    <input type="hidden" name="modulo" value="ciclo">
                    <input type="hidden" name="accion" id="cicloFormAction" value="create">
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción del Ciclo *</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        <div class="form-text">
                            Ejemplo: 2024, 2025, Ciclo 2024-2025
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cicloActivo" name="activo" value="1">
                            <label class="form-check-label" for="cicloActivo">
                                Establecer como ciclo activo
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Solo un ciclo puede estar activo a la vez. Al activar este ciclo, los demás se desactivarán automáticamente.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="cicloSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Guardar Ciclo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales para ciclos
let cicloModal = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando modales...');
    
    // Inicializar modales de Bootstrap
    if (typeof bootstrap !== 'undefined') {
        const cicloModalEl = document.getElementById('cicloModal');
        if (cicloModalEl) {
            cicloModal = new bootstrap.Modal(cicloModalEl);
            console.log('Modal de ciclo inicializado');
        } else {
            console.error('No se encontró el elemento #cicloModal');
        }
    } else {
        console.error('Bootstrap no está disponible');
    }
    
    // Configurar tooltips
    initTooltips();
});

// Inicializar tooltips
function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Función para abrir modal de crear ciclo
function openCreateCicloModal() {
    if (!cicloModal) {
        alert('Modal no inicializado. Asegúrese de que Bootstrap esté cargado.');
        return;
    }
    
    document.getElementById('cicloModalTitle').textContent = 'Nuevo Ciclo';
    document.getElementById('cicloFormAction').value = 'create';
    document.getElementById('cicloId').value = '';
    document.getElementById('cicloForm').reset();
    document.getElementById('cicloActivo').checked = false;
    
    cicloModal.show();
}

// Función para abrir modal de editar ciclo
function openEditCicloModal(id) {
    if (!cicloModal) {
        alert('Modal no inicializado. Asegúrese de que Bootstrap esté cargado.');
        return;
    }
    
    console.log(`Solicitando ciclo ID: ${id}`);
    
    // Usar la ruta completa
    fetch(`index.php?modulo=ciclo&accion=edit&id=${id}`, {
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        
        // Si es redirección 302, manejar sesión expirada
        if (response.status === 302 || response.redirected) {
            window.location.href = 'index.php?modulo=auth&accion=login';
            throw new Error('Sesión expirada');
        }
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            const ciclo = data.data;
            
            document.getElementById('cicloModalTitle').textContent = 'Editar Ciclo';
            document.getElementById('cicloFormAction').value = 'edit';
            document.getElementById('cicloId').value = ciclo.id;
            document.getElementById('descripcion').value = ciclo.descripcion;
            document.getElementById('cicloActivo').checked = ciclo.activo == 1;
            
            cicloModal.show();
        } else {
            // Si hay redirección en la respuesta
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al cargar el ciclo'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Solo mostrar alerta si no es redirección de sesión
        if (!error.message.includes('Sesión expirada')) {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor. Verifique su conexión.'
            });
        }
    });
}

// Función para manejar respuestas de fetch
function handleFetchResponse(response) {
    console.log('Response status:', response.status, response.statusText);
    
    // Si es redirección 302, manejar sesión expirada
    if (response.status === 302 || response.redirected) {
        window.location.href = 'index.php?modulo=auth&accion=login';
        throw new Error('Sesión expirada');
    }
    
    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
    }
    
    return response.json();
}

// Función para manejar errores de AJAX
function handleAjaxError(error) {
    console.error('Error:', error);
    
    // Solo mostrar alerta si no es redirección de sesión
    if (!error.message.includes('Sesión expirada')) {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
    }
    
    return Promise.reject(error);
}

// Enviar formulario de ciclo
const cicloForm = document.getElementById('cicloForm');
if (cicloForm) {
    cicloForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('cicloSubmitBtn');
        if (!submitBtn) return;
        
        const spinner = submitBtn.querySelector('.spinner-border');
        const originalText = submitBtn.textContent;
        
        // Mostrar loading
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Procesando...';
        
        const formData = new FormData(this);
        
        // Obtener la acción del formulario
        const action = document.getElementById('cicloFormAction').value;
        
        // Usar la ruta completa con parámetros en la URL
        let url = '';
        if (action === 'create') {
            url = `index.php?modulo=ciclo&accion=create`;
        } else if (action === 'edit') {
            const id = document.getElementById('cicloId').value;
            url = `index.php?modulo=ciclo&accion=edit&id=${id}`;
        }
        
        fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(handleFetchResponse)
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    if (cicloModal) {
                        cicloModal.hide();
                    }
                    window.location.reload();
                });
            } else {
                // Si hay redirección en la respuesta
                if (data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Error al guardar el ciclo'
                });
            }
        })
        .catch(handleAjaxError)
        .finally(() => {
            // Restaurar botón
            spinner.classList.add('d-none');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
}

// Delegación de eventos para botones dinámicos
document.addEventListener('click', function(e) {
    // Activar ciclo
    if (e.target.closest('.set-active')) {
        e.preventDefault();
        const button = e.target.closest('.set-active');
        const id = button.dataset.id;
        const descripcion = button.dataset.descripcion;
        
        Swal.fire({
            title: '¿Activar ciclo?',
            html: `¿Está seguro de activar el ciclo <strong>${descripcion}</strong>?<br><br>
                  <small class="text-muted">Al activar este ciclo, el ciclo actual se desactivará automáticamente.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                activateCiclo(id, descripcion);
            }
        });
    }
    
    // Eliminar ciclo
    if (e.target.closest('.delete-ciclo')) {
        e.preventDefault();
        const button = e.target.closest('.delete-ciclo');
        const id = button.dataset.id;
        const descripcion = button.dataset.descripcion;
        
        Swal.fire({
            title: '¿Eliminar ciclo?',
            html: `¿Está seguro de eliminar el ciclo <strong>${descripcion}</strong>?<br><br>
                  <small class="text-danger">Esta acción no se puede deshacer.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCiclo(id, descripcion);
            }
        });
    }
});

// Función para activar ciclo (con ruta completa)
function activateCiclo(id, descripcion) {
    // Mostrar loading
    Swal.fire({
        title: 'Activando ciclo...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Usar la ruta completa con parámetros
    const url = `index.php?modulo=ciclo&accion=activate&ciclo_id=${id}`;
    
    fetch(url, {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(handleFetchResponse)
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            // Si hay redirección en la respuesta
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al activar el ciclo'
            });
        }
    })
    .catch(handleAjaxError);
}

// Función para eliminar ciclo (con ruta completa)
function deleteCiclo(id, descripcion) {
    // Mostrar loading
    Swal.fire({
        title: 'Eliminando ciclo...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Usar la ruta completa con parámetros
    const url = `index.php?modulo=ciclo&accion=delete&ciclo_id=${id}`;
    
    fetch(url, {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(handleFetchResponse)
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            // Si hay redirección en la respuesta
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al eliminar el ciclo'
            });
        }
    })
    .catch(handleAjaxError);
}

// Función de depuración
function debugCiclo() {
    console.log('=== DEBUG CICLO ===');
    console.log('cicloModal:', cicloModal);
    console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
    console.log('=== FIN DEBUG ===');
}

// Función para probar endpoints (usar en consola)
function testEndpoint(action, id, extraData = {}) {
    // Construir URL con parámetros
    let url = `index.php?modulo=ciclo&accion=${action}`;
    
    // Agregar parámetros a la URL
    if (id) {
        url += `&ciclo_id=${id}`;
    }
    
    // Agregar parámetros extra a la URL
    const params = new URLSearchParams(extraData).toString();
    if (params) {
        url += `&${params}`;
    }
    
    console.log(`Testing endpoint: ${url}`);
    
    fetch(url, {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Status:', response.status, response.statusText);
        return response.text();
    })
    .then(text => {
        console.log('Raw response (first 500 chars):', text.substring(0, 500));
        try {
            const json = JSON.parse(text);
            console.log('Parsed JSON:', json);
        } catch (e) {
            console.log('Not valid JSON:', e.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>