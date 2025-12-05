<?php
// Definir título de página
$page_title = "Gestión de Evidencias";

// Al inicio del archivo, después de $page_title
$ciclo_info = isset($ciclo_actual) ? $ciclo_actual : null;
?>

<style>
/* Estilos personalizados para la subida de imágenes */
.image-upload-area {
    border: 3px dashed #dee2e6;
    border-radius: 10px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    min-height: 200px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.image-upload-area:hover {
    border-color: #4e73df;
    background: #edf2ff;
}

.image-upload-area.dragover {
    border-color: #4e73df;
    background: #e3e8ff;
    transform: scale(1.02);
}

.image-upload-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 15px;
    transition: all 0.3s ease;
}

.image-upload-area:hover .image-upload-icon {
    color: #4e73df;
    transform: translateY(-5px);
}

.image-preview-container {
    position: relative;
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.image-preview-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.image-preview {
    width: 100%;
    height: 150px;
    object-fit: cover;
    transition: all 0.3s ease;
}

.image-preview-container:hover .image-preview {
    transform: scale(1.05);
}

.image-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    cursor: pointer;
    z-index: 10;
}

.image-preview-container:hover .image-remove-btn {
    opacity: 1;
}

.image-remove-btn:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.image-upload-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: #e9ecef;
    overflow: hidden;
}

.image-upload-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4e73df, #36b9cc);
    width: 0%;
    transition: width 0.3s ease;
}

.image-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px;
    font-size: 11px;
    display: flex;
    justify-content: space-between;
}

.add-more-btn {
    width: 150px;
    height: 150px;
    border: 3px dashed #dee2e6;
    border-radius: 10px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 10px auto;
}

.add-more-btn:hover {
    border-color: #4e73df;
    background: #edf2ff;
    transform: scale(1.05);
}

.add-more-icon {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.add-more-btn:hover .add-more-icon {
    color: #4e73df;
    transform: rotate(90deg);
}

.images-counter {
    background: #4e73df;
    color: white;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 12px;
    margin-left: 10px;
}

.upload-loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.upload-loader-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.upload-loader-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #4e73df;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.image-count-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.upload-status {
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
    display: none;
}

.upload-status.success {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}

.upload-status.error {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}

.upload-status.info {
    background: #cfe2ff;
    color: #084298;
    border: 1px solid #b6d4fe;
}

/* Responsive */
@media (max-width: 768px) {
    .image-preview {
        height: 120px;
    }
    
    .add-more-btn {
        width: 120px;
        height: 120px;
    }
}


</style>

<script>

// Función para abrir modal de ver evidencias
function viewAllEvidencesModal(institucionId, institucionNombre) {
    console.log('Abriendo modal de evidencias para institución:', institucionId, institucionNombre);
    
    // Configurar modal
    document.getElementById('viewModalInstitucionNombre').textContent = institucionNombre;
    
    // Mostrar loader
    document.getElementById('viewEvidencesLoading').style.display = 'block';
    document.getElementById('viewEvidencesContent').style.display = 'none';
    
    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('viewEvidencesModal'));
    modal.show();
    
    // Cargar evidencias vía AJAX
    loadEvidencesForModal(institucionId, institucionNombre);
}

