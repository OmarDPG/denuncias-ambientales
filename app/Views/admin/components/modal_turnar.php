<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Modal para Turnar Denuncia                                      -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Modal para turnar denuncias entre departamentos
 * 
 * Uso:
 * - Se activa con: openTurnarModal(idDenuncia)
 * - Se cierra con: closeTurnarModal()
 * - Permite seleccionar área destino y subir oficio de turnado
 */
?>

<!-- Modal de Turnar Denuncia -->
<div id="turnarModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    onclick="closeTurnarModalOnOverlay(event)">
    <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col"
        onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div
            class="flex-shrink-0 bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="font-headline font-extrabold text-2xl">Turnar Denuncia</h2>
                <p class="text-sm text-primary-fixed opacity-90 mt-1" id="turnarModalFolio">Folio: —</p>
            </div>
            <button onclick="closeTurnarModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
            <!-- Formulario de Turnado -->
            <form id="turnarForm" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" id="turnarIdDenuncia" name="id_denuncia" value="" />
                <input type="hidden" id="turnarEstadoActual" name="estado_actual" value="" />
                
                <!-- Área Destino -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Turnar a Departamento <span class="text-error">*</span>
                    </label>
                    <select id="areaDestino" name="id_area_destino" required
                        onchange="validarFlujoExcepcional()"
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                        <option value="">Seleccione un departamento...</option>
                        <option value="2">Departamento de Normativa y Sanciones (DNS)</option>
                        <option value="1">Departamento de Supervisión (DS)</option>
                    </select>
                    <p class="text-xs text-secondary mt-1">
                        Seleccione el departamento al que se turnará la denuncia para su atención.
                    </p>
                </div>
                
                <!-- Advertencia de Flujo Excepcional (Flujo A) -->
                <div id="advertenciaFlujoExcepcional" class="hidden bg-yellow-50 border-l-4 border-yellow-600 p-4 rounded-lg">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-yellow-600 text-2xl">warning</span>
                        <div>
                            <p class="font-headline font-bold text-sm text-yellow-900 mb-1">⚠️ TURNADO DIRECTO EXCEPCIONAL (Flujo A)</p>
                            <p class="text-sm text-yellow-800">
                                Está turnando directamente a <strong>Supervisión sin pasar por Normativa y Sanciones</strong>. 
                                Este es un flujo excepcional que omite la revisión normativa estándar.
                            </p>
                            <p class="text-xs text-yellow-700 mt-2 font-bold">
                                DEBE proporcionar justificación detallada (mínimo 50 caracteres) y documento soporte OBLIGATORIO.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Justificación de Flujo Excepcional -->
                <div id="campoJustificacionExcepcional" class="hidden space-y-2">
                    <label class="font-headline font-bold text-sm text-error uppercase tracking-wider">
                        Justificación del Flujo Excepcional <span class="text-error">* (OBLIGATORIO)</span>
                    </label>
                    <textarea id="razonFlujoExcepcional" name="razon_flujo_excepcional"
                        class="w-full bg-yellow-50 border-2 border-yellow-600 focus:border-error focus:ring-0 py-3 px-4 text-on-surface font-body rounded-lg resize-none"
                        placeholder="Explique detalladamente por qué esta denuncia debe turnarse directamente a Supervisión omitiendo la revisión de Normativa y Sanciones. Incluya fundamento legal, urgencia, riesgo inminente, o cualquier circunstancia excepcional..."
                        rows="6"></textarea>
                    <p class="text-xs text-error font-bold">
                        Mínimo 50 caracteres. Esta justificación será registrada en el expediente y auditoría.
                    </p>
                </div>

                <!-- Observaciones -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        Observaciones / Instrucciones <span class="text-error">*</span>
                    </label>
                    <textarea id="turnarObservaciones" name="observaciones" required
                        class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body resize-none"
                        placeholder="Indique las instrucciones o comentarios para el departamento..." rows="4"></textarea>
                </div>

                <!-- Oficio de Turnado -->
                <div class="space-y-2">
                    <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                        <span id="labelOficio">Oficio de Turnado</span> 
                        <span id="oficioOpcional" class="text-secondary">(Opcional)</span>
                        <span id="oficioObligatorio" class="hidden text-error">* (OBLIGATORIO - Flujo Excepcional)</span>
                    </label>
                    <div class="relative">
                        <input type="file" id="oficioTurnado" name="archivo"
                            accept=".pdf,.doc,.docx"
                            class="w-full bg-surface-container-low border-2 border-dashed border-outline-variant/40 focus:border-primary rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-bold file:text-xs hover:file:opacity-90 transition-all" />
                    </div>
                    <div class="flex items-start gap-2 mt-2">
                        <span class="material-symbols-outlined text-secondary text-sm">info</span>
                        <p class="text-xs text-secondary" id="oficioInfoNormal">
                            Adjunte el oficio oficial de turnado (PDF, DOC, DOCX). Máximo 10MB.
                        </p>
                        <p class="text-xs text-error font-bold hidden" id="oficioInfoExcepcional">
                            OBLIGATORIO para turnado directo. Debe adjuntar documento que justifique el flujo excepcional.
                        </p>
                    </div>
                    <div id="oficioPreview" class="mt-3"></div>
                </div>

                <!-- Información -->
                <div class="bg-secondary-container/20 rounded-lg p-4 flex items-start gap-3">
                    <span class="material-symbols-outlined text-secondary text-xl mt-0.5">info</span>
                    <div class="text-xs text-secondary leading-relaxed">
                        <p class="font-bold mb-1">Nota Importante:</p>
                        <p>El turnado quedará registrado en el historial. El departamento receptor recibirá una notificación 
                        y podrá visualizar el oficio adjunto.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeTurnarModal()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cancelar
            </button>
            <button onclick="saveTurnado()" type="button" id="btnTurnarSubmit"
                class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">send</span>
                Turnar Denuncia
            </button>
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div id="turnarToast" class="hidden fixed top-4 right-4 z-[60] max-w-sm">
    <div id="turnarToastContent" class="bg-surface-container-highest text-on-surface rounded-lg shadow-2xl p-4 flex items-start gap-3">
        <span id="turnarToastIcon" class="material-symbols-outlined text-2xl"></span>
        <div class="flex-1">
            <p id="turnarToastTitle" class="font-headline font-bold text-sm"></p>
            <p id="turnarToastMessage" class="text-xs text-secondary mt-1"></p>
        </div>
        <button onclick="closeToast('turnarToast')" class="text-secondary hover:text-on-surface">
            <span class="material-symbols-outlined text-lg">close</span>
        </button>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal de Turnar
