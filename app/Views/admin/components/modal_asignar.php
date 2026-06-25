<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Modal para Asignar Denuncia a Usuario                           -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Modal para asignar denuncias a usuarios específicos
 * 
 * Uso:
 * - Se activa con: openAsignarModal(idDenuncia, idAreaResponsable)
 * - Se cierra con: closeAsignarModal()
 * - Permite asignar responsable o tomar caso personalmente
 */
?>

<!-- Modal de Asignar Denuncia -->
<div id="asignarModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    onclick="closeAsignarModalOnOverlay(event)">
    <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col"
        onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div
            class="flex-shrink-0 bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="font-headline font-extrabold text-2xl">Asignar Caso</h2>
                <p class="text-sm text-primary-fixed opacity-90 mt-1" id="asignarModalFolio">Folio: —</p>
            </div>
            <button onclick="closeAsignarModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
            <!-- Opciones de Asignación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tomar Caso Personalmente -->
                <button onclick="tomarCasoPersonal()" type="button"
                    class="p-6 bg-primary/10 hover:bg-primary/20 border-2 border-primary rounded-xl transition-all group">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-primary text-3xl">person_check</span>
                        </div>
                        <div>
                            <h3 class="font-headline font-bold text-primary text-lg">Tomar Caso</h3>
                            <p class="text-xs text-secondary mt-1">Asignármelo a mí</p>
                        </div>
                    </div>
                </button>

                <!-- Asignar a Otro Usuario -->
                <button onclick="mostrarFormularioAsignacion()" type="button"
                    class="p-6 bg-surface-container hover:bg-surface-container-high border-2 border-outline-variant/30 rounded-xl transition-all group">
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="w-16 h-16 bg-secondary/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-secondary text-3xl">person_add</span>
                        </div>
                        <div>
                            <h3 class="font-headline font-bold text-primary text-lg">Asignar a Usuario</h3>
                            <p class="text-xs text-secondary mt-1">Seleccionar responsable</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Formulario de Asignación (Oculto por defecto) -->
            <div id="formularioAsignacion" class="hidden space-y-6 pt-4 border-t border-outline-variant/20">
                <form id="asignarForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="asignarIdDenuncia" name="id_denuncia" value="" />
                    <input type="hidden" id="asignarIdUsuario" name="id_usuario_asignado" value="" />
                    
                    <div class="space-y-2">
                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                            Seleccionar Responsable <span class="text-error">*</span>
                        </label>
                        <select id="usuarioAsignado" name="id_usuario_select" required
                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                            <option value="">Cargando usuarios...</option>
                        </select>
                        <p class="text-xs text-secondary mt-1">
                            Solo se muestran usuarios del mismo departamento.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                            Instrucciones para el Responsable
                        </label>
                        <textarea id="asignarInstrucciones" name="instrucciones"
                            class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body resize-none"
                            placeholder="Opcional: Indicaciones especiales para el responsable asignado..." rows="3"></textarea>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button onclick="ocultarFormularioAsignacion()" type="button"
                            class="flex-1 px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                            Volver
                        </button>
                        <button onclick="saveAsignacion()" type="button"
                            class="flex-1 px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">person_add</span>
                            Asignar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Información -->
            <div class="bg-secondary-container/20 rounded-lg p-4 flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-xl mt-0.5">info</span>
                <div class="text-xs text-secondary leading-relaxed">
                    <p class="font-bold mb-1">Nota:</p>
                    <p>La asignación quedará registrada en el historial. El responsable recibirá una notificación 
                    y el caso aparecerá en su bandeja de trabajo.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal de Asignar
// ═══════════════════════════════════════════════════════════════════════════

let currentAsignarDenunciaId = null;
let currentAsignarAreaId = null;

function openAsignarModal(idDenuncia, idAreaResponsable = null) {
    currentAsignarDenunciaId = idDenuncia;
    currentAsignarAreaId = idAreaResponsable;
    document.getElementById('asignarIdDenuncia').value = idDenuncia;
    document.getElementById('asignarModalFolio').textContent = 'Folio: #' + idDenuncia;
    
    const modal = document.getElementById('asignarModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Ocultar formulario
    ocultarFormularioAsignacion();
}

function closeAsignarModal() {
    const modal = document.getElementById('asignarModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentAsignarDenunciaId = null;
}

function closeAsignarModalOnOverlay(event) {
    if (event.target.id === 'asignarModal') {
        closeAsignarModal();
    }
}

function tomarCasoPersonal() {
    if (!currentAsignarDenunciaId) return;
    
    if (confirm('¿Está seguro de tomar este caso para usted?')) {
        // Mostrar loading
        const btnTomar = event.target.closest('button');
        const originalHTML = btnTomar.innerHTML;
        btnTomar.disabled = true;
        btnTomar.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span>';
        
        // Enviar petición para tomar caso
        const formData = new FormData();
        formData.append('id_denuncia', currentAsignarDenunciaId);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        fetch('<?= base_url("admin/tomarCaso") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('asignarToast', 'success', 'Caso tomado', data.message || 'Caso asignado exitosamente a usted');
                closeAsignarModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('asignarToast', 'error', 'Error al tomar caso', data.message || 'No se pudo asignar el caso');
                btnTomar.disabled = false;
                btnTomar.innerHTML = originalHTML;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('asignarToast', 'error', 'Error de conexión', 'No se pudo comunicar con el servidor');
            btnTomar.disabled = false;
            btnTomar.innerHTML = originalHTML;
        });
    }
}

function mostrarFormularioAsignacion() {
    document.getElementById('formularioAsignacion').classList.remove('hidden');
    cargarUsuariosDisponibles();
}

function ocultarFormularioAsignacion() {
    document.getElementById('formularioAsignacion').classList.add('hidden');
    document.getElementById('asignarForm').reset();
}

function cargarUsuariosDisponibles() {
    const select = document.getElementById('usuarioAsignado');
    select.innerHTML = '<option value="">Cargando usuarios...</option>';
    
    // Validar que tenemos el área de la denuncia
    if (!currentAsignarAreaId) {
        console.warn('No se proporcionó id_area_responsable. Usando área DNS (2) por defecto.');
        currentAsignarAreaId = 2; // Fallback a área DNS
    }
    
    // Cargar usuarios via AJAX
    fetch('<?= base_url("admin/obtenerUsuariosArea/") ?>' + currentAsignarAreaId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            select.innerHTML = '<option value="">Seleccione un usuario...</option>';
            data.data.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id_adm;
                option.textContent = `${usuario.nombre} ${usuario.apellidoP} ${usuario.apellidoM || ''}`.trim();
                select.appendChild(option);
            });
        } else {
            select.innerHTML = '<option value="">No hay usuarios disponibles</option>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        select.innerHTML = '<option value="">Error al cargar usuarios</option>';
    });
}

function saveAsignacion() {
    const form = document.getElementById('asignarForm');
    const selectUser = document.getElementById('usuarioAsignado');
    
    if (!selectUser.value) {
        showToast('asignarToast', 'error', 'Campo requerido', 'Debe seleccionar un responsable');
        return;
    }
    
    const btnSubmit = event.target;
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Asignando...';
    
    const formData = new FormData();
    formData.append('id_denuncia', currentAsignarDenunciaId);
    formData.append('id_usuario_destino', selectUser.value);
    formData.append('observaciones', document.getElementById('asignarInstrucciones').value);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    
    fetch('<?= base_url("admin/asignarCaso") ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('asignarToast', 'success', 'Asignación exitosa', data.message || 'Caso asignado correctamente');
            closeAsignarModal();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('asignarToast', 'error', 'Error al asignar', data.message || 'No se pudo asignar el caso');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm">person_add</span> Asignar';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('asignarToast', 'error', 'Error de conexión', 'No se pudo comunicar con el servidor');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<span class="material-symbols-outlined text-sm">person_add</span> Asignar';
    });
}

// Compartir funciones de toast si no están definidas globalmente
if (typeof showToast === 'undefined') {
    window.showToast = function(toastId, type, title, message) {
        // Crear toast si no existe
        let toast = document.getElementById(toastId);
        if (!toast) {
            toast = document.createElement('div');
            toast.id = toastId;
            toast.className = 'fixed top-4 right-4 z-[60] max-w-sm';
            toast.innerHTML = `
                <div id="${toastId}Content" class="bg-surface-container-highest rounded-lg shadow-2xl p-4 flex items-start gap-3">
                    <span id="${toastId}Icon" class="material-symbols-outlined text-2xl"></span>
                    <div class="flex-1">
                        <p id="${toastId}Title" class="font-headline font-bold text-sm"></p>
                        <p id="${toastId}Message" class="text-xs text-secondary mt-1"></p>
                    </div>
                    <button onclick="closeToast('${toastId}')" class="text-secondary hover:text-on-surface">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
        }
        
        const icon = document.getElementById(toastId + 'Icon');
        const titleEl = document.getElementById(toastId + 'Title');
        const messageEl = document.getElementById(toastId + 'Message');
        const content = document.getElementById(toastId + 'Content');
        
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
        
        toast.classList.remove('hidden');
        setTimeout(() => closeToast(toastId), 5000);
    };
    
    window.closeToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) toast.classList.add('hidden');
    };
}

// Cerrar con ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('asignarModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeAsignarModal();
        }
    }
});
</script>
