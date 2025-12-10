<?php
// Definir título de página
$page_title = "Gestión de Instituciones";
?>

<style>
/* Estilos para la vista previa del escudo y paginación */
.escudo-thumbnail {
    cursor: pointer;
    transition: all 0.3s ease;
}

.escudo-thumbnail:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.escudo-preview-modal .modal-dialog {
    max-width: 500px;
}

.escudo-preview-img {
    width: 100%;
    height: auto;
    max-height: 70vh;
    object-fit: contain;
}

.load-more-container {
    text-align: center;
    padding: 20px;
}

.load-more-btn {
    min-width: 150px;
}

.load-more-btn .spinner-border {
    width: 1rem;
    height: 1rem;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    display: none;
}

.results-count {
    font-size: 0.9rem;
    color: #6c757d;
}

.pagination-info {
    font-size: 0.85rem;
}
</style>

<div class="container-fluid">
    <!-- Header con título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-university me-2"></i> Instituciones
        </h1>
        <button type="button" class="btn btn-primary" onclick="openCreateInstitucionModal()">
            <i class="fas fa-plus me-2"></i> Nueva Institución
        </button>
    </div>

    <!-- Card principal -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Instituciones</h5>
            <div class="d-flex align-items-center">
                <!-- En el formulario de búsqueda -->
				<form id="searchForm" method="GET" class="d-flex me-2" style="min-width: 300px;">
					<input type="hidden" name="modulo" value="institucion">
					<input type="hidden" name="accion" value="index">
					<input type="hidden" name="page" value="1" id="currentPage">
					<input type="text" 
						   name="search" 
						   class="form-control form-control-sm" 
						   placeholder="Buscar por nombre o ciudad..." 
						   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
						   id="searchInput"
						   autocomplete="off">
					<button type="submit" class="btn btn-sm btn-primary ms-2" id="searchBtn">
						<i class="fas fa-search"></i>
					</button>
					<?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
					<a href="index.php?modulo=institucion&accion=index" 
					   class="btn btn-sm btn-outline-secondary ms-2"
					   id="clearSearchBtn">
						<i class="fas fa-times"></i>
					</a>
					<?php endif; ?>
				</form>
            </div>
        </div>
        
        <!-- Corrección: Verificar si las variables existen -->
