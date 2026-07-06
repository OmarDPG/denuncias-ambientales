<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal: Aprobar Inspección (DNS → DS) -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<div id="aprobarInspeccionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeAprobarInspeccionModalOnOverlay(event)">
    <div class="bg-surface w-full max-w-2xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex-shrink-0 bg-gradient-to-br from-primary to-primary-container text-white rounded-t-3xl px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="font-headline font-bold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                    Aprobar para Inspección
                </h2>
                <p id="aprobarInspeccionModalFolio" class="text-white/80 text-sm mt-1">Folio: #DA-XXXX</p>
            </div>
            <button onclick="closeAprobarInspeccionModal()" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6">
            <form id="aprobarInspeccionForm" class="space-y-6">
                <input type="hidden" id="aprobarInspeccionIdDenuncia" name="id_denuncia">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Información -->
                <div class="bg-surface-container-low border-l-4 border-primary p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-primary text-2xl">info</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-primary mb-1">Acción a realizar</p>
                            <p class="text-sm text-on-surface">
                                Al aprobar esta denuncia, se enviará al <strong>Departamento de Supervisión (DS)</strong> 
                                para que procedan con la inspección en campo.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Observaciones para Supervisión <span class="text-error">*</span>
                    </label>
                    <textarea id="aprobarInspeccionObservaciones" name="observaciones" required
                        rows="5"
                        placeholder="Indique las acciones específicas que debe realizar el equipo de supervisión..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">lightbulb</span>
                        Incluya detalles como tipo de inspección, aspectos a verificar, normatividad aplicable, etc.
                    </p>
                </div>

                <!-- Archivo de Aprobación -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Documento de Aprobación <span class="text-secondary">(Opcional)</span>
                    </label>
                    <input type="file" id="aprobarInspeccionArchivo" name="archivo"
                        accept=".pdf,.doc,.docx"
                        class="w-full bg-surface-container-low border-2 border-dashed border-outline-variant/40 focus:border-primary rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-bold file:text-xs hover:file:opacity-90"
                        data-preview="aprobarInspeccionPreview" />
                    <p class="text-xs text-secondary">Adjunte el dictamen técnico o documento de aprobación (PDF, DOC, DOCX). Máximo 10MB.</p>
                    <div id="aprobarInspeccionPreview" class="mt-3"></div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeAprobarInspeccionModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveAprobarInspeccion()" type="button" id="btnAprobarInspeccionSubmit"
                class="px-6 py-2 bg-gradient-to-br from-green-600 to-green-500 text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                Aprobar para Inspección
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal Aprobar Inspección
// ═══════════════════════════════════════════════════════════════════════════

let currentAprobarInspeccionDenunciaId = null;

function openAprobarInspeccionModal(idDenuncia) {
    currentAprobarInspeccionDenunciaId = idDenuncia;
    document.getElementById('aprobarInspeccionIdDenuncia').value = idDenuncia;
    
    const modal = document.getElementById('aprobarInspeccionModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('aprobarInspeccionForm').reset();
    document.getElementById('aprobarInspeccionPreview').innerHTML = '';
    
    // Obtener folio real desde la base de datos
    document.getElementById('aprobarInspeccionModalFolio').textContent = 'Cargando...';
    
    fetch(`<?= base_url('admin/obtenerDenunciaDetalle/') ?>${idDenuncia}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data && data.data.denuncia) {
            const folio = data.data.denuncia.folio || '#DA-' + String(idDenuncia).padStart(4, '0');
            document.getElementById('aprobarInspeccionModalFolio').textContent = 'Folio: ' + folio;
        } else {
            // Fallback al formato antiguo si falla
            document.getElementById('aprobarInspeccionModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
        }
    })
    .catch(error => {
        console.error('Error al obtener folio:', error);
        // Fallback al formato antiguo si hay error
        document.getElementById('aprobarInspeccionModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    });
}

function closeAprobarInspeccionModal() {
    const modal = document.getElementById('aprobarInspeccionModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentAprobarInspeccionDenunciaId = null;
}

function closeAprobarInspeccionModalOnOverlay(event) {
    if (event.target.id === 'aprobarInspeccionModal') {
        closeAprobarInspeccionModal();
    }
}

function saveAprobarInspeccion() {
    const form = document.getElementById('aprobarInspeccionForm');
    const btnSubmit = document.getElementById('btnAprobarInspeccionSubmit');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar observaciones
    const observaciones = document.getElementById('aprobarInspeccionObservaciones').value.trim();
    if (observaciones.length < 20) {
        showToast('error', 'Observaciones insuficientes', 'Debe proporcionar al menos 20 caracteres de instrucciones para el equipo de supervisión');
        return;
    }
    
    // Confirmar acción
    if (!confirm('¿Está seguro de aprobar esta denuncia para inspección? Se enviará al Departamento de Supervisión.')) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData manualmente para asegurar que el archivo se incluya
    const formData = new FormData();
    formData.append('id_denuncia', document.getElementById('aprobarInspeccionIdDenuncia').value);
    formData.append('observaciones', observaciones);
    
    // Agregar CSRF token
    const csrfInput = form.querySelector('input[name^="csrf_"]');
    if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
    }
    
    // Agregar archivo si existe
    const archivoInput = document.getElementById('aprobarInspeccionArchivo');
    if (archivoInput && archivoInput.files.length > 0) {
        formData.append('archivo', archivoInput.files[0]);
        console.log('Archivo adjunto:', archivoInput.files[0].name, '(', archivoInput.files[0].size, 'bytes)');
    }
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/aprobarInspeccion") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Inspección aprobada', data.message || 'Denuncia enviada a Supervisión correctamente');
            closeAprobarInspeccionModal();
            recargarPagina();
        } else {
            showToast('error', 'Error al aprobar', data.message || 'No se pudo aprobar la inspección');
            setButtonLoading(btnSubmit, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        setButtonLoading(btnSubmit, false);
    });
}
</script>
