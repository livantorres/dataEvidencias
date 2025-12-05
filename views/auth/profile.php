<?php
$page_title = "Mi Perfil";
?>

<style>
/* Estilos para la vista de perfil */
.profile-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 30px;
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.3);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #667eea;
    font-size: 3rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.profile-avatar-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.profile-stats {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.password-strength {
    height: 5px;
    background: #e9ecef;
    border-radius: 3px;
    margin-top: 5px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0%;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.password-strength.weak .password-strength-bar {
    background: #dc3545;
    width: 33%;
}

.password-strength.medium .password-strength-bar {
    background: #ffc107;
    width: 66%;
}

.password-strength.strong .password-strength-bar {
    background: #28a745;
    width: 100%;
}

.password-requirements {
    font-size: 0.85rem;
}

.password-requirements ul {
    padding-left: 20px;
    margin-bottom: 0;
}

.password-requirements li {
    margin-bottom: 5px;
}

.password-requirements li.valid {
    color: #28a745;
}

.password-requirements li.invalid {
    color: #6c757d;
}

.password-requirements li.valid:before {
    content: "✓ ";
    font-weight: bold;
}

.password-requirements li.invalid:before {
    content: "○ ";
}

.form-tab {
    cursor: pointer;
    padding: 15px;
    border-bottom: 3px solid transparent;
    font-weight: 500;
    transition: all 0.3s ease;
}

.form-tab:hover {
    color: #667eea;
}

.form-tab.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.upload-avatar {
    position: relative;
    cursor: pointer;
    width: 150px;
    margin: 0 auto;
}

.upload-avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.upload-avatar:hover .upload-avatar-overlay {
    opacity: 1;
}

.upload-avatar-text {
    color: white;
    text-align: center;
    padding: 10px;
}

.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.info-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.info-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 5px;
}

.info-value {
    color: #212529;
    font-size: 1.1rem;
}

.activity-log {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    padding: 10px 15px;
    border-left: 3px solid #667eea;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 0 5px 5px 0;
}

.activity-time {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        padding: 20px;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }
    
    .form-tab {
        padding: 10px;
        font-size: 0.9rem;
    }
}
</style>

