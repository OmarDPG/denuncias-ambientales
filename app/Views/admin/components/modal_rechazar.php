<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal: Rechazar Denuncia -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<div id="rechazarDenunciaModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeRechazarDenunciaModalOnOverlay(event)">
    <div class="bg-surface w-full max-w-2xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex-shrink-0 bg-gradient-to-br from-red-700 to-red-600 text-white rounded-t-3xl px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="font-headline font-bold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">cancel</span>
                    Rechazar Denuncia
                </h2>
                <p id="rechazarDenunciaModalFolio" class="text-white/80 text-sm mt-1">Folio: #DA-XXXX</p>
            </div>
            <button onclick="closeRechazarDenunciaModal()" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6">
            <form id="rechazarDenunciaForm" class="space-y-6">
                <input type="hidden" id="rechazarDenunciaIdDenuncia" name="id_denuncia">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Advertencia -->
                <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-600 text-2xl">warning</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-red-900 mb-1">Acción irreversible</p>
                            <p class="text-sm text-red-800">
                                Al rechazar esta denuncia, se marcará como <strong>no procedente</strong> y 
                                se notificará al denunciante sobre el motivo del rechazo.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Motivo del Rechazo -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Motivo del Rechazo <span class="text-error">*</span>
                    </label>
                    <select id="rechazarDenunciaMotivo" name="id_motivo_rechazo" required
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                        <option value="">Seleccione un motivo...</option>
                        <?php if (isset($motivosRechazo) && is_array($motivosRechazo)): ?>
                            <?php foreach ($motivosRechazo as $motivo): ?>
                                <option value="<?= esc($motivo['id_motivo']) ?>" 
                                    title="<?= esc($motivo['descripcion'] ?? '') ?>">
                                    <?= esc($motivo['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay motivos disponibles</option>
                        <?php endif; ?>
                    </select>
                    <p class="text-xs text-secondary">Seleccione el motivo legal que fundamenta el rechazo</p>
                </div>

                <!-- Fundamento Legal (Auto-carga según motivo seleccionado) -->
                <div class="space-y-2" id="rechazarDenunciaFundamentoContainer">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Fundamento Legal <span class="text-secondary">(Auto-llenado)</span>
                    </label>
                    <div id="rechazarDenunciaFundamentoDisplay" 
                        class="bg-surface-container-low border-l-4 border-primary p-3 rounded-lg text-sm text-on-surface">
                        <p class="text-secondary italic">Seleccione un motivo para ver el fundamento legal</p>
                    </div>
                </div>

                <!-- Descripción Detallada -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Observaciones Detalladas <span class="text-error">*</span>
                    </label>
                    <textarea id="rechazarDenunciaDescripcion" name="observaciones" required
                        rows="6"
                        placeholder="Explique detalladamente por qué se rechaza la denuncia. Esta información será enviada al denunciante..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary">Mínimo 50 caracteres. Debe justificar claramente la no competencia.</p>
                </div>

                <!-- Documento de Rechazo (OBLIGATORIO - Flujo B) -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Oficio de Rechazo <span class="text-error">* (OBLIGATORIO)</span>
                    </label>
                    <input type="file" id="rechazarDenunciaArchivo" name="archivo" required
                        accept=".pdf"
                        class="w-full bg-surface-container-low border-2 border-dashed border-error/60 focus:border-error rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-red-700 file:text-white file:font-bold file:text-xs hover:file:opacity-90"
                        data-preview="rechazarDenunciaPreview" />
                    <div class="bg-red-50 border-l-4 border-red-600 p-3 rounded">
                        <p class="text-xs text-red-900">
                            <strong>⚠️ OBLIGATORIO:</strong> Debe adjuntar el oficio oficial de rechazo firmado en formato PDF (máximo 10MB). 
                            El rechazo no procederá sin este documento.
                        </p>
                    </div>
                    <div id="rechazarDenunciaPreview" class="mt-3"></div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeRechazarDenunciaModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveRechazarDenuncia()" type="button" id="btnRechazarDenunciaSubmit"
                class="px-6 py-2 bg-gradient-to-br from-red-700 to-red-600 text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">cancel</span>
                Rechazar Denuncia
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal Rechazar Denuncia
// ═══════════════════════════════════════════════════════════════════════════

let currentRechazarDenunciaId = null;

function openRechazarDenunciaModal(idDenuncia) {
    currentRechazarDenunciaId = idDenuncia;
    document.getElementById('rechazarDenunciaIdDenuncia').value = idDenuncia;
    
    const modal = document.getElementById('rechazarDenunciaModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('rechazarDenunciaForm').reset();
    document.getElementById('rechazarDenunciaPreview').innerHTML = '';
    
    // Obtener folio real desde la base de datos
    document.getElementById('rechazarDenunciaModalFolio').textContent = 'Cargando...';
    
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
            document.getElementById('rechazarDenunciaModalFolio').textContent = 'Folio: ' + folio;
        } else {
            // Fallback al formato antiguo si falla
            document.getElementById('rechazarDenunciaModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
        }
    })
    .catch(error => {
        console.error('Error al obtener folio:', error);
        // Fallback al formato antiguo si hay error
        document.getElementById('rechazarDenunciaModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    });
}

function closeRechazarDenunciaModal() {
    const modal = document.getElementById('rechazarDenunciaModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentRechazarDenunciaId = null;
}

function closeRechazarDenunciaModalOnOverlay(event) {
    if (event.target.id === 'rechazarDenunciaModal') {
        closeRechazarDenunciaModal();
    }
}

function saveRechazarDenuncia() {
    const form = document.getElementById('rechazarDenunciaForm');
    const btnSubmit = document.getElementById('btnRechazarDenunciaSubmit');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar motivo
    const motivo = document.getElementById('rechazarDenunciaMotivo').value;
    if (!motivo) {
        showToast('error', 'Campo requerido', 'Debe seleccionar un motivo de rechazo');
        return;
    }
    
    // Validar descripción
    const descripcion = document.getElementById('rechazarDenunciaDescripcion').value.trim();
    if (descripcion.length < 50) {
        showToast('error', 'Descripción insuficiente', 'Debe proporcionar al menos 50 caracteres explicando el motivo del rechazo');
        return;
    }
    
    // Validar archivo OBLIGATORIO (Flujo B)
    const archivo = document.getElementById('rechazarDenunciaArchivo').files[0];
    if (!archivo) {
        showToast('error', 'Documento obligatorio', 'Debe adjuntar el oficio de rechazo en PDF. El rechazo no procederá sin este documento.');
        return;
    }
    
    // Validar que sea PDF
    if (archivo.type !== 'application/pdf') {
        showToast('error', 'Formato inválido', 'El documento debe ser formato PDF');
        return;
    }
    
    // Validar tamaño (10MB máximo)
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (archivo.size > maxSize) {
        showToast('error', 'Archivo muy grande', 'El archivo no debe exceder 10MB');
        return;
    }
    
    // Confirmar acción (doble confirmación por ser destructiva)
    if (!confirm('⚠️ ¿Está COMPLETAMENTE SEGURO de rechazar esta denuncia?\n\nEsta acción marcará el caso como NO COMPETENTE y NO puede deshacerse.')) {
        return;
    }
    
    if (!confirm('Confirme nuevamente: ¿Rechazar la denuncia ' + document.getElementById('rechazarDenunciaModalFolio').textContent + '?')) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData
    const formData = new FormData(form);
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/rechazarDenuncia") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Denuncia rechazada', data.message || 'Denuncia rechazada correctamente');
            closeRechazarDenunciaModal();
            recargarPagina();
        } else {
            showToast('error', 'Error al rechazar', data.message || 'No se pudo rechazar la denuncia');
            setButtonLoading(btnSubmit, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        setButtonLoading(btnSubmit, false);
    });
}

// Cargar fundamento legal al seleccionar motivo
document.addEventListener('DOMContentLoaded', function() {
    const motivoSelect = document.getElementById('rechazarDenunciaMotivo');
    const fundamentoDisplay = document.getElementById('rechazarDenunciaFundamentoDisplay');
    
    // Datos de fundamentos legales desde PHP
    const fundamentosLegales = {
        <?php if (isset($motivosRechazo) && is_array($motivosRechazo)): ?>
            <?php foreach ($motivosRechazo as $index => $motivo): ?>
                <?= $motivo['id_motivo'] ?>: {
                    nombre: <?= json_encode($motivo['nombre']) ?>,
                    fundamento: <?= json_encode($motivo['fundamento_legal'] ?? 'No especificado') ?>,
                    descripcion: <?= json_encode($motivo['descripcion'] ?? '') ?>
                }<?= $index < count($motivosRechazo) - 1 ? ',' : '' ?>
            <?php endforeach; ?>
        <?php endif; ?>
    };
    
    motivoSelect?.addEventListener('change', function() {
        const motivoId = parseInt(this.value);
        if (motivoId && fundamentosLegales[motivoId]) {
            const motivo = fundamentosLegales[motivoId];
            fundamentoDisplay.innerHTML = `
                <div class="space-y-2">
                    <p class="font-headline font-bold text-primary">${motivo.nombre}</p>
                    <p class="text-sm"><strong>Fundamento Legal:</strong> ${motivo.fundamento}</p>
                    ${motivo.descripcion ? `<p class="text-xs text-secondary">${motivo.descripcion}</p>` : ''}
                </div>
            `;
        } else {
            fundamentoDisplay.innerHTML = '<p class="text-secondary italic">Seleccione un motivo para ver el fundamento legal</p>';
        }
    });
});
</script>
