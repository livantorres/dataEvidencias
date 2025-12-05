<nav class="sidebar-nav">
    <div class="px-3 py-4">
        <!-- Información del usuario -->
        <div class="text-center text-white mb-4">
            <div class="mb-3">
                <i class="fas fa-user-circle fa-3x"></i>
            </div>
            <h6 class="mb-1"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></h6>
            <small class="text-white-50"><?php echo htmlspecialchars($_SESSION['rol_nombre']); ?></small>
        </div>
        
        <!-- Selector de Ciclo -->
        <div class="card bg-dark border-0 mb-4">
            <div class="card-body p-3">
                <h6 class="card-title text-white mb-2">
                    <i class="fas fa-calendar-alt me-2"></i>Ciclo Actual
                </h6>
                <form method="GET" class="ciclo-selector-form">
                    <input type="hidden" name="modulo" value="<?php echo $_GET['modulo'] ?? 'dashboard'; ?>">
                    <input type="hidden" name="accion" value="<?php echo $_GET['accion'] ?? 'index'; ?>">
                    
                    <select name="cambiar_ciclo" class="form-select form-select-sm bg-dark text-white border-secondary" 
                            onchange="this.form.submit()">
                        <option value="">Seleccionar ciclo</option>
                        <?php
                        // Obtener todos los ciclos
                        require_once 'models/Ciclo.php';
                        $database = new Database();
                        $db = $database->getConnection();
                        $cicloModel = new Ciclo($db);
                        $ciclos = $cicloModel->getAll();
                        
                        while ($ciclo = $ciclos->fetch(PDO::FETCH_ASSOC)):
                        ?>
                        <option value="<?php echo $ciclo['id']; ?>" 
                                <?php echo (isset($_SESSION['ciclo_actual']) && $_SESSION['ciclo_actual'] == $ciclo['id']) ? 'selected' : ''; ?>
                                class="<?php echo $ciclo['activo'] == 1 ? 'text-success' : 'text-secondary'; ?>">
                            <?php echo htmlspecialchars($ciclo['descripcion']); ?>
                            <?php echo $ciclo['activo'] == 1 ? ' (Activo)' : ''; ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </form>
                <div class="mt-2 text-center">
                    <small class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        <?php echo isset($_SESSION['ciclo_descripcion']) ? $_SESSION['ciclo_descripcion'] : 'Sin ciclo'; ?>
                    </small>
                </div>
            </div>
        </div>
        
        <hr class="sidebar-divider">
        
        <!-- Menú de navegación -->
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'dashboard') ? 'active' : ''; ?>" 
                   href="index.php?modulo=dashboard&accion=index">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'evidencias') ? 'active' : ''; ?>" 
                   href="index.php?modulo=evidencia&accion=index">
                    <i class="fas fa-camera"></i> Evidencias
                </a>
            </li>
            
            <?php if ($_SESSION['rol_id'] == 1): ?>
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'instituciones') ? 'active' : ''; ?>" 
                   href="index.php?modulo=institucion&accion=index">
                    <i class="fas fa-university"></i> Instituciones
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'ciclos') ? 'active' : ''; ?>" 
                   href="index.php?modulo=ciclo&accion=index">
                    <i class="fas fa-calendar-alt"></i> Ciclos
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'usuarios') ? 'active' : ''; ?>" 
                   href="index.php?modulo=usuarios&accion=index">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item mb-2">
                <a class="nav-link <?php echo (isset($_GET['modulo']) && $_GET['modulo'] == 'reportes') ? 'active' : ''; ?>" 
                   href="index.php?modulo=reportes&accion=index">
                    <i class="fas fa-chart-bar"></i> Reportes
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a class="nav-link" href="index.php?modulo=auth&accion=profile">
                    <i class="fas fa-user-cog"></i> Mi Perfil
                </a>
            </li>
			
			<li>
                                <a class="dropdown-item text-danger" href="index.php?modulo=auth&accion=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
        </ul>
        
        <hr class="sidebar-divider mt-4">
        
        <!-- Información del sistema -->
        <div class="text-center text-white-50 small mt-4">
            <div class="mb-2">
                <i class="fas fa-clock me-1"></i>
                <span class="time-display"><?php echo date('d/m/Y H:i:s'); ?></span>
            </div>
            <div class="mb-2">
                <i class="fas fa-info-circle me-1"></i>
                Versión 1.0.0
            </div>
            <div>
                <i class="fas fa-copyright me-1"></i>
                <?php echo date('Y'); ?> dataEvidencias
            </div>
        </div>
    </div>
</nav>