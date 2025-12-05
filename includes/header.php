<?php
// Inicializar variable de ciclo en sesión si no existe
if (isset($_SESSION['usuario_id']) && !isset($_SESSION['ciclo_actual'])) {
    require_once 'models/Ciclo.php';
    $database = new Database();
    $db = $database->getConnection();
    $cicloModel = new Ciclo($db);
    
    // Obtener ciclo activo por defecto
    $ciclo_activo = $cicloModel->getActiveCiclo();
    $_SESSION['ciclo_actual'] = $ciclo_activo ? $ciclo_activo['id'] : null;
    $_SESSION['ciclo_descripcion'] = $ciclo_activo ? $ciclo_activo['descripcion'] : 'No definido';
}

// Manejar cambio de ciclo desde GET
if (isset($_GET['cambiar_ciclo']) && is_numeric($_GET['cambiar_ciclo'])) {
    $nuevo_ciclo_id = $_GET['cambiar_ciclo'];
    
    require_once 'models/Ciclo.php';
    $database = new Database();
    $db = $database->getConnection();
    $cicloModel = new Ciclo($db);
    
    $ciclo = $cicloModel->getById($nuevo_ciclo_id);
    
    if ($ciclo) {
        $_SESSION['ciclo_actual'] = $ciclo['id'];
        $_SESSION['ciclo_descripcion'] = $ciclo['descripcion'];
        $_SESSION['success'] = "Ciclo cambiado a: " . $ciclo['descripcion'];
        
        // Redirigir a la misma página sin el parámetro
        $url = str_replace('&cambiar_ciclo=' . $nuevo_ciclo_id, '', $_SERVER['REQUEST_URI']);
        $url = str_replace('?cambiar_ciclo=' . $nuevo_ciclo_id, '', $url);
        header("Location: $url");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dataEvidencias - <?php echo isset($page_title) ? $page_title : 'Sistema de Gestión de Evidencias'; ?></title>
    <link rel="shortcut icon" href="assets/img/icondata.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --sidebar-width: 250px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 0.75rem 1rem;
            z-index: 1030;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: 56px;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 0.5px;
        }
        
        .navbar-brand i {
            margin-right: 10px;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,.8) !important;
            padding: 0.5rem 1rem;
        }
        
        .navbar-nav .nav-link:hover {
            color: #fff !important;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            color: #5a5c69;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fc;
            color: #4e73df;
        }
        
        /* Sidebar Styles */
        #sidebarMenu {
            position: fixed;
            top: 56px;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.8);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .sidebar-nav .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.1);
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255,255,255,.2);
            margin: 1.5rem 1.5rem;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-top: 56px;
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: calc(100vh - 56px);
            background-color: #f8f9fc;
            transition: all 0.3s;
        }
        
        .main-content.full-width {
            margin-left: 0;
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            #sidebarMenu {
                transform: translateX(-100%);
                width: 280px;
            }
            
            #sidebarMenu.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .navbar-toggler {
                border-color: rgba(255,255,255,.1);
            }
            
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
        }
        
        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-left: 4px solid var(--success-color);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
            border-left: 4px solid var(--danger-color);
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: #5a5c69;
        }
        
        /* Color Utilities */
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        
        .border-primary { border-color: var(--primary-color) !important; }
        .border-success { border-color: var(--success-color) !important; }
        .border-info { border-color: var(--info-color) !important; }
        .border-warning { border-color: var(--warning-color) !important; }
        .border-danger { border-color: var(--danger-color) !important; }
        
        /* Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Time display */
        .time-display {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <!-- Sidebar Toggle (visible solo en móviles) -->
            <button class="navbar-toggler d-lg-none me-2" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php?modulo=dashboard&accion=index">
			<img src="assets/img/icondata.png" alt="Logo dataEvidencias" height="32" class="me-2">
			<div>
				<span class="fw-bold d-block lh-1">dataEvidencias</span>
				<small class="text-muted d-none d-md-block" style="font-size: 0.7rem;">Sistema de gestión de evidencias</small>
			</div>
			  <i class="fas fa-camera me-2"></i>
		</a>
            
            <!-- User Menu -->
            <div class="navbar-collapse collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="me-2 text-end">
                                <div class="fw-bold small"><?php echo htmlspecialchars($_SESSION['nombre_completo']); ?></div>
                                <small class="text-white-50"><?php echo htmlspecialchars($_SESSION['rol_nombre']); ?></small>
                            </div>
                            <i class="fas fa-user-circle fa-2x"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="index.php?modulo=auth&accion=profile">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="index.php?modulo=auth&accion=logout">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <?php if (isset($_SESSION['usuario_id'])): ?>
        <div class="sidebar d-lg-block" id="sidebarMenu">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <main class="main-content <?php echo isset($_SESSION['usuario_id']) ? '' : 'full-width'; ?>" id="mainContent">
            <?php
            // Mostrar mensajes de éxito o error
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['success']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['success']);
            }
            
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($_SESSION['error']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['error']);
            }
            
            if (isset($_SESSION['warning'])) {
                echo '<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($_SESSION['warning']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['warning']);
            }
            
            if (isset($_SESSION['info'])) {
                echo '<div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>' . htmlspecialchars($_SESSION['info']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['info']);
            }
            ?>