<div class="container-fluid py-4">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-circle me-2"></i>Mi Perfil
                    </h1>
                    <p class="text-muted mb-0">Gestiona tu información personal y seguridad</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6 p-2">
                        <i class="fas fa-user-shield me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['rol_nombre']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alertas -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Columna izquierda - Información general -->
        <div class="col-lg-4 mb-4">
            <div class="card profile-card h-100">
                <div class="profile-header">
                    <!-- Avatar del usuario -->
                    <div class="upload-avatar" onclick="document.getElementById('avatarInput').click()">
                        <?php
                        // Verificar si existe avatar
                        $avatar_path = "storage/usuarios/avatars/" . $_SESSION['usuario_id'] . ".*";
                        $avatar_files = glob($avatar_path);
                        $avatar_img = count($avatar_files) > 0 ? $avatar_files[0] : null;
                        ?>
                        
                        <?php if ($avatar_img): ?>
                        <img src="<?php echo $avatar_img; ?>" 
                             class="avatar-preview" 
                             alt="<?php echo htmlspecialchars($_SESSION['nombre_completo']); ?>"
                             id="avatarPreview">
                        <?php else: ?>
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="upload-avatar-overlay">
                            <div class="upload-avatar-text">
                                <i class="fas fa-camera fa-2x mb-2"></i>
                                <p class="mb-0">Cambiar foto</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input oculto para subir avatar -->
                    <form id="avatarForm" action="index.php?modulo=auth&accion=uploadAvatar" method="POST" enctype="multipart/form-data" style="display: none;">
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="uploadAvatar(this.files[0])">
                    </form>
                    
                    <h4 class="mb-2"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h4>
                    <p class="mb-1">
                        <i class="fas fa-envelope me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['email']); ?>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-user-tag me-1"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </p>
                </div>
                
                <div class="card-body">
                    <!-- Estadísticas -->
                    <div class="profile-stats mb-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="stat-item">
                                    <span class="stat-number" id="totalEvidencias">0</span>
                                    <span class="stat-label">Evidencias</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <span class="stat-number" id="totalInstituciones">0</span>
                                    <span class="stat-label">Instituciones</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información de la cuenta -->
                    <div class="info-card">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información de la Cuenta</h6>
                        <div class="mb-2">
                            <div class="info-label">ID de Usuario</div>
                            <div class="info-value">#<?php echo $_SESSION['usuario_id']; ?></div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">Rol</div>
                            <div class="info-value">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($_SESSION['rol_nombre']); ?></span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">Último Acceso</div>
                            <div class="info-value">
                                <i class="fas fa-clock me-1"></i>
                                <span id="lastLogin">Cargando...</span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Miembro desde</div>
                            <div class="info-value">
                                <i class="fas fa-calendar me-1"></i>
                                <span id="memberSince">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actividad reciente -->
                    <div class="mt-4">
                        <h6><i class="fas fa-history me-2"></i>Actividad Reciente</h6>
                        <div class="activity-log mt-3" id="activityLog">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Cargando actividad...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Columna derecha - Formularios -->
        <div class="col-lg-8">
            <!-- Pestañas -->
            <div class="card profile-card mb-4">
                <div class="card-header bg-white border-0">
                    <ul class="nav nav-tabs nav-fill" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link form-tab active" 
                               data-bs-toggle="tab" 
                               href="#personalInfoTab"
                               onclick="switchTab('personalInfoTab')">
                                <i class="fas fa-user-edit me-2"></i>Información Personal
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link form-tab" 
                               data-bs-toggle="tab" 
                               href="#securityTab"
                               onclick="switchTab('securityTab')">
                                <i class="fas fa-lock me-2"></i>Seguridad
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link form-tab" 
                               data-bs-toggle="tab" 
                               href="#preferencesTab"
                               onclick="switchTab('preferencesTab')">
                                <i class="fas fa-cog me-2"></i>Preferencias
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <!-- Pestaña 1: Información Personal -->
                    <div id="personalInfoTab" class="tab-content active">
                        <h5 class="mb-4"><i class="fas fa-user-edit me-2"></i>Editar Información Personal</h5>
                        
                        <form id="personalInfoForm" action="index.php?modulo=auth&accion=profile" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_completo" class="form-label">Nombre Completo *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre_completo" 
                                           name="nombre_completo" 
                                           value="<?php echo isset($usuario['nombre_completo']) ? htmlspecialchars($usuario['nombre_completo']) : ''; ?>"
                                           required>
                                    <div class="form-text">Tu nombre y apellidos</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Correo Electrónico *</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo isset($usuario['email']) ? htmlspecialchars($usuario['email']) : ''; ?>"
                                           required>
                                    <div class="form-text">Para notificaciones y recuperación</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?php echo isset($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : ''; ?>">
                                    <div class="form-text">Opcional - Para contacto</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="dni" class="form-label">DNI / Identificación</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dni" 
                                           name="dni" 
                                           value="<?php echo isset($usuario['dni']) ? htmlspecialchars($usuario['dni']) : ''; ?>">
                                    <div class="form-text">Número de identificación</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="2"><?php echo isset($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : ''; ?></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Los cambios en tu información personal se reflejarán inmediatamente en el sistema.
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="resetPersonalForm()">
                                    <i class="fas fa-undo me-1"></i> Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pestaña 2: Seguridad -->
                    <div id="securityTab" class="tab-content">
                        <h5 class="mb-4"><i class="fas fa-lock me-2"></i>Seguridad y Contraseña</h5>
                        
                        <form id="changePasswordForm" action="index.php?modulo=auth&accion=changePassword" method="POST">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="current_password" class="form-label">Contraseña Actual *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Ingresa tu contraseña actual para verificar tu identidad</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">Nueva Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password" 
                                               name="new_password" 
                                               required
                                               onkeyup="checkPasswordStrength()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2" id="passwordStrength">
                                        <div class="password-strength-bar"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña *</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               required
                                               onkeyup="checkPasswordMatch()">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text" id="passwordMatchText"></div>
                                </div>
                            </div>
                            
                            <!-- Requisitos de contraseña -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Requisitos de Seguridad</h6>
                                </div>
                                <div class="card-body password-requirements">
                                    <ul class="mb-0">
                                        <li id="reqLength" class="invalid">Mínimo 8 caracteres</li>
                                        <li id="reqUppercase" class="invalid">Al menos una mayúscula</li>
                                        <li id="reqLowercase" class="invalid">Al menos una minúscula</li>
                                        <li id="reqNumber" class="invalid">Al menos un número</li>
                                        <li id="reqSpecial" class="invalid">Al menos un carácter especial</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Después de cambiar tu contraseña, se cerrará tu sesión y deberás iniciar sesión nuevamente.
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="resetPasswordForm()">
                                    <i class="fas fa-undo me-1"></i> Limpiar
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitPasswordBtn">
                                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Pestaña 3: Preferencias -->
                    <div id="preferencesTab" class="tab-content">
                        <h5 class="mb-4"><i class="fas fa-cog me-2"></i>Preferencias del Sistema</h5>
                        
                        <form id="preferencesForm" action="index.php?modulo=auth&accion=updatePreferences" method="POST">
                            <!-- Tema -->
                            <div class="mb-4">
                                <label class="form-label d-block">Tema de Interfaz</label>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check card theme-option" onclick="selectTheme('light')">
                                            <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light" checked>
                                            <label class="form-check-label card-body text-center" for="themeLight">
                                                <i class="fas fa-sun fa-2x mb-2 text-warning"></i>
                                                <h6>Claro</h6>
                                                <small class="text-muted">Tema predeterminado</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check card theme-option" onclick="selectTheme('dark')">
                                            <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark">
                                            <label class="form-check-label card-body text-center" for="themeDark">
                                                <i class="fas fa-moon fa-2x mb-2 text-secondary"></i>
                                                <h6>Oscuro</h6>
                                                <small class="text-muted">Modo nocturno</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check card theme-option" onclick="selectTheme('auto')">
                                            <input class="form-check-input" type="radio" name="theme" id="themeAuto" value="auto">
                                            <label class="form-check-label card-body text-center" for="themeAuto">
                                                <i class="fas fa-adjust fa-2x mb-2 text-info"></i>
                                                <h6>Automático</h6>
                                                <small class="text-muted">Según sistema</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notificaciones -->
                            <div class="mb-4">
                                <h6 class="mb-3"><i class="fas fa-bell me-2"></i>Notificaciones</h6>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="notifyEmail" name="notify_email" checked>
                                    <label class="form-check-label" for="notifyEmail">
                                        Notificaciones por correo electrónico
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="notifyEvidences" name="notify_evidences" checked>
                                    <label class="form-check-label" for="notifyEvidences">
                                        Notificarme sobre nuevas evidencias
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="notifyUpdates" name="notify_updates" checked>
                                    <label class="form-check-label" for="notifyUpdates">
                                        Actualizaciones del sistema
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Preferencias de visualización -->
                            <div class="mb-4">
                                <h6 class="mb-3"><i class="fas fa-eye me-2"></i>Visualización</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="items_per_page" class="form-label">Elementos por página</label>
                                        <select class="form-select" id="items_per_page" name="items_per_page">
                                            <option value="10">10 elementos</option>
                                            <option value="25" selected>25 elementos</option>
                                            <option value="50">50 elementos</option>
                                            <option value="100">100 elementos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="date_format" class="form-label">Formato de fecha</label>
                                        <select class="form-select" id="date_format" name="date_format">
                                            <option value="d/m/Y" selected>DD/MM/AAAA</option>
                                            <option value="m/d/Y">MM/DD/AAAA</option>
                                            <option value="Y-m-d">AAAA-MM-DD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Las preferencias se guardarán en tu navegador y se aplicarán automáticamente en tu próxima visita.
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="resetPreferences()">
                                    <i class="fas fa-undo me-1"></i> Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Preferencias
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card profile-card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Seguridad de la Cuenta</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Autenticación Básica</h6>
                                    <small class="text-muted">Usuario y contraseña</small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Sesión Activa</h6>
                                    <small class="text-muted">Última actividad: <span id="lastActivity">Hace 5 min</span></small>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button class="btn btn-outline-danger" onclick="confirmLogoutAll()">
                                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Todas las Sesiones
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card profile-card h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-download me-2"></i>Exportar Datos</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Puedes descargar tus datos personales en formato estándar.</p>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="exportData('pdf')">
                                    <i class="fas fa-file-pdf me-1"></i> Exportar a PDF
                                </button>
                                <button class="btn btn-outline-success" onclick="exportData('json')">
                                    <i class="fas fa-file-code me-1"></i> Exportar a JSON
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let originalPersonalData = {};
let passwordValid = false;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Guardar datos originales del formulario
    saveOriginalData();
    
    // Cargar estadísticas
    loadUserStats();
    
    // Cargar actividad
    loadUserActivity();
    
    // Cargar preferencias guardadas
    loadUserPreferences();
    
    // Configurar validación de formularios
    setupFormValidation();
});

