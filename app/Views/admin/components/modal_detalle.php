<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Modal de Detalle de Denuncia                                    -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Modal para visualizar detalles completos de una denuncia
 * 
 * Uso:
 * - Se activa con: openDetailModal(idDenuncia)
 * - Se cierra con: closeDetailModal()
 * - Los datos se cargan via AJAX desde el controlador
 */
?>

<!-- Modal de Detalles de Denuncia -->
<div id="complaintDetailModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 p-4"
    onclick="closeDetailModalOnOverlay(event)">
    <div class="flex items-center justify-center min-h-full">
    <div class="bg-surface-container-lowest rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
        onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div
            class="sticky top-0 bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
            <div>
                <h2 class="font-headline font-extrabold text-2xl">Detalles de la Denuncia</h2>
                <p class="text-sm text-primary-fixed opacity-90 mt-1" id="detailModalFolio">Folio: —</p>
            </div>
            <button onclick="closeDetailModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(80vh-140px)] px-8 py-6 space-y-8">
            <!-- Información del Reporte -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">
                    Información del Reporte
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Estado Actual</p>
                        <div id="detailEstadoBadge"></div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Fecha de Denuncia</p>
                        <p class="font-medium text-on-surface" id="detailFecha">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Área Responsable</p>
                        <p class="font-medium text-on-surface" id="detailArea">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Responsable Asignado</p>
                        <p class="font-medium text-on-surface" id="detailResponsable">Sin asignar</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Denuncia</p>
                        <p class="font-medium text-on-surface" id="detailTipoDenuncia">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Prioridad</p>
                        <p class="font-medium text-on-surface" id="detailPrioridad">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Fecha Último Cambio</p>
                        <p class="font-medium text-on-surface text-xs" id="detailFechaUltimoCambio">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Fecha Resolución</p>
                        <p class="font-medium text-on-surface text-xs" id="detailFechaResolucion">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Flujo Excepcional</p>
                        <p class="font-medium text-on-surface text-xs" id="detailFlujoExcepcional">—</p>
                    </div>
                </div>
                <div class="space-y-1" id="detailNotasInternas" style="display:none;">
                    <p class="text-xs font-label uppercase tracking-widest text-secondary">Notas Internas</p>
                    <div class="bg-yellow-50 rounded-lg p-3 border-l-4 border-yellow-400">
                        <p class="text-sm text-on-surface" id="detailNotasInternasTexto">—</p>
                    </div>
                </div>
            </section>

            <!-- Datos del Denunciante -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">
                    Datos del Denunciante
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Persona</p>
                        <p class="font-medium text-on-surface" id="detailTipoPersona">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre Completo</p>
                        <p class="font-medium text-on-surface" id="detailNombre">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Género</p>
                        <p class="font-medium text-on-surface" id="detailGenero">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Correo Electrónico</p>
                        <p class="font-medium text-on-surface" id="detailEmail">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Teléfono</p>
                        <p class="font-medium text-on-surface" id="detailTelefono">—</p>
                    </div>
                </div>
                
                <!-- Domicilio del Denunciante -->
                <div class="bg-surface-container-low rounded-lg p-4 mt-4">
                    <h4 class="font-headline font-semibold text-sm text-primary mb-3">Domicilio del Denunciante</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Estado</p>
                            <p class="font-medium text-on-surface text-sm" id="detailEstadoDomicilio">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Municipio</p>
                            <p class="font-medium text-on-surface text-sm" id="detailMunicipio">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Colonia</p>
                            <p class="font-medium text-on-surface text-sm" id="detailColonia">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Calle</p>
                            <p class="font-medium text-on-surface text-sm" id="detailCalle">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Número Exterior</p>
                            <p class="font-medium text-on-surface text-sm" id="detailNumeroExterior">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Número Interior</p>
                            <p class="font-medium text-on-surface text-sm" id="detailNumeroInterior">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Código Postal</p>
                            <p class="font-medium text-on-surface text-sm" id="detailCodigoPostal">—</p>
                        </div>
                    </div>
                </div>

                <!-- Representante Legal (si aplica) -->
                <div id="detailRepresentanteSection" style="display:none;" class="bg-blue-50 rounded-lg p-4 mt-4">
                    <h4 class="font-headline font-semibold text-sm text-blue-800 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">account_circle</span>
                        Representante Legal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-blue-700">Razón Social</p>
                            <p class="font-medium text-blue-900 text-sm" id="detailRazonSocial">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-blue-700">Nombre del Representante</p>
                            <p class="font-medium text-blue-900 text-sm" id="detailNombreRepresentante">—</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Detalles de la Denuncia -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">
                    Detalles de la Denuncia
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Denuncia</p>
                        <p class="font-medium text-on-surface" id="detailTipoDenunciaDetalle">—</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Tema Específico</p>
                        <p class="font-medium text-on-surface" id="detailTemaDenuncia">—</p>
                    </div>
                </div>
                
                <!-- Ubicación del Incidente -->
                <div class="bg-surface-container-low rounded-lg p-4 mt-4">
                    <h4 class="font-headline font-semibold text-sm text-primary mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">location_on</span>
                        Ubicación del Incidente
                    </h4>
                    <div class="space-y-3">
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary">Descripción de Ubicación</p>
                            <p class="font-medium text-on-surface text-sm" id="detailUbicacion">—</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="detailCoordenadasSection" style="display:none;">
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Latitud</p>
                                <p class="font-medium text-on-surface text-sm" id="detailLatitud">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Longitud</p>
                                <p class="font-medium text-on-surface text-sm" id="detailLongitud">—</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hechos Denunciados -->
                <div class="pt-4">
                    <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Hechos Denunciados</p>
                    <div class="bg-surface-container-low rounded-lg p-4">
                        <p class="text-sm text-on-surface leading-relaxed" id="detailHechos">—</p>
                    </div>
                </div>
            </section>

            <!-- Datos del Denunciado -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-error border-b-2 border-error/20 pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined">report</span>
                    Datos del Denunciado
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Persona</p>
                        <p class="font-medium text-on-surface" id="detailDenunciadoTipo">—</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre/Razón Social</p>
                        <p class="font-medium text-on-surface" id="detailNombreDenunciado">—</p>
                    </div>
                </div>
                
                <!-- Razón Social del Denunciado (si aplica) -->
                <div id="detailDenunciadoRazonSocialSection" style="display:none;" class="space-y-1">
                    <p class="text-xs font-label uppercase tracking-widest text-secondary">Razón Social del Denunciado</p>
                    <p class="font-medium text-on-surface" id="detailRazonSocialDenunciado">—</p>
                </div>

                <!-- Domicilio del Denunciado -->
                <div class="bg-red-50 rounded-lg p-4 mt-4">
                    <h4 class="font-headline font-semibold text-sm text-error mb-3">Domicilio del Denunciado</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Municipio</p>
                            <p class="font-medium text-red-900 text-sm" id="detailMunicipioDenunciado">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Colonia</p>
                            <p class="font-medium text-red-900 text-sm" id="detailColoniaDenunciado">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Calle</p>
                            <p class="font-medium text-red-900 text-sm" id="detailCalleDenunciado">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Número Exterior</p>
                            <p class="font-medium text-red-900 text-sm" id="detailNumeroExteriorDenunciado">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Número Interior</p>
                            <p class="font-medium text-red-900 text-sm" id="detailNumeroInteriorDenunciado">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-red-700">Código Postal</p>
                            <p class="font-medium text-red-900 text-sm" id="detailCodigoPostalDenunciado">—</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Centro de Verificación Vehicular (si aplica) -->
            <section id="detailCVVSection" style="display:none;" class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-purple-700 border-b-2 border-purple-300 pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined">directions_car</span>
                    Centro de Verificación Vehicular
                </h3>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-purple-700">Clave CVV</p>
                            <p class="font-medium text-purple-900" id="detailClaveCVV">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-purple-700">Municipio</p>
                            <p class="font-medium text-purple-900" id="detailMunicipioCVV">—</p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-xs font-label uppercase tracking-widest text-purple-700">Dirección</p>
                            <p class="font-medium text-purple-900" id="detailDireccionCVV">—</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-label uppercase tracking-widest text-purple-700">Teléfono</p>
                            <p class="font-medium text-purple-900" id="detailTelefonoCVV">—</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Documentos Adjuntos -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined">folder_open</span>
                    Documentos Adjuntos
                </h3>
                
                <!-- Identificación -->
                <div id="detailDocumentosIdentificacion" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-blue-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">badge</span>
                        Identificación Oficial
                    </h4>
                    <div id="detailDocumentosIdentificacionLista" class="space-y-2"></div>
                </div>

                <!-- Evidencias -->
                <div id="detailDocumentosEvidencias" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-green-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">photo_camera</span>
                        Evidencias y Fotografías
                    </h4>
                    <div id="detailDocumentosEvidenciasLista" class="space-y-2"></div>
                </div>

                <!-- Documentos de Inspección -->
                <div id="detailDocumentosInspeccion" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-orange-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">assignment</span>
                        Documentos de Inspección
                    </h4>
                    <div id="detailDocumentosInspeccionLista" class="space-y-2"></div>
                </div>

                <!-- Oficios de Turnado -->
                <div id="detailDocumentosTurnado" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-purple-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">forward</span>
                        Oficios de Turnado
                    </h4>
                    <div id="detailDocumentosTurnadoLista" class="space-y-2"></div>
                </div>

                <!-- Documentos de Sanción -->
                <div id="detailDocumentosSancion" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-red-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">gavel</span>
                        Resoluciones y Sanciones
                    </h4>
                    <div id="detailDocumentosSancionLista" class="space-y-2"></div>
                </div>

                <!-- Otros Documentos -->
                <div id="detailDocumentosOtros" style="display:none;">
                    <h4 class="font-headline font-semibold text-sm text-gray-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">description</span>
                        Otros Documentos
                    </h4>
                    <div id="detailDocumentosOtrosLista" class="space-y-2"></div>
                </div>

                <!-- Sin documentos -->
                <div id="detailSinDocumentos">
                    <p class="text-secondary text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">folder_off</span>
                        No hay documentos adjuntos
                    </p>
                </div>
            </section>

            <!-- Historial -->
            <section class="space-y-4">
                <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">
                    Historial de Movimientos
                </h3>
                <div id="detailHistorial" class="space-y-3">
                    <p class="text-secondary text-sm">Cargando historial...</p>
                </div>
            </section>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
            <button onclick="closeDetailModal()"
                class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                Cerrar
            </button>
            <div class="flex gap-3">
                <button onclick="exportarPDF()" id="btnExportarPDF"
                    class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════════════════════════
