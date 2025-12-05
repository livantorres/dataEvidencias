<?php
// Definir título de página
$page_title = "Gestión de Ciclos";
?>

<div class="container-fluid">
    <!-- Header con título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calendar-alt me-2"></i> Ciclos
        </h1>
        <!--<a href="index.php?modulo=ciclos&accion=create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Nuevo Ciclo
        </a>-->
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
            <?php if (isset($ciclos) && $ciclos->rowCount() > 0): ?>
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
                        <?php while ($ciclo = $ciclos->fetch(PDO::FETCH_ASSOC)): ?>
                        <?php
                        // Obtener número de evidencias para este ciclo
                        $query_evidencias = "SELECT COUNT(*) as total FROM evidencias WHERE ciclo_id = ?";
                        $stmt_ev = $this->db->prepare($query_evidencias);
                        $stmt_ev->execute([$ciclo['id']]);
                        $total_evidencias = $stmt_ev->fetch(PDO::FETCH_ASSOC)['total'];
                        ?>
                        <tr>
                            <td><?php echo $ciclo['id']; ?></td>
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
                                   <!-- <a href="index.php?modulo=ciclos&accion=edit&id=<?php echo $ciclo['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Editar ciclo">
                                        <i class="fas fa-edit"></i>
                                    </a>-->
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
                <a href="index.php?modulo=ciclos&accion=create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Agregar Primer Ciclo
                </a>
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
                    <input type="hidden" name="modulo" value="ciclos">
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
let cicloModal = new bootstrap.Modal(document.getElementById('cicloModal'));

// Función para abrir modal de crear ciclo
function openCreateCicloModal() {
    document.getElementById('cicloModalTitle').textContent = 'Nuevo Ciclo';
    document.getElementById('cicloFormAction').value = 'create';
    document.getElementById('cicloId').value = '';
    document.getElementById('cicloForm').reset();
    document.getElementById('cicloActivo').checked = false;
    
    cicloModal.show();
}

// Función para abrir modal de editar ciclo
function openEditCicloModal(id) {
    fetch(`index.php?modulo=ciclos&accion=edit&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const ciclo = data.data;
                
                document.getElementById('cicloModalTitle').textContent = 'Editar Ciclo';
                document.getElementById('cicloFormAction').value = 'edit';
                document.getElementById('cicloId').value = ciclo.id;
                document.getElementById('descripcion').value = ciclo.descripcion;
                document.getElementById('cicloActivo').checked = ciclo.activo == 1;
                
                cicloModal.show();
            } else {
                Swal.fire('Error', data.message || 'Error al cargar el ciclo', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
}

// Enviar formulario de ciclo
document.getElementById('cicloForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('cicloSubmitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar loading
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Procesando...';
    
    const formData = new FormData(this);
    const action = document.getElementById('cicloFormAction').value;
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                cicloModal.hide();
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Error al guardar el ciclo', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    })
    .finally(() => {
        // Restaurar botón
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Activar ciclo
document.querySelectorAll('.set-active').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const descripcion = this.dataset.descripcion;
        
        const modal = new bootstrap.Modal(document.getElementById('activateModal'));
        document.getElementById('modalCicloId').value = id;
        document.getElementById('activateModalMessage').innerHTML = 
            `¿Está seguro de activar el ciclo <strong>${descripcion}</strong>?`;
        
        modal.show();
    });
});

// Eliminar ciclo
document.querySelectorAll('.delete-ciclo').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const descripcion = this.dataset.descripcion;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('deleteCicloId').value = id;
        document.getElementById('deleteModalMessage').innerHTML = 
            `¿Está seguro de eliminar el ciclo <strong>${descripcion}</strong>?`;
        
        modal.show();
    });
});

// Enviar formulario de activar ciclo
document.getElementById('activateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'ciclos');
    formData.append('accion', 'activate');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error al activar el ciclo');
        }
    });
});

// Enviar formulario de eliminar ciclo
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'ciclos');
    formData.append('accion', 'delete');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error al eliminar el ciclo');
        }
    });
});
</script>