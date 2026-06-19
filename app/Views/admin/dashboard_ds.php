<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD: Usuario DS (Departamento de Supervisión - Inspectores)           -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Sistema de pestañas para evitar saturación con múltiples registros          -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Dashboard para rol USR_DS
 * 
 * Funcionalidades:
 * - Tomar denuncias turnadas para inspección
 * - Realizar inspecciones en campo
 * - Subir actas de inspección
 * - Concluir inspecciones y regresar a DNS
 * 
 * Variables disponibles:
 * @var array $denunciasTurnadas Denuncias TURNADA_DS sin asignar
 * @var array $inspeccionesActivas Denuncias EN_INSPECCION asignadas al usuario
 * @var array $inspeccionesConcluidas Denuncias INSPECCION_CONCLUIDA finalizadas
 * @var string $adminNombre Nombre del usuario (usado en header)
 * @var string $nombreRol Nombre del rol
 * @var string $nombreArea Nombre del área
 */

$totalTurnadas = isset($denunciasTurnadas) ? count($denunciasTurnadas) : 0;
$totalActivas = isset($inspeccionesActivas) ? count($inspeccionesActivas) : 0;
$totalConcluidas = isset($inspeccionesConcluidas) ? count($inspeccionesConcluidas) : 0;
$totalInspecciones = $totalActivas + $totalConcluidas;
?>

