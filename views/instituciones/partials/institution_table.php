<?php if (isset($instituciones) && $instituciones->rowCount() > 0): ?>
<?php while ($institucion = $instituciones->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
    <td><?php echo $institucion['id']; ?></td>
    <td>
        <strong><?php echo htmlspecialchars($institucion['nombre']); ?></strong>
        <?php if (isset($institucion['total_imagenes']) && $institucion['total_imagenes'] > 0): ?>
        <small class="text-muted d-block">
            <i class="fas fa-image fa-xs me-1"></i>
            <?php echo $institucion['total_imagenes']; ?> imágenes
        </small>
        <?php endif; ?>
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
        $escudo_img = count($escudo_files) > 0 ? $escudo_files[0] : 'assets/img/default-institution.png';
        $escudo_alt = htmlspecialchars($institucion['nombre']);
        ?>
        
        <img src="<?php echo $escudo_img; ?>" 
             class="escudo-thumbnail rounded-circle" 
             style="width: 50px; height: 50px; object-fit: cover;"
             alt="Escudo <?php echo $escudo_alt; ?>"
             title="Haz clic para ver escudo"
             onclick="viewEscudoPreview('<?php echo $escudo_img; ?>', '<?php echo addslashes($escudo_alt); ?>')"
             onerror="this.src='assets/img/default-institution.png'">
    </td>
    <td>
        <div class="d-flex flex-column">
            <span class="badge bg-info mb-1"><?php echo $institucion['total_evidencias'] ?? 0; ?> evidencias</span>
            <?php if (isset($institucion['total_imagenes']) && $institucion['total_imagenes'] > 0): ?>
            <span class="badge bg-secondary">
                <?php echo $institucion['total_imagenes']; ?> imágenes
            </span>
            <?php endif; ?>
        </div>
    </td>
    <td>
        <?php if (isset($institucion['ultima_evidencia_fecha']) && $institucion['ultima_evidencia_fecha']): ?>
        <small class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i>
            <?php echo date('d/m/Y', strtotime($institucion['ultima_evidencia_fecha'])); ?>
        </small>
        <?php else: ?>
        <span class="badge bg-light text-dark">Sin evidencias</span>
        <?php endif; ?>
    </td>
    <td>
        <div class="btn-group" role="group">
            <button type="button" 
                    class="btn btn-sm btn-outline-primary" 
                    onclick="openEditInstitucionModal(<?php echo $institucion['id']; ?>)" 
                    title="Editar institución">
                <i class="fas fa-edit"></i>
            </button>
            
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
<?php else: ?>
<tr>
    <td colspan="8" class="text-center py-4">
        <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
        <div class="text-muted">
            <i class="fas fa-search fa-2x mb-3"></i>
            <h5>No se encontraron instituciones</h5>
            <p>No hay instituciones que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
        </div>
        <?php else: ?>
        <div class="text-muted">
            <i class="fas fa-university fa-2x mb-3"></i>
            <h5>No hay instituciones registradas</h5>
            <p>Comience agregando su primera institución</p>
            <button type="button" class="btn btn-primary mt-2" onclick="openCreateInstitucionModal()">
                <i class="fas fa-plus me-2"></i> Agregar Institución
            </button>
        </div>
        <?php endif; ?>
    </td>
</tr>
<?php endif; ?>