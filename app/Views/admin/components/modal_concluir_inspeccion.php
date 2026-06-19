<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal: Concluir Inspección (DS) -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<div id="concluirInspeccionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeConcluirInspeccionModalOnOverlay(event)">
    <div class="bg-surface w-full max-w-3xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex-shrink-0 bg-gradient-to-br from-teal-700 to-teal-600 text-white rounded-t-3xl px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="font-headline font-bold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">task_alt</span>
                    Concluir Inspección
                </h2>
                <p id="concluirInspeccionModalFolio" class="text-white/80 text-sm mt-1">Folio: #DA-XXXX</p>
            </div>
            <button onclick="closeConcluirInspeccionModal()" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6">
            <form id="concluirInspeccionForm" class="space-y-6">
                <input type="hidden" id="concluirInspeccionIdDenuncia" name="id_denuncia">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Información -->
                <div class="bg-teal-50 border-l-4 border-teal-600 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-teal-600 text-2xl">info</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-teal-900 mb-1">Cierre de inspección</p>
                            <p class="text-sm text-teal-800">
                                Al concluir la inspección, el caso regresará al DNS para determinar si procede la 
                                elaboración de un acta administrativa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resultado de la Inspección -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Resultado de la Inspección <span class="text-error">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 bg-surface-container-low rounded-lg cursor-pointer border-2 border-transparent hover:border-primary transition-all">
                            <input type="radio" name="resultado_inspeccion" value="INFRACCION_DETECTADA" required
                                class="peer sr-only">
                            <div class="peer-checked:border-primary absolute inset-0 border-2 rounded-lg"></div>
                            <div class="relative flex items-center gap-3">
                                <span class="material-symbols-outlined text-red-600 text-2xl">report_problem</span>
                                <div>
                                    <p class="font-headline font-bold text-sm text-on-surface">Infracción Detectada</p>
                                    <p class="text-xs text-secondary mt-1">Se encontraron violaciones a la normatividad</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 bg-surface-container-low rounded-lg cursor-pointer border-2 border-transparent hover:border-primary transition-all">
                            <input type="radio" name="resultado_inspeccion" value="SIN_INFRACCIONES" required
                                class="peer sr-only">
                            <div class="peer-checked:border-primary absolute inset-0 border-2 rounded-lg"></div>
                            <div class="relative flex items-center gap-3">
                                <span class="material-symbols-outlined text-green-600 text-2xl">verified</span>
                                <div>
                                    <p class="font-headline font-bold text-sm text-on-surface">Sin Infracciones</p>
                                    <p class="text-xs text-secondary mt-1">No se detectaron incumplimientos</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Hallazgos de la Inspección -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Hallazgos de la Inspección <span class="text-error">*</span>
                    </label>
                    <textarea id="concluirInspeccionHallazgos" name="hallazgos" required
                        rows="6"
                        placeholder="Describa detalladamente los hallazgos encontrados durante la inspección: condiciones observadas, mediciones realizadas, evidencias recopiladas, etc..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary">Mínimo 100 caracteres</p>
                </div>

                <!-- Recomendaciones -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Recomendaciones para DNS <span class="text-error">*</span>
                    </label>
                    <textarea id="concluirInspeccionRecomendaciones" name="recomendaciones" required
                        rows="4"
                        placeholder="Indique las recomendaciones para el DNS respecto a las acciones a seguir..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                </div>

                <!-- Acta de Inspección -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Acta de Inspección <span class="text-error">*</span>
                    </label>
                    <input type="file" id="concluirInspeccionActa" name="archivo" required
                        accept=".pdf"
                        class="w-full bg-surface-container-low border-2 border-dashed border-outline-variant/40 focus:border-primary rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-teal-700 file:text-white file:font-bold file:text-xs hover:file:opacity-90"
                        data-preview="concluirInspeccionPreview" />
                    <div class="flex items-start gap-2 mt-2">
                        <span class="material-symbols-outlined text-secondary text-sm">info</span>
                        <p class="text-xs text-secondary">
                            <strong>Requerido:</strong> Adjunte el acta circunstanciada de inspección firmada y sellada en formato PDF. Máximo 15MB.
                        </p>
                    </div>
                    <div id="concluirInspeccionPreview" class="mt-3"></div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeConcluirInspeccionModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveConcluirInspeccion()" type="button" id="btnConcluirInspeccionSubmit"
                class="px-6 py-2 bg-gradient-to-br from-teal-700 to-teal-600 text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">task_alt</span>
                Concluir Inspección
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal Concluir Inspección
// ═══════════════════════════════════════════════════════════════════════════

let currentConcluirInspeccionDenunciaId = null;

function openConcluirInspeccionModal(idDenuncia) {
    currentConcluirInspeccionDenunciaId = idDenuncia;
    document.getElementById('concluirInspeccionIdDenuncia').value = idDenuncia;
    
    const modal = document.getElementById('concluirInspeccionModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('concluirInspeccionForm').reset();
    document.getElementById('concluirInspeccionPreview').innerHTML = '';
    
    // Obtener folio real desde la base de datos
    document.getElementById('concluirInspeccionModalFolio').textContent = 'Cargando...';
    
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
            document.getElementById('concluirInspeccionModalFolio').textContent = 'Folio: ' + folio;
        } else {
            // Fallback al formato antiguo si falla
            document.getElementById('concluirInspeccionModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
        }
    })
    .catch(error => {
        console.error('Error al obtener folio:', error);
        // Fallback al formato antiguo si hay error
        document.getElementById('concluirInspeccionModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    });
}

function closeConcluirInspeccionModal() {
    const modal = document.getElementById('concluirInspeccionModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentConcluirInspeccionDenunciaId = null;
}

function closeConcluirInspeccionModalOnOverlay(event) {
    if (event.target.id === 'concluirInspeccionModal') {
        closeConcluirInspeccionModal();
    }
}

function saveConcluirInspeccion() {
    const form = document.getElementById('concluirInspeccionForm');
    const btnSubmit = document.getElementById('btnConcluirInspeccionSubmit');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar resultado
    const resultado = document.querySelector('input[name="resultado_inspeccion"]:checked');
    if (!resultado) {
        showToast('error', 'Campo requerido', 'Debe seleccionar el resultado de la inspección');
        return;
    }
    
    // Validar hallazgos
    const hallazgos = document.getElementById('concluirInspeccionHallazgos').value.trim();
    if (hallazgos.length < 100) {
        showToast('error', 'Hallazgos insuficientes', 'Debe proporcionar al menos 100 caracteres describiendo los hallazgos');
        return;
    }
    
    // Validar archivo
    const archivo = document.getElementById('concluirInspeccionActa').files[0];
    if (!archivo) {
        showToast('error', 'Acta requerida', 'Debe adjuntar el acta de inspección');
        return;
    }
    
    const validacion = validarArchivo(archivo, 15728640, ['pdf']);
    if (!validacion.valid) {
        showToast('error', 'Archivo inválido', validacion.message);
        return;
    }
    
    // Confirmar acción
    if (!confirm('¿Está seguro de concluir la inspección y regresar el caso al DNS?')) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData
    const formData = new FormData(form);
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/concluirInspeccion") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Inspección concluida', data.message || 'Inspección concluida y enviada al DNS correctamente');
            closeConcluirInspeccionModal();
            recargarPagina();
        } else {
            showToast('error', 'Error al concluir', data.message || 'No se pudo concluir la inspección');
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