// Guardar datos originales
function saveOriginalData() {
    originalPersonalData = {
        nombre_completo: document.getElementById('nombre_completo').value,
        email: document.getElementById('email').value,
        telefono: document.getElementById('telefono').value || '',
        dni: document.getElementById('dni').value || '',
        direccion: document.getElementById('direccion').value || ''
    };
}

// Cambiar entre pestañas
function switchTab(tabId) {
    // Ocultar todas las pestañas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remover active de todos los tabs
    document.querySelectorAll('.form-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Mostrar pestaña seleccionada
    document.getElementById(tabId).classList.add('active');
    
    // Activar tab correspondiente
    document.querySelector(`[href="#${tabId}"]`).classList.add('active');
}

// Cargar estadísticas del usuario
async function loadUserStats() {
    try {
        const response = await fetch(`index.php?modulo=auth&accion=getUserStats&user_id=<?php echo $_SESSION['usuario_id']; ?>`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('totalEvidencias').textContent = data.total_evidencias || 0;
            document.getElementById('totalInstituciones').textContent = data.total_instituciones || 0;
            document.getElementById('lastLogin').textContent = data.last_login || 'Hoy';
            document.getElementById('memberSince').textContent = data.member_since || 'No disponible';
            document.getElementById('lastActivity').textContent = data.last_activity || 'Hace unos momentos';
        }
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}

// Cargar actividad del usuario
async function loadUserActivity() {
    try {
        const response = await fetch(`index.php?modulo=auth&accion=getUserActivity&user_id=<?php echo $_SESSION['usuario_id']; ?>`);
        const data = await response.json();
        
        const activityLog = document.getElementById('activityLog');
        
        if (data.success && data.activities && data.activities.length > 0) {
            let html = '';
            data.activities.forEach(activity => {
                html += `
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${activity.action}</strong>
                                <p class="mb-0 small">${activity.description}</p>
                            </div>
                            <div class="activity-time">${activity.time}</div>
                        </div>
                    </div>
                `;
            });
            activityLog.innerHTML = html;
        } else {
            activityLog.innerHTML = `
                <div class="text-center py-3">
                    <i class="fas fa-history fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No hay actividad registrada</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error cargando actividad:', error);
    }
}

// Subir avatar
async function uploadAvatar(file) {
    if (!file) return;
    
    // Validar tamaño (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire('Error', 'La imagen no debe superar los 2MB', 'error');
        return;
    }
    
    // Validar tipo
    if (!file.type.startsWith('image/')) {
        Swal.fire('Error', 'Solo se permiten imágenes', 'error');
        return;
    }
    
    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        if (preview) {
            preview.src = e.target.result;
        } else {
            // Crear imagen si no existe
            const avatarContainer = document.querySelector('.profile-avatar');
            if (avatarContainer) {
                avatarContainer.innerHTML = `<img src="${e.target.result}" class="avatar-preview" id="avatarPreview" alt="Avatar">`;
            }
        }
    };
    reader.readAsDataURL(file);
    
    // Subir al servidor
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('user_id', <?php echo $_SESSION['usuario_id']; ?>);
    
    try {
        const response = await fetch('index.php?modulo=auth&accion=uploadAvatar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire('Éxito', 'Avatar actualizado correctamente', 'success');
        } else {
            Swal.fire('Error', result.message || 'Error al subir avatar', 'error');
        }
    } catch (error) {
        console.error('Error subiendo avatar:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    }
}

// Verificar fortaleza de contraseña
function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const strengthBar = document.querySelector('.password-strength');
    const strength = calculatePasswordStrength(password);
    
    // Actualizar barra
    strengthBar.className = 'password-strength ' + strength.level;
    
    // Actualizar requisitos
    updatePasswordRequirements(password);
    
    // Verificar si todas las condiciones se cumplen
    passwordValid = strength.level === 'strong';
    updateSubmitButton();
}

// Calcular fortaleza de contraseña
function calculatePasswordStrength(password) {
    let score = 0;
    
    // Longitud
    if (password.length >= 8) score += 20;
    if (password.length >= 12) score += 10;
    
    // Mayúsculas
    if (/[A-Z]/.test(password)) score += 20;
    
    // Minúsculas
    if (/[a-z]/.test(password)) score += 20;
    
    // Números
    if (/[0-9]/.test(password)) score += 20;
    
    // Caracteres especiales
    if (/[^A-Za-z0-9]/.test(password)) score += 20;
    
    // Determinar nivel
    if (score >= 80) return { score, level: 'strong' };
    if (score >= 50) return { score, level: 'medium' };
    return { score, level: 'weak' };
}

// Actualizar requisitos de contraseña
function updatePasswordRequirements(password) {
    const requirements = {
        reqLength: password.length >= 8,
        reqUppercase: /[A-Z]/.test(password),
        reqLowercase: /[a-z]/.test(password),
        reqNumber: /[0-9]/.test(password),
        reqSpecial: /[^A-Za-z0-9]/.test(password)
    };
    
    Object.keys(requirements).forEach(reqId => {
        const element = document.getElementById(reqId);
        if (element) {
            if (requirements[reqId]) {
                element.classList.remove('invalid');
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
            }
        }
    });
}

// Verificar coincidencia de contraseñas
function checkPasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const matchText = document.getElementById('passwordMatchText');
    
    if (!password || !confirm) {
        matchText.textContent = '';
        matchText.className = 'form-text';
        return;
    }
    
    if (password === confirm) {
        matchText.textContent = '✓ Las contraseñas coinciden';
        matchText.className = 'form-text text-success';
    } else {
        matchText.textContent = '✗ Las contraseñas no coinciden';
        matchText.className = 'form-text text-danger';
    }
    
    updateSubmitButton();
}

// Actualizar estado del botón de envío
function updateSubmitButton() {
    const password = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    const current = document.getElementById('current_password').value;
    const button = document.getElementById('submitPasswordBtn');
    
    const allFilled = password && confirm && current;
    const passwordsMatch = password === confirm;
    
    button.disabled = !(allFilled && passwordsMatch && passwordValid);
}

// Mostrar/ocultar contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Restablecer formulario personal
function resetPersonalForm() {
    if (confirm('¿Restablecer los cambios?')) {
        document.getElementById('nombre_completo').value = originalPersonalData.nombre_completo;
        document.getElementById('email').value = originalPersonalData.email;
        document.getElementById('telefono').value = originalPersonalData.telefono;
        document.getElementById('dni').value = originalPersonalData.dni;
        document.getElementById('direccion').value = originalPersonalData.direccion;
    }
}

// Restablecer formulario de contraseña
function resetPasswordForm() {
    if (confirm('¿Limpiar el formulario de contraseña?')) {
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';
        
        // Resetear indicadores
        document.querySelector('.password-strength').className = 'password-strength';
        document.getElementById('passwordMatchText').textContent = '';
        document.getElementById('passwordMatchText').className = 'form-text';
        
        // Resetear requisitos
        document.querySelectorAll('.password-requirements li').forEach(li => {
            li.classList.remove('valid');
            li.classList.add('invalid');
        });
        
        updateSubmitButton();
    }
}

// Seleccionar tema
function selectTheme(theme) {
    document.getElementById(`theme${theme.charAt(0).toUpperCase() + theme.slice(1)}`).checked = true;
    
    // Aplicar tema inmediatamente (opcional)
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem('userTheme', theme);
}

// Cargar preferencias del usuario
function loadUserPreferences() {
    const savedTheme = localStorage.getItem('userTheme') || 'light';
    selectTheme(savedTheme);
    
    // Cargar otras preferencias de localStorage
    const notifyEmail = localStorage.getItem('notifyEmail') !== 'false';
    const notifyEvidences = localStorage.getItem('notifyEvidences') !== 'false';
    const notifyUpdates = localStorage.getItem('notifyUpdates') !== 'false';
    const itemsPerPage = localStorage.getItem('itemsPerPage') || '25';
    const dateFormat = localStorage.getItem('dateFormat') || 'd/m/Y';
    
    document.getElementById('notifyEmail').checked = notifyEmail;
    document.getElementById('notifyEvidences').checked = notifyEvidences;
    document.getElementById('notifyUpdates').checked = notifyUpdates;
    document.getElementById('items_per_page').value = itemsPerPage;
    document.getElementById('date_format').value = dateFormat;
}

// Restablecer preferencias
function resetPreferences() {
    if (confirm('¿Restablecer todas las preferencias a los valores predeterminados?')) {
        localStorage.clear();
        loadUserPreferences();
        Swal.fire('Restablecido', 'Las preferencias se han restablecido', 'info');
    }
}

// Configurar validación de formularios
function setupFormValidation() {
    // Validación de información personal
    const personalForm = document.getElementById('personalInfoForm');
    if (personalForm) {
        personalForm.addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre_completo').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (!nombre || !email) {
                e.preventDefault();
                Swal.fire('Error', 'Nombre y correo electrónico son obligatorios', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                Swal.fire('Error', 'Ingresa un correo electrónico válido', 'error');
                return;
            }
            
            // Mostrar confirmación
            if (!confirm('¿Guardar los cambios en tu información personal?')) {
                e.preventDefault();
            }
        });
    }
    
    // Validación de cambio de contraseña
    const passwordForm = document.getElementById('changePasswordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const current = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('confirm_password').value;
            
            if (!current || !newPass || !confirmPass) {
                e.preventDefault();
                Swal.fire('Error', 'Todos los campos son obligatorios', 'error');
                return;
            }
            
            if (newPass !== confirmPass) {
                e.preventDefault();
                Swal.fire('Error', 'Las contraseñas nuevas no coinciden', 'error');
                return;
            }
            
            if (!passwordValid) {
                e.preventDefault();
                Swal.fire('Error', 'La nueva contraseña no cumple con los requisitos de seguridad', 'error');
                return;
            }
            
            if (!confirm('¿Estás seguro de cambiar tu contraseña? Se cerrará tu sesión actual.')) {
                e.preventDefault();
            }
        });
    }
}

// Validar email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Confirmar cierre de todas las sesiones
function confirmLogoutAll() {
    Swal.fire({
        title: '¿Cerrar todas las sesiones?',
        text: 'Se cerrarán todas tus sesiones activas en otros dispositivos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cerrar todas',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php?modulo=auth&accion=logoutAll';
        }
    });
}

// Exportar datos
function exportData(format) {
    Swal.fire({
        title: 'Exportando datos...',
        text: 'Preparando archivo para descarga',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    
    // Simular exportación (implementar según backend)
    setTimeout(() => {
        Swal.close();
        
        let url, filename;
        if (format === 'pdf') {
            url = 'index.php?modulo=auth&accion=exportPDF&user_id=<?php echo $_SESSION['usuario_id']; ?>';
            filename = `perfil_<?php echo $_SESSION['username']; ?>_${new Date().toISOString().split('T')[0]}.pdf`;
        } else {
            url = 'index.php?modulo=auth&accion=exportJSON&user_id=<?php echo $_SESSION['usuario_id']; ?>';
            filename = `perfil_<?php echo $_SESSION['username']; ?>_${new Date().toISOString().split('T')[0]}.json`;
        }
        
        // Crear enlace temporal para descarga
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        Swal.fire('Éxito', `Archivo ${format.toUpperCase()} generado`, 'success');
    }, 1500);
}

// Mostrar/ocultar contraseña con tecla
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'h') {
        const activeInput = document.activeElement;
        if (activeInput && activeInput.type === 'password') {
            togglePassword(activeInput.id);
        }
    }
});
</script>