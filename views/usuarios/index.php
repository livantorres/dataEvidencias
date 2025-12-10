<?php
// Definir título de página
$page_title = "Gestión de Usuarios";
?>
<style>
/* Estilos para el badge de rol clickeable */
.change-role {
    transition: all 0.3s ease;
    cursor: pointer;
}

.change-role:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.change-role.bg-danger:hover {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

.change-role.bg-success:hover {
    background-color: #198754 !important;
    border-color: #198754 !important;
}

.change-role.bg-secondary:hover {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Tooltip para el badge */
.change-role[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    margin-bottom: 5px;
}
</style>
<div class="container-fluid">
    <!-- Header con título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i> Usuarios
        </h1>
       <!-- <a href="index.php?modulo=usuarios&accion=create" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
        </a>-->
		<button type="button" class="btn btn-primary" onclick="openCreateUsuarioModal()">
			<i class="fas fa-user-plus me-2"></i> Nuevo Usuario
		</button>
    </div>

    <!-- Card principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Usuarios</h5>
            <div class="text-muted small">
                Total: <?php echo $usuarios->rowCount(); ?> usuarios
            </div>
        </div>
        
        <div class="card-body">
            <?php if ($usuarios->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($usuario['username']); ?></strong>
                                <?php if ($usuario['id'] == $_SESSION['usuario_id']): ?>
                                <span class="badge bg-info">Yo</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td>
								<span class="badge bg-<?php 
									echo $usuario['rol_id'] == 1 ? 'danger' : 
										 ($usuario['rol_id'] == 2 ? 'success' : 'secondary'); 
								?> change-role" 
									  style="cursor: pointer;"
									  data-id="<?php echo $usuario['id']; ?>"
									  data-username="<?php echo htmlspecialchars($usuario['username']); ?>"
									  data-current-role="<?php echo $usuario['rol_id']; ?>"
									  data-current-role-name="<?php echo htmlspecialchars($usuario['rol_nombre']); ?>"
									  title="Haz clic para cambiar el rol">
									<?php echo htmlspecialchars($usuario['rol_nombre']); ?>
								</span>
							</td>
                            <td>
                                <?php if ($usuario['activo'] == 1): ?>
                                <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario['created_at']): ?>
                                <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?>
                                <?php else: ?>
                                <span class="text-muted">Nunca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning reset-password" 
                                            data-id="<?php echo $usuario['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($usuario['username']); ?>"
                                            title="Restablecer contraseña">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    
                                    <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                        <?php if ($usuario['activo'] == 1): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger toggle-status" 
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($usuario['username']); ?>"
                                                data-action="deactivate"
                                                title="Desactivar usuario">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                        <?php else: ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success toggle-status" 
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($usuario['username']); ?>"
                                                data-action="activate"
                                                title="Activar usuario">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-user" 
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($usuario['username']); ?>"
                                                title="Eliminar usuario">
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
                    <i class="fas fa-users fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No hay usuarios registrados</h4>
                <p class="text-muted mb-4">
                    Comience agregando usuarios al sistema
                </p>
                <a href="index.php?modulo=usuarios&accion=create" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i> Agregar Usuario
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para restablecer contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restablecer Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="resetPasswordForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" id="resetUsuarioId">
                    
                    <p id="resetPasswordMessage"></p>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                        <input type="password" name="new_password" id="new_password" 
                               class="form-control" required minlength="6">
                        <div class="form-text">
                            Mínimo 6 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" 
                               class="form-control" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="userStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userStatusTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userStatusForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" id="statusUsuarioId">
                    <input type="hidden" name="action" id="statusAction">
                    
                    <p id="userStatusMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteUserForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" id="deleteUsuarioId">
                    
                    <p id="deleteUserMessage"></p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Esta acción no se puede deshacer. El usuario perderá acceso al sistema.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- MODAL PARA CREAR USUARIO -->
<div class="modal fade" id="usuarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usuarioModalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="usuarioForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="usuarios">
                    <input type="hidden" name="accion" value="create">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario *</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña *</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   minlength="6">
                            <button type="button" 
                                    class="btn btn-outline-secondary toggle-password" 
                                    data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rol_id" class="form-label">Rol *</label>
                        <select class="form-select" id="rol_id" name="rol_id" required>
                            <option value="">Seleccionar rol...</option>
                            <?php 
                            // Reiniciar el puntero del resultado
                            $roles->execute();
                            while ($rol = $roles->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <option value="<?php echo $rol['id']; ?>">
                                <?php echo htmlspecialchars($rol['nombre']); ?> - <?php echo htmlspecialchars($rol['descripcion']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="usuarioActivo" name="activo" value="1" checked>
                            <label class="form-check-label" for="usuarioActivo">
                                Usuario Activo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="usuarioSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambiar rol -->
<div class="modal fade" id="changeRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Rol de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changeRoleForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="usuario_id" id="changeRoleUsuarioId">
                    <input type="hidden" name="current_role" id="currentRoleId">
                    
                    <p id="changeRoleMessage"></p>
                    
                    <div class="mb-3">
                        <label for="new_role_id" class="form-label">Seleccionar Nuevo Rol</label>
                        <select class="form-select" id="new_role_id" name="new_role_id" required>
                            <option value="">Seleccionar rol...</option>
                            <?php 
                            // Reiniciar el puntero del resultado
                            $roles->execute();
                            while ($rol = $roles->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <option value="<?php echo $rol['id']; ?>">
                                <?php echo htmlspecialchars($rol['nombre']); ?> - <?php echo htmlspecialchars($rol['descripcion']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Al cambiar el rol, el usuario obtendrá los permisos correspondientes al nuevo rol.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal para usuarios - inicializar con seguridad
let usuarioModal = null;

// Inicializar modal solo cuando Bootstrap esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        usuarioModal = new bootstrap.Modal(document.getElementById('usuarioModal'));
        
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    } else {
        console.error('Bootstrap no está cargado. Recargue la página.');
    }
});

// Función para abrir modal de crear usuario
function openCreateUsuarioModal() {
    if (!usuarioModal) {
        alert('El sistema no está completamente cargado. Por favor, recargue la página.');
        return;
    }
    
    document.getElementById('usuarioModalTitle').textContent = 'Nuevo Usuario';
    document.getElementById('usuarioForm').reset();
    document.getElementById('usuarioActivo').checked = true;
    document.getElementById('rol_id').value = '';
    
    usuarioModal.show();
}

// Alternar visibilidad de contraseña
document.addEventListener('click', function(e) {
    if (e.target.closest('.toggle-password')) {
        const button = e.target.closest('.toggle-password');
        const targetId = button.dataset.target;
        const input = document.getElementById(targetId);
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
});

// Enviar formulario de usuario
document.getElementById('usuarioForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('usuarioSubmitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar loading
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=usuarios&accion=create', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
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
                if (usuarioModal) {
                    usuarioModal.hide();
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
                text: data.message || 'Error al crear el usuario'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
    })
    .finally(() => {
        // Restaurar botón
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Delegación de eventos para botones dinámicos
document.addEventListener('click', function(e) {
    // Restablecer contraseña
    if (e.target.closest('.reset-password')) {
        e.preventDefault();
        const button = e.target.closest('.reset-password');
        const id = button.dataset.id;
        const username = button.dataset.username;
        
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        document.getElementById('resetUsuarioId').value = id;
        document.getElementById('resetPasswordMessage').innerHTML = 
            `Restablecer contraseña para el usuario <strong>${username}</strong>`;
        
        modal.show();
    }
    
    // Cambiar estado de usuario
    if (e.target.closest('.toggle-status')) {
        e.preventDefault();
        const button = e.target.closest('.toggle-status');
        const id = button.dataset.id;
        const username = button.dataset.username;
        const action = button.dataset.action;
        
        const modal = new bootstrap.Modal(document.getElementById('userStatusModal'));
        const title = document.getElementById('userStatusTitle');
        const message = document.getElementById('userStatusMessage');
        
        document.getElementById('statusUsuarioId').value = id;
        document.getElementById('statusAction').value = action;
        
        if (action === 'deactivate') {
            title.textContent = 'Desactivar Usuario';
            message.innerHTML = `¿Está seguro de desactivar al usuario <strong>${username}</strong>?<br>
                                <small class="text-muted">El usuario perderá acceso al sistema.</small>`;
        } else {
            title.textContent = 'Activar Usuario';
            message.innerHTML = `¿Está seguro de activar al usuario <strong>${username}</strong>?`;
        }
        
        modal.show();
    }
    
    // Eliminar usuario
    if (e.target.closest('.delete-user')) {
        e.preventDefault();
        const button = e.target.closest('.delete-user');
        const id = button.dataset.id;
        const username = button.dataset.username;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        document.getElementById('deleteUsuarioId').value = id;
        document.getElementById('deleteUserMessage').innerHTML = 
            `¿Está seguro de eliminar al usuario <strong>${username}</strong>?`;
        
        modal.show();
    }
});

// Enviar formulario de restablecer contraseña
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=usuarios&accion=resetPassword', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Ocultar modal
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
            if (modalInstance) {
                modalInstance.hide();
            }
            
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al restablecer la contraseña'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Enviar formulario de cambio de estado
document.getElementById('userStatusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=usuarios&accion=toggleStatus', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Ocultar modal
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('userStatusModal'));
            if (modalInstance) {
                modalInstance.hide();
            }
            
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al cambiar el estado'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Enviar formulario de eliminar usuario
document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=usuarios&accion=delete', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Ocultar modal
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
            if (modalInstance) {
                modalInstance.hide();
            }
            
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al eliminar el usuario'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
// Agrega esto al script existente, después de los otros event listeners

// Delegación de eventos para cambiar rol al hacer clic en el badge
document.addEventListener('click', function(e) {
    // Cambiar rol al hacer clic en el badge
    if (e.target.closest('.change-role')) {
        e.preventDefault();
        const badge = e.target.closest('.change-role');
        const id = badge.dataset.id;
        const username = badge.dataset.username;
        const currentRole = badge.dataset.currentRole;
        const currentRoleName = badge.dataset.currentRoleName;
        
        const modal = new bootstrap.Modal(document.getElementById('changeRoleModal'));
        document.getElementById('changeRoleUsuarioId').value = id;
        document.getElementById('currentRoleId').value = currentRole;
        document.getElementById('changeRoleMessage').innerHTML = 
            `Cambiar rol del usuario <strong>${username}</strong><br>
             <small class="text-muted">Rol actual: ${currentRoleName}</small>`;
        
        // Seleccionar el rol actual en el select
        document.getElementById('new_role_id').value = currentRole;
        
        modal.show();
    }
});

// Enviar formulario de cambio de rol
document.getElementById('changeRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=usuarios&accion=changeRole', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
    if (data.success) {
        // Ocultar modal
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('changeRoleModal'));
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Actualizar el badge sin recargar la página
        const userId = document.getElementById('changeRoleUsuarioId').value;
        const newRoleId = document.getElementById('new_role_id').value;
        const newRoleName = data.new_role_name || 'Nuevo Rol';
        
        updateRoleBadge(userId, newRoleId, newRoleName);
        
        Swal.fire({
            title: '¡Éxito!',
            text: data.message,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Error al cambiar el rol'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
})
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Verifique su conexión.'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Función para actualizar el badge del rol en la tabla (sin recargar la página)
function updateRoleBadge(userId, newRoleId, newRoleName) {
    const badge = document.querySelector(`.change-role[data-id="${userId}"]`);
    if (badge) {
        // Actualizar datos
        badge.dataset.currentRole = newRoleId;
        badge.dataset.currentRoleName = newRoleName;
        
        // Actualizar texto
        badge.textContent = newRoleName;
        
        // Actualizar color del badge
        badge.className = 'badge change-role';
        if (newRoleId == 1) {
            badge.classList.add('bg-danger');
        } else if (newRoleId == 2) {
            badge.classList.add('bg-success');
        } else {
            badge.classList.add('bg-secondary');
        }
    }
}
// Función para probar endpoints (usar en consola)
function testUsuarioEndpoint(action, extraData = {}) {
    const formData = new FormData();
    formData.append('modulo', 'usuarios');
    formData.append('accion', action);
    
    for (const key in extraData) {
        formData.append(key, extraData[key]);
    }
    
    console.log(`Testing usuarios endpoint: ${action}`);
    
    fetch('index.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
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