<!-- Dashboard Body -->
<div class="p-8 space-y-8">
    <?php
    // Estadísticas para usuario DS (Inspectores)
    $stats = [
        [
            'titulo' => 'Nuevas Asignaciones',
            'valor' => $totalTurnadas,
            'icono' => 'inbox',
            'estilo' => 'primary'
        ],
        [
            'titulo' => 'Inspecciones Activas',
            'valor' => $totalActivas,
            'icono' => 'engineering',
            'estilo' => 'warning'
        ],
        [
            'titulo' => 'Inspecciones Concluidas',
            'valor' => $totalConcluidas,
            'icono' => 'task_alt',
            'estilo' => 'success'
        ],
        [
            'titulo' => 'Total Asignadas',
            'valor' => $totalInspecciones,
            'icono' => 'assignment',
            'estilo' => 'default'
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
            <nav class="flex" role="tablist" aria-label="Pestañas de inspección">
                <button 
                    id="tab-turnadas" 
                    class="tab-button active flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-primary text-primary"
                    onclick="cambiarTabDS('turnadas')"
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
                    id="tab-activas" 
                    class="tab-button flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest"
                    onclick="cambiarTabDS('activas')"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-activas">
                    <span class="material-symbols-outlined text-xl" data-icon="engineering">engineering</span>
                    Inspecciones en Curso
                    <?php if ($totalActivas > 0): ?>
                        <span class="ml-2 bg-error text-on-error px-2.5 py-0.5 rounded-full text-xs font-bold animate-pulse"><?= $totalActivas ?></span>
                    <?php endif; ?>
                </button>
                
                <button 
                    id="tab-concluidas" 
                    class="tab-button flex items-center gap-2 px-6 py-4 font-semibold text-sm transition-all border-b-2 border-transparent text-on-surface-variant hover:text-on-surface hover:bg-surface-container-highest"
                    onclick="cambiarTabDS('concluidas')"
                    role="tab"
                    aria-selected="false"
                    aria-controls="panel-concluidas">
                    <span class="material-symbols-outlined text-xl" data-icon="task_alt">task_alt</span>
                    Inspecciones Concluidas
                    <?php if ($totalConcluidas > 0): ?>
                        <span class="ml-2 bg-tertiary text-on-tertiary px-2.5 py-0.5 rounded-full text-xs font-bold"><?= $totalConcluidas ?></span>
                    <?php endif; ?>
                </button>
            </nav>
        </div>

        <!-- Tab Panels -->
        <div class="p-8">
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 1: NUEVAS ASIGNACIONES (Estado: TURNADA_DS)              -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-turnadas" class="tab-panel" role="tabpanel" aria-labelledby="tab-turnadas">
                <?php if (isset($denunciasTurnadas) && !empty($denunciasTurnadas)): ?>
                    <div class="space-y-4">
                        <div class="bg-secondary-container/20 rounded-xl p-6 border-l-4 border-secondary">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary text-2xl" data-icon="inbox">inbox</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">Nuevas Asignaciones</h3>
                                    <p class="text-xs text-secondary">Estado: TURNADA_DS • Tomar caso para iniciar inspección</p>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $denunciasTurnadas,
                            'titulo' => 'Casos Disponibles para Inspección',
                            'descripcion' => 'Casos sin asignar que requieren inspección física',
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
            <!-- PANEL 2: INSPECCIONES EN CURSO (Estado: EN_INSPECCION)         -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-activas" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-activas">
                <?php if (isset($inspeccionesActivas) && !empty($inspeccionesActivas)): ?>
                    <div class="space-y-4">
                        <div class="bg-error-container/20 rounded-xl p-6 border-l-4 border-error">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-error text-2xl animate-pulse" data-icon="engineering">engineering</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">⚠️ Inspecciones en Curso</h3>
                                    <p class="text-xs text-secondary">Estado: EN_INSPECCION • Requieren conclusión con acta</p>
                                </div>
                            </div>
                            <div class="bg-error-container/30 rounded-lg p-3 mt-3 text-xs text-on-error-container">
                                <span class="material-symbols-outlined text-xs align-middle mr-1" data-icon="warning">warning</span>
                                <strong>Pendiente:</strong> Casos bajo tu responsabilidad que requieren inspección en campo y conclusión.
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $inspeccionesActivas,
                            'titulo' => 'Mis Inspecciones Activas',
                            'descripcion' => 'Casos en proceso que debo concluir',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver', 'editar'],
                            'mostrarFiltros' => true
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="engineering">engineering</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay inspecciones activas</h3>
                        <p class="text-sm text-secondary">No tienes casos en proceso de inspección en este momento.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ═══════════════════════════════════════════════════════════════ -->
            <!-- PANEL 3: INSPECCIONES CONCLUIDAS (Estado: INSPECCION_CONCLUIDA) -->
            <!-- ═══════════════════════════════════════════════════════════════ -->
            <div id="panel-concluidas" class="tab-panel hidden" role="tabpanel" aria-labelledby="tab-concluidas">
                <?php if (isset($inspeccionesConcluidas) && !empty($inspeccionesConcluidas)): ?>
                    <div class="space-y-4">
                        <div class="bg-tertiary-fixed/20 rounded-xl p-6 border-l-4 border-tertiary">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-tertiary text-2xl" data-icon="task_alt">task_alt</span>
                                <div>
                                    <h3 class="text-lg font-headline font-bold text-primary">✅ Inspecciones Concluidas</h3>
                                    <p class="text-xs text-secondary">Estado: INSPECCION_CONCLUIDA • Pendientes de revisión DNS</p>
                                </div>
                            </div>
                            <div class="bg-tertiary-container/20 rounded-lg p-3 mt-3 text-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-xs align-middle mr-1" data-icon="check_circle">check_circle</span>
                                <strong>Completado:</strong> Casos finalizados en espera de análisis por Normativa y Sanciones.
                            </div>
                        </div>
                        
                        <?php
                        echo view('admin/components/tabla_denuncias', [
                            'denuncias' => $inspeccionesConcluidas,
                            'titulo' => 'Casos Concluidos por Mí',
                            'descripcion' => 'Inspecciones finalizadas - Solo lectura',
                            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                            'acciones' => ['ver'],
                            'mostrarFiltros' => true
                        ]);
                        ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-secondary text-6xl mb-4 block" data-icon="task_alt">task_alt</span>
                        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay inspecciones concluidas</h3>
                        <p class="text-sm text-secondary">Aún no has concluido ninguna inspección.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para manejo de pestañas -->
<script>
function cambiarTabDS(tabName) {
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
    sessionStorage.setItem('dashboard_ds_active_tab', tabName);
}

// Restaurar tab activo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const savedTab = sessionStorage.getItem('dashboard_ds_active_tab');
    if (savedTab && document.getElementById('panel-' + savedTab)) {
        cambiarTabDS(savedTab);
    }
});
</script>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_asignar') ?>
<?= view('admin/components/modal_gestionar_caso') ?>
<?= view('admin/components/modal_concluir_inspeccion') ?>
