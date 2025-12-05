<?php
// Definir título de página
$page_title = "Gestión de Usuarios";
?>

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
                                ?>">
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
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.dataset.target;
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
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
                if (usuarioModal) {
                    usuarioModal.hide();
                }
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Error al crear el usuario', 'error');
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

// Restablecer contraseña
document.querySelectorAll('.reset-password').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const username = this.dataset.username;
        
        // Verificar que Bootstrap esté cargado
        if (typeof bootstrap === 'undefined') {
            alert('El sistema no está completamente cargado. Recargue la página.');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        document.getElementById('resetUsuarioId').value = id;
        document.getElementById('resetPasswordMessage').innerHTML = 
            `Restablecer contraseña para el usuario <strong>${username}</strong>`;
        
        modal.show();
    });
});

// Cambiar estado de usuario
document.querySelectorAll('.toggle-status').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const username = this.dataset.username;
        const action = this.dataset.action;
        
        // Verificar que Bootstrap esté cargado
        if (typeof bootstrap === 'undefined') {
            alert('El sistema no está completamente cargado. Recargue la página.');
            return;
        }
        
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
    });
});

// Eliminar usuario
document.querySelectorAll('.delete-user').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const username = this.dataset.username;
        
        // Verificar que Bootstrap esté cargado
        if (typeof bootstrap === 'undefined') {
            alert('El sistema no está completamente cargado. Recargue la página.');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        document.getElementById('deleteUsuarioId').value = id;
        document.getElementById('deleteUserMessage').innerHTML = 
            `¿Está seguro de eliminar al usuario <strong>${username}</strong>?`;
        
        modal.show();
    });
});

// Enviar formulario de restablecer contraseña
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'usuarios');
    formData.append('accion', 'resetPassword');
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || 'Error al restablecer la contraseña');
        }
    });
});

// Enviar formulario de cambio de estado
document.getElementById('userStatusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'usuarios');
    formData.append('accion', 'toggleStatus');
    
    fetch('index.php', {
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
    });
});

// Enviar formulario de eliminar usuario
document.getElementById('deleteUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('modulo', 'usuarios');
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
            alert(data.message || 'Error al eliminar el usuario');
        }
    });
});
</script>