<div class="card-body position-relative">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
    
    <!-- Información de resultados -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="results-count" id="resultsCount">
            <?php 
            // Usar operador ternario con valor por defecto
            $total_instituciones = isset($total_instituciones) ? $total_instituciones : 0;
            $current_count = isset($instituciones) ? $instituciones->rowCount() : 0;
            $has_more = isset($has_more) ? $has_more : false;
            ?>
            <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
            <span id="totalResults"><?php echo $total_instituciones; ?></span> resultados para "<?php echo htmlspecialchars($_GET['search']); ?>"
            <?php else: ?>
            <span id="totalResults"><?php echo $total_instituciones; ?></span> instituciones en total
            <?php endif; ?>
        </div>
        <div class="pagination-info">
            Mostrando <span id="showingCount"><?php echo $current_count; ?></span> de <span id="totalCount"><?php echo $total_instituciones; ?></span>
        </div>
    </div>
    
    <!-- Tabla de instituciones -->
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="institutionsTable">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Nombre</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Escudo</th>
                    <th>Evidencias</th>
                    <th>Última Evidencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="institutionsTbody">
                <!-- Las instituciones se cargarán aquí -->
                <?php 
                // Incluir la tabla parcial solo si $instituciones existe
                if (isset($instituciones)) {
                    include 'views/instituciones/partials/institution_table.php';
                } else {
                    echo '<tr><td colspan="8" class="text-center py-4">Cargando instituciones...</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Contenedor para "Cargar más" -->
    <div id="loadMoreContainer" class="load-more-container" 
         style="<?php echo (!$has_more || $current_count == 0) ? 'display: none;' : ''; ?>">
                <button type="button" class="btn btn-outline-primary load-more-btn" id="loadMoreBtn" onclick="loadMoreInstitutions()">
                    <span class="btn-text">Cargar más instituciones</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
                <small class="text-muted d-block mt-2">
                    <span id="remainingCount"><?php echo max(0, $total_instituciones - $current_count); ?></span> instituciones restantes
                </small>
            </div>
            
            <?php if ($current_count == 0): ?>
            <div class="text-center py-5" id="noResultsMessage">
                <div class="mb-4">
                    <i class="fas fa-university fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                    No se encontraron instituciones
                    <?php else: ?>
                    No hay instituciones registradas
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-4">
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                    No hay instituciones que coincidan con "<?php echo htmlspecialchars($_GET['search']); ?>"
                    <?php else: ?>
                    Comience agregando su primera institución
                    <?php endif; ?>
                </p>
                <button type="button" class="btn btn-primary" onclick="openCreateInstitucionModal()">
                    <i class="fas fa-plus me-2"></i> Agregar Institución
                </button>
                <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                <a href="index.php?modulo=institucion&accion=index" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i> Limpiar búsqueda
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para vista previa del escudo -->
<div class="modal fade escudo-preview-modal" id="escudoPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="escudoModalTitle">Escudo de Institución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="escudoPreviewImage" src="" class="escudo-preview-img img-fluid rounded" alt="">
                <div class="mt-3">
                    <h6 id="escudoInstitucionNombre"></h6>
                    <small class="text-muted" id="escudoImageInfo"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <a href="#" id="escudoDownloadBtn" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                <input type="hidden" name="modulo" value="institucion">
                <input type="hidden" name="accion" value="toggleStatus">
                <div class="modal-body">
                    <input type="hidden" name="institucion_id" id="modalInstitucionId">
                    <input type="hidden" name="action" id="modalAction">
                    
                    <p id="statusModalMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PARA CREAR/EDITAR INSTITUCIÓN -->
<div class="modal fade" id="institucionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Institución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="institucionForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="institucionId">
                    <input type="hidden" name="modulo" value="institucion">
                    <input type="hidden" name="accion" id="formAction" value="create">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la Institución *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                                    <label class="form-check-label" for="activo">
                                        Institución Activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Las instituciones inactivas no aparecerán en el listado para registrar evidencias.
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <label class="form-label d-block">Escudo de la Institución</label>
                                    <div class="image-upload-container">
                                        <img id="escudoPreview" 
                                             src="assets/img/default-institution.png" 
                                             class="img-thumbnail rounded-circle mb-2" 
                                             style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                             onclick="document.getElementById('escudoInput').click()">
                                        <input type="file" 
                                               class="form-control d-none" 
                                               id="escudoInput" 
                                               name="escudo" 
                                               accept="image/*">
                                        <div class="mt-2">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    onclick="document.getElementById('escudoInput').click()">
                                                <i class="fas fa-upload me-1"></i> Subir Escudo
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    id="removeEscudoBtn" 
                                                    style="display: none;"
                                                    onclick="removeEscudoPreview()">
                                                <i class="fas fa-trash me-1"></i> Quitar
                                            </button>
                                        </div>
                                        <small class="form-text text-muted d-block mt-2">
                                            Tamaño máximo: 5MB<br>
                                            Formatos: JPG, PNG, GIF
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Guardar Institución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales
let institucionModal = null;
let escudoPreviewModal = null;
let currentPage = <?php echo isset($_GET['page']) ? (int)$_GET['page'] : 1; ?>;
let isLoading = false;
let hasMore = <?php echo isset($has_more) && $has_more ? 'true' : 'false'; ?>;
let totalInstitutions = <?php echo isset($total_instituciones) ? $total_instituciones : 0; ?>;
let showingCount = <?php echo isset($instituciones) ? $instituciones->rowCount() : 0; ?>;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar modales de Bootstrap
    if (typeof bootstrap !== 'undefined') {
        institucionModal = new bootstrap.Modal(document.getElementById('institucionModal'));
        escudoPreviewModal = new bootstrap.Modal(document.getElementById('escudoPreviewModal'));
    }
    
    // Inicializar tooltips
    initTooltips();
    
    // Configurar vista previa del escudo
    setupEscudoPreview();
    
    // Configurar búsqueda
    setupSearch();
    
    // Configurar scroll infinito opcional
    setupInfiniteScroll();
    
    // Actualizar contadores iniciales
    updateCounters();
});

