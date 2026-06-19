<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD: Usuario DNS (Departamento de Normativa y Sanciones)              -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Sistema de pestañas para evitar saturación con múltiples registros          -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Dashboard para rol USR_DNS
 * 
 * Funcionalidades:
 * - Revisión de denuncias turnadas
 * - Aprobar o rechazar denuncias
 * - Solicitar inspecciones
 * - Elaborar y emitir sanciones
 * - Ver denuncias regresadas de inspección
 * 
 * Variables disponibles:
 * @var array $denunciasTurnadas Denuncias TURNADA_DNS sin asignar
 * @var array $misRevisiones Denuncias EN_REVISION_DNS asignadas al usuario
 * @var array $regresadasDS Denuncias REGRESADA_DNS de vuelta de inspección
 * @var array $enSancion Denuncias en elaboración de sanción
 * @var string $adminNombre Nombre del usuario (usado en header)
 * @var string $nombreRol Nombre del rol
 * @var string $nombreArea Nombre del área
 */

$totalTurnadas = isset($denunciasTurnadas) ? count($denunciasTurnadas) : 0;
$totalRevisiones = isset($misRevisiones) ? count($misRevisiones) : 0;
$totalRegresadas = isset($regresadasDS) ? count($regresadasDS) : 0;
$totalSanciones = isset($enSancion) ? count($enSancion) : 0;
?>