// Funciones para Modal de Detalle
// ═══════════════════════════════════════════════════════════════════════════

let currentDenunciaId = null;

function openDetailModal(idDenuncia) {
    // Validar que se recibe un ID válido
    if (!idDenuncia || idDenuncia === 'null' || idDenuncia === 'undefined') {
        console.error('ID de denuncia inválido:', idDenuncia);
        if (typeof showToast === 'function') {
            showToast('error', 'Error', 'ID de denuncia no válido');
        }
        return;
    }
    
    currentDenunciaId = idDenuncia;
    const modal = document.getElementById('complaintDetailModal');
    if (!modal) {
        console.error('Modal de detalle no encontrado');
        return;
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Mostrar loading
    mostrarLoadingDetalle();
    
    // Cargar datos via AJAX
    cargarDetallesDenuncia(idDenuncia);
}

function closeDetailModal() {
    const modal = document.getElementById('complaintDetailModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    currentDenunciaId = null;
}

function closeDetailModalOnOverlay(event) {
    if (event.target.id === 'complaintDetailModal') {
        closeDetailModal();
    }
}

function mostrarLoadingDetalle() {
    const loading = '<div class="flex items-center gap-2 text-secondary"><span class="material-symbols-outlined animate-spin">progress_activity</span> Cargando...</div>';
    
    // Información del reporte
    document.getElementById('detailModalFolio').innerHTML = 'Folio: <span class="animate-pulse">●●●</span>';
    document.getElementById('detailEstadoBadge').innerHTML = loading;
    document.getElementById('detailFecha').innerHTML = loading;
    document.getElementById('detailArea').innerHTML = loading;
    document.getElementById('detailResponsable').innerHTML = loading;
    document.getElementById('detailTipoDenuncia').innerHTML = loading;
    document.getElementById('detailPrioridad').innerHTML = loading;
    document.getElementById('detailFechaUltimoCambio').innerHTML = loading;
    document.getElementById('detailFechaResolucion').innerHTML = loading;
    document.getElementById('detailFlujoExcepcional').innerHTML = loading;
    
    // Datos del denunciante
    document.getElementById('detailTipoPersona').innerHTML = loading;
    document.getElementById('detailNombre').innerHTML = loading;
    document.getElementById('detailGenero').innerHTML = loading;
    document.getElementById('detailEmail').innerHTML = loading;
    document.getElementById('detailTelefono').innerHTML = loading;
    document.getElementById('detailEstadoDomicilio').innerHTML = loading;
    document.getElementById('detailMunicipio').innerHTML = loading;
    document.getElementById('detailColonia').innerHTML = loading;
    document.getElementById('detailCalle').innerHTML = loading;
    document.getElementById('detailNumeroExterior').innerHTML = loading;
    document.getElementById('detailNumeroInterior').innerHTML = loading;
    document.getElementById('detailCodigoPostal').innerHTML = loading;
    
    // Detalles de la denuncia
    document.getElementById('detailTipoDenunciaDetalle').innerHTML = loading;
    document.getElementById('detailTemaDenuncia').innerHTML = loading;
    document.getElementById('detailUbicacion').innerHTML = loading;
    document.getElementById('detailHechos').innerHTML = loading;
    
    // Datos del denunciado
    document.getElementById('detailDenunciadoTipo').innerHTML = loading;
    document.getElementById('detailNombreDenunciado').innerHTML = loading;
    document.getElementById('detailMunicipioDenunciado').innerHTML = loading;
    document.getElementById('detailColoniaDenunciado').innerHTML = loading;
    document.getElementById('detailCalleDenunciado').innerHTML = loading;
    document.getElementById('detailNumeroExteriorDenunciado').innerHTML = loading;
    document.getElementById('detailNumeroInteriorDenunciado').innerHTML = loading;
    document.getElementById('detailCodigoPostalDenunciado').innerHTML = loading;
    
    // Documentos e historial
    document.getElementById('detailSinDocumentos').innerHTML = loading;
    document.getElementById('detailHistorial').innerHTML = loading;
    
    // Ocultar secciones condicionales
    document.getElementById('detailRepresentanteSection').style.display = 'none';
    document.getElementById('detailCoordenadasSection').style.display = 'none';
    document.getElementById('detailDenunciadoRazonSocialSection').style.display = 'none';
    document.getElementById('detailCVVSection').style.display = 'none';
    document.getElementById('detailNotasInternas').style.display = 'none';
    document.getElementById('detailDocumentosIdentificacion').style.display = 'none';
    document.getElementById('detailDocumentosEvidencias').style.display = 'none';
    document.getElementById('detailDocumentosInspeccion').style.display = 'none';
    document.getElementById('detailDocumentosTurnado').style.display = 'none';
    document.getElementById('detailDocumentosSancion').style.display = 'none';
    document.getElementById('detailDocumentosOtros').style.display = 'none';
}

function cargarDetallesDenuncia(idDenuncia) {
    fetch('<?= base_url("admin/obtenerDenunciaDetalle/") ?>' + idDenuncia, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            renderizarDetalleDenuncia(data.data);
        } else {
            mostrarErrorDetalle(data.message || 'No se pudo cargar la información');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarErrorDetalle('Error de conexión con el servidor');
    });
}

function renderizarDetalleDenuncia(data) {
    const denuncia = data.denuncia;
    
    // ═══════════════════════════════════════════════════════════════════════════
    // INFORMACIÓN DEL REPORTE
    // ═══════════════════════════════════════════════════════════════════════════
    document.getElementById('detailModalFolio').textContent = 'Folio: ' + (denuncia.folio || '#DA-' + String(denuncia.id_denuncia).padStart(4, '0'));
    document.getElementById('detailEstadoBadge').innerHTML = generarBadgeEstado(denuncia.nombre_estado || 'Sin estado');
    document.getElementById('detailFecha').textContent = formatearFecha(denuncia.fecha_captura);
    document.getElementById('detailArea').textContent = denuncia.nombre_area || 'Sin asignar';
    
    // Responsable asignado
    const responsableNombre = denuncia.nombre_responsable ? 
        `${denuncia.nombre_responsable} ${denuncia.apellidoP_responsable || ''} ${denuncia.apellidoM_responsable || ''}`.trim() : 
        'Sin asignar';
    document.getElementById('detailResponsable').textContent = responsableNombre;
    
    document.getElementById('detailTipoDenuncia').textContent = denuncia.nombre_tipo_denuncia || denuncia.tipo_denuncia || 'No especificado';
    document.getElementById('detailPrioridad').textContent = denuncia.prioridad || 'MEDIA';
    document.getElementById('detailFechaUltimoCambio').textContent = formatearFechaCorta(denuncia.fecha_ultimo_cambio_estado);
    document.getElementById('detailFechaResolucion').textContent = formatearFechaCorta(denuncia.fecha_resolucion);
    
    // Flujo excepcional
    const flujoTexto = denuncia.flujo_excepcional == 1 ? 
        `Sí${denuncia.razon_flujo_excepcional ? ' - ' + denuncia.razon_flujo_excepcional : ''}` : 
        'No';
    document.getElementById('detailFlujoExcepcional').textContent = flujoTexto;
    
    // Notas internas
    if (denuncia.notas_internas && denuncia.notas_internas.trim() !== '') {
        document.getElementById('detailNotasInternas').style.display = 'block';
        document.getElementById('detailNotasInternasTexto').textContent = denuncia.notas_internas;
    }
    
    // ═══════════════════════════════════════════════════════════════════════════
    // DATOS DEL DENUNCIANTE
    // ═══════════════════════════════════════════════════════════════════════════
    document.getElementById('detailTipoPersona').textContent = formatearTipoPersona(denuncia.tipo_persona);
    document.getElementById('detailNombre').textContent = denuncia.nombre_completo || 'Anónimo';
    document.getElementById('detailGenero').textContent = formatearGenero(denuncia.genero);
    document.getElementById('detailEmail').textContent = denuncia.email || 'No proporcionado';
    document.getElementById('detailTelefono').textContent = denuncia.telefono || 'No proporcionado';
    
    // Domicilio del denunciante
    document.getElementById('detailEstadoDomicilio').textContent = denuncia.estado || 'No especificado';
    document.getElementById('detailMunicipio').textContent = denuncia.municipio || 'No especificado';
    document.getElementById('detailColonia').textContent = denuncia.colonia || 'No especificada';
    document.getElementById('detailCalle').textContent = denuncia.calle || 'No especificada';
    document.getElementById('detailNumeroExterior').textContent = denuncia.numero_exterior || 'S/N';
    document.getElementById('detailNumeroInterior').textContent = denuncia.numero_interior || 'N/A';
    document.getElementById('detailCodigoPostal').textContent = denuncia.codigo_postal || 'No especificado';
    
    // Representante legal
    if (denuncia.es_representante == 1 && (denuncia.razon_social || denuncia.nombre_representante)) {
        document.getElementById('detailRepresentanteSection').style.display = 'block';
        document.getElementById('detailRazonSocial').textContent = denuncia.razon_social || 'No especificada';
        document.getElementById('detailNombreRepresentante').textContent = denuncia.nombre_representante || 'No especificado';
    }
    
    // ═══════════════════════════════════════════════════════════════════════════
    // DETALLES DE LA DENUNCIA
    // ═══════════════════════════════════════════════════════════════════════════
    document.getElementById('detailTipoDenunciaDetalle').textContent = denuncia.nombre_tipo_denuncia || denuncia.tipo_denuncia || 'No especificado';
    document.getElementById('detailTemaDenuncia').textContent = denuncia.nombre_tema_denuncia || 'No especificado';
    document.getElementById('detailUbicacion').textContent = denuncia.ubicacion_incidente || 'No especificada';
    document.getElementById('detailHechos').textContent = denuncia.hechos_denunciados || 'Sin descripción';
    
    // Coordenadas
    if (denuncia.latitud && denuncia.longitud) {
        document.getElementById('detailCoordenadasSection').style.display = 'grid';
        document.getElementById('detailLatitud').textContent = denuncia.latitud;
        document.getElementById('detailLongitud').textContent = denuncia.longitud;
    }
    
    // ═══════════════════════════════════════════════════════════════════════════
    // DATOS DEL DENUNCIADO
    // ═══════════════════════════════════════════════════════════════════════════
    document.getElementById('detailDenunciadoTipo').textContent = denuncia.denunciado_es_moral == 1 ? 'Persona Moral' : 'Persona Física';
    document.getElementById('detailNombreDenunciado').textContent = denuncia.nombre_denunciado || 'No especificado';
    
    if (denuncia.denunciado_es_moral == 1 && denuncia.razon_social_denunciado) {
        document.getElementById('detailDenunciadoRazonSocialSection').style.display = 'block';
        document.getElementById('detailRazonSocialDenunciado').textContent = denuncia.razon_social_denunciado;
    }
    
    // Domicilio del denunciado
    document.getElementById('detailMunicipioDenunciado').textContent = denuncia.municipio_denunciado || 'No especificado';
    document.getElementById('detailColoniaDenunciado').textContent = denuncia.colonia_denunciado || 'No especificada';
    document.getElementById('detailCalleDenunciado').textContent = denuncia.calle_denunciado || 'No especificada';
    document.getElementById('detailNumeroExteriorDenunciado').textContent = denuncia.numero_exterior_denunciado || 'S/N';
    document.getElementById('detailNumeroInteriorDenunciado').textContent = denuncia.numero_interior_denunciado || 'N/A';
    document.getElementById('detailCodigoPostalDenunciado').textContent = denuncia.codigo_postal_denunciado || 'No especificado';
    
    // ═══════════════════════════════════════════════════════════════════════════
    // CENTRO DE VERIFICACIÓN VEHICULAR
    // ═══════════════════════════════════════════════════════════════════════════
    if (denuncia.clave_cvv || denuncia.id_tipo_denuncia == 7) {
        document.getElementById('detailCVVSection').style.display = 'block';
        document.getElementById('detailClaveCVV').textContent = denuncia.clave_cvv || 'No especificada';
        document.getElementById('detailMunicipioCVV').textContent = denuncia.municipio_cvv || 'No especificado';
        document.getElementById('detailDireccionCVV').textContent = denuncia.direccion_cvv || 'No especificada';
        document.getElementById('detailTelefonoCVV').textContent = denuncia.telefono_cvv || 'No especificado';
    }
    
    // ═══════════════════════════════════════════════════════════════════════════
    // DOCUMENTOS
    // ═══════════════════════════════════════════════════════════════════════════
    renderizarDocumentosOrganizados(data.documentos_organizados || {});
    
    // ═══════════════════════════════════════════════════════════════════════════
    // HISTORIAL
    // ═══════════════════════════════════════════════════════════════════════════
    renderizarHistorial(data.historial || []);
}

function generarBadgeEstado(estado) {
    const estadosConfig = {
        'RECIBIDA': { color: 'bg-blue-100 text-blue-800', icon: 'inbox' },
        'TURNADA_DNS': { color: 'bg-purple-100 text-purple-800', icon: 'forward' },
        'EN_REVISION_DNS': { color: 'bg-yellow-100 text-yellow-800', icon: 'rate_review' },
        'APROBADA_INSPECCION': { color: 'bg-green-100 text-green-800', icon: 'check_circle' },
        'TURNADA_DS': { color: 'bg-purple-100 text-purple-800', icon: 'forward' },
        'EN_INSPECCION': { color: 'bg-orange-100 text-orange-800', icon: 'assignment' },
        'INSPECCION_CONCLUIDA': { color: 'bg-teal-100 text-teal-800', icon: 'task_alt' },
        'REGRESADA_DNS': { color: 'bg-indigo-100 text-indigo-800', icon: 'undo' },
        'EN_ELABORACION_SANCION': { color: 'bg-red-100 text-red-800', icon: 'gavel' },
        'SANCIONADA': { color: 'bg-red-200 text-red-900', icon: 'report' },
        'RECHAZADA': { color: 'bg-gray-200 text-gray-800', icon: 'cancel' },
        'FINALIZADA': { color: 'bg-green-200 text-green-900', icon: 'done_all' }
    };
    
    const config = estadosConfig[estado] || { color: 'bg-gray-100 text-gray-800', icon: 'help' };
    
    return `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold ${config.color}">
        <span class="material-symbols-outlined text-sm">${config.icon}</span>
        ${estado.replace(/_/g, ' ')}
    </span>`;
}

function formatearFechaCorta(fecha) {
    if (!fecha) return 'No registrada';
    const d = new Date(fecha);
    return d.toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
    });
}