// Inicializar tooltips de Bootstrap
function initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        // Inicializar tooltips existentes
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Actualizar tooltips después de cargar más contenido
function updateTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        // Limpiar tooltips antiguos
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            const instance = bootstrap.Tooltip.getInstance(el);
            if (instance) {
                instance.dispose();
            }
        });
        
        // Inicializar nuevos tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Configurar vista previa del escudo
function setupEscudoPreview() {
    const escudoInput = document.getElementById('escudoInput');
    if (escudoInput) {
        escudoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validar tamaño (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', 'La imagen no debe superar los 5MB', 'error');
                    this.value = '';
                    return;
                }
                
                // Validar tipo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire('Error', 'Solo se permiten imágenes JPG, PNG o GIF', 'error');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('escudoPreview').src = e.target.result;
                    document.getElementById('removeEscudoBtn').style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Quitar vista previa del escudo
function removeEscudoPreview() {
    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
    document.getElementById('escudoInput').value = '';
    document.getElementById('removeEscudoBtn').style.display = 'none';
}

// Vista previa del escudo en modal
function viewEscudoPreview(imageSrc, institucionNombre) {
    if (!escudoPreviewModal) {
        alert('No se pudo cargar el visor de imágenes');
        return;
    }
    
    const img = document.getElementById('escudoPreviewImage');
    img.src = imageSrc;
    
    document.getElementById('escudoModalTitle').textContent = 'Escudo de Institución';
    document.getElementById('escudoInstitucionNombre').textContent = institucionNombre;
    
    // Configurar botón de descarga
    const downloadBtn = document.getElementById('escudoDownloadBtn');
    downloadBtn.href = imageSrc;
    downloadBtn.download = 'escudo_' + institucionNombre.replace(/\s+/g, '_') + '.jpg';
    
    // Mostrar información de la imagen
    const imgInfo = document.getElementById('escudoImageInfo');
    const tempImg = new Image();
    tempImg.src = imageSrc;
    tempImg.onload = function() {
        imgInfo.textContent = `${this.naturalWidth} × ${this.naturalHeight} px`;
    };
    
    escudoPreviewModal.show();
}

// Configurar búsqueda
function setupSearch() {
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            searchInstitutions();
        });
        
        // Búsqueda en tiempo real opcional (con delay)
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    searchInstitutions();
                }
            }, 500);
        });
    }
}

// Buscar instituciones
// Corrección en la función searchInstitutions():
function searchInstitutions() {
    const searchForm = document.getElementById('searchForm');
    const searchValue = document.getElementById('searchInput').value;
    
    // Resetear a página 1 cuando se busca
    document.getElementById('currentPage').value = 1;
    currentPage = 1;
    
    // Mostrar loading
    showLoading();
    
    // Ocultar mensaje de no resultados
    const noResultsMessage = document.getElementById('noResultsMessage');
    if (noResultsMessage) {
        noResultsMessage.style.display = 'none';
    }
    
    // Construir URL correctamente
    const url = `index.php?modulo=institucion&accion=index&search=${encodeURIComponent(searchValue)}&page=1`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            throw new Error("La respuesta no es JSON");
        }
    })
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Actualizar tabla
            document.getElementById('institutionsTbody').innerHTML = data.html;
            
            // Actualizar variables
            hasMore = data.has_more;
            totalInstitutions = data.total;
            showingCount = data.showing_count || (data.page * 20);
            
            // Actualizar contadores
            updateCounters();
            
            // Mostrar/ocultar botón "Cargar más"
            const loadMoreContainer = document.getElementById('loadMoreContainer');
            if (data.html.includes('<tr>') && hasMore) {
                loadMoreContainer.style.display = 'block';
            } else {
                loadMoreContainer.style.display = 'none';
            }
            
            // Actualizar tooltips
            updateTooltips();
            
        } else {
            Swal.fire('Error', data.message || 'Error al buscar instituciones', 'error');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    });
}

