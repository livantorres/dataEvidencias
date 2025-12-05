<?php
// Definir título de página
$page_title = "Editar Evidencia";
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit me-2"></i> Editar Evidencia
        </h1>
        <div>
            <a href="index.php?modulo=evidencia&accion=view&id=<?php echo $evidencia['id']; ?>" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>
    
    <!-- Formulario de edición -->
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Editar Información</h5>
                </div>
                <div class="card-body">
                    <form id="editEvidenciaForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $evidencia['id']; ?>">
                        <input type="hidden" name="modulo" value="evidencia">
                        <input type="hidden" name="accion" value="edit">
                        
                        <div class="mb-3">
                            <label class="form-label">Institución:</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo htmlspecialchars($evidencia['institucion_nombre']); ?>" 
                                   readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ciclo:</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo htmlspecialchars($evidencia['ciclo_descripcion']); ?>" 
                                   readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción:</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="4"><?php echo htmlspecialchars($evidencia['descripcion']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" 
                                   value="<?php echo $evidencia['fecha']; ?>" required>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Imágenes Existentes</h5>
                </div>
                <div class="card-body">
                    <?php if ($imagenes->rowCount() > 0): ?>
                    <div class="row">
                        <?php while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                                     class="card-img-top" 
                                     style="height: 100px; object-fit: cover;"
                                     alt="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>"
                                     loading="lazy">
                                <div class="card-body p-2 text-center">
                                   <button type="button" 
											class="btn btn-sm btn-danger" 
											onclick="deleteImage(<?php echo $imagen['id']; ?>, '<?php echo addslashes($imagen['nombre_archivo']); ?>')">
										<i class="fas fa-trash"></i>
									</button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center">No hay imágenes</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Agregar Más Imágenes</h5>
                </div>
                <div class="card-body">
                    <form id="addImagesForm" enctype="multipart/form-data">
                        <input type="hidden" name="evidencia_id" value="<?php echo $evidencia['id']; ?>">
                        <input type="hidden" name="modulo" value="evidencia">
                        <input type="hidden" name="accion" value="addImages">
                        
                        <div class="mb-3">
                            <input type="file" 
                                   class="form-control" 
                                   id="nuevasImagenes" 
                                   name="nuevas_imagenes[]" 
                                   multiple 
                                   accept="image/*">
                        </div>
                        
                        <div id="nuevasImagenesPreview" class="row mb-3"></div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus me-1"></i> Agregar Imágenes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enviar formulario de edición
document.getElementById('editEvidenciaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('index.php?modulo=evidencia&accion=edit', {
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
                window.location.href = 'index.php?modulo=evidencia&accion=view&id=<?php echo $evidencia['id']; ?>';
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    });
});

// Vista previa de nuevas imágenes
document.getElementById('nuevasImagenes').addEventListener('change', function(e) {
    const preview = document.getElementById('nuevasImagenesPreview');
    preview.innerHTML = '';
    
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'col-6 mb-2';
            div.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 80px; object-fit: cover;"
                         alt="Nueva imagen ${index + 1}">
                    <div class="card-body p-1 text-center">
                        <small class="text-muted text-truncate d-block">${file.name}</small>
                    </div>
                </div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// Enviar formulario para agregar imágenes
document.getElementById('addImagesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Subiendo...';
    
    fetch('index.php?modulo=evidencia&accion=addImages', {
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
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

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
</script>