// Cargar evidencias para el modal
async function loadEvidencesForModal(institucionId, institucionNombre) {
    try {
        const response = await fetch(`index.php?modulo=evidencia&accion=getByInstitucionAjax&institucion_id=${institucionId}`);
        const data = await response.json();
        
        console.log('Evidencias cargadas:', data);
        
        // Ocultar loader
        document.getElementById('viewEvidencesLoading').style.display = 'none';
        document.getElementById('viewEvidencesContent').style.display = 'block';
        
        if (data.success && data.evidencias && data.evidencias.length > 0) {
            // Mostrar evidencias
            displayEvidencesForModal(data.evidencias, institucionNombre);
        } else {
            document.getElementById('viewEvidencesList').innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay evidencias registradas</h5>
                    <p class="text-muted">Esta institución aún no tiene evidencias registradas</p>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error cargando evidencias:', error);
        document.getElementById('viewEvidencesLoading').style.display = 'none';
        document.getElementById('viewEvidencesContent').style.display = 'block';
        document.getElementById('viewEvidencesList').innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Error al cargar evidencias</h5>
                <p class="text-muted">${error.message}</p>
            </div>
        `;
    }
}

// Mostrar lista de evidencias en el modal
function displayEvidencesForModal(evidencias, institucionNombre) {
    const container = document.getElementById('viewEvidencesList');
    container.innerHTML = '';
    
    if (evidencias.length === 0) {
        document.getElementById('viewNoResults').style.display = 'block';
        return;
    }
    
    document.getElementById('viewNoResults').style.display = 'none';
    
    evidencias.forEach(evidencia => {
        const evidenciaCard = createEvidenceCardForModal(evidencia, institucionNombre);
        container.appendChild(evidenciaCard);
    });
}

// Crear tarjeta de evidencia para el modal
function createEvidenceCardForModal(evidencia, institucionNombre) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    
    // Formatear fecha
    const fecha = new Date(evidencia.fecha);
    const fechaFormatted = fecha.toLocaleDateString('es-ES');
    
    // Obtener primera imagen (si existe)
    const primeraImagen = evidencia.imagenes && evidencia.imagenes.length > 0 
        ? evidencia.imagenes[0].ruta 
        : 'assets/img/no-image.jpg';
    
    col.innerHTML = `
        <div class="card evidence-card h-100">
            <div class="position-relative">
                <img src="${primeraImagen}" 
                     class="card-img-top" 
                     style="height: 150px; object-fit: cover; cursor: pointer;"
                     onclick="openSingleEvidenceModal(${evidencia.id})"
                     alt="Evidencia ${evidencia.id}">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success">
                        <i class="fas fa-image me-1"></i>${evidencia.imagenes_count || 0}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 text-truncate" style="max-width: 70%;">
                        ${evidencia.ciclo_descripcion || 'Sin ciclo'}
                    </h6>
                    <small class="text-muted">${fechaFormatted}</small>
                </div>
                
                ${evidencia.descripcion ? `
                <p class="card-text small text-muted mb-2 line-clamp-2" style="max-height: 40px; overflow: hidden;">
                    ${evidencia.descripcion}
                </p>
                ` : ''}
                
                <div class="d-grid">
                    <button class="btn btn-sm btn-outline-primary" onclick="openSingleEvidenceModal(${evidencia.id})">
                        <i class="fas fa-eye me-1"></i> Ver Evidencia
                    </button>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>${fechaFormatted}
                </small>
            </div>
        </div>
    `;
    
    return col;
}

// Abrir modal de evidencia individual
async function openSingleEvidenceModal(evidenciaId) {
    try {
        const response = await fetch(`index.php?modulo=evidencia&accion=getQuickView&id=${evidenciaId}`);
        const data = await response.json();
        
        if (data.success) {
            const ev = data.evidencia;
            const fecha = new Date(ev.fecha).toLocaleDateString('es-ES');
            
            // Crear contenido del modal
            let imagesHtml = '';
            if (ev.imagenes && ev.imagenes.length > 0) {
                imagesHtml = `
                    <div class="row mt-3">
                        ${ev.imagenes.map((img, index) => `
                            <div class="col-6 col-md-4 mb-3">
                                <div class="card">
                                    <img src="${img.ruta}" 
                                         class="card-img-top" 
                                         style="height: 150px; object-fit: cover; cursor: pointer;"
                                         onclick="window.open('${img.ruta}', '_blank')"
                                         alt="${img.nombre_archivo}"
                                         title="Haz clic para ver imagen completa">
                                    <div class="card-body p-2 text-center">
                                        <small class="text-muted">${img.nombre_archivo}</small>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            document.getElementById('singleEvidenceTitle').textContent = `Evidencia - ${ev.ciclo_descripcion || 'Sin ciclo'}`;
            document.getElementById('singleEvidenceContent').innerHTML = `
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <h6><i class="fas fa-university me-2"></i>Institución</h6>
                            <p class="mb-0">${ev.institucion_nombre}</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="fas fa-sync-alt me-2"></i>Ciclo</h6>
                                    <p class="mb-0">${ev.ciclo_descripcion || 'Sin ciclo'}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6><i class="fas fa-calendar-alt me-2"></i>Fecha</h6>
                                    <p class="mb-0">${fecha}</p>
                                </div>
                            </div>
                        </div>
                        
                        ${ev.descripcion ? `
                        <div class="mb-3">
                            <h6><i class="fas fa-align-left me-2"></i>Descripción</h6>
                            <p class="mb-0" style="white-space: pre-wrap;">${ev.descripcion}</p>
                        </div>
                        ` : ''}
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-images me-2"></i>Imágenes (${ev.imagenes_count || 0})</h6>
                            ${imagesHtml || '<p class="text-muted">No hay imágenes</p>'}
                        </div>
                    </div>
                </div>
            `;
            
            // Configurar enlace para vista completa
            document.getElementById('singleEvidenceFullLink').href = `index.php?modulo=evidencia&accion=view&id=${evidenciaId}`;
            
            // Cerrar modal de lista de evidencias si está abierto
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewEvidencesModal'));
            if (viewModal) {
                viewModal.hide();
            }
            
            // Mostrar modal de evidencia individual
            const singleModal = new bootstrap.Modal(document.getElementById('singleEvidenceModal'));
            singleModal.show();
        }
    } catch (error) {
        console.error('Error cargando vista de evidencia:', error);
        Swal.fire('Error', 'No se pudo cargar la evidencia', 'error');
    }
}

// Función para agregar evidencia desde el modal
function addEvidenceFromModal() {
    // Obtener institución del modal actual
    const institucionNombre = document.getElementById('viewModalInstitucionNombre').textContent;
    const modalInstitucionId = window.currentInstitucionId; // Necesitarías guardar esto globalmente
    
    if (modalInstitucionId) {
        // Cerrar modal actual
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewEvidencesModal'));
        if (viewModal) {
            viewModal.hide();
        }
        
        // Abrir modal de agregar evidencia
        setTimeout(() => {
            openEvidenceModal(modalInstitucionId, institucionNombre);
        }, 300);
    }
}

// Modificar viewAllEvidencesModal para guardar el ID globalmente
function viewAllEvidencesModal(institucionId, institucionNombre) {
    // Guardar ID globalmente para usarlo después
    window.currentInstitucionId = institucionId;
    
    // Resto del código original...
    document.getElementById('viewModalInstitucionNombre').textContent = institucionNombre;
    document.getElementById('viewEvidencesLoading').style.display = 'block';
    document.getElementById('viewEvidencesContent').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('viewEvidencesModal'));
    modal.show();
    
    loadEvidencesForModal(institucionId, institucionNombre);
}

// Función global para abrir modal de evidencia (mantener existente)
function openEvidenceModal(institucionId, institucionNombre) {
    console.log('Abriendo modal para institución:', institucionId, institucionNombre);
    
    try {
        const institucionIdInput = document.getElementById('modal_institucion_id');
        const institucionNombreInput = document.getElementById('modal_institucion_nombre');
        const evidenceModal = document.getElementById('evidenceModal');
        
        if (!institucionIdInput || !institucionNombreInput || !evidenceModal) {
            throw new Error('Elementos del modal no encontrados');
        }
        
        institucionIdInput.value = institucionId;
        institucionNombreInput.value = decodeURIComponent(institucionNombre);
        
        resetImagePreview();
        
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(evidenceModal);
            modalInstance.show();
        }
        
    } catch (error) {
        console.error('Error al abrir modal:', error);
        Swal.fire('Error', 'Error al abrir el formulario: ' + error.message, 'error');
    }
}

// Variables globales
let selectedImages = [];
let uploadInProgress = false;

// Función para inicializar el sistema de subida
function initializeImageUpload() {
    const uploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('imagenes');
    const addMoreBtn = document.getElementById('addMoreImagesBtn');
    const imagePreview = document.getElementById('imagePreview');
    const imageCount = document.getElementById('imageCount');
    const uploadStatus = document.getElementById('uploadStatus');
    
    // Configurar área de drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleImageSelection(files);
        }
    });
    
    // Click en área de subida
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Cambio en input de archivos
    fileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            handleImageSelection(this.files);
            this.value = ''; // Resetear para permitir seleccionar las mismas imágenes
        }
    });
    
    // Botón "Agregar más"
    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }
    
    // Actualizar contador
    updateImageCount();
}

// Manejar selección de imágenes
function handleImageSelection(files) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    Array.from(files).forEach(file => {
        // Validar tipo
        if (!allowedTypes.includes(file.type)) {
            showUploadStatus('error', `Tipo de archivo no permitido: ${file.name}. Solo se permiten imágenes JPG, PNG, GIF o WebP.`);
            return;
        }
        
        // Validar tamaño
        if (file.size > maxSize) {
            showUploadStatus('error', `La imagen ${file.name} es demasiado grande (${(file.size/1024/1024).toFixed(2)}MB). Máximo 5MB.`);
            return;
        }
        
        // Agregar a la lista
        const imageId = Date.now() + Math.random();
        selectedImages.push({
            id: imageId,
            file: file,
            preview: null,
            uploaded: false
        });
        
        // Crear vista previa
        createImagePreview(imageId, file);
    });
    
    // Actualizar contador
    updateImageCount();
    
    // Mostrar botón de agregar más si hay imágenes
    toggleAddMoreButton();
    
    // Actualizar input files
    updateFileInput();
}

// Crear vista previa de imagen
function createImagePreview(imageId, file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'col-md-3 mb-3 image-preview-container';
        previewContainer.id = `preview-${imageId}`;
        
        // Calcular tamaño en formato legible
        const fileSize = file.size < 1024 * 1024 
            ? `${(file.size/1024).toFixed(1)} KB` 
            : `${(file.size/(1024*1024)).toFixed(2)} MB`;
        
        previewContainer.innerHTML = `
            <img src="${e.target.result}" class="image-preview" alt="${file.name}">
            <button type="button" class="image-remove-btn" onclick="removeImage('${imageId}')" title="Eliminar imagen">
                <i class="fas fa-times"></i>
            </button>
            <div class="image-upload-progress">
                <div class="image-upload-progress-bar" id="progress-${imageId}"></div>
            </div>
            <div class="image-info">
                <span class="text-truncate">${file.name}</span>
                <span>${fileSize}</span>
            </div>
        `;
        
        document.getElementById('imagePreview').appendChild(previewContainer);
        
        // Guardar preview en el objeto
        const imageIndex = selectedImages.findIndex(img => img.id === imageId);
        if (imageIndex !== -1) {
            selectedImages[imageIndex].preview = e.target.result;
        }
    };
    
    reader.readAsDataURL(file);
}

// Eliminar imagen
function removeImage(imageId) {
    // Encontrar y eliminar de la lista
    const imageIndex = selectedImages.findIndex(img => img.id === imageId);
    if (imageIndex !== -1) {
        selectedImages.splice(imageIndex, 1);
    }
    
    // Eliminar vista previa del DOM
    const previewElement = document.getElementById(`preview-${imageId}`);
    if (previewElement) {
        previewElement.remove();
    }
    
    // Actualizar contador
    updateImageCount();
    
    // Mostrar/ocultar botón de agregar más
    toggleAddMoreButton();
    
    // Actualizar input files
    updateFileInput();
    
    showUploadStatus('info', 'Imagen eliminada');
}

// Actualizar contador de imágenes
function updateImageCount() {
    const imageCount = document.getElementById('imageCount');
    if (imageCount) {
        imageCount.textContent = selectedImages.length;
        imageCount.style.display = selectedImages.length > 0 ? 'inline-block' : 'none';
    }
    
    const uploadAreaText = document.getElementById('uploadAreaText');
    if (uploadAreaText) {
        if (selectedImages.length === 0) {
            uploadAreaText.innerHTML = `
                <div class="image-upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h5 class="mb-2">Arrastra imágenes aquí</h5>
                <p class="text-muted mb-0">o haz clic para seleccionar</p>
            `;
        } else {
            uploadAreaText.innerHTML = `
                <div class="image-upload-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h5 class="mb-2">${selectedImages.length} imagen(es) seleccionada(s)</h5>
                <p class="text-muted mb-0">Haz clic para agregar más imágenes</p>
            `;
        }
    }
}

// Mostrar/ocultar botón de agregar más
function toggleAddMoreButton() {
    const addMoreBtn = document.getElementById('addMoreImagesBtn');
    if (addMoreBtn) {
        addMoreBtn.style.display = selectedImages.length > 0 ? 'flex' : 'none';
    }
}

// Actualizar input files
// Función mejorada para actualizar input files
function updateFileInput() {
    // Eliminar el input file antiguo
    const oldFileInput = document.getElementById('imagenes');
    const newFileInput = document.createElement('input');
    
    // Configurar nuevo input
    newFileInput.type = 'file';
    newFileInput.name = 'imagenes[]';
    newFileInput.id = 'imagenes';
    newFileInput.className = 'd-none';
    newFileInput.multiple = true;
    newFileInput.accept = 'image/*';
    
    // Reemplazar el viejo con el nuevo
    oldFileInput.parentNode.replaceChild(newFileInput, oldFileInput);
    
    // Crear DataTransfer y agregar archivos
    const dataTransfer = new DataTransfer();
    selectedImages.forEach(image => {
        dataTransfer.items.add(image.file);
    });
    
    // Asignar archivos al nuevo input
    newFileInput.files = dataTransfer.files;
    
    console.log("Nuevo input creado con", newFileInput.files.length, "archivos");
    
    // Reasignar event listener para cambios
    newFileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            handleImageSelection(this.files);
            this.value = '';
        }
    });
    
    return newFileInput;
}

// Mostrar estado de subida
function showUploadStatus(type, message) {
    const uploadStatus = document.getElementById('uploadStatus');
    if (uploadStatus) {
        uploadStatus.className = `upload-status ${type}`;
        uploadStatus.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
            </div>
        `;
        uploadStatus.style.display = 'block';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            uploadStatus.style.display = 'none';
        }, 5000);
    }
}

// Mostrar loader de subida
function showUploadLoader(message = 'Subiendo imágenes...') {
    const loader = document.getElementById('uploadLoader');
    const loaderMessage = document.getElementById('loaderMessage');
    
    if (loader && loaderMessage) {
        loaderMessage.textContent = message;
        loader.style.display = 'flex';
        uploadInProgress = true;
        
        // Bloquear formulario
        document.getElementById('evidenceForm').querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = true;
        });
    }
}