// Limpiar búsqueda
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('currentPage').value = 1;
    searchInstitutions();
}

// Cargar más instituciones
function loadMoreInstitutions() {
    if (isLoading || !hasMore) return;
    
    isLoading = true;
    currentPage++;
    
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const btnText = loadMoreBtn.querySelector('.btn-text');
    const spinner = loadMoreBtn.querySelector('.spinner-border');
    
    // Mostrar loading en el botón
    btnText.textContent = 'Cargando...';
    spinner.classList.remove('d-none');
    loadMoreBtn.disabled = true;
    
    const searchValue = document.getElementById('searchInput').value;
    const url = searchValue 
        ? `index.php?modulo=institucion&accion=index&search=${encodeURIComponent(searchValue)}&page=${currentPage}`
        : `index.php?modulo=institucion&accion=index&page=${currentPage}`;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        isLoading = false;
        
        // Restaurar botón
        btnText.textContent = 'Cargar más instituciones';
        spinner.classList.add('d-none');
        loadMoreBtn.disabled = false;
        
        if (data.success) {
            // Agregar nuevas filas a la tabla
            document.getElementById('institutionsTbody').insertAdjacentHTML('beforeend', data.html);
            
            // Actualizar variables
            hasMore = data.has_more;
            showingCount += 20; // Asumiendo 20 por página
            
            // Actualizar contadores
            updateCounters();
            
            // Ocultar botón si no hay más
            if (!hasMore) {
                document.getElementById('loadMoreContainer').style.display = 'none';
            }
            
            // Actualizar tooltips
            updateTooltips();
            
        } else {
            Swal.fire('Error', 'Error al cargar más instituciones', 'error');
            currentPage--; // Revertir página en caso de error
        }
    })
    .catch(error => {
        isLoading = false;
        
        // Restaurar botón
        btnText.textContent = 'Cargar más instituciones';
        spinner.classList.add('d-none');
        loadMoreBtn.disabled = false;
        currentPage--; // Revertir página en caso de error
        
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}

// Configurar scroll infinito (opcional)
function setupInfiniteScroll() {
    window.addEventListener('scroll', function() {
        // Si prefieres scroll infinito en lugar de botón
        // Descomenta este código y oculta el botón "Cargar más"
        
        /*
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        if (!loadMoreContainer || loadMoreContainer.style.display === 'none') return;
        
        const containerRect = loadMoreContainer.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        
        // Cargar más cuando el contenedor esté cerca de la parte inferior
        if (containerRect.top <= windowHeight + 100) {
            loadMoreInstitutions();
        }
        */
    });
}

// Actualizar contadores
function updateCounters() {
    document.getElementById('totalResults').textContent = totalInstitutions;
    document.getElementById('showingCount').textContent = showingCount;
    document.getElementById('totalCount').textContent = totalInstitutions;
    document.getElementById('remainingCount').textContent = Math.max(0, totalInstitutions - showingCount);
    
    // Actualizar información de paginación
    const resultsCount = document.getElementById('resultsCount');
    const searchValue = document.getElementById('searchInput').value;
    
    if (searchValue) {
        resultsCount.innerHTML = `<span id="totalResults">${totalInstitutions}</span> resultados para "${searchValue}"`;
    } else {
        resultsCount.innerHTML = `<span id="totalResults">${totalInstitutions}</span> instituciones en total`;
    }
}

// Mostrar loading
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

// Ocultar loading
function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

// Función para abrir modal de crear institución
function openCreateInstitucionModal() {
    if (!institucionModal) {
        alert('Bootstrap no está cargado. Recargue la página.');
        return;
    }
    
    document.getElementById('modalTitle').textContent = 'Nueva Institución';
    document.getElementById('formAction').value = 'create';
    document.getElementById('institucionId').value = '';
    document.getElementById('institucionForm').reset();
    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
    document.getElementById('removeEscudoBtn').style.display = 'none';
    document.getElementById('activo').checked = true;
    
    institucionModal.show();
}

// Función para abrir modal de editar institución
function openEditInstitucionModal(id) {
    if (!institucionModal) {
        alert('Bootstrap no está cargado. Recargue la página.');
        return;
    }
    
    fetch(`index.php?modulo=institucion&accion=edit&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const institucion = data.data;
                
                document.getElementById('modalTitle').textContent = 'Editar Institución';
                document.getElementById('formAction').value = 'edit';
                document.getElementById('institucionId').value = institucion.id;
                document.getElementById('nombre').value = institucion.nombre;
                document.getElementById('ciudad').value = institucion.ciudad;
                document.getElementById('activo').checked = institucion.activo == 1;
                
                if (institucion.escudo) {
                    document.getElementById('escudoPreview').src = institucion.escudo + '?' + new Date().getTime();
                    document.getElementById('removeEscudoBtn').style.display = 'inline-block';
                } else {
                    document.getElementById('escudoPreview').src = 'assets/img/default-institution.png';
                    document.getElementById('removeEscudoBtn').style.display = 'none';
                }
                
                institucionModal.show();
            } else {
                Swal.fire('Error', data.message || 'Error al cargar la institución', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión', 'error');
        });
}

// Enviar formulario de institución
document.getElementById('institucionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('modalSubmitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const originalText = submitBtn.innerHTML;
    
    // Mostrar loading
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Procesando...';
    
    const formData = new FormData(this);
    const action = document.getElementById('formAction').value;
    
    // URL de la solicitud
    const url = `index.php?modulo=institucion&accion=${action}`;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
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
                if (institucionModal) {
                    institucionModal.hide();
                }
                // Recargar la página para mostrar la nueva institución
                window.location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Error al guardar la institución', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión: ' + error.message, 'error');
    })
    .finally(() => {
        // Restaurar botón
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Manejar cambio de estado
document.addEventListener('click', function(e) {
    if (e.target.closest('.toggle-status')) {
        const button = e.target.closest('.toggle-status');
        const id = button.dataset.id;
        const action = button.dataset.action;
        const name = button.dataset.name;
        
        const modal = new bootstrap.Modal(document.getElementById('statusModal'));
        const title = document.getElementById('statusModalTitle');
        const message = document.getElementById('statusModalMessage');
        
        document.getElementById('modalInstitucionId').value = id;
        document.getElementById('modalAction').value = action;
        
        if (action === 'deactivate') {
            title.textContent = 'Desactivar Institución';
            message.innerHTML = `
                ¿Está seguro de desactivar la institución <strong>${name}</strong>?<br>
                <small class="text-muted">
                    • No podrá registrar nuevas evidencias para esta institución<br>
                    • No aparecerá en el listado principal<br>
                    • Las evidencias existentes se mantendrán
                </small>`;
        } else {
            title.textContent = 'Activar Institución';
            message.innerHTML = `
                ¿Está seguro de activar la institución <strong>${name}</strong>?<br>
                <small class="text-muted">La institución aparecerá nuevamente en todos los listados</small>`;
        }
        
        modal.show();
    }
});

// Enviar formulario de cambio de estado
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Procesando...';
    
    const formData = new FormData(this);
    
    fetch('index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            Swal.fire('Error', data.message || 'Error al cambiar el estado', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Manejar clic en escudos de la tabla
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('escudo-thumbnail')) {
        const imageSrc = e.target.src;
        const altText = e.target.alt || 'Escudo de Institución';
        const institucionNombre = altText.replace('Escudo ', '');
        
        viewEscudoPreview(imageSrc, institucionNombre);
    }
});
</script>