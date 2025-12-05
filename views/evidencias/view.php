<?php
// Definir título de página
$page_title = "Ver Evidencia";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-camera me-2"></i> Ver Evidencia
        </h1>
        <div>
            <a href="index.php?modulo=evidencia&accion=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
            <a href="index.php?modulo=evidencia&accion=download&id=<?php echo $evidencia['id']; ?>" 
               class="btn btn-primary">
                <i class="fas fa-download me-1"></i> Descargar ZIP
            </a>
            <?php if ($_SESSION['rol_id'] == 1 || $evidencia['usuario_id'] == $_SESSION['usuario_id']): ?>
            <a href="index.php?modulo=evidencia&accion=edit&id=<?php echo $evidencia['id']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDeleteEvi(<?php echo $evidencia['id']; ?>)">
                <i class="fas fa-trash me-1"></i> Eliminar
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Información de la evidencia -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información de la Evidencia</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Institución:</strong><br>
                            <?php echo htmlspecialchars($evidencia['institucion_nombre']); ?></p>
                            
                            <p><strong>Ciudad:</strong><br>
                            <?php echo htmlspecialchars($evidencia['ciudad']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ciclo:</strong><br>
                            <?php echo htmlspecialchars($evidencia['ciclo_descripcion']); ?></p>
                            
                            <p><strong>Fecha:</strong><br>
                            <?php echo date('d/m/Y', strtotime($evidencia['fecha'])); ?></p>
                        </div>
                    </div>
                    
                    <?php if (!empty($evidencia['descripcion'])): ?>
                    <hr>
                    <p><strong>Descripción:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($evidencia['descripcion'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalles Técnicos</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID de Evidencia:</strong><br>
                    <?php echo $evidencia['id']; ?></p>
                    
                    <p><strong>Total de imágenes:</strong><br>
                    <?php echo $imagenes->rowCount(); ?></p>
                    
                    <p><strong>Creado por:</strong><br>
                    Usuario ID: <?php echo $evidencia['usuario_id']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Galería de imágenes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Imágenes de la Evidencia</h5>
            <span class="badge bg-primary"><?php echo $imagenes->rowCount(); ?> imágenes</span>
        </div>
        <div class="card-body">
            <?php if ($imagenes->rowCount() > 0): ?>
            <div class="row">
                <?php while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover; cursor: pointer;"
                             onclick="openImageModal('<?php echo htmlspecialchars($imagen['ruta']); ?>')"
                             alt="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>"
                             loading="lazy">
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate">
                                <?php echo htmlspecialchars($imagen['nombre_archivo']); ?>
                            </small>
                            <small class="text-muted">
                                <?php echo number_format($imagen['tamaño'] / 1024, 2); ?> KB
                            </small>
                            <?php if ($_SESSION['rol_id'] == 1 || $evidencia['usuario_id'] == $_SESSION['usuario_id']): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-danger mt-2 w-100"
                                    onclick="deleteImage(<?php echo $imagen['id']; ?>, '<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No hay imágenes</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para imagen grande -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa de imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<script>
// Modal para ver imagen en grande
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Eliminar evidencia - VERSIÓN SIMPLIFICADA
function confirmDeleteEvi(evidenciaId) {
    console.log('Eliminar evidencia ID:', evidenciaId);

    Swal.fire({
        title: '¿Eliminar evidencia?',
        html: `¿Está seguro de eliminar esta evidencia?<br>
               Se eliminarán todas las imágenes asociadas.<br>
               <strong>Esta acción no se puede deshacer.</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        allowOutsideClick: false
    }).then((result) => {
        if (!result.isConfirmed) return;

        console.log('Usuario confirmó eliminación de evidencia ID:', evidenciaId);

        Swal.fire({
            title: 'Eliminando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        // 🚀 Crear correctamente el FormData
        const formData = new FormData();
        formData.append('modulo', 'evidencia');
        formData.append('accion', 'delete');
        formData.append('id', evidenciaId);

        console.log('URL de la petición:', 'index.php?modulo=evidencia&accion=delete');
        console.log('Datos enviados:', {
            modulo: 'evidencia',
            accion: 'deleteEvi',
            id: evidenciaId
        });

        fetch('index.php?modulo=evidencia&accion=delete', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(response => {
            console.log('Status response:', response.status, response.statusText);
            return response.text();
        })
        .then(text => {
            console.log('Respuesta completa:', text.substring(0, 500));
            try {
                return JSON.parse(text);
            } catch (err) {
                console.error("No es JSON válido:", text);
                throw new Error("Respuesta no JSON del servidor");
            }
        })
        .then(data => {
            Swal.close();

            if (data.success) {
                Swal.fire({
                    title: '¡Eliminada!',
                    text: data.message || 'Evidencia eliminada exitosamente',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php?modulo=evidencia&accion=index';
                });
            } else {
                Swal.fire('Error', data.message || 'Error al eliminar la evidencia', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', error.message, 'error');
        });
    });
}

// Eliminar imagen
async function deleteImage(imageId, imageName) {
    console.log('Eliminando imagen ID:', imageId, 'Nombre:', imageName);
    
    const result = await Swal.fire({
        title: '¿Eliminar imagen?',
        html: `¿Está seguro de eliminar la imagen <strong>${imageName}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    // Mostrar loader
    Swal.fire({
        title: 'Eliminando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        // Crear FormData
        const formData = new FormData();
        formData.append('modulo', 'evidencia');
        formData.append('accion', 'deleteImage');
        formData.append('imagen_id', imageId);
        
        console.log('Enviando petición DELETE a:', 'index.php?modulo=evidencia&accion=deleteImage');
        
        // Enviar petición
        const response = await fetch('index.php?modulo=evidencia&accion=deleteImage', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin' // Incluir cookies de sesión
        });
        
        console.log('Response status:', response.status, response.statusText);
        
        // Verificar si la respuesta es exitosa
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error en respuesta HTTP:', errorText);
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
        }
        
        // Obtener el texto de respuesta
        const responseText = await response.text();
        console.log('Respuesta completa:', responseText);
        
        // Verificar si es HTML (error)
        if (responseText.trim().startsWith('<!DOCTYPE') || 
            responseText.trim().startsWith('<html') ||
            responseText.includes('<!DOCTYPE')) {
            console.error('Se recibió HTML en lugar de JSON');
            throw new Error('El servidor devolvió una página HTML en lugar de datos JSON. Verifica la configuración del backend.');
        }
        
        // Intentar parsear como JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Error parseando JSON:', parseError, 'Texto recibido:', responseText.substring(0, 200));
            throw new Error('Respuesta del servidor no es JSON válido');
        }
        
        console.log('Datos parseados:', data);
        
        Swal.close();
        
        if (data.success) {
            // Eliminar del DOM sin recargar la página
            const imagenElement = document.getElementById(`imagen-${imageId}`);
            if (imagenElement) {
                console.log('Eliminando elemento del DOM:', `imagen-${imageId}`);
                imagenElement.remove();
                
                // Si no quedan imágenes, mostrar mensaje
                const totalImagenes = document.querySelectorAll('#imagenesExistentes .col-md-3').length;
                if (totalImagenes === 0) {
                    document.getElementById('imagenesExistentes').innerHTML = 
                        '<div class="col-12"><p class="text-muted text-center">No hay imágenes</p></div>';
                }
            }
            
            await Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            await Swal.fire('Error', data.message || 'Error desconocido al eliminar', 'error');
        }
        
    } catch (error) {
        console.error('Error completo:', error);
        Swal.close();
        
        await Swal.fire({
            title: 'Error',
            html: `Error al eliminar la imagen:<br><small>${error.message}</small>`,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
}
// Eliminar evidencia


</script>