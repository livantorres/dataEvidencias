<?php
// Definir título de página
$page_title = "Dashboard";
?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard</h1>
    
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user"></i> Bienvenido, <?php echo $_SESSION['nombre_completo']; ?>
                    </h5>
                    <p class="card-text">
                        Rol: <span class="badge bg-light text-dark"><?php echo $_SESSION['rol_nombre']; ?></span>
                        | Último acceso: <span class="time-display"><?php echo date('d/m/Y H:i:s'); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary card-statistic">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Instituciones</h6>
                            <h3 class="mb-0"><?php echo $estadisticas['total_instituciones']; ?></h3>
                            <small class="text-muted">Activas</small>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-success card-statistic">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Evidencias Totales</h6>
                            <h3 class="mb-0"><?php echo $estadisticas['total_evidencias']; ?></h3>
                            <small class="text-muted">Registradas</small>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-camera fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-info card-statistic">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Ciclos Activos</h6>
                            <h3 class="mb-0"><?php echo $estadisticas['total_ciclos']; ?></h3>
                            <small class="text-muted">En curso</small>
                        </div>
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-warning card-statistic">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Evidencias Hoy</h6>
                            <h3 class="mb-0"><?php echo $estadisticas['evidencias_hoy']; ?></h3>
                            <small class="text-muted">Hoy <?php echo date('d/m/Y'); ?></small>
                        </div>
                        <div class="icon-circle bg-warning text-white">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="row">
        <!-- Evidencias recientes -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> Evidencias Recientes
                    </h5>
                    <a href="index.php?modulo=evidencia&accion=index" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Nueva
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Institución</th>
                                    <th>Ciclo</th>
                                    <th>Fecha</th>
                                    <th>Registrado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($evidencias_recientes->rowCount() > 0): ?>
                                    <?php while ($evidencia = $evidencias_recientes->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evidencia['institucion_nombre']); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($evidencia['ciclo_descripcion']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($evidencia['fecha'])); ?></td>
                                        <td><?php echo htmlspecialchars($evidencia['usuario_nombre'] ?? 'Sistema'); ?></td>
                                        <td>
                                            <a href="index.php?modulo=evidencia&accion=view&id=<?php echo $evidencia['id']; ?>" 
                                               class="btn btn-sm btn-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="Ver evidencia">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                            No hay evidencias registradas<br>
                                            <a href="index.php?modulo=evidencia&accion=index" class="btn btn-sm btn-primary mt-2">
                                                Registrar primera evidencia
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar derecha -->
        <div class="col-lg-4">
            <!-- Instituciones con más evidencias -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Top Instituciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php 
                        $instituciones_activas->execute(); // Asegurarse de ejecutar nuevamente
                        while ($institucion = $instituciones_activas->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($institucion['nombre']); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars($institucion['ciudad']); ?></small>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                <?php echo $institucion['total_evidencias']; ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            
            <!-- Evidencias por ciclo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Evidencias por Ciclo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php 
                        $evidencias_por_ciclo->execute(); // Asegurarse de ejecutar nuevamente
                        while ($ciclo = $evidencias_por_ciclo->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Ciclo <?php echo htmlspecialchars($ciclo['descripcion']); ?></span>
                            <span class="badge bg-success rounded-pill">
                                <?php echo $ciclo['total']; ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?modulo=evidencia&accion=index" 
                           class="btn btn-primary">
                            <i class="fas fa-camera"></i> Registrar Evidencia
                        </a>
                        
                        <?php if ($_SESSION['rol_id'] == 1): ?>
                        <a href="index.php?modulo=institucion&accion=index" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-university"></i> Gestionar Instituciones
                        </a>
                        <a href="index.php?modulo=ciclo&accion=index" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt"></i> Gestionar Ciclos
                        </a>
                        <?php endif; ?>
                        
                        <a href="index.php?modulo=auth&accion=profile" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-user-cog"></i> Mi Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-statistic {
    transition: transform 0.3s ease;
}

.card-statistic:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.table th {
    font-weight: 600;
    background-color: #f8f9fc;
}
</style>