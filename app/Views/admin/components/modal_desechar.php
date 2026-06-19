<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal: Desechar Denuncia (Flujo D - Solo Administradores) -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<div id="desecharDenunciaModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeDesecharDenunciaModalOnOverlay(event)">
    <div class="bg-surface w-full max-w-2xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex-shrink-0 bg-gradient-to-br from-yellow-700 to-yellow-600 text-white rounded-t-3xl px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="font-headline font-bold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">delete_forever</span>
                    Desechar Denuncia
                </h2>
                <p id="desecharDenunciaModalFolio" class="text-white/80 text-sm mt-1">Folio: #DA-XXXX</p>
                <p class="text-white/90 text-xs mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                    Acción exclusiva de administradores
                </p>
            </div>
            <button onclick="closeDesecharDenunciaModal()" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6">
            <form id="desecharDenunciaForm" class="space-y-6">
                <input type="hidden" id="desecharDenunciaIdDenuncia" name="id_denuncia">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Advertencia Crítica -->
                <div class="bg-yellow-50 border-l-4 border-yellow-600 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 text-2xl">warning</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-yellow-900 mb-1">⚠️ Acción Administrativa Crítica</p>
                            <p class="text-sm text-yellow-800">
                                El desechamiento elimina la denuncia del flujo operativo de manera <strong>PERMANENTE</strong>. 
                                Esta acción solo debe usarse en casos excepcionales que no puedan seguir el flujo normal.
                            </p>
                            <p class="text-xs text-yellow-700 mt-2 font-bold">
                                Esta acción NO puede deshacerse y quedará registrada en el historial de auditoría.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Motivo del Desechamiento -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Motivo del Desechamiento <span class="text-error">*</span>
                    </label>
                    <select id="desecharDenunciaMotivo" name="motivo_desechamiento" required
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                        <option value="">Seleccione un motivo...</option>
                        <option value="DENUNCIA_ANONIMA_SIN_PRUEBAS">Denuncia anónima sin elementos probatorios</option>
                        <option value="INFORMACION_INSUFICIENTE">Información insuficiente para proceder</option>
                        <option value="DUPLICADO">Denuncia duplicada o ya atendida</option>
                        <option value="FUERA_COMPETENCIA">Fuera de competencia municipal</option>
                        <option value="NO_IDENTIFICA_INFRACTOR">No identifica al presunto infractor</option>
                        <option value="HECHO_NO_VERIFICABLE">Hecho no verificable o inexistente</option>
                        <option value="PRESCRIPCION">Prescripción del derecho a denunciar</option>
                        <option value="SOLICITUD_DENUNCIANTE">A solicitud del denunciante</option>
                        <option value="ARCHIVO_SIN_SEGUIMIENTO">Archivo sin seguimiento (inactiva > 6 meses)</option>
                        <option value="OTRO">Otro motivo administrativo</option>
                    </select>
                    <p class="text-xs text-secondary">Seleccione el motivo administrativo o legal que justifica el desechamiento</p>
                </div>

                <!-- Justificación Detallada -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Justificación Detallada <span class="text-error">*</span>
                    </label>
                    <textarea id="desecharDenunciaJustificacion" name="justificacion" required
                        rows="8"
                        placeholder="Explique detalladamente por qué se desecha la denuncia. Incluya fundamentos legales, administrativos o técnicos que soporten esta decisión. Esta justificación será parte del expediente permanente..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary">Mínimo 50 caracteres. Debe fundamentar claramente la decisión administrativa.</p>
                </div>

                <!-- Documento de Desechamiento (OBLIGATORIO) -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Oficio de Desechamiento <span class="text-error">* (OBLIGATORIO)</span>
                    </label>
                    <input type="file" id="desecharDenunciaArchivo" name="archivo" required
                        accept=".pdf"
                        class="w-full bg-surface-container-low border-2 border-dashed border-error/60 focus:border-error rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-yellow-700 file:text-white file:font-bold file:text-xs hover:file:opacity-90"
                        data-preview="desecharDenunciaPreview" />
                    <div class="bg-red-50 border-l-4 border-red-600 p-3 rounded">
                        <p class="text-xs text-red-900">
                            <strong>⚠️ DOCUMENTO OBLIGATORIO:</strong> Debe adjuntar el oficio administrativo firmado que autoriza 
                            el desechamiento en formato PDF (máximo 10MB). El desechamiento no procederá sin este documento.
                        </p>
                    </div>
                    <div id="desecharDenunciaPreview" class="mt-3"></div>
                </div>

                <!-- Información Legal -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-lg">info</span>
                        <div class="text-xs text-blue-900">
                            <p class="font-bold mb-1">Registro de Auditoría</p>
                            <p>Esta acción se registrará en el historial con: usuario, fecha, hora, motivo, justificación y documento asociado.</p>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeDesecharDenunciaModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveDesecharDenuncia()" type="button" id="btnDesecharDenunciaSubmit"
                class="px-6 py-2 bg-gradient-to-br from-yellow-700 to-yellow-600 text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">delete_forever</span>
                Desechar Denuncia
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal Desechar Denuncia
// ═══════════════════════════════════════════════════════════════════════════

let currentDesecharDenunciaId = null;

function openDesecharDenunciaModal(idDenuncia) {
    currentDesecharDenunciaId = idDenuncia;
    document.getElementById('desecharDenunciaIdDenuncia').value = idDenuncia;
    document.getElementById('desecharDenunciaModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    
    const modal = document.getElementById('desecharDenunciaModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('desecharDenunciaForm').reset();
    document.getElementById('desecharDenunciaPreview').innerHTML = '';
}

function closeDesecharDenunciaModal() {
    const modal = document.getElementById('desecharDenunciaModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentDesecharDenunciaId = null;
}

function closeDesecharDenunciaModalOnOverlay(event) {
    if (event.target.id === 'desecharDenunciaModal') {
        closeDesecharDenunciaModal();
    }
}

function saveDesecharDenuncia() {
    const form = document.getElementById('desecharDenunciaForm');
    const btnSubmit = document.getElementById('btnDesecharDenunciaSubmit');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar motivo
    const motivo = document.getElementById('desecharDenunciaMotivo').value;
    if (!motivo) {
        showToast('error', 'Campo requerido', 'Debe seleccionar un motivo de desechamiento');
        return;
    }
    
    // Validar justificación
    const justificacion = document.getElementById('desecharDenunciaJustificacion').value.trim();
    if (justificacion.length < 50) {
        showToast('error', 'Justificación insuficiente', 'Debe proporcionar al menos 50 caracteres fundamentando el desechamiento');
        return;
    }
    
    // Validar archivo OBLIGATORIO
    const archivo = document.getElementById('desecharDenunciaArchivo').files[0];
    if (!archivo) {
        showToast('error', 'Documento obligatorio', 'Debe adjuntar el oficio de desechamiento en PDF. El desechamiento no procederá sin este documento.');
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
    
    // Confirmar acción (triple confirmación por ser acción crítica administrativa)
    if (!confirm('⚠️⚠️⚠️ ACCIÓN CRÍTICA ⚠️⚠️⚠️\n\n¿Está COMPLETAMENTE SEGURO de DESECHAR esta denuncia?\n\nEsta acción:\n• Es PERMANENTE e IRREVERSIBLE\n• Elimina el caso del flujo operativo\n• Requiere autorización administrativa\n• Quedará registrada en auditoría\n\n¿Desea continuar?')) {
        return;
    }
    
    if (!confirm('Segunda confirmación:\n\n¿DESECHAR la denuncia ' + document.getElementById('desecharDenunciaModalFolio').textContent + '?\n\nMotivo: ' + document.getElementById('desecharDenunciaMotivo').options[document.getElementById('desecharDenunciaMotivo').selectedIndex].text)) {
        return;
    }
    
    if (!confirm('CONFIRMACIÓN FINAL:\n\nEsta es su última oportunidad para cancelar.\n¿Proceder con el desechamiento?')) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData
    const formData = new FormData(form);
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/desecharDenuncia") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Denuncia desechada', data.message || 'Denuncia desechada correctamente');
            closeDesecharDenunciaModal();
            recargarPagina();
        } else {
            showToast('error', 'Error al desechar', data.message || 'No se pudo desechar la denuncia');
            setButtonLoading(btnSubmit, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        setButtonLoading(btnSubmit, false);
    });
}

// Preview de archivo
document.addEventListener('DOMContentLoaded', function() {
    const archivoInput = document.getElementById('desecharDenunciaArchivo');
    const preview = document.getElementById('desecharDenunciaPreview');
    
    archivoInput?.addEventListener('change', function() {
        const archivo = this.files[0];
        if (archivo) {
            preview.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-green-600">description</span>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-green-900">${archivo.name}</p>
                        <p class="text-xs text-green-700">${(archivo.size / 1024).toFixed(2)} KB</p>
                    </div>
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                </div>
            `;
        } else {
            preview.innerHTML = '';
        }
    });
});
</script>