// ═══════════════════════════════════════════════════════════════════════════

let currentTurnarDenunciaId = null;
let currentEstadoDenuncia = null;

function openTurnarModal(idDenuncia, estadoActual) {
    currentTurnarDenunciaId = idDenuncia;
    currentEstadoDenuncia = estadoActual || null;
    
    document.getElementById('turnarIdDenuncia').value = idDenuncia;
    document.getElementById('turnarEstadoActual').value = estadoActual || '';
    document.getElementById('turnarModalFolio').textContent = 'Folio: #DA-' + String(idDenuncia).padStart(4, '0');
    
    const modal = document.getElementById('turnarModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Limpiar formulario
    document.getElementById('turnarForm').reset();
    document.getElementById('oficioPreview').innerHTML = '';
    
    // Ocultar advertencia de flujo excepcional
    document.getElementById('advertenciaFlujoExcepcional').classList.add('hidden');
    document.getElementById('campoJustificacionExcepcional').classList.add('hidden');
    document.getElementById('oficioOpcional').classList.remove('hidden');
    document.getElementById('oficioObligatorio').classList.add('hidden');
    document.getElementById('oficioInfoNormal').classList.remove('hidden');
    document.getElementById('oficioInfoExcepcional').classList.add('hidden');
}

// Validar si es flujo excepcional (RECIBIDA → TURNADA_DS)
function validarFlujoExcepcional() {
    const areaDestino = document.getElementById('areaDestino').value;
    const estadoActual = document.getElementById('turnarEstadoActual').value;
    
    // Flujo A: RECIBIDA (estado 1) → TURNADA_DS (área 1)
    const esFlujoExcepcional = (estadoActual === '1' && areaDestino === '1');
    
    // Mostrar/ocultar advertencia y campo de justificación
    const advertencia = document.getElementById('advertenciaFlujoExcepcional');
    const campoJustificacion = document.getElementById('campoJustificacionExcepcional');
    const oficioOpcional = document.getElementById('oficioOpcional');
    const oficioObligatorio = document.getElementById('oficioObligatorio');
    const oficioInfoNormal = document.getElementById('oficioInfoNormal');
    const oficioInfoExcepcional = document.getElementById('oficioInfoExcepcional');
    const razonInput = document.getElementById('razonFlujoExcepcional');
    
    if (esFlujoExcepcional) {
        advertencia.classList.remove('hidden');
        campoJustificacion.classList.remove('hidden');
        oficioOpcional.classList.add('hidden');
        oficioObligatorio.classList.remove('hidden');
        oficioInfoNormal.classList.add('hidden');
        oficioInfoExcepcional.classList.remove('hidden');
        razonInput.required = true;
    } else {
        advertencia.classList.add('hidden');
        campoJustificacion.classList.add('hidden');
        oficioOpcional.classList.remove('hidden');
        oficioObligatorio.classList.add('hidden');
        oficioInfoNormal.classList.remove('hidden');
        oficioInfoExcepcional.classList.add('hidden');
        razonInput.required = false;
        razonInput.value = '';
    }
}

function closeTurnarModal() {
    const modal = document.getElementById('turnarModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentTurnarDenunciaId = null;
}

function closeTurnarModalOnOverlay(event) {
    if (event.target.id === 'turnarModal') {
        closeTurnarModal();
    }
}

function saveTurnado() {
    const form = document.getElementById('turnarForm');
    const btnSubmit = document.getElementById('btnTurnarSubmit');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Validar área destino
    const areaDestino = document.getElementById('areaDestino').value;
    if (!areaDestino) {
        showToast('turnarToast', 'error', 'Campo requerido', 'Debe seleccionar un departamento destino');
        return;
    }
    
    // Validar observaciones
    const observaciones = document.getElementById('turnarObservaciones').value.trim();
    if (observaciones.length < 10) {
        showToast('turnarToast', 'error', 'Observaciones insuficientes', 'Debe proporcionar al menos 10 caracteres de instrucciones');
        return;
    }
    
    // Validar flujo excepcional (Flujo A)
    const estadoActual = document.getElementById('turnarEstadoActual').value;
    const esFlujoExcepcional = (estadoActual === '1' && areaDestino === '1');
    
    if (esFlujoExcepcional) {
        // Validar justificación obligatoria
        const razon = document.getElementById('razonFlujoExcepcional').value.trim();
        if (!razon || razon.length < 50) {
            showToast('turnarToast', 'error', 'Justificación insuficiente', 
                'El turnado directo excepcional requiere justificación de al menos 50 caracteres.');
            return;
        }
        
        // Validar documento obligatorio
        const archivo = document.getElementById('oficioTurnado').files[0];
        if (!archivo) {
            showToast('turnarToast', 'error', 'Documento obligatorio', 
                'El turnado directo excepcional requiere documento de justificación. No puede proceder sin este documento.');
            return;
        }
        
        // Confirmación adicional para flujo excepcional
        if (!confirm('⚠️ FLUJO EXCEPCIONAL ⚠️\n\n' +
            'Está a punto de turnar DIRECTAMENTE a Supervisión omitiendo Normativa y Sanciones.\n\n' +
            '¿Confirma que esta acción está justificada y cuenta con la autorización correspondiente?')) {
            return;
        }
    }
    
    // Deshabilitar botón y mostrar loading
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Turnando...';
    
    // Preparar FormData
    const formData = new FormData(form);
    
    // Enviar via AJAX
    fetch('<?= base_url("admin/turnarDenuncia") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('turnarToast', 'success', 'Turnado exitoso', data.message || 'Denuncia turnada correctamente');
            closeTurnarModal();
            
            // Recargar página después de 1.5 segundos
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('turnarToast', 'error', 'Error al turnar', data.message || 'No se pudo turnar la denuncia');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm">send</span> Turnar Denuncia';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('turnarToast', 'error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm">send</span> Turnar Denuncia';
    });
}

// Preview de archivo
document.addEventListener('DOMContentLoaded', function() {
    const oficioInput = document.getElementById('oficioTurnado');
    if (oficioInput) {
        oficioInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('oficioPreview');
            
            if (file) {
                // Validar tamaño (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    showToast('turnarToast', 'error', 'Archivo muy grande', 'El tamaño máximo es 10MB');
                    this.value = '';
                    preview.innerHTML = '';
                    return;
                }
                
                const icon = file.type.includes('pdf') ? 'picture_as_pdf' : 'description';
                preview.innerHTML = 
                    '<div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-lg">' +
                    '<span class="material-symbols-outlined text-error">' + icon + '</span>' +
                    '<div class="flex-1"><p class="text-sm font-medium text-on-surface">' + file.name + '</p>' +
                    '<p class="text-xs text-secondary">' + (file.size / 1024 / 1024).toFixed(2) + ' MB</p></div>' +
                    '<button type="button" onclick="clearFileInput(\'oficioTurnado\', \'oficioPreview\')" class="text-secondary hover:text-error">' +
                    '<span class="material-symbols-outlined text-sm">close</span></button>' +
                    '</div>';
            } else {
                preview.innerHTML = '';
            }
        });
    }
});

