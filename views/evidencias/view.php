<?php
// Definir título de página
$page_title = "Ver Evidencia";
?>

<style>
/* Estilos para edición de nombre de imagen */
.image-name-editable {
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 2px 4px;
    border-radius: 3px;
}

.image-name-editable:hover {
    background-color: #f0f0f0;
}

.image-name-input {
    width: 100%;
    padding: 2px 4px;
    border: 1px solid #4e73df;
    border-radius: 3px;
    font-size: 12px;
}

.image-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: none;
    gap: 5px;
    z-index: 10;
}

.image-card:hover .image-actions {
    display: flex;
}

.image-actions-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-actions-btn:hover {
    background: white;
    transform: scale(1.1);
}

.image-actions-btn.edit {
    color: #4e73df;
}

.image-actions-btn.delete {
    color: #e74a3b;
}

.rename-tooltip {
    position: absolute;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    z-index: 100;
    display: none;
}

.image-card {
    position: relative;
}

.image-card:hover .rename-tooltip {
    display: block;
}
</style>

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
            <div class="row" id="imagenesContainer">
                <?php 
                $imagen_counter = 0;
                while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)): 
                    $imagen_counter++;
                ?>
                <div class="col-md-3 mb-4" id="imagenCard-<?php echo $imagen['id']; ?>">
                    <div class="card image-card h-100">
                        <!-- Botones de acción flotantes -->
                        <div class="image-actions">
                            <button type="button" 
                                    class="image-actions-btn edit"
                                    onclick="renameImage(<?php echo $imagen['id']; ?>)"
                                    title="Renombrar imagen">
                                <i class="fas fa-edit fa-sm"></i>
                            </button>
                            <?php if ($_SESSION['rol_id'] == 1 || $evidencia['usuario_id'] == $_SESSION['usuario_id']): ?>
                            <button type="button" 
                                    class="image-actions-btn delete"
                                    onclick="deleteImage(<?php echo $imagen['id']; ?>, '<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>')"
                                    title="Eliminar imagen">
                                <i class="fas fa-trash fa-sm"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Imagen -->
                        <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover; cursor: pointer;"
                             ondblclick="renameImage(<?php echo $imagen['id']; ?>)"
                             onclick="openImageModal('<?php echo htmlspecialchars($imagen['ruta']); ?>')"
                             alt="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>"
                             loading="lazy"
                             title="Doble clic para renombrar">
                        
                        <!-- Tooltip para renombrar -->
                        <div class="rename-tooltip">Doble clic para renombrar</div>
                        
                        <!-- Información de la imagen -->
                        <div class="card-body p-2">
                            <!-- Nombre de la imagen (editable) -->
                            <div class="mb-1">
                                <span id="imageName-<?php echo $imagen['id']; ?>" 
                                      class="image-name-editable d-block text-truncate"
                                      ondblclick="renameImage(<?php echo $imagen['id']; ?>)"
                                      title="Doble clic para renombrar">
                                    <?php echo htmlspecialchars($imagen['nombre_archivo']); ?>
                                </span>
                                <input type="text" 
                                       id="imageNameInput-<?php echo $imagen['id']; ?>" 
                                       class="image-name-input d-none" 
                                       value="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>"
                                       onkeypress="handleImageNameKeypress(event, <?php echo $imagen['id']; ?>)"
                                       onblur="cancelImageRename(<?php echo $imagen['id']; ?>)">
                            </div>
                            
                            <!-- Tamaño y metadata -->
                            <small class="text-muted d-block">
                                <i class="fas fa-hdd me-1"></i>
                                <?php echo number_format($imagen['tamaño'] / 1024, 2); ?> KB
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-file-image me-1"></i>
                                <?php echo htmlspecialchars($imagen['tipo']); ?>
                            </small>
                            
                            <!-- Fecha de subida -->
                            <small class="text-muted d-block">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($imagen['created_at'])); ?>
                            </small>
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
                <div class="mt-3">
                    <small class="text-muted" id="modalImageName"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="renameFromModalBtn">
                    <i class="fas fa-edit me-1"></i> Renombrar Imagen
                </button>
                <a href="#" class="btn btn-success" id="downloadImageBtn" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentEditingImageId = null;
let originalImageName = '';

// Modal para ver imagen en grande
function openImageModal(imageSrc) {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSrc;
    
    // Obtener nombre de la imagen del atributo alt
    const imageName = modalImage.alt || imageSrc.split('/').pop();
    document.getElementById('modalImageName').textContent = imageName;
    
    // Configurar botón de descarga
    document.getElementById('downloadImageBtn').href = imageSrc;
    document.getElementById('downloadImageBtn').download = imageName;
    
    // Configurar botón de renombrar desde modal
    document.getElementById('renameFromModalBtn').onclick = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
        modal.hide();
        
        // Encontrar el ID de la imagen por su ruta
        setTimeout(() => {
            const imageCard = document.querySelector(`img[src="${imageSrc}"]`).closest('.image-card');
            if (imageCard) {
                const imageId = imageCard.querySelector('[id^="imageName-"]').id.split('-')[1];
                renameImage(parseInt(imageId));
            }
        }, 300);
    };
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Renombrar imagen (activar modo edición)
function renameImage(imageId) {
    // Si ya hay una en edición, cancelar primero
    if (currentEditingImageId && currentEditingImageId !== imageId) {
        cancelImageRename(currentEditingImageId);
    }
    
    const nameSpan = document.getElementById(`imageName-${imageId}`);
    const nameInput = document.getElementById(`imageNameInput-${imageId}`);
    
    // Guardar nombre original
    originalImageName = nameInput.value;
    
    // Cambiar a modo edición
    nameSpan.classList.add('d-none');
    nameInput.classList.remove('d-none');
    
    // Seleccionar texto y enfocar
    nameInput.focus();
    nameInput.select();
    
    currentEditingImageId = imageId;
    
    // Agregar evento de escape
    nameInput.onkeydown = function(e) {
        if (e.key === 'Escape') {
            cancelImageRename(imageId);
        }
    };
}

