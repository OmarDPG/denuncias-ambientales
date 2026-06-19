<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Modal de Gestión de Caso (Acciones Contextuales)               -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ═══════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Modal para gestionar acciones sobre una denuncia según estado y rol
 * 
 * Uso:
 * - Se activa con: abrirGestionCaso(idDenuncia, estado)
 * - Muestra acciones disponibles según rol y estado
 * - Redirige a los modales específicos de cada acción
 */
?>

<!-- Modal de Gestión de Caso -->
<div id="gestionarCasoModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 p-4"
    onclick="cerrarGestionCasoOnOverlay(event)">
    <div class="flex items-center justify-center min-h-full">
    <div class="bg-surface-container-lowest rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
        onclick="event.stopPropagation()">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-br from-tertiary to-tertiary-container text-white px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="font-headline font-extrabold text-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-3xl">settings</span>
                    Gestionar Caso
                </h2>
                <p class="text-sm text-tertiary-fixed opacity-90 mt-1" id="gestionCasoFolio">Folio: —</p>
            </div>
            <button onclick="cerrarGestionCasoCompleto()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(80vh-140px)] px-8 py-6">
            
            <!-- Estado Actual -->
            <div class="mb-6 bg-surface-container-low border-l-4 border-tertiary p-4 rounded-lg">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-tertiary text-2xl">info</span>
                    <div class="flex-1">
                        <p class="font-headline font-bold text-sm text-tertiary mb-1">Estado Actual</p>
                        <div id="gestionEstadoActual" class="text-sm text-on-surface">—</div>
                    </div>
                </div>
            </div>

            <!-- Acciones Disponibles -->
            <div class="space-y-3" id="gestionAccionesDisponibles">
                <p class="text-sm text-secondary text-center py-8">Cargando acciones disponibles...</p>
            </div>

            <!-- Sección de Ver Detalles Completos -->
            <div class="mt-6 pt-6 border-t border-outline-variant/20">
                <button onclick="verDetallesDesdeGestion()" 
                    class="w-full py-3 px-4 bg-surface-container border border-outline-variant/40 hover:bg-surface-container-high text-primary rounded-lg font-headline font-bold text-sm transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">visibility</span>
                    Ver Detalles Completos
                </button>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-end">
            <button onclick="cerrarGestionCasoCompleto()" type="button"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cerrar
            </button>
        </div>
    </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal de Gestión de Caso
// ═══════════════════════════════════════════════════════════════════════════

let gestionCasoActual = null;

