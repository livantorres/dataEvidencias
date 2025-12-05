<?php
// Habilitar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Para requests AJAX, evitar redirecciones
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    
    // Evitar redirecciones en AJAX
    function checkSessionAjax() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Sesión expirada',
                'redirect' => 'index.php?modulo=auth&accion=login'
            ]);
            exit();
        }
    }
}

// Definir título de página
$page_title = "Gestión de Instituciones";
?>

<div class="container-fluid">
    <!-- Header con título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-university me-2"></i> Instituciones
        </h1>
        <button type="button" class="btn btn-primary" onclick="openCreateInstitucionModal()">
            <i class="fas fa-plus me-2"></i> Nueva Institución
        </button>
    </div>

    <!-- Card principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Instituciones</h5>
            <div class="d-flex align-items-center">
                <form method="GET" class="d-flex me-2" style="min-width: 300px;">
                    <input type="hidden" name="accion" value="index">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Buscar institución..." 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    <button type="submit" class="btn btn-sm btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                    <a href="index.php?modulo=institucion&accion=index" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <?php if (isset($instituciones) && $instituciones->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Estado</th>
                            <th>Escudo</th>
                            <th>Evidencias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($institucion = $instituciones->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php
                        // Obtener número de evidencias para esta institución
                        $query_evidencias = "SELECT COUNT(*) as total FROM evidencias WHERE institucion_id = ?";
                        $stmt_ev = $this->db->prepare($query_evidencias);
                        $stmt_ev->execute([$institucion['id']]);
                        $total_evidencias = $stmt_ev->fetch(PDO::FETCH_ASSOC)['total'];
                        ?>
                        <tr>
                            <td><?php echo $institucion['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($institucion['nombre']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($institucion['ciudad']); ?></td>
                            <td>
                                <?php if ($institucion['activo'] == 1): ?>
                                <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $escudo_path = "storage/instituciones/escudos/{$institucion['id']}.*";
                                $escudo_files = glob($escudo_path);
                                if (count($escudo_files) > 0):
                                ?>
                                <img src="<?php echo $escudo_files[0]; ?>" 
                                     class="rounded-circle" 
                                     style="width: 50px; height: 50px; object-fit: cover;"
                                     alt="Escudo <?php echo htmlspecialchars($institucion['nombre']); ?>">
                                <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-university text-muted"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $total_evidencias; ?></span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary" 
                                            onclick="openEditInstitucionModal(<?php echo $institucion['id']; ?>)" 
                                            title="Editar institución">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!--<a href="index.php?modulo=evidencias&accion=index&institucion=<?php //echo $institucion['id']; ?>" -->
									<a href="index.php?modulo=evidencia&accion=index&institucion=<?php echo $institucion['id']; ?>"

                                       class="btn btn-sm btn-outline-success" 
                                       data-bs-toggle="tooltip" 
                                       title="Ver evidencias">
                                        <i class="fas fa-camera"></i>
                                    </a>
                                    
                                    <?php if ($institucion['activo'] == 1): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning toggle-status" 
                                            data-id="<?php echo $institucion['id']; ?>"
                                            data-action="deactivate"
                                            data-name="<?php echo htmlspecialchars($institucion['nombre']); ?>"
                                            data-bs-toggle="tooltip" 
                                            title="Desactivar institución">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    <?php else: ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success toggle-status" 
                                            data-id="<?php echo $institucion['id']; ?>"
                                            data-action="activate"
                                            data-name="<?php echo htmlspecialchars($institucion['nombre']); ?>"
                                            data-bs-toggle="tooltip" 
                                            title="Activar institución">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación (si aplica) -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
            
            <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-university fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No hay instituciones registradas</h4>
                <p class="text-muted mb-4">
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                    No se encontraron instituciones que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"
                    <?php else: ?>
                    Comience agregando su primera institución
                    <?php endif; ?>
                </p>
                <button type="button" class="btn btn-primary" onclick="openCreateInstitucionModal()">
                    <i class="fas fa-plus me-2"></i> Agregar Institución
                </button>
                <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                <a href="index.php?modulo=institucion&accion=index" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Limpiar búsqueda
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="institucion_id" id="modalInstitucionId">
                    <input type="hidden" name="action" id="modalAction">
                    
                    <p id="statusModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA CREAR/EDITAR INSTITUCIÓN -->
<div class="modal fade" id="institucionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Institución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="institucionForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="institucionId">
                    <input type="hidden" name="modulo" value="institucion">

					
                    <input type="hidden" name="accion" id="formAction" value="create">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la Institución *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                    <label class="form-check-label" for="activo">
                                        Institución Activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Las instituciones inactivas no aparecerán en el listado para registrar evidencias.
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <label class="form-label d-block">Escudo de la Institución</label>
                                    <div class="image-upload-container">
                                        <img id="escudoPreview" 
                                             src="assets/img/default-institution.png" 
                                             class="img-thumbnail rounded-circle mb-2" 
                                             style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                             onclick="document.getElementById('escudoInput').click()">
                                        <input type="file" 
                                               class="form-control d-none" 
                                               id="escudoInput" 
                                               name="escudo" 
                                               accept="image/*">
                                        <div class="mt-2">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    onclick="document.getElementById('escudoInput').click()">
                                                <i class="fas fa-upload me-1"></i> Subir Escudo
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    id="removeEscudoBtn" 
                                                    style="display: none;">
                                                <i class="fas fa-trash me-1"></i> Quitar
                                            </button>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            Tamaño máximo: 5MB<br>
                                            Formatos: JPG, PNG, GIF
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Guardar Institución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales
let institucionModal = null;

// Inicializar modal solo cuando Bootstrap esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        institucionModal = new bootstrap.Modal(document.getElementById('institucionModal'));
    } else {
        console.error('Bootstrap no está cargado');
    }
    
    // Inicializar tooltips
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Función para abrir modal de crear institución
function openCreateInstitucionModal() {
    if (!institucionModal) {
        alert('Bootstrap no está cargado. Recargue la página.');
        return;
    }
    
    document.getElementById('modalTitle').textContent = 'Nueva Institución';
    document.getElementById('formAction').value = 'create';
    document.getElementById('institucionId').value = '';
    document.getElementById('institucionForm').reset();
    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
    document.getElementById('removeEscudoBtn').style.display = 'none';
    document.getElementById('activo').checked = true;
    
    institucionModal.show();
}

// Función para abrir modal de editar institución
function openEditInstitucionModal(id) {
    if (!institucionModal) {
        alert('Bootstrap no está cargado. Recargue la página.');
        return;
    }
    
   // fetch(`index.php?modulo=instituciones&accion=edit&id=${id}`)
	fetch(`index.php?modulo=institucion&accion=edit&id=${id}`)

        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const institucion = data.data;
                
                document.getElementById('modalTitle').textContent = 'Editar Institución';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('institucionId').value = institucion.id;
                document.getElementById('nombre').value = institucion.nombre;
                document.getElementById('ciudad').value = institucion.ciudad;
                document.getElementById('activo').checked = institucion.activo == 1;
                
                if (institucion.escudo) {
                    document.getElementById('escudoPreview').src = institucion.escudo + '?' + new Date().getTime();
                    document.getElementById('removeEscudoBtn').style.display = 'inline-block';
                } else {
                    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
                    document.getElementById('removeEscudoBtn').style.display = 'none';
                }
                
                institucionModal.show();
            } else {
                Swal.fire('Error', data.message || 'Error al cargar la institución', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
}

// Vista previa del escudo
document.getElementById('escudoInput').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('escudoPreview').src = e.target.result;
            document.getElementById('removeEscudoBtn').style.display = 'inline-block';
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// Quitar escudo
document.getElementById('removeEscudoBtn').addEventListener('click', function() {
    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
    document.getElementById('escudoInput').value = '';
    this.style.display = 'none';
});

// Enviar formulario de institución
document.getElementById('institucionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('modalSubmitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar loading
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Procesando...';
    
    const formData = new FormData(this);
    const action = document.getElementById('formAction').value;
    
    // IMPORTANTE: Agregar parámetros a la URL
    const url = `index.php?modulo=institucion&accion=${action}`;
    
    console.log("URL de la solicitud:", url);
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log("Respuesta recibida. Status:", response.status);
        console.log("Content-Type:", response.headers.get('Content-Type'));
        
        // Verificar si es JSON
        const contentType = response.headers.get('Content-Type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // Si no es JSON, obtener texto para debug
            return response.text().then(text => {
                console.error("No es JSON. Contenido recibido:", text.substring(0, 500));
                throw new Error('El servidor no devolvió JSON. Posible error o redirección.');
            });
        }
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                if (institucionModal) {
                    institucionModal.hide();
                }
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Error al guardar la institución', 'error');
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        // Restaurar botón
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Manejar cambio de estado
document.querySelectorAll('.toggle-status').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const action = this.dataset.action;
        const name = this.dataset.name;
        
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        const title = document.getElementById('statusModalTitle');
        const message = document.getElementById('statusModalMessage');
        
        document.getElementById('modalInstitucionId').value = id;
        document.getElementById('modalAction').value = action;
        
        if (action === 'deactivate') {
            title.textContent = 'Desactivar Institución';
            message.innerHTML = `¿Está seguro de desactivar la institución <strong>${name}</strong>?<br>
                                <small class="text-muted">No podrá registrar nuevas evidencias para esta institución.</small>`;
        } else {
            title.textContent = 'Activar Institución';
            message.innerHTML = `¿Está seguro de activar la institución <strong>${name}</strong>?`;
        }
        
        modal.show();
    });
});

// Enviar formulario de cambio de estado
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'institucion');
    formData.append('accion', 'toggleStatus');
    
    //fetch('index.php', {
	fetch('index.php?modulo=institucion&accion=toggleStatus', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error al cambiar el estado');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
});
</script>