function formatearTipoPersona(tipo) {
    if (!tipo) return 'No especificado';
    return tipo === 'fisica' ? 'Persona Física' : 'Persona Moral';
}

function formatearGenero(genero) {
    if (!genero) return 'No especificado';
    const generos = {
        'masculino': 'Masculino',
        'femenino': 'Femenino',
        'otro': 'Otro',
        'prefiero-no-decir': 'Prefiero no decir'
    };
    return generos[genero] || genero;
}

function renderizarDocumentosOrganizados(documentosOrg) {
    let hayDocumentos = false;
    
    // Identificación
    if (documentosOrg.identificacion && documentosOrg.identificacion.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosIdentificacion').style.display = 'block';
        document.getElementById('detailDocumentosIdentificacionLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.identificacion, 'blue');
    }
    
    // Evidencias
    if (documentosOrg.evidencias && documentosOrg.evidencias.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosEvidencias').style.display = 'block';
        document.getElementById('detailDocumentosEvidenciasLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.evidencias, 'green');
    }
    
    // Inspección
    if (documentosOrg.inspeccion && documentosOrg.inspeccion.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosInspeccion').style.display = 'block';
        document.getElementById('detailDocumentosInspeccionLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.inspeccion, 'orange');
    }
    
    // Turnado
    if (documentosOrg.turnado && documentosOrg.turnado.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosTurnado').style.display = 'block';
        document.getElementById('detailDocumentosTurnadoLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.turnado, 'purple');
    }
    
    // Sanción
    if (documentosOrg.sancion && documentosOrg.sancion.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosSancion').style.display = 'block';
        document.getElementById('detailDocumentosSancionLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.sancion, 'red');
    }
    
    // Otros
    if (documentosOrg.otros && documentosOrg.otros.length > 0) {
        hayDocumentos = true;
        document.getElementById('detailDocumentosOtros').style.display = 'block';
        document.getElementById('detailDocumentosOtrosLista').innerHTML = 
            renderizarListaDocumentos(documentosOrg.otros, 'gray');
    }
    
    // Mostrar/ocultar mensaje de sin documentos
    document.getElementById('detailSinDocumentos').style.display = hayDocumentos ? 'none' : 'block';
}

function renderizarListaDocumentos(documentos, colorClass) {
    const baseUrl = '<?= base_url() ?>';
    let html = '';
    documentos.forEach(doc => {
        // Detectar origen del documento para usar campos correctos
        const esUsuario = doc.origen === 'usuario';
        const icon = obtenerIconoDocumento(esUsuario ? doc.tipo_archivo : doc.tipo_mime);
        const size = doc.peso_bytes ? formatearTamano(doc.peso_bytes) : '';
        
        // Campos según origen
        const tipoNombre = esUsuario ? (doc.tipo_documento || 'Documento') : (doc.tipo_documento_nombre || 'Documento Oficial');
        const nombreArchivo = doc.nombre_original || '';
        const idDoc = esUsuario ? doc.id_evidencia : doc.id_documento;
        const urlBase = esUsuario ? 'admin/verEvidencia/' : 'admin/verDocumento/';
        const urlCompleta = baseUrl + '/' + urlBase + idDoc;
        
        // Información adicional
        let infoAdicional = `Subido el ${formatearFechaCorta(doc.fecha_subida)}`;
        if (!esUsuario && doc.nombre_usuario_subida) {
            infoAdicional = `Subido por ${doc.nombre_usuario_subida} ${doc.apellidoP_usuario_subida || ''} • ${formatearFechaCorta(doc.fecha_subida)}`;
            if (doc.area_nombre) {
                infoAdicional += ` • ${doc.area_nombre}`;
            }
        }
        if (esUsuario) {
            infoAdicional += ' • Denunciante';
        } else {
            infoAdicional += ' • Oficial';
        }
        
        html += `
            <div class="flex items-center justify-between p-3 bg-${colorClass}-50 rounded-lg hover:bg-${colorClass}-100 transition-colors border border-${colorClass}-200">
                <div class="flex items-center gap-3 flex-1">
                    <span class="material-symbols-outlined text-${colorClass}-600">${icon}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-${colorClass}-900">${tipoNombre}</p>
                        <p class="text-xs text-${colorClass}-700">${nombreArchivo} ${size ? '• ' + size : ''}</p>
                        <p class="text-xs text-${colorClass}-600">${infoAdicional}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="${urlCompleta}" target="_blank"
                        class="p-2 text-${colorClass}-600 hover:bg-${colorClass}-200 rounded-lg transition-colors" title="Ver">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                    </a>
                    <a href="${urlCompleta}" download
                        class="p-2 text-${colorClass}-600 hover:bg-${colorClass}-200 rounded-lg transition-colors" title="Descargar">
                        <span class="material-symbols-outlined text-lg">download</span>
                    </a>
                </div>
            </div>
        `;
    });
    return html;
}

function obtenerIconoDocumento(mimeType) {
    if (!mimeType) return 'description';
    if (mimeType.includes('pdf')) return 'picture_as_pdf';
    if (mimeType.includes('image')) return 'image';
    if (mimeType.includes('video')) return 'videocam';
    if (mimeType.includes('word')) return 'article';
    if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'table_chart';
    return 'description';
}

function formatearTamano(bytes) {
    if (!bytes || bytes === 0) return '';
    const mb = bytes / 1024 / 1024;
    if (mb >= 1) return mb.toFixed(2) + ' MB';
    const kb = bytes / 1024;
    return kb.toFixed(2) + ' KB';
}

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

function renderizarHistorial(historial) {
    const container = document.getElementById('detailHistorial');
    
    if (!historial || historial.length === 0) {
        container.innerHTML = '<p class="text-secondary text-sm">No hay movimientos registrados</p>';
        return;
    }
    
    let html = '<div class="relative border-l-2 border-outline-variant/20 pl-6 space-y-4">';
    historial.forEach((item, index) => {
        const iconConfig = {
            'TURNADO': { icon: 'forward', color: 'text-purple-600' },
            'ASIGNACION': { icon: 'person_add', color: 'text-blue-600' },
            'APROBACION_INSPECCION': { icon: 'check_circle', color: 'text-green-600' },
            'RECHAZO': { icon: 'cancel', color: 'text-red-600' },
            'CONCLUSION_INSPECCION': { icon: 'task_alt', color: 'text-teal-600' },
            'REGRESO_DNS': { icon: 'undo', color: 'text-indigo-600' },
            'EMISION_SANCION': { icon: 'gavel', color: 'text-red-700' },
            'FINALIZACION': { icon: 'done_all', color: 'text-green-700' }
        };
        
        const config = iconConfig[item.accion] || { icon: 'fiber_manual_record', color: 'text-secondary' };
        
        const nombreUsuario = item.nombre_usuario ? 
            `${item.nombre_usuario} ${item.apellidoP_usuario || ''} ${item.apellidoM_usuario || ''}`.trim() : 
            'Sistema';
        
        html += `
            <div class="relative">
                <div class="absolute -left-[1.6rem] w-8 h-8 rounded-full bg-surface-container border-2 border-outline-variant/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm ${config.color}">${config.icon}</span>
                </div>
                <div class="bg-surface-container-low rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-headline font-bold text-sm text-primary">${item.accion.replace(/_/g, ' ')}</p>
                        <span class="text-xs text-secondary">${formatearFecha(item.fecha_accion)}</span>
                    </div>
                    ${item.estado_anterior ? `<p class="text-xs text-secondary">De: <span class="font-medium">${item.estado_anterior}</span></p>` : ''}
                    ${item.estado_nuevo ? `<p class="text-xs text-secondary">A: <span class="font-medium">${item.estado_nuevo}</span></p>` : ''}
                    ${item.area_origen_nombre ? `<p class="text-xs text-secondary">Origen: ${item.area_origen_nombre}</p>` : ''}
                    ${item.area_destino_nombre ? `<p class="text-xs text-secondary">Destino: ${item.area_destino_nombre}</p>` : ''}
                    ${item.observaciones ? `<p class="text-xs text-on-surface mt-2 italic bg-white/50 p-2 rounded">"${item.observaciones}"</p>` : ''}
                    <p class="text-xs text-secondary mt-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">person</span>
                        ${nombreUsuario}
                    </p>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function mostrarErrorDetalle(mensaje) {
    const errorHtml = `
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <span class="material-symbols-outlined text-6xl text-error mb-4">error</span>
            <p class="text-lg font-headline font-bold text-error mb-2">Error al cargar datos</p>
            <p class="text-sm text-secondary">${mensaje}</p>
        </div>
    `;
    
    document.getElementById('detailModalFolio').textContent = 'Folio: Error';
    document.querySelector('#complaintDetailModal .overflow-y-auto').innerHTML = errorHtml;
}

function exportarPDF() {
    if (!currentDenunciaId) return;
    
    // TODO: Implementar exportación a PDF
    alert('Funcionalidad de exportación a PDF en desarrollo');
}

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('complaintDetailModal');
        if (!modal.classList.contains('hidden')) {
            closeDetailModal();
        }
    }
});
</script>