function clearFileInput(inputId, previewId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).innerHTML = '';
}

function showToast(toastId, type, title, message) {
    const toast = document.getElementById(toastId);
    const icon = document.getElementById(toastId.replace('Toast', 'ToastIcon'));
    const titleEl = document.getElementById(toastId.replace('Toast', 'ToastTitle'));
    const messageEl = document.getElementById(toastId.replace('Toast', 'ToastMessage'));
    const content = document.getElementById(toastId + 'Content');
    
    // Configurar icono y colores según tipo
    const config = {
        success: { icon: 'check_circle', bg: 'bg-green-100', text: 'text-green-800', iconColor: 'text-green-600' },
        error: { icon: 'error', bg: 'bg-red-100', text: 'text-red-800', iconColor: 'text-red-600' },
        warning: { icon: 'warning', bg: 'bg-yellow-100', text: 'text-yellow-800', iconColor: 'text-yellow-600' },
        info: { icon: 'info', bg: 'bg-blue-100', text: 'text-blue-800', iconColor: 'text-blue-600' }
    };
    
    const conf = config[type] || config.info;
    
    icon.textContent = conf.icon;
    icon.className = 'material-symbols-outlined text-2xl ' + conf.iconColor;
    titleEl.textContent = title;
    messageEl.textContent = message;
    content.className = 'rounded-lg shadow-2xl p-4 flex items-start gap-3 ' + conf.bg + ' ' + conf.text;
    
    // Mostrar toast
    toast.classList.remove('hidden');
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        closeToast(toastId);
    }, 5000);
}

function closeToast(toastId) {
    document.getElementById(toastId).classList.add('hidden');
}

// Cerrar con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('turnarModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeTurnarModal();
        }
    }
});
</script>