function abrirGestionCaso(idDenuncia) {
    console.log('abrirGestionCaso llamado con ID:', idDenuncia, 'tipo:', typeof idDenuncia);
    
    // Validar que se recibe un ID válido
    if (!idDenuncia || idDenuncia === 'null' || idDenuncia === 'undefined') {
        console.error('ID de denuncia inválido:', idDenuncia);
        if (typeof showToast === 'function') {
            showToast('error', 'Error', 'ID de denuncia no válido');
        }
        return;
    }
    
    // Guardar el ID actual
    gestionCasoActual = idDenuncia;
    console.log('gestionCasoActual establecido a:', gestionCasoActual);
    
    const modal = document.getElementById('gestionarCasoModal');
    if (!modal) {
        console.error('Modal de gestión no encontrado');
        return;
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Cargar información de la denuncia
    cargarDatosGestionCaso(idDenuncia);
}

function cerrarGestionCaso() {
    const modal = document.getElementById('gestionarCasoModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    // NO limpiar gestionCasoActual aquí porque puede ser necesario para otros modales
    // Solo se limpiará cuando se abra un nuevo caso
}

function cerrarGestionCasoCompleto() {
    const modal = document.getElementById('gestionarCasoModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    gestionCasoActual = null;
}

function cerrarGestionCasoOnOverlay(event) {
    if (event.target.id === 'gestionarCasoModal') {
        cerrarGestionCasoCompleto();
    }
}

function cargarDatosGestionCaso(idDenuncia) {
    // Mostrar loading
    document.getElementById('gestionAccionesDisponibles').innerHTML = 
        '<p class="text-sm text-secondary text-center py-8">Cargando acciones disponibles...</p>';
    
    // Cargar datos via AJAX
    fetch(`<?= base_url('admin/obtenerDenunciaDetalle/') ?>${idDenuncia}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarAccionesGestion(data.data.denuncia);
            } else {
                document.getElementById('gestionAccionesDisponibles').innerHTML = 
                    `<p class="text-sm text-error text-center py-8">${data.message || 'Error al cargar datos'}</p>`;
                if (typeof showToast === 'function') {
                    showToast('error', 'Error', data.message || 'No se pudieron cargar los datos');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('gestionAccionesDisponibles').innerHTML = 
                '<p class="text-sm text-error text-center py-8">Error de conexión</p>';
            if (typeof showToast === 'function') {
                showToast('error', 'Error', 'Error de conexión al servidor');
            }
        });
}

function mostrarAccionesGestion(denuncia) {
    // Guardar/actualizar el ID actual de la denuncia
    if (denuncia.id_denuncia) {
        gestionCasoActual = denuncia.id_denuncia;
    }
    
    // Validar que tenemos un ID válido
    if (!gestionCasoActual) {
        console.error('ID de denuncia no disponible en mostrarAccionesGestion');
        document.getElementById('gestionAccionesDisponibles').innerHTML = 
            '<p class="text-sm text-error text-center py-8">Error: No se pudo cargar el ID de la denuncia</p>';
        return;
    }
    
    // Actualizar folio y estado
    document.getElementById('gestionCasoFolio').textContent = `Folio: ${denuncia.folio || '—'}`;
    
    const estadoId = denuncia.id_estado_actual;
    const estadoNombre = denuncia.nombre_estado || denuncia.estatus || 'Sin estado';
    const coloresEstado = getEstadoColor(estadoId);
    
    document.getElementById('gestionEstadoActual').innerHTML = 
        `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold ${coloresEstado.bg} ${coloresEstado.text}">
            ${estadoNombre}
        </span>`;
    
    const container = document.getElementById('gestionAccionesDisponibles');
    
    // Generar botones de acción según estado (usar gestionCasoActual en lugar de denuncia.id_denuncia)
    const accionesHTML = generarBotonesAccion(estadoId, gestionCasoActual);
    
    container.innerHTML = accionesHTML || 
        '<p class="text-sm text-secondary text-center py-8">No hay acciones disponibles para este estado</p>';
}

function generarBotonesAccion(estadoCodigo, idDenuncia) {
    // Validar que idDenuncia sea válido
    if (!idDenuncia || idDenuncia === 'null' || idDenuncia === 'undefined') {
        console.error('ID de denuncia inválido en generarBotonesAccion:', idDenuncia);
        return '<p class="text-sm text-error text-center py-8">Error: ID de denuncia no válido</p>';
    }
    
    const acciones = [];
    
    // Determinar acciones según estado (simplificado - ajustar según tu lógica)
    switch(parseInt(estadoCodigo)) {
        case 3: // EN_REVISION_DNS
            acciones.push({
                titulo: 'Aprobar para Inspección',
                descripcion: 'Enviar al Departamento de Supervisión',
                icono: 'check_circle',
                color: 'success',
                funcion: `abrirAprobarInspeccion('${idDenuncia}')`
            });
            acciones.push({
                titulo: 'Rechazar Denuncia',
                descripcion: 'No procede o fuera de competencia',
                icono: 'cancel',
                color: 'error',
                funcion: `abrirRechazar('${idDenuncia}')`
            });
            break;
            
        case 6: // EN_INSPECCION
            acciones.push({
                titulo: 'Concluir Inspección',
                descripcion: 'Subir acta y finalizar inspección',
                icono: 'task_alt',
                color: 'success',
                funcion: `abrirConcluirInspeccion('${idDenuncia}')`
            });
            break;
            
        case 8: // REGRESADA_DNS
            acciones.push({
                titulo: 'Emitir Sanción',
                descripcion: 'Generar acta de sanción',
                icono: 'policy',
                color: 'error',
                funcion: `abrirEmitirSancion('${idDenuncia}')`
            });
            acciones.push({
                titulo: 'Finalizar sin Sanción',
                descripcion: 'Cerrar caso sin sancionar',
                icono: 'check_circle',
                color: 'success',
                funcion: `abrirFinalizar('${idDenuncia}')`
            });
            break;
            
        case 9: // EN_ELABORACION_SANCION
            acciones.push({
                titulo: 'Emitir Sanción',
                descripcion: 'Generar acta de sanción definitiva',
                icono: 'policy',
                color: 'error',
                funcion: `abrirEmitirSancion('${idDenuncia}')`
            });
            break;
    }
    
    // Generar HTML
    return acciones.map(accion => {
        const clases = getClasesAccion(accion.color);
        return `
        <button onclick="${accion.funcion}" 
            class="w-full ${clases.bg} ${clases.hover} ${clases.border} text-left p-4 rounded-lg transition-all group">
            <div class="flex items-start gap-4">
                <span class="material-symbols-outlined text-3xl ${clases.icon}">
                    ${accion.icono}
                </span>
                <div class="flex-1">
                    <h4 class="font-headline font-bold text-sm text-on-surface ${clases.textHover} mb-1">
                        ${accion.titulo}
                    </h4>
                    <p class="text-xs text-secondary">${accion.descripcion}</p>
                </div>
                <span class="material-symbols-outlined text-secondary group-hover:translate-x-1 transition-transform">
                    arrow_forward
                </span>
            </div>
        </button>
    `;
    }).join('');
}

function getClasesAccion(color) {
    const clasesMap = {
        'success': {
            bg: 'bg-green-50',
            hover: 'hover:bg-green-100',
            border: 'border-l-4 border-green-500',
            icon: 'text-green-600',
            textHover: 'group-hover:text-green-700'
        },
        'error': {
            bg: 'bg-red-50',
            hover: 'hover:bg-red-100',
            border: 'border-l-4 border-red-500',
            icon: 'text-red-600',
            textHover: 'group-hover:text-red-700'
        },
        'warning': {
            bg: 'bg-yellow-50',
            hover: 'hover:bg-yellow-100',
            border: 'border-l-4 border-yellow-500',
            icon: 'text-yellow-600',
            textHover: 'group-hover:text-yellow-700'
        }
    };
    return clasesMap[color] || clasesMap['warning'];
}

function getEstadoColor(codigo) {
    const coloresClases = {
        1: { bg: 'bg-blue-100', text: 'text-blue-700' },      // RECIBIDA
        2: { bg: 'bg-purple-100', text: 'text-purple-700' },  // TURNADA_DNS
        3: { bg: 'bg-yellow-100', text: 'text-yellow-700' },  // EN_REVISION_DNS
        6: { bg: 'bg-orange-100', text: 'text-orange-700' },  // EN_INSPECCION
        8: { bg: 'bg-amber-100', text: 'text-amber-700' },    // REGRESADA_DNS
        10: { bg: 'bg-green-100', text: 'text-green-700' },   // SANCIONADA
        15: { bg: 'bg-emerald-100', text: 'text-emerald-700' } // FINALIZADA
    };
    return coloresClases[parseInt(codigo)] || { bg: 'bg-gray-100', text: 'text-gray-700' };
}

function verDetallesDesdeGestion() {
    console.log('verDetallesDesdeGestion llamado, gestionCasoActual:', gestionCasoActual, 'tipo:', typeof gestionCasoActual);
    
    if (!gestionCasoActual || gestionCasoActual === 'null' || gestionCasoActual === 'undefined') {
        console.error('No hay caso actual para ver detalles:', gestionCasoActual);
        if (typeof showToast === 'function') {
            showToast('error', 'Error', 'No se pudo identificar el caso');
        }
        return;
    }
    
    cerrarGestionCaso();
    
    console.log('Intentando abrir modal de detalle con ID:', gestionCasoActual);
    
    if (typeof openDetailModal === 'function') {
        openDetailModal(gestionCasoActual);
    } else {
        console.error('Función openDetailModal no disponible');
        if (typeof showToast === 'function') {
            showToast('error', 'Error', 'No se pudo abrir el modal de detalles');
        }
    }
}

// Funciones para abrir modales específicos (verificar que existan)
function abrirAprobarInspeccion(id) {
    cerrarGestionCaso();
    if (typeof openAprobarInspeccionModal === 'function') {
        openAprobarInspeccionModal(id);
    } else {
        showToast('error', 'Error', 'Modal de aprobación no disponible');
    }
}

function abrirRechazar(id) {
    cerrarGestionCaso();
    if (typeof openRechazarDenunciaModal === 'function') {
        openRechazarDenunciaModal(id);
    } else {
        showToast('error', 'Error', 'Modal de rechazo no disponible');
    }
}

function abrirConcluirInspeccion(id) {
    cerrarGestionCaso();
    if (typeof openConcluirInspeccionModal === 'function') {
        openConcluirInspeccionModal(id);
    } else {
        showToast('error', 'Error', 'Modal de conclusión no disponible');
    }
}

function abrirEmitirSancion(id) {
    cerrarGestionCaso();
    if (typeof openEmitirSancionModal === 'function') {
        openEmitirSancionModal(id);
    } else {
        showToast('error', 'Error', 'Modal de sanción no disponible');
    }
}

function abrirFinalizar(id) {
    cerrarGestionCaso();
    
    // Confirmar acción
    if (!confirm('¿Está seguro de finalizar este caso sin sanción?')) {
        return;
    }
    
    // Enviar petición
    const formData = new FormData();
    formData.append('id_denuncia', id);
    formData.append('observaciones', 'Caso finalizado sin sanción');
    
    fetch('<?= base_url('admin/finalizarCaso') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Éxito', 'Caso finalizado correctamente');
            recargarPagina(1500);
        } else {
            showToast('error', 'Error', data.message || 'No se pudo finalizar el caso');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error', 'Error de conexión');
    });
}
</script>