// Ocultar loader de subida
function hideUploadLoader() {
    const loader = document.getElementById('uploadLoader');
    if (loader) {
        loader.style.display = 'none';
        uploadInProgress = false;
        
        // Desbloquear formulario
        document.getElementById('evidenceForm').querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = false;
        });
    }
}

// Resetear vista previa
function resetImagePreview() {
    selectedImages = [];
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('imagenes').value = '';
    updateImageCount();
    toggleAddMoreButton();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializeImageUpload();
    
    // Manejar envío del formulario
    const evidenceForm = document.getElementById('evidenceForm');
    if (evidenceForm) {
        evidenceForm.addEventListener('submit', function(e) {
            // Validar imágenes
            if (selectedImages.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Sin imágenes',
                    text: 'Debe seleccionar al menos una imagen para la evidencia.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            // Validar ciclo
            const cicloSelect = document.getElementById('ciclo_id');
            if (!cicloSelect.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Ciclo requerido',
                    text: 'Debe seleccionar un ciclo para la evidencia.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }
            
            // Mostrar loader
            showUploadLoader('Guardando evidencia...');
            
            // Si todo está bien, el formulario se envía normalmente
            // El loader se ocultará cuando la página recargue
        });
    }
    
    // Cerrar modal resetea las imágenes
    const evidenceModal = document.getElementById('evidenceModal');
    if (evidenceModal) {
        evidenceModal.addEventListener('hidden.bs.modal', function() {
            resetImagePreview();
        });
    }
});

// Tomar foto con cámara (para móviles)
function takePhoto() {
    const fileInput = document.getElementById('imagenes');
    
    // Para dispositivos móviles que soportan capture
    fileInput.setAttribute('capture', 'environment');
    fileInput.click();
    
    // Restaurar después de hacer clic
    setTimeout(() => {
        fileInput.removeAttribute('capture');
    }, 100);
}
</script>

<div class="container-fluid">
    <!-- Header con información del ciclo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="fas fa-calendar-alt me-2"></i> Registrar Evidencias
                            </h5>
                            <p class="card-text mb-0">
                                <?php if ($ciclo_info): ?>
                                <span class="badge bg-light text-dark me-2">
                                    Ciclo actual: <?php echo htmlspecialchars($ciclo_info['descripcion']); ?>
                                </span>
                                <?php else: ?>
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    No hay ciclo seleccionado
                                </span>
                                <?php endif; ?>
                                <small class="opacity-75">
                                    Haga clic en el escudo de una institución para agregar evidencia
                                </small>
                            </p>
                        </div>
                        <?php if (!$ciclo_info): ?>
                        <a href="index.php?modulo=ciclos&accion=index" class="btn btn-light">
                            <i class="fas fa-calendar-plus me-2"></i> Seleccionar Ciclo
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Barra de búsqueda -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <input type="hidden" name="modulo" value="evidencia">
                <input type="hidden" name="accion" value="index">
                <?php if (isset($_GET['institucion'])): ?>
                <input type="hidden" name="institucion" value="<?php echo $_GET['institucion']; ?>">
                <?php endif; ?>
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar institución por nombre o ciudad..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Cards de instituciones -->
   <!-- Cards de instituciones - VISTA SIMPLIFICADA -->
    <div class="row">
        <?php 
        if ($instituciones && $instituciones->rowCount() > 0):
            while ($inst = $instituciones->fetch(PDO::FETCH_ASSOC)): 
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <!-- Escudo de la institución -->
                    <?php
                    $escudo_path = "storage/instituciones/escudos/{$inst['id']}.*";
                    $escudo_files = glob($escudo_path);
                    $escudo_img = count($escudo_files) > 0 ? $escudo_files[0] : 'assets/img/default-institution.png';
                    $nombre_escaped = htmlspecialchars($inst['nombre'], ENT_QUOTES);
                    ?>
                    <img src="<?php echo $escudo_img; ?>" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover; cursor: pointer;" 
                         onclick="openEvidenceModal(<?php echo $inst['id']; ?>, '<?php echo $nombre_escaped; ?>')"
                         alt="<?php echo $nombre_escaped; ?>"
                         title="Haz clic para agregar evidencia">
                    
                    <h5 class="card-title"><?php echo $nombre_escaped; ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($inst['ciudad']); ?></p>
                    
                    <!-- Información de evidencias existentes - SIMPLIFICADA -->
                    <?php
                    if (isset($this->evidencia) && method_exists($this->evidencia, 'getByInstitucion')) {
                        $evidencias = $this->evidencia->getByInstitucion($inst['id']);
                        $evidencias_data = $evidencias ? $evidencias->fetchAll(PDO::FETCH_ASSOC) : [];
                        $total_evidencias = count($evidencias_data);
                        
                        // Contar imágenes totales
                        $total_imagenes = 0;
                        foreach($evidencias_data as $ev) {
                            $total_imagenes += $this->evidencia->countImages($ev['id']);
                        }
                        
                        // Fecha de última evidencia
                        $ultima_fecha = 'Nunca';
                        if ($total_evidencias > 0) {
                            $ultima_evidencia = $evidencias_data[0];
                            $ultima_fecha = date('d/m/Y', strtotime($ultima_evidencia['fecha']));
                        }
                    ?>
                    <hr>
                    <div class="evidencias-info-simple">
                        <!-- Contador total de evidencias -->
                        <div class="mb-2">
                            <span class="badge bg-primary rounded-pill">
                                <i class="fas fa-images me-1"></i>
                                <?php echo $total_evidencias; ?> evidencias
                            </span>
                            <span class="badge bg-info rounded-pill ms-1">
                                <i class="fas fa-image me-1"></i>
                                <?php echo $total_imagenes; ?> imágenes
                            </span>
                        </div>
                        
                        <!-- Última evidencia -->
                        <div class="small text-muted mb-3">
                            <i class="fas fa-clock me-1"></i>
                            Última: <?php echo $ultima_fecha; ?>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="d-grid gap-2">
                            <button type="button" 
                                    class="btn btn-sm btn-primary"
                                    onclick="openEvidenceModal(<?php echo $inst['id']; ?>, '<?php echo $nombre_escaped; ?>')">
                                <i class="fas fa-plus me-1"></i> Agregar Evidencia
                            </button>
                            
                            <?php if ($total_evidencias > 0): ?>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-info"
                                    onclick="viewAllEvidencesModal(<?php echo $inst['id']; ?>, '<?php echo addslashes($nombre_escaped); ?>')">
                                <i class="fas fa-eye me-1"></i> Ver Evidencias
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php } else { ?>
                    <hr>
                    <div class="text-center py-2">
                        <p class="text-muted mb-2"><small>Información de evidencias no disponible</small></p>
                        <button type="button" 
                                class="btn btn-sm btn-success"
                                onclick="openEvidenceModal(<?php echo $inst['id']; ?>, '<?php echo $nombre_escaped; ?>')">
                            <i class="fas fa-plus me-1"></i> Agregar Evidencia
                        </button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php 
            endwhile;
        else: 
        ?>
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-university fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No hay instituciones activas</h4>
                <p class="text-muted mb-4">
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                    No se encontraron instituciones que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"
                    <?php else: ?>
                    No hay instituciones activas disponibles para registrar evidencias.
                    <?php endif; ?>
                </p>
                <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                <a href="index.php?modulo=evidencia&accion=index<?php echo isset($_GET['institucion']) ? '&institucion=' . $_GET['institucion'] : ''; ?>" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Limpiar búsqueda
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Loader de subida -->
<div class="upload-loader" id="uploadLoader">
    <div class="upload-loader-content">
        <div class="upload-loader-spinner"></div>
        <h5 class="mt-3">Procesando...</h5>
        <p id="loaderMessage" class="text-muted">Subiendo imágenes...</p>
        <small class="text-muted">Por favor, espere...</small>
    </div>
</div>
<!-- Modal para ver todas las evidencias de una institución (SIMPLIFICADO) -->
<div class="modal fade" id="viewEvidencesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>
                    Evidencias de <span id="viewModalInstitucionNombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewEvidencesLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando evidencias...</p>
                </div>
                
                <div id="viewEvidencesContent" style="display: none;">
                    <!-- Lista de evidencias -->
                    <div id="viewEvidencesList" class="row">
                        <!-- Se llenará con JavaScript -->
                    </div>
                    
                    <!-- Sin resultados -->
                    <div id="viewNoResults" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron evidencias</h5>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="addEvidenceFromModal()">
                    <i class="fas fa-plus me-1"></i> Agregar Nueva Evidencia
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista de evidencia individual -->
<div class="modal fade" id="singleEvidenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    <span id="singleEvidenceTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="singleEvidenceContent">
                    <!-- Se llenará con JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <a href="#" id="singleEvidenceFullLink" class="btn btn-primary" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i> Ver Completo
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal para agregar evidencia -->
<div class="modal fade" id="evidenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Agregar Evidencia
                    <span class="badge bg-primary images-counter" id="imageCount" style="display: none;">0</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!--<form id="evidenceForm" action="index.php?modulo=evidencia&accion=create" method="POST" enctype="multipart/form-data">-->
			<form id="evidenceForm" action="index.php?modulo=evidencia&accion=create" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="modulo" value="evidencia">
                    <input type="hidden" name="accion" value="create">
                    <input type="hidden" name="institucion_id" id="modal_institucion_id">
                    
                    <div class="mb-4">
                        <label class="form-label">Institución:</label>
                        <input type="text" id="modal_institucion_nombre" class="form-control" readonly>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ciclo_id" class="form-label">Ciclo:</label>
                                <select name="ciclo_id" id="ciclo_id" class="form-control" required>
                                    <option value="">Seleccionar ciclo</option>
                                    <?php 
                                    if (isset($ciclos) && $ciclos->rowCount() > 0):
                                        $ciclos->execute();
                                        while ($ciclo = $ciclos->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <option value="<?php echo $ciclo['id']; ?>"
                                        <?php echo ($ciclo_info && $ciclo['id'] == $ciclo_info['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ciclo['descripcion']); ?>
                                    </option>
                                    <?php 
                                        endwhile;
                                    else: 
                                    ?>
                                    <option value="" disabled>No hay ciclos disponibles</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha:</label>
                                <input type="date" name="fecha" id="fecha" class="form-control" required 
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="descripcion" class="form-label">Descripción (opcional):</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" 
                                  placeholder="Describe esta evidencia..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Imágenes de evidencia:</label>
                        
                        <!-- Área de subida principal -->
                        <div class="image-upload-area mb-3" id="imageUploadArea">
                            <div id="uploadAreaText">
                                <div class="image-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h5 class="mb-2">Arrastra imágenes aquí</h5>
                                <p class="text-muted mb-0">o haz clic para seleccionar</p>
                            </div>
                        </div>
                        
                        <!-- Input de archivos oculto -->
                        <input type="file" name="imagenes[]" id="imagenes" class="d-none" multiple 
                               accept="image/*">
                        
                        <!-- Botón para tomar foto en móviles -->
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('imagenes').click()">
                                <i class="fas fa-folder-open me-2"></i>Seleccionar archivos
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="takePhoto()">
                                <i class="fas fa-camera me-2"></i>Tomar foto
                            </button>
                        </div>
                        
                        <!-- Contador y límites -->
                        <div class="alert alert-info py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Puedes subir múltiples imágenes
                                </small>
                                <small>
                                    Máximo: 10 imágenes • 5MB cada una
                                </small>
                            </div>
                        </div>
                        
                        <!-- Vista previa de imágenes -->
                        <div class="row mt-3" id="imagePreview">
                            <!-- Las imágenes aparecerán aquí -->
                        </div>
                        
                        <!-- Botón para agregar más imágenes -->
                        <div class="text-center">
                            <div class="add-more-btn" id="addMoreImagesBtn" style="display: none;">
                                <div class="add-more-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span>Agregar más</span>
                            </div>
                        </div>
                        
                        <!-- Estado de subida -->
                        <div id="uploadStatus" class="upload-status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i> Guardar Evidencia
                        <span class="badge bg-light text-dark ms-2" id="imagesCountBadge">0</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA EDITAR EVIDENCIA -->
<?php if (isset($evidencia) && isset($evidencia['id'])): ?>
<div class="modal fade" id="editEvidenciaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Evidencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editEvidenciaForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editEvidenciaId">
                    <input type="hidden" name="modulo" value="evidencia">
                    <input type="hidden" name="accion" value="edit">
                    
                    <div class="mb-3">
                        <label class="form-label">Institución</label>
                        <input type="text" class="form-control" 
                               value="<?php echo isset($evidencia['institucion_nombre']) ? htmlspecialchars($evidencia['institucion_nombre']) : ''; ?>" 
                               readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"><?php echo isset($evidencia['descripcion']) ? htmlspecialchars($evidencia['descripcion']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editFecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="editFecha" name="fecha" 
                               value="<?php echo isset($evidencia['fecha']) ? $evidencia['fecha'] : date('Y-m-d'); ?>" required>
                    </div>
                    
                    <hr>
                    
                    <h6>Imágenes existentes</h6>
                    <div id="imagenesExistentes" class="row mb-3">
                        <?php if (isset($imagenes) && $imagenes->rowCount() > 0): ?>
                            <?php while ($imagen = $imagenes->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="col-md-3 mb-2" id="imagen-<?php echo $imagen['id']; ?>">
                                <div class="card">
                                    <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                                         class="card-img-top" 
                                         style="height: 100px; object-fit: cover;"
                                         alt="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>"
                                         loading="lazy">
                                    <div class="card-body p-2 text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-image-btn" 
                                                data-id="<?php echo $imagen['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($imagen['nombre_archivo']); ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-muted text-center">No hay imágenes</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <h6>Agregar nuevas imágenes</h6>
                    <div class="mb-3">
                        <input type="file" 
                               class="form-control" 
                               id="nuevasImagenes" 
                               name="nuevas_imagenes[]" 
                               multiple 
                               accept="image/*">
                        <div class="form-text">
                            Puede seleccionar múltiples imágenes para agregar a la evidencia
                        </div>
                    </div>
                    
                    <div id="nuevasImagenesPreview" class="row mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="editEvidenciaSubmitBtn">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver todas las evidencias de una institución -->
<div class="modal fade" id="allEvidencesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images me-2"></i>
                    Evidencias de <span id="modalInstitucionNombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="evidencesLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando evidencias...</p>
                </div>
                
                <div id="evidencesContent" style="display: none;">
                    <!-- Filtros -->
                    <div class="row mb-4 evidence-filters">
                        <div class="col-md-6">
                            <input type="text" id="filterSearch" class="form-control" 
                                   placeholder="Buscar en descripciones...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterCiclo" class="form-control">
                                <option value="">Todos los ciclos</option>
                                <!-- Se llenará con JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filterOrden" class="form-control">
                                <option value="fecha_desc">Más recientes primero</option>
                                <option value="fecha_asc">Más antiguas primero</option>
                                <option value="imagenes_desc">Más imágenes primero</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-3 text-center">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h4 id="totalEvidences">0</h4>
                                <small>Total Evidencias</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-3 text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-image"></i>
                                </div>
                                <h4 id="totalImages">0</h4>
                                <small>Total Imágenes</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-3 text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4 id="lastEvidenceDate">--/--/----</h4>
                                <small>Última evidencia</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card p-3 text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="stat-icon mb-2">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <h4 id="avgImages">0</h4>
                                <small>Prom. imágenes</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de evidencias -->
                    <div id="evidencesList" class="row">
                        <!-- Se llenará con JavaScript -->
                    </div>
                    
                    <!-- Sin resultados -->
                    <div id="noResults" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron evidencias</h5>
                        <p class="text-muted">Intenta con otros filtros de búsqueda</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="openEvidenceModalFromModal()">
                    <i class="fas fa-plus me-1"></i> Agregar Nueva Evidencia
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para vista rápida de evidencia -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Vista Rápida de Evidencia
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="quickViewContent">
                    <!-- Se llenará con JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <a href="#" id="fullViewLink" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i> Ver Completo
                </a>
            </div>
        </div>
    </div>
</div>

<script>

// Función para ver todas las evidencias de una institución
function viewAllEvidences(institucionId, institucionNombre) {
    console.log('Cargando evidencias para institución:', institucionId, institucionNombre);
    
    // Configurar modal
    document.getElementById('modalInstitucionNombre').textContent = institucionNombre;
    
    // Mostrar loader
    document.getElementById('evidencesLoading').style.display = 'block';
    document.getElementById('evidencesContent').style.display = 'none';
    
    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('allEvidencesModal'));
    modal.show();
    
    // Cargar evidencias vía AJAX
    loadInstitutionEvidences(institucionId);
}

// Cargar evidencias de la institución
async function loadInstitutionEvidences(institucionId) {
    try {
        const response = await fetch(`index.php?modulo=evidencia&accion=getByInstitucionAjax&institucion_id=${institucionId}`);
        const data = await response.json();
        
        console.log('Evidencias cargadas:', data);
        
        // Ocultar loader
        document.getElementById('evidencesLoading').style.display = 'none';
        document.getElementById('evidencesContent').style.display = 'block';
        
        if (data.success && data.evidencias && data.evidencias.length > 0) {
            // Actualizar estadísticas
            updateEvidenceStats(data.evidencias);
            
            // Mostrar evidencias
            displayEvidencesList(data.evidencias);
            
            // Configurar filtros
            setupEvidenceFilters(data.evidencias);
        } else {
            document.getElementById('evidencesList').innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay evidencias registradas</h5>
                    <p class="text-muted">Esta institución aún no tiene evidencias registradas</p>
                    <button class="btn btn-primary mt-2" onclick="openEvidenceModalFromModal()">
                        <i class="fas fa-plus me-1"></i> Agregar Primera Evidencia
                    </button>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('Error cargando evidencias:', error);
        document.getElementById('evidencesLoading').style.display = 'none';
        document.getElementById('evidencesContent').style.display = 'block';
        document.getElementById('evidencesList').innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h5 class="text-danger">Error al cargar evidencias</h5>
                <p class="text-muted">${error.message}</p>
            </div>
        `;
    }
}

// Actualizar estadísticas
function updateEvidenceStats(evidencias) {
    const totalEvidences = evidencias.length;
    let totalImages = 0;
    let lastDate = null;
    
    evidencias.forEach(ev => {
        totalImages += ev.imagenes_count || 0;
        
        const evDate = new Date(ev.fecha);
        if (!lastDate || evDate > lastDate) {
            lastDate = evDate;
        }
    });
    
    document.getElementById('totalEvidences').textContent = totalEvidences;
    document.getElementById('totalImages').textContent = totalImages;
    document.getElementById('avgImages').textContent = totalEvidences > 0 ? (totalImages / totalEvidences).toFixed(1) : '0';
    
    if (lastDate) {
        document.getElementById('lastEvidenceDate').textContent = 
            lastDate.toLocaleDateString('es-ES');
    }
}

// Mostrar lista de evidencias
function displayEvidencesList(evidencias) {
    const container = document.getElementById('evidencesList');
    container.innerHTML = '';
    
    if (evidencias.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        return;
    }
    
    document.getElementById('noResults').style.display = 'none';
    
    evidencias.forEach(evidencia => {
        const evidenciaCard = createEvidenceCard(evidencia);
        container.appendChild(evidenciaCard);
    });
}

// Crear tarjeta de evidencia
function createEvidenceCard(evidencia) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    
    // Formatear fecha
    const fecha = new Date(evidencia.fecha);
    const fechaFormatted = fecha.toLocaleDateString('es-ES');
    
    // Obtener primera imagen (si existe)
    const primeraImagen = evidencia.imagenes && evidencia.imagenes.length > 0 
        ? evidencia.imagenes[0].ruta 
        : 'assets/img/no-image.jpg';
    
    col.innerHTML = `
        <div class="card evidence-card h-100">
            <div class="position-relative">
                <img src="${primeraImagen}" 
                     class="card-img-top" 
                     style="height: 150px; object-fit: cover; cursor: pointer;"
                     onclick="openQuickView(${evidencia.id})"
                     alt="Evidencia ${evidencia.id}">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success">
                        <i class="fas fa-image me-1"></i>${evidencia.imagenes_count || 0}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 text-truncate" style="max-width: 70%;">
                        ${evidencia.ciclo_descripcion || 'Sin ciclo'}
                    </h6>
                    <small class="text-muted">${fechaFormatted}</small>
                </div>
                
                ${evidencia.descripcion ? `
                <p class="card-text small text-muted mb-2 line-clamp-2" style="max-height: 40px; overflow: hidden;">
                    ${evidencia.descripcion}
                </p>
                ` : ''}
                
                <!-- Mini galería -->
                ${evidencia.imagenes && evidencia.imagenes.length > 0 ? `
                <div class="row g-1 mb-2">
                    ${evidencia.imagenes.slice(0, 4).map((img, idx) => `
                    <div class="col-3">
                        <img src="${img.ruta}" 
                             class="img-fluid rounded" 
                             style="width: 100%; height: 40px; object-fit: cover; cursor: pointer;"
                             onclick="openQuickView(${evidencia.id}, ${idx})"
                             alt="Imagen ${idx + 1}">
                    </div>
                    `).join('')}
                    
                    ${evidencia.imagenes_count > 4 ? `
                    <div class="col-3">
                        <div class="d-flex align-items-center justify-content-center h-100 bg-light rounded text-muted small">
                            +${evidencia.imagenes_count - 4}
                        </div>
                    </div>
                    ` : ''}
                </div>
                ` : ''}
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>${fechaFormatted}
                    </small>
                    <div class="evidence-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="openQuickView(${evidencia.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a href="index.php?modulo=evidencia&accion=view&id=${evidencia.id}" 
                           class="btn btn-sm btn-outline-info ms-1">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

// Configurar filtros
function setupEvidenceFilters(evidencias) {
    // Obtener ciclos únicos
    const ciclos = [...new Set(evidencias.map(ev => ev.ciclo_descripcion).filter(Boolean))];
    const filterCiclo = document.getElementById('filterCiclo');
    
    ciclos.forEach(ciclo => {
        const option = document.createElement('option');
        option.value = ciclo;
        option.textContent = ciclo;
        filterCiclo.appendChild(option);
    });
    
    // Configurar eventos de filtro
    const filterSearch = document.getElementById('filterSearch');
    const filterOrden = document.getElementById('filterOrden');
    
    const applyFilters = () => {
        let filtered = [...evidencias];
        
        // Filtrar por búsqueda
        const searchTerm = filterSearch.value.toLowerCase();
        if (searchTerm) {
            filtered = filtered.filter(ev => 
                (ev.descripcion && ev.descripcion.toLowerCase().includes(searchTerm)) ||
                (ev.ciclo_descripcion && ev.ciclo_descripcion.toLowerCase().includes(searchTerm))
            );
        }
        
        // Filtrar por ciclo
        const cicloFilter = filterCiclo.value;
        if (cicloFilter) {
            filtered = filtered.filter(ev => ev.ciclo_descripcion === cicloFilter);
        }
        
        // Ordenar
        const orden = filterOrden.value;
        switch(orden) {
            case 'fecha_desc':
                filtered.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));
                break;
            case 'fecha_asc':
                filtered.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));
                break;
            case 'imagenes_desc':
                filtered.sort((a, b) => (b.imagenes_count || 0) - (a.imagenes_count || 0));
                break;
        }
        
        // Mostrar resultados filtrados
        displayEvidencesList(filtered);
    };
    
    filterSearch.addEventListener('input', applyFilters);
    filterCiclo.addEventListener('change', applyFilters);
    filterOrden.addEventListener('change', applyFilters);
}

// Vista rápida de evidencia
async function openQuickView(evidenciaId, imageIndex = 0) {
    try {
        const response = await fetch(`index.php?modulo=evidencia&accion=getQuickView&id=${evidenciaId}`);
        const data = await response.json();
        
        if (data.success) {
            const ev = data.evidencia;
            const fecha = new Date(ev.fecha).toLocaleDateString('es-ES');
            
            let imagesHtml = '';
            if (ev.imagenes && ev.imagenes.length > 0) {
                imagesHtml = ev.imagenes.map(img => `
                    <div class="col-6 col-md-4 col-lg-3 mb-2">
                        <img src="${img.ruta}" 
                             class="img-fluid rounded" 
                             style="height: 100px; object-fit: cover; cursor: pointer;"
                             onclick="window.open('${img.ruta}', '_blank')"
                             alt="${img.nombre_archivo}"
                             title="${img.nombre_archivo}">
                    </div>
                `).join('');
            }
            
            document.getElementById('quickViewContent').innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        ${ev.imagenes && ev.imagenes.length > 0 ? `
                        <img src="${ev.imagenes[0].ruta}" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 200px; width: 100%; object-fit: cover;"
                             alt="Imagen principal">
                        ` : `
                        <div class="text-center py-5 bg-light rounded mb-3">
                            <i class="fas fa-images fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">Sin imágenes</p>
                        </div>
                        `}
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-calendar-alt me-2"></i>Fecha</h6>
                            <p class="mb-0">${fecha}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-images me-2"></i>Imágenes</h6>
                            <p class="mb-0">${ev.imagenes_count || 0} imágenes</p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="mb-3">
                            <h6><i class="fas fa-university me-2"></i>Institución</h6>
                            <p class="mb-0">${ev.institucion_nombre}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-sync-alt me-2"></i>Ciclo</h6>
                            <p class="mb-0">${ev.ciclo_descripcion || 'Sin ciclo'}</p>
                        </div>
                        
                        ${ev.descripcion ? `
                        <div class="mb-3">
                            <h6><i class="fas fa-align-left me-2"></i>Descripción</h6>
                            <p class="mb-0" style="white-space: pre-wrap;">${ev.descripcion}</p>
                        </div>
                        ` : ''}
                        
                        ${ev.imagenes && ev.imagenes.length > 0 ? `
                        <div class="mt-4">
                            <h6><i class="fas fa-th me-2"></i>Galería</h6>
                            <div class="row mt-2">
                                ${imagesHtml}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            // Configurar enlace para vista completa
            document.getElementById('fullViewLink').href = `index.php?modulo=evidencia&accion=view&id=${evidenciaId}`;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
            modal.show();
        }
    } catch (error) {
        console.error('Error cargando vista rápida:', error);
        Swal.fire('Error', 'No se pudo cargar la evidencia', 'error');
    }
}

// Abrir modal de evidencia desde el modal de todas las evidencias
function openEvidenceModalFromModal() {
    const allEvidencesModal = bootstrap.Modal.getInstance(document.getElementById('allEvidencesModal'));
    if (allEvidencesModal) {
        allEvidencesModal.hide();
    }
    
    // Obtener ID de institución del modal actual (necesitarías guardarlo)
    // openEvidenceModal(institucionId, institucionNombre);
}

// Función global para abrir modal de evidencia
function openEvidenceModal(institucionId, institucionNombre) {
    console.log('Abriendo modal para institución:', institucionId, institucionNombre);
    
    try {
        // Obtener elementos
        const institucionIdInput = document.getElementById('modal_institucion_id');
        const institucionNombreInput = document.getElementById('modal_institucion_nombre');
        const evidenceModal = document.getElementById('evidenceModal');
        
        if (!institucionIdInput || !institucionNombreInput || !evidenceModal) {
            throw new Error('Elementos del modal no encontrados');
        }
        
        // Asignar valores
        institucionIdInput.value = institucionId;
        institucionNombreInput.value = decodeURIComponent(institucionNombre);
        
        // Resetear vista previa de imágenes
        resetImagePreview();
        
        // Abrir modal
        if (typeof bootstrap !== 'undefined') {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(evidenceModal);
            modalInstance.show();
        }
        
    } catch (error) {
        console.error('Error al abrir modal:', error);
        Swal.fire('Error', 'Error al abrir el formulario: ' + error.message, 'error');
    }
}

// Variables globales
let selectedImages = [];
let uploadInProgress = false;

// Función para inicializar el sistema de subida
function initializeImageUpload() {
    const uploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('imagenes');
    const addMoreBtn = document.getElementById('addMoreImagesBtn');
    
    // Configurar área de drop
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageSelection(files);
            }
        });
        
        // Click en área de subida
        uploadArea.addEventListener('click', function() {
            if (fileInput) {
                fileInput.click();
            }
        });
    }
    
    // Cambio en input de archivos
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                handleImageSelection(this.files);
                this.value = ''; // Resetear para permitir seleccionar las mismas imágenes
            }
        });
    }
    
    // Botón "Agregar más"
    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function() {
            if (fileInput) {
                fileInput.click();
            }
        });
    }
    
    // Actualizar contador
    updateImageCount();
}

// Manejar selección de imágenes
function handleImageSelection(files) {
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    const maxImages = 10;
    
    // Verificar límite de imágenes
    if (selectedImages.length + files.length > maxImages) {
        showUploadStatus('error', `Máximo ${maxImages} imágenes permitidas. Ya tienes ${selectedImages.length} seleccionadas.`);
        return;
    }
    
    Array.from(files).forEach(file => {
        // Validar tipo
        if (!allowedTypes.includes(file.type.toLowerCase())) {
            showUploadStatus('error', `Tipo de archivo no permitido: ${file.name}. Solo se permiten imágenes JPG, PNG, GIF o WebP.`);
            return;
        }
        
        // Validar tamaño
        if (file.size > maxSize) {
            showUploadStatus('error', `La imagen ${file.name} es demasiado grande (${(file.size/1024/1024).toFixed(2)}MB). Máximo 5MB.`);
            return;
        }
        
        // Agregar a la lista
        const imageId = Date.now() + Math.random();
        selectedImages.push({
            id: imageId,
            file: file,
            preview: null,
            uploaded: false
        });
        
        // Crear vista previa
        createImagePreview(imageId, file);
    });
    
    // Actualizar contador
    updateImageCount();
    
    // Mostrar botón de agregar más si hay imágenes
    toggleAddMoreButton();
    
    // Actualizar input files
    updateFileInput();
}

// Crear vista previa de imagen
function createImagePreview(imageId, file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const previewContainer = document.createElement('div');
        previewContainer.className = 'col-md-3 mb-3 image-preview-container';
        previewContainer.id = `preview-${imageId}`;
        
        // Calcular tamaño en formato legible
        const fileSize = file.size < 1024 * 1024 
            ? `${(file.size/1024).toFixed(1)} KB` 
            : `${(file.size/(1024*1024)).toFixed(2)} MB`;
        
        previewContainer.innerHTML = `
            <img src="${e.target.result}" class="image-preview" alt="${file.name}">
            <button type="button" class="image-remove-btn" onclick="removeImage('${imageId}')" title="Eliminar imagen">
                <i class="fas fa-times"></i>
            </button>
            <div class="image-upload-progress">
                <div class="image-upload-progress-bar" id="progress-${imageId}"></div>
            </div>
            <div class="image-info">
                <span class="text-truncate">${file.name}</span>
                <span>${fileSize}</span>
            </div>
        `;
        
        const imagePreview = document.getElementById('imagePreview');
        if (imagePreview) {
            imagePreview.appendChild(previewContainer);
        }
        
        // Guardar preview en el objeto
        const imageIndex = selectedImages.findIndex(img => img.id === imageId);
        if (imageIndex !== -1) {
            selectedImages[imageIndex].preview = e.target.result;
        }
    };
    
    reader.readAsDataURL(file);
}

// Eliminar imagen
function removeImage(imageId) {
    // Encontrar y eliminar de la lista
    const imageIndex = selectedImages.findIndex(img => img.id === imageId);
    if (imageIndex !== -1) {
        selectedImages.splice(imageIndex, 1);
    }
    
    // Eliminar vista previa del DOM
    const previewElement = document.getElementById(`preview-${imageId}`);
    if (previewElement) {
        previewElement.remove();
    }
    
    // Actualizar contador
    updateImageCount();
    
    // Mostrar/ocultar botón de agregar más
    toggleAddMoreButton();
    
    // Actualizar input files
    updateFileInput();
    
    showUploadStatus('info', 'Imagen eliminada');
}

// Actualizar contador de imágenes
function updateImageCount() {
    const imageCount = document.getElementById('imageCount');
    if (imageCount) {
        imageCount.textContent = selectedImages.length;
        imageCount.style.display = selectedImages.length > 0 ? 'inline-block' : 'none';
    }
    
    const imagesCountBadge = document.getElementById('imagesCountBadge');
    if (imagesCountBadge) {
        imagesCountBadge.textContent = selectedImages.length;
    }
    
    const uploadAreaText = document.getElementById('uploadAreaText');
    if (uploadAreaText) {
        if (selectedImages.length === 0) {
            uploadAreaText.innerHTML = `
                <div class="image-upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h5 class="mb-2">Arrastra imágenes aquí</h5>
                <p class="text-muted mb-0">o haz clic para seleccionar</p>
            `;
        } else {
            uploadAreaText.innerHTML = `
                <div class="image-upload-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h5 class="mb-2">${selectedImages.length} imagen(es) seleccionada(s)</h5>
                <p class="text-muted mb-0">Haz clic para agregar más imágenes</p>
            `;
        }
    }
}

// Mostrar/ocultar botón de agregar más
function toggleAddMoreButton() {
    const addMoreBtn = document.getElementById('addMoreImagesBtn');
    if (addMoreBtn) {
        addMoreBtn.style.display = selectedImages.length > 0 ? 'flex' : 'none';
    }
}

// Actualizar input files - SOLUCIÓN MEJORADA
function updateFileInput() {
    // Crear nuevo input file
    const oldFileInput = document.getElementById('imagenes');
    if (!oldFileInput) return;
    
    const newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.name = 'imagenes[]';
    newFileInput.id = 'imagenes';
    newFileInput.className = 'd-none';
    newFileInput.multiple = true;
    newFileInput.accept = 'image/*';
    
    // Usar DataTransfer para asignar archivos
    const dataTransfer = new DataTransfer();
    selectedImages.forEach(image => {
        dataTransfer.items.add(image.file);
    });
    newFileInput.files = dataTransfer.files;
    
    // Reemplazar el input antiguo
    oldFileInput.parentNode.replaceChild(newFileInput, oldFileInput);
    
    // Reasignar event listener
    newFileInput.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            handleImageSelection(this.files);
            this.value = '';
        }
    });
    
    console.log('Input file actualizado con', selectedImages.length, 'archivos');
}

// Mostrar estado de subida
function showUploadStatus(type, message) {
    const uploadStatus = document.getElementById('uploadStatus');
    if (uploadStatus) {
        uploadStatus.className = `upload-status ${type}`;
        uploadStatus.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <span>${message}</span>
            </div>
        `;
        uploadStatus.style.display = 'block';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            uploadStatus.style.display = 'none';
        }, 5000);
    }
}

// Mostrar loader de subida
function showUploadLoader(message = 'Subiendo imágenes...') {
    const loader = document.getElementById('uploadLoader');
    const loaderMessage = document.getElementById('loaderMessage');
    
    if (loader && loaderMessage) {
        loaderMessage.textContent = message;
        loader.style.display = 'flex';
        uploadInProgress = true;
        
        // Bloquear formulario
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
    }
}

// Ocultar loader de subida
function hideUploadLoader() {
    const loader = document.getElementById('uploadLoader');
    if (loader) {
        loader.style.display = 'none';
        uploadInProgress = false;
        
        // Desbloquear formulario
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }
}

// Resetear vista previa
function resetImagePreview() {
    selectedImages = [];
    const imagePreview = document.getElementById('imagePreview');
    if (imagePreview) {
        imagePreview.innerHTML = '';
    }
    
    const fileInput = document.getElementById('imagenes');
    if (fileInput) {
        fileInput.value = '';
    }
    
    updateImageCount();
    toggleAddMoreButton();
}

// Tomar foto con cámara (para móviles)
function takePhoto() {
    const fileInput = document.getElementById('imagenes');
    if (!fileInput) return;
    
    // Para dispositivos móviles que soportan capture
    fileInput.setAttribute('capture', 'environment');
    fileInput.click();
    
    // Restaurar después de hacer clic
    setTimeout(() => {
        fileInput.removeAttribute('capture');
    }, 100);
}

// ENVÍO DEL FORMULARIO - SOLUCIÓN DEFINITIVA
async function submitEvidenceForm(e) {
    e.preventDefault();
    
    console.log('Iniciando envío del formulario...');
    
    // Validar imágenes
    if (selectedImages.length === 0) {
        Swal.fire({
            title: 'Sin imágenes',
            text: 'Debe seleccionar al menos una imagen para la evidencia.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Validar ciclo
    const cicloSelect = document.getElementById('ciclo_id');
    if (!cicloSelect || !cicloSelect.value) {
        Swal.fire({
            title: 'Ciclo requerido',
            text: 'Debe seleccionar un ciclo para la evidencia.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Validar institución
    const institucionId = document.getElementById('modal_institucion_id');
    if (!institucionId || !institucionId.value) {
        Swal.fire({
            title: 'Institución requerida',
            text: 'No se ha seleccionado una institución.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Mostrar loader
    showUploadLoader('Guardando evidencia...');
    
    try {
        // Crear FormData manualmente
        const formData = new FormData();
        
        // Agregar campos del formulario
        formData.append('modulo', 'evidencia');
        formData.append('accion', 'create');
        formData.append('institucion_id', institucionId.value);
        formData.append('ciclo_id', cicloSelect.value);
        formData.append('fecha', document.getElementById('fecha').value);
        formData.append('descripcion', document.getElementById('descripcion').value);
        
        // DEBUG: Verificar valores
        console.log('Valores del formulario:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Agregar imágenes
        console.log('Agregando', selectedImages.length, 'imágenes...');
        selectedImages.forEach((image, index) => {
            formData.append('imagenes[]', image.file, image.file.name);
            console.log(`Imagen ${index}:`, image.file.name, image.file.size, image.file.type);
        });
        
        // Enviar por AJAX
        const response = await fetch('index.php?modulo=evidencia&accion=create', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // Obtener respuesta
        const responseText = await response.text();
        console.log('Respuesta del servidor:', responseText);
        
        // Verificar si la respuesta es JSON
        let responseData;
        try {
            responseData = JSON.parse(responseText);
        } catch (e) {
            // Si no es JSON, verificar si es redirección
            if (responseText.includes('Location:') || responseText.includes('redirect')) {
                // Redirección exitosa
                hideUploadLoader();
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('evidenceModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Evidencia creada correctamente',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
                return;
            } else {
                throw new Error('Respuesta del servidor no válida');
            }
        }
        
        // Manejar respuesta JSON
        if (responseData.success) {
            hideUploadLoader();
            
            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('evidenceModal'));
            if (modal) {
                modal.hide();
            }
            
            // Mostrar mensaje de éxito
            Swal.fire({
                title: '¡Éxito!',
                text: responseData.message || 'Evidencia creada correctamente',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(responseData.message || 'Error al crear la evidencia');
        }
        
    } catch (error) {
        hideUploadLoader();
        console.error('Error en submit:', error);
        
        Swal.fire({
            title: 'Error',
            text: 'Error al guardar la evidencia: ' + error.message,
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar sistema de subida
    initializeImageUpload();
    
    // Configurar evento submit del formulario
    const evidenceForm = document.getElementById('evidenceForm');
    if (evidenceForm) {
        evidenceForm.addEventListener('submit', submitEvidenceForm);
    }
    
    // Cerrar modal resetea las imágenes
    const evidenceModal = document.getElementById('evidenceModal');
    if (evidenceModal) {
        evidenceModal.addEventListener('hidden.bs.modal', function() {
            resetImagePreview();
        });
    }
    
    // Configurar evento para el botón "Tomar foto"
    const takePhotoBtn = document.querySelector('button[onclick*="takePhoto"]');
    if (takePhotoBtn) {
        takePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            takePhoto();
        });
    }
});

// ========== CÓDIGO PARA EDITAR EVIDENCIA (si existe) ==========
<?php if (isset($evidencia) && isset($evidencia['id'])): ?>
// Variables para edición
let editEvidenciaModal = null;
let evidenciaId = <?php echo isset($evidencia['id']) ? $evidencia['id'] : 'null'; ?>;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar modal de edición
    const editModalElement = document.getElementById('editEvidenciaModal');
    if (editModalElement && typeof bootstrap !== 'undefined') {
        editEvidenciaModal = new bootstrap.Modal(editModalElement);
    }
    
    // Vista previa de nuevas imágenes
    const nuevasImagenesInput = document.getElementById('nuevasImagenes');
    if (nuevasImagenesInput) {
        nuevasImagenesInput.addEventListener('change', function(e) {
            const preview = document.getElementById('nuevasImagenesPreview');
            if (!preview) return;
            
            preview.innerHTML = '';
            
            Array.from(this.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'col-md-3 mb-2';
                    div.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" 
                                 class="card-img-top" 
                                 style="height: 100px; object-fit: cover;"
                                 alt="Nueva imagen ${index + 1}"
                                 loading="lazy">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    }
    
    // Eliminar imagen existente
    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.dataset.id;
            const imageName = this.dataset.name;
            
            Swal.fire({
                title: '¿Eliminar imagen?',
                html: `¿Está seguro de eliminar la imagen <strong>${imageName}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('modulo', 'evidencia');
                    formData.append('accion', 'deleteImage');
                    formData.append('imagen_id', imageId);
                    
                    fetch(`index.php?modulo=evidencia&accion=deleteImage`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Eliminar del DOM
                            const imagenElement = document.getElementById(`imagen-${imageId}`);
                            if (imagenElement) {
                                imagenElement.remove();
                            }
                            Swal.fire('Éxito', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });
                }
            });
        });
    });
    
    // Enviar formulario de edición
    const editForm = document.getElementById('editEvidenciaForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('editEvidenciaSubmitBtn');
            const originalHtml = submitBtn.innerHTML;
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
            
            const formData = new FormData(this);
            
            fetch(`index.php?modulo=evidencia&accion=edit`, {
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
                        if (editEvidenciaModal) {
                            editEvidenciaModal.hide();
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Error al guardar los cambios', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    }
});

// Función para abrir modal de edición
function openEditEvidenciaModal() {
    if (editEvidenciaModal) {
        document.getElementById('editEvidenciaId').value = evidenciaId;
        editEvidenciaModal.show();
    }
}
<?php endif; ?>
</script>
<?php endif; ?>