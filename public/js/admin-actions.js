/**
 * Sistema de Denuncias Ambientales - Admin Actions
 * Funciones globales para acciones administrativas
 * Compatible con: JavaScript ES6+, PHP 7.4+, CodeIgniter 4
 */

// ═══════════════════════════════════════════════════════════════════════════
// Sistema de Notificaciones Toast
// ═══════════════════════════════════════════════════════════════════════════

function showToast(type, title, message) {
    // Crear toast si no existe
    let toastContainer = document.getElementById('globalToastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'globalToastContainer';
        toastContainer.className = 'fixed top-4 right-4 z-[70] space-y-2';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const config = {
        success: { icon: 'check_circle', bg: 'bg-green-100', text: 'text-green-800', iconColor: 'text-green-600' },
        error: { icon: 'error', bg: 'bg-red-100', text: 'text-red-800', iconColor: 'text-red-600' },
        warning: { icon: 'warning', bg: 'bg-yellow-100', text: 'text-yellow-800', iconColor: 'text-yellow-600' },
        info: { icon: 'info', bg: 'bg-blue-100', text: 'text-blue-800', iconColor: 'text-blue-600' }
    };
    
    const conf = config[type] || config.info;
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = 'transform transition-all duration-300 translate-x-0';
    toast.innerHTML = `
        <div class="${conf.bg} ${conf.text} rounded-lg shadow-2xl p-4 flex items-start gap-3 max-w-sm">
            <span class="material-symbols-outlined text-2xl ${conf.iconColor}">${conf.icon}</span>
            <div class="flex-1">
                <p class="font-headline font-bold text-sm">${title}</p>
                <p class="text-xs mt-1">${message}</p>
            </div>
            <button onclick="closeToast('${toastId}')" class="hover:opacity-70">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => closeToast(toastId), 5000);
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.transform = 'translateX(400px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Confirmaciones de Acciones
// ═══════════════════════════════════════════════════════════════════════════

function confirmarAccion(mensaje, callback) {
    if (confirm(mensaje)) {
        callback();
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Utilidades
// ═══════════════════════════════════════════════════════════════════════════

function formatearFecha(fecha) {
    if (!fecha) return 'No disponible';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatearBytes(bytes) {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function validarArchivo(archivo, maxSize = 10485760, allowedTypes = []) {
    if (!archivo) {
        return { valid: false, message: 'No se seleccionó ningún archivo' };
    }
    
    if (archivo.size > maxSize) {
        return { valid: false, message: `El archivo no debe superar ${formatearBytes(maxSize)}` };
    }
    
    if (allowedTypes.length > 0) {
        const extension = archivo.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(extension)) {
            return { valid: false, message: `Tipo de archivo no permitido. Permitidos: ${allowedTypes.join(', ')}` };
        }
    }
    
    return { valid: true };
}

// ═══════════════════════════════════════════════════════════════════════════
// Manejo de Botones de Envío
// ═══════════════════════════════════════════════════════════════════════════

function setButtonLoading(button, loading, originalHTML = null) {
    if (loading) {
        button.dataset.originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span> Procesando...';
    } else {
        button.disabled = false;
        button.innerHTML = originalHTML || button.dataset.originalHTML || 'Enviar';
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// Preview de Archivos
// ═══════════════════════════════════════════════════════════════════════════

function mostrarPreviewArchivo(inputElement, previewContainerId) {
    const file = inputElement.files[0];
    const container = document.getElementById(previewContainerId);
    
    if (!file) {
        container.innerHTML = '';
        return;
    }
    
    const icon = file.type.includes('pdf') ? 'picture_as_pdf' : 
                 file.type.includes('image') ? 'image' : 'description';
    
    container.innerHTML = `
        <div class="flex items-center gap-3 bg-surface-container-low p-3 rounded-lg">
            <span class="material-symbols-outlined text-error">${icon}</span>
            <div class="flex-1">
                <p class="text-sm font-medium text-on-surface">${file.name}</p>
                <p class="text-xs text-secondary">${formatearBytes(file.size)}</p>
            </div>
            <button type="button" onclick="clearFileInput('${inputElement.id}', '${previewContainerId}')" 
                class="text-secondary hover:text-error">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
    `;
}

function clearFileInput(inputId, previewId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).innerHTML = '';
}

// ═══════════════════════════════════════════════════════════════════════════
// Acciones Específicas
// ═══════════════════════════════════════════════════════════════════════════

/**
 * Editar/Gestionar denuncia
 * Abre el modal de gestión de caso que muestra acciones disponibles
 * según el estado de la denuncia y el rol del usuario
 * 
 * @param {string} idDenuncia - ID de la denuncia a gestionar
 */
function editarDenuncia(idDenuncia) {
    // Abrir modal de gestión contextual
    if (typeof abrirGestionCaso === 'function') {
        abrirGestionCaso(idDenuncia);
    } else {
        console.error('La función abrirGestionCaso no está disponible');
        showToast('error', 'Error', 'No se pudo abrir el modal de gestión');
    }
}

function recargarPagina(delay = 1500) {
    setTimeout(() => {
        window.location.reload();
    }, delay);
}

// ═══════════════════════════════════════════════════════════════════════════
// Inicialización
// ═══════════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin Actions initialized');
    
    // Configurar todos los inputs de archivo para mostrar preview
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        const previewId = input.dataset.preview;
        input.addEventListener('change', () => mostrarPreviewArchivo(input, previewId));
    });
});
