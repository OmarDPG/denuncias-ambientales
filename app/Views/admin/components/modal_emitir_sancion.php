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
                                Se elaborará un acta administrativa con sanción económica por las infracciones 
                                detectadas durante la inspección.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tipo de Sanción -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Tipo de Sanción <span class="text-error">*</span>
                    </label>
                    <select id="emitirSancionTipo" name="tipo_sancion" required
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                        <option value="">Seleccione un tipo...</option>
                        <option value="MULTA_ECONOMICA">Multa económica</option>
                        <option value="CLAUSURA_TEMPORAL">Clausura temporal</option>
                        <option value="CLAUSURA_DEFINITIVA">Clausura definitiva</option>
                        <option value="SUSPENSION_ACTIVIDADES">Suspensión de actividades</option>
                        <option value="REMEDIACION_AMBIENTAL">Remediación ambiental obligatoria</option>
                        <option value="SANCION_COMBINADA">Sanción combinada (multa + medidas)</option>
                    </select>
                </div>

                <!-- Monto de la Multa -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Monto de la Multa (UMA) <span class="text-error">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="emitirSancionMonto" name="monto_uma" required
                            min="1" step="0.01"
                            placeholder="0.00"
                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 pl-16 text-on-surface font-body text-lg font-bold" />
                        <span class="absolute left-0 top-3 text-secondary font-headline font-bold text-sm">UMA</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-xs text-secondary mt-2">
                        <div class="bg-surface-container-low p-3 rounded-lg">
                            <p class="font-bold">Valor UMA 2024:</p>
                            <p class="text-lg font-headline text-on-surface">$103.74</p>
                        </div>
                        <div class="bg-surface-container-low p-3 rounded-lg">
                            <p class="font-bold">Equivalente en pesos:</p>
                            <p id="montoEquivalente" class="text-lg font-headline text-on-surface">$0.00</p>
                        </div>
                    </div>
                </div>

                <!-- Fundamento Legal -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Fundamento Legal <span class="text-error">*</span>
                    </label>
                    <textarea id="emitirSancionFundamento" name="fundamento_legal" required
                        rows="3"
                        placeholder="Artículos, fracciones y leyes aplicables..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary">Ejemplo: Art. 171 LGEEPA, Art. 55 Ley Ambiental del Estado de Puebla</p>
                </div>

                <!-- Infracciones Cometidas -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Descripción de Infracciones <span class="text-error">*</span>
                    </label>
                    <textarea id="emitirSancionInfracciones" name="infracciones" required
                        rows="6"
                        placeholder="Describa detalladamente las infracciones cometidas y su gravedad..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                    <p class="text-xs text-secondary">Mínimo 100 caracteres</p>
                </div>

                <!-- Medidas Correctivas -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Medidas Correctivas <span class="text-secondary">(Opcional)</span>
                    </label>
                    <textarea id="emitirSancionMedidas" name="medidas_correctivas"
                        rows="4"
                        placeholder="Indique las medidas que debe implementar el sancionado para corregir la situación..."
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"></textarea>
                </div>

                <!-- Plazo para Cumplir -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Plazo para Cumplimiento <span class="text-secondary">(días hábiles)</span>
                    </label>
                    <input type="number" id="emitirSancionPlazo" name="plazo_dias"
                        min="1" max="180" value="30"
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body" />
                    <p class="text-xs text-secondary">Plazo en días hábiles para que el sancionado cumpla con las medidas correctivas</p>
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
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar monto
    const monto = parseFloat(document.getElementById('emitirSancionMonto').value);
    if (!monto || monto <= 0) {
        showToast('error', 'Monto inválido', 'El monto de la multa debe ser mayor a 0');
        return;
    }
    
    // Validar infracciones
    const infracciones = document.getElementById('emitirSancionInfracciones').value.trim();
    if (infracciones.length < 100) {
        showToast('error', 'Descripción insuficiente', 'Debe describir las infracciones con al menos 100 caracteres');
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
    const montoEquiv = (monto * 103.74).toFixed(2);
    if (!confirm(`¿Está seguro de emitir una sanción de ${monto} UMA ($${montoEquiv} MXN)?`)) {
        return;
    }
    
    // Mostrar loading
    setButtonLoading(btnSubmit, true);
    
    // Preparar FormData
    const formData = new FormData(form);
    
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

// Calcular equivalente en pesos al cambiar el monto UMA
document.addEventListener('DOMContentLoaded', function() {
    const montoInput = document.getElementById('emitirSancionMonto');
    if (montoInput) {
        montoInput.addEventListener('input', function() {
            const monto = parseFloat(this.value) || 0;
            const equivalente = monto * 103.74;
            document.getElementById('montoEquivalente').textContent = '$' + equivalente.toFixed(2);
        });
    }
});
</script>