<!-- Dashboard Body -->
<div class="p-8 space-y-8">
    <?php
    // Estadísticas para usuario DNS (Normativa y Sanciones)
    $stats = [
        [
            'titulo' => 'Nuevas Asignaciones',
            'valor' => $totalTurnadas,
            'icono' => 'inbox',
            'estilo' => 'primary'
        ],
        [
            'titulo' => 'En Revisión',
            'valor' => $totalRevisiones,
            'icono' => 'assignment',
            'estilo' => 'warning'
        ],
        [
            'titulo' => 'Regresadas de Inspección',
            'valor' => $totalRegresadas,
            'icono' => 'assignment_return',
            'estilo' => 'tertiary'
        ],
        [
            'titulo' => 'En Procedimiento Sancionador',
            'valor' => $totalSanciones,
            'icono' => 'policy',
            'estilo' => 'error'
        ]
    ];
    ?>
    
    <?= view('admin/components/stats_cards', ['stats' => $stats]) ?>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- SISTEMA DE NAVEGACIÓN POR PESTAÑAS                                       -->
    <!-- Evita saturar la vista mostrando todas las tablas simultáneamente        -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div class="bg-surface rounded-2xl shadow-lg overflow-hidden">
        <!-- Tabs Navigation -->
        <div class="bg-surface-container border-b border-outline-variant">
            <nav class="flex" role="tablist" aria-label="Pestañas de revisión DNS">
                <button 
                    id="tab-turnadas" 
                    class="tab-button active flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-primary text-primary"
                    onclick="cambiarTabDNS('turnadas')"
                    role="tab"
                    aria-selected="true"
                    aria-controls="panel-turnadas">
                    <span class="material-symbols-outlined text-xl" data-icon="inbox">inbox</span>
                    Nuevas Asignaciones
                    <?php if ($totalTurnadas > 0): ?>
                        <span class="ml-2 bg-secondary text-on-secondary px-2.5 py-0.5 rounded-full text-xs font-bold"><?= $totalTurnadas ?></span>
                    <?php endif; ?>
                </button>
                
                <button 
                    id="tab-revisiones" 
                    class="tab-button flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest"
                    onclick="cambiarTabDNS('revisiones')"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-revisiones">
                    <span class="material-symbols-outlined text-xl" data-icon="assignment">assignment</span>
                    Mis Casos en Revisión
                    <?php if ($totalRevisiones > 0): ?>
                        <span class="ml-2 bg-warning text-on-warning px-2.5 py-0.5 rounded-full text-xs font-bold animate-pulse"><?= $totalRevisiones ?></span>
                    <?php endif; ?>
                </button>
                
                <button 
                    id="tab-regresadas" 
                    class="tab-button flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest"
                    onclick="cambiarTabDNS('regresadas')"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-regresadas">
                    <span class="material-symbols-outlined text-xl" data-icon="assignment_return">assignment_return</span>
                    Regresadas
                    <?php if ($totalRegresadas > 0): ?>
                        <span class="ml-2 bg-tertiary text-on-tertiary px-2.5 py-0.5 rounded-full text-xs font-bold"><?= $totalRegresadas ?></span>
                    <?php endif; ?>
                </button>
                
                <button 
                    id="tab-sanciones" 
                    class="tab-button flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest"
                    onclick="cambiarTabDNS('sanciones')"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-sanciones">
                    <span class="material-symbols-outlined text-xl" data-icon="policy">policy</span>
                    Sanciones
                    <?php if ($totalSanciones > 0): ?>
                        <span class="ml-2 bg-error text-on-error px-2.5 py-0.5 rounded-full text-xs font-bold"><?= $totalSanciones ?></span>
                    <?php endif; ?>
                </button>
            </nav>
        </div>

        <!-- Tab Panels -->
        <div class="p-8">
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 1: NUEVAS ASIGNACIONES (Estado: TURNADA_DNS)             -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-turnadas" class="tab-panel" role="tabpanel" aria-labelledby="tab-turnadas">
                <?php if (isset($denunciasTurnadas) && !empty($denunciasTurnadas)): ?>
                    <div class="space-y-4">
                        <div class="bg-secondary-container/20 rounded-xl p-6 border-l-4 border-secondary">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary text-2xl" data-icon="inbox">inbox</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">Nuevas Asignaciones</h3>
                                    <p class="text-xs text-secondary">Estado: TURNADA_DNS • Tomar caso para iniciar revisión</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $denunciasTurnadas,
                            'titulo' => 'Denuncias Disponibles',
                            'descripcion' => 'Casos sin asignar que requieren revisión normativa',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver', 'asignar'],
                            'mostrarFiltros' => true,
                            'pager' => $pager ?? null
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="inbox">inbox</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay nuevas asignaciones</h3>
                        <p class="text-sm text-secondary">Todas las denuncias han sido tomadas o no hay casos pendientes.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 2: CASOS EN REVISIÓN (Estado: EN_REVISION_DNS)           -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-revisiones" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-revisiones">
                <?php if (isset($misRevisiones) && !empty($misRevisiones)): ?>
                    <div class="space-y-4">
                        <div class="bg-warning-container/20 rounded-xl p-6 border-l-4 border-warning">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-warning text-2xl animate-pulse" data-icon="assignment">assignment</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">⚖️ Mis Casos en Revisión</h3>
                                    <p class="text-xs text-secondary">Estado: EN_REVISION_DNS • Requieren análisis y decisión</p>
                                </div>
                            </div>
                            <div class="bg-warning-container/30 rounded-lg p-3 mt-3 text-xs text-on-warning-container">
                                <span class="material-symbols-outlined text-xs align-middle mr-1" data-icon="gavel">gavel</span>
                                <strong>Pendiente:</strong> Analizar normativa y decidir: aprobar para inspección o rechazar por no competencia.
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $misRevisiones,
                            'titulo' => 'Mis Casos en Revisión Normativa',
                            'descripcion' => 'Casos que debo analizar y resolver',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver', 'editar'],
                            'mostrarFiltros' => true
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="assignment">assignment</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay casos en revisión</h3>
                        <p class="text-sm text-secondary">No tienes casos asignados para revisión en este momento.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 3: REGRESADAS DE INSPECCIÓN (Estado: REGRESADA_DNS)      -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-regresadas" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-regresadas">
                <?php if (isset($regresadasDS) && !empty($regresadasDS)): ?>
                    <div class="space-y-4">
                        <div class="bg-tertiary-fixed/20 rounded-xl p-6 border-l-4 border-tertiary">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-tertiary text-2xl" data-icon="assignment_return">assignment_return</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">↩️ Regresadas de Inspección</h3>
                                    <p class="text-xs text-secondary">Estado: REGRESADA_DNS • Requieren análisis de resultados</p>
                                </div>
                            </div>
                            <div class="bg-tertiary-container/20 rounded-lg p-3 mt-3 text-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-xs align-middle mr-1" data-icon="find_in_page">find_in_page</span>
                                <strong>Análisis post-inspección:</strong> Revisar acta y determinar si procede sanción o cierre.
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $regresadasDS,
                            'titulo' => 'Casos Post-Inspección',
                            'descripcion' => 'Analizar resultados y decidir siguiente acción',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver', 'editar'],
                            'mostrarFiltros' => true
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="assignment_return">assignment_return</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay casos regresados</h3>
                        <p class="text-sm text-secondary">No hay casos que hayan regresado de inspección.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 4: PROCEDIMIENTO SANCIONADOR                              -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-sanciones" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-sanciones">
                <?php if (isset($enSancion) && !empty($enSancion)): ?>
                    <div class="space-y-4">
                        <div class="bg-error-container/20 rounded-xl p-6 border-l-4 border-error">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-error text-2xl" data-icon="policy">policy</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">📋 Procedimiento Sancionador</h3>
                                    <p class="text-xs text-secondary">Estado: EN_ELABORACION_SANCION/SANCIONADA</p>
                                </div>
                            </div>
                            <div class="bg-error-container/30 rounded-lg p-3 mt-3 text-xs text-on-error-container">
                                <span class="material-symbols-outlined text-xs align-middle mr-1" data-icon="description">description</span>
                                <strong>Elaboración:</strong> Redactar acta de sanción y documentos legales del procedimiento.
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $enSancion,
                            'titulo' => 'Sanciones en Proceso',
                            'descripcion' => 'Casos que requieren elaboración de actas y documentos legales',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver', 'editar'],
                            'mostrarFiltros' => true
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="policy">policy</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay sanciones en proceso</h3>
                        <p class="text-sm text-secondary">No hay casos en procedimiento sancionador actualmente.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para manejo de pestañas -->