// Manejar tecla Enter en el input
function handleImageNameKeypress(event, imageId) {
    if (event.key === 'Enter') {
        event.preventDefault();
        saveImageName(imageId);
    }
}

// Guardar nuevo nombre de imagen
async function saveImageName(imageId) {
    const nameInput = document.getElementById(`imageNameInput-${imageId}`);
    const newName = nameInput.value.trim();
    
    // Validar
    if (!newName) {
        Swal.fire('Error', 'El nombre no puede estar vacío', 'error');
        nameInput.value = originalImageName;
        cancelImageRename(imageId);
        return;
    }
    
    if (newName === originalImageName) {
        cancelImageRename(imageId);
        return;
    }
    
    // Validar extensión
    const hasExtension = newName.includes('.');
    if (!hasExtension) {
        Swal.fire('Error', 'El nombre debe incluir la extensión del archivo (ej: imagen.jpg)', 'error');
        nameInput.focus();
        return;
    }
    
    // Mostrar loader
    Swal.fire({
        title: 'Renombrando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    try {
        // Enviar petición al servidor
        const formData = new FormData();
        formData.append('modulo', 'evidencia');
        formData.append('accion', 'renameImage');
        formData.append('imagen_id', imageId);
        formData.append('nuevo_nombre', newName);
        
        const response = await fetch('index.php?modulo=evidencia&accion=renameImage', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        Swal.close();
        
        if (data.success) {
            // Actualizar en la interfaz
            document.getElementById(`imageName-${imageId}`).textContent = newName;
            document.getElementById(`imageName-${imageId}`).title = newName;
            
            // También actualizar el alt de la imagen si existe
            const imageElement = document.querySelector(`#imagenCard-${imageId} img`);
            if (imageElement) {
                imageElement.alt = newName;
            }
            
            // Salir del modo edición
            cancelImageRename(imageId);
            
            // Mostrar mensaje de éxito
            await Swal.fire({
                title: '¡Éxito!',
                text: data.message || 'Nombre cambiado correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
        } else {
            Swal.fire('Error', data.message || 'Error al renombrar la imagen', 'error');
            nameInput.value = originalImageName;
            cancelImageRename(imageId);
        }
        
    } catch (error) {
        Swal.close();
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
        nameInput.value = originalImageName;
        cancelImageRename(imageId);
    }
}

// Cancelar renombrado
function cancelImageRename(imageId) {
    const nameSpan = document.getElementById(`imageName-${imageId}`);
    const nameInput = document.getElementById(`imageNameInput-${imageId}`);
    
    // Restaurar nombre original si se canceló
    if (currentEditingImageId === imageId) {
        nameInput.value = originalImageName;
    }
    
    // Volver a modo visual
    nameSpan.classList.remove('d-none');
    nameInput.classList.add('d-none');
    
    currentEditingImageId = null;
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
        
        // Enviar petición
        const response = await fetch('index.php?modulo=evidencia&accion=deleteImage', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        Swal.close();
        
        if (data.success) {
            // Eliminar del DOM
            const imageCard = document.getElementById(`imagenCard-${imageId}`);
            if (imageCard) {
                imageCard.remove();
            }
            
            // Actualizar contador
            const totalImages = document.querySelectorAll('#imagenesContainer .col-md-3').length;
            const badge = document.querySelector('.badge.bg-primary');
            if (badge) {
                badge.textContent = `${totalImages} imágenes`;
            }
            
            // Si no quedan imágenes
            if (totalImages === 0) {
                document.getElementById('imagenesContainer').innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay imágenes</h5>
                        </div>
                    </div>
                `;
            }
            
            await Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            await Swal.fire('Error', data.message || 'Error al eliminar la imagen', 'error');
        }
        
    } catch (error) {
        Swal.close();
        await Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    }
}

// Eliminar evidencia
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

        // Crear FormData
        const formData = new FormData();
        formData.append('modulo', 'evidencia');
        formData.append('accion', 'delete');
        formData.append('id', evidenciaId);

        fetch('index.php?modulo=evidencia&accion=delete', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include'
        })
        .then(response => response.text())
        .then(text => {
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

// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips si Bootstrap los soporta
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Prevenir doble clic en imágenes de abrir el modal Y renombrar
    document.querySelectorAll('.image-card img').forEach(img => {
        let clickCount = 0;
        let clickTimer;
        
        img.addEventListener('click', function(e) {
            clickCount++;
            if (clickCount === 1) {
                clickTimer = setTimeout(function() {
                    // Click simple - abrir modal
                    openImageModal(img.src);
                    clickCount = 0;
                }, 300);
            } else if (clickCount === 2) {
                // Doble clic - renombrar
                clearTimeout(clickTimer);
                const imageCard = img.closest('.image-card');
                if (imageCard) {
                    const imageId = imageCard.querySelector('[id^="imageName-"]').id.split('-')[1];
                    renameImage(parseInt(imageId));
                }
                clickCount = 0;
            }
        });
    });
});
</script>