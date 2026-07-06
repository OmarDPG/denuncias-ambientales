<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- Modal: Emitir Sanción (DNS) -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<div id="emitirSancionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closeEmitirSancionModalOnOverlay(event)">
    <div class="bg-surface w-full max-w-3xl rounded-3xl shadow-2xl flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="flex-shrink-0 bg-gradient-to-br from-red-800 to-red-700 text-white rounded-t-3xl px-8 py-6 flex items-center justify-between">
            <div>
                <h2 class="font-headline font-bold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">gavel</span>
                    Emitir Acta Administrativa
                </h2>
                <p id="emitirSancionModalFolio" class="text-white/80 text-sm mt-1">Folio: #DA-XXXX</p>
            </div>
            <button onclick="closeEmitirSancionModal()" class="text-white/80 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6">
            <form id="emitirSancionForm" class="space-y-6">
                <input type="hidden" id="emitirSancionIdDenuncia" name="id_denuncia">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                <!-- Información -->
                <div class="bg-red-50 border-l-4 border-red-800 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-red-800 text-2xl">info</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-red-900 mb-1">Emisión de acta administrativa</p>
                            <p class="text-sm text-red-800">
                                Adjunte el acta administrativa de sanción firmada y sellada. Los detalles de la sanción 
                                deben estar contenidos en el documento.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Observaciones <span class="text-secondary">(Opcional)</span>
                    </label>
                    <textarea id="emitirSancionObservaciones" name="observaciones"
                        rows="4"
                        placeholder="Observaciones adicionales sobre el acta de sanción..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">lightbulb</span>
                        Puede incluir información adicional relevante sobre la sanción emitida
                    </p>
                </div>

                <!-- Acta Administrativa -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Acta Administrativa <span class="text-error">*</span>
                    </label>
                    <input type="file" id="emitirSancionActa" name="archivo" required
                        accept=".pdf"
                        class="w-full bg-surface-container-low border-2 border-dashed border-outline-variant/40 focus:border-primary rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-red-800 file:text-white file:font-bold file:text-xs hover:file:opacity-90"
                        data-preview="emitirSancionPreview" />
                    <div class="flex items-start gap-2 mt-2">
                        <span class="material-symbols-outlined text-secondary text-sm">info</span>
                        <p class="text-xs text-secondary">
                            <strong>Requerido:</strong> Adjunte el acta administrativa firmada y sellada en formato PDF. Máximo 15MB.
                        </p>
                    </div>
                    <div id="emitirSancionPreview" class="mt-3"></div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeEmitirSancionModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveEmitirSancion()" type="button" id="btnEmitirSancionSubmit"
                class="px-6 py-2 bg-gradient-to-br from-red-800 to-red-700 text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">gavel</span>
                Emitir Acta
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal Emitir Sanción
// ═══════════════════════════════════════════════════════════════════════════

let currentEmitirSancionDenunciaId = null;

function openEmitirSancionModal(idDenuncia) {
    currentEmitirSancionDenunciaId = idDenuncia;
    document.getElementById('emitirSancionIdDenuncia').value = idDenuncia;
    document.getElementById('emitirSancionModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    
    const modal = document.getElementById('emitirSancionModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('emitirSancionForm').reset();
    document.getElementById('emitirSancionPreview').innerHTML = '';
}

function closeEmitirSancionModal() {
    const modal = document.getElementById('emitirSancionModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentEmitirSancionDenunciaId = null;
}

function closeEmitirSancionModalOnOverlay(event) {
    if (event.target.id === 'emitirSancionModal') {
        closeEmitirSancionModal();
    }
}

function saveEmitirSancion() {
    const form = document.getElementById('emitirSancionForm');
    const btnSubmit = document.getElementById('btnEmitirSancionSubmit');
    
    // Validar formulario básico
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar archivo
    const archivo = document.getElementById('emitirSancionActa').files[0];
    if (!archivo) {
        showToast('error', 'Acta requerida', 'Debe adjuntar el acta administrativa');
        return;
    }
    
    const validacion = validarArchivo(archivo, 15728640, ['pdf']);
    if (!validacion.valid) {
        showToast('error', 'Archivo inválido', validacion.message);
        return;
    }
    
    // Confirmar acción
    if (!confirm('¿Está seguro de emitir el acta administrativa de sanción?')) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData manualmente
    const formData = new FormData();
    formData.append('id_denuncia', document.getElementById('emitirSancionIdDenuncia').value);
    
    // Observaciones (opcional)
    const observaciones = document.getElementById('emitirSancionObservaciones').value.trim();
    formData.append('observaciones', observaciones);
    
    // Valores por defecto para campos requeridos por el controlador
    formData.append('id_tipo_sancion', '1'); // Valor por defecto
    formData.append('monto_sancion', '0');    // Valor por defecto
    
    // Agregar CSRF token
    const csrfInput = form.querySelector('input[name^="csrf_"]');
    if (csrfInput) {
        formData.append(csrfInput.name, csrfInput.value);
    }
    
    // Agregar archivo
    formData.append('archivo', archivo);
    console.log('Archivo adjunto:', archivo.name, '(', archivo.size, 'bytes)');
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/emitirSancion") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Sanción emitida', data.message || 'Acta administrativa emitida correctamente');
            closeEmitirSancionModal();
            recargarPagina();
        } else {
            showToast('error', 'Error al emitir', data.message || 'No se pudo emitir la sanción');
            setButtonLoading(btnSubmit, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        setButtonLoading(btnSubmit, false);
    });
}

// Remover el event listener del monto UMA ya que ese campo fue eliminado
document.addEventListener('DOMContentLoaded', function() {
    // Ya no se necesita calcular equivalentes de UMA
});
</script>