<script>
function cambiarTabDNS(tabName) {
    // Ocultar todos los paneles
    const panels = document.querySelectorAll('.tab-panel');
    panels.forEach(panel => {
        panel.classList.add('hidden');
        panel.setAttribute('aria-hidden', 'true');
    });
    
    // Desactivar todos los botones
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('active', 'border-primary', 'text-primary');
        button.classList.add('border-transparent', 'text-on-surface-variant');
        button.setAttribute('aria-selected', 'false');
    });
    
    // Activar el panel seleccionado
    const activePanel = document.getElementById('panel-' + tabName);
    if (activePanel) {
        activePanel.classList.remove('hidden');
        activePanel.setAttribute('aria-hidden', 'false');
    }
    
    // Activar el botón seleccionado
    const activeButton = document.getElementById('tab-' + tabName);
    if (activeButton) {
        activeButton.classList.add('active', 'border-primary', 'text-primary');
        activeButton.classList.remove('border-transparent', 'text-on-surface-variant');
        activeButton.setAttribute('aria-selected', 'true');
    }
    
    // Guardar preferencia en sessionStorage
    sessionStorage.setItem('dashboard_dns_active_tab', tabName);
}

// Restaurar tab activo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('dashboard_dns_active_tab');
    if (savedTab && document.getElementById('panel-' + savedTab)) {
        cambiarTabDNS(savedTab);
    }
});
</script>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_asignar') ?>
<?= view('admin/components/modal_gestionar_caso') ?>
<?= view('admin/components/modal_aprobar_inspeccion') ?>
<?= view('admin/components/modal_rechazar') ?>
<?= view('admin/components/modal_emitir_sancion') ?>
<?= view('admin/components/modal_desechar') ?>
