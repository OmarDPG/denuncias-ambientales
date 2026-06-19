<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD: Usuario DS (Departamento de Supervisión - Inspectores)           -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Este archivo se incluye entre header.php y footer.php - NO debe tener       -->
<!-- estructura HTML completa, solo el contenido del dashboard                   -->
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
 * @var array $misInspecciones Denuncias EN_INSPECCION asignadas al usuario
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
                    onclick="cambiarTab('turnadas')"
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
                    onclick="cambiarTab('activas')"
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
                    onclick="cambiarTab('concluidas')"
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
        <div class="p-8"><?= view('admin/components/stats_cards', ['stats' => $stats]) ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 1: NUEVAS ASIGNACIONES (Estado: TURNADA_DS)                  -->
    <!-- Permiso: Ver denuncias sin asignar en área DS                        -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($denunciasTurnadas) && !empty($denunciasTurnadas)): ?>
    <section id="denuncias-turnadas" class="bg-gradient-to-br from-secondary-container/30 to-secondary-container/10 rounded-2xl p-8 border-l-4 border-secondary shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-secondary/10 p-3 rounded-xl">
                <span class="material-symbols-outlined text-secondary text-4xl" data-icon="inbox">inbox</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">Nuevas Asignaciones</h2>
                <p class="text-sm text-secondary mt-1">Denuncias turnadas pendientes de tomar • Estado: TURNADA_DS</p>
            </div>
            <div class="ml-auto bg-secondary text-on-secondary px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalTurnadas ?>
            </div>
        </div>
        
        <div class="bg-surface/50 rounded-xl p-4 mb-4 border-l-2 border-secondary/30">
            <p class="text-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="info">info</span>
                <strong>Acción requerida:</strong> Tome un caso para asignarlo a su usuario e iniciar la inspección en campo.
            </p>
        </div>
        
        <?php
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $denunciasTurnadas,
            'titulo' => 'Casos Disponibles para Inspección',
            'descripcion' => 'Casos sin asignar que requieren inspección física',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
            'acciones' => ['ver', 'asignar'],
            'mostrarFiltros' => false,
            'pager' => $pager ?? null
        ]);
        ?>
    </section>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 2: INSPECCIONES EN CURSO (Estado: EN_INSPECCION)             -->
    <!-- Permiso: Editar/concluir inspecciones asignadas al usuario           -->
    <!-- Vista exclusiva: Solo inspector asignado puede gestionar             -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($inspeccionesActivas) && !empty($inspeccionesActivas)): ?>
    <section id="inspecciones-activas" class="bg-gradient-to-br from-error-container/30 to-error-container/10 rounded-2xl p-8 border-l-4 border-error shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-error/10 p-3 rounded-xl animate-pulse">
                <span class="material-symbols-outlined text-error text-4xl" data-icon="engineering">engineering</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">⚠️ Inspecciones en Curso</h2>
                <p class="text-sm text-secondary mt-1">Casos asignados a mí • Estado: EN_INSPECCION • Requieren acta y conclusión</p>
            </div>
            <div class="ml-auto bg-error text-on-error px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalActivas ?>
            </div>
        </div>
        
        <div class="bg-error-container/40 rounded-xl p-4 mb-4 border-l-2 border-error">
            <p class="text-sm text-on-error-container">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="warning">warning</span>
                <strong>Pendiente:</strong> Estos casos están bajo tu responsabilidad. Debes realizar la inspección en campo, documentar hallazgos y concluir con acta.
            </p>
        </div>
        
        <div class="bg-surface rounded-xl p-6 shadow-inner">
            <?php
            echo view('admin/components/tabla_denuncias', [
                'denuncias' => $inspeccionesActivas,
                'titulo' => 'Mis Inspecciones Activas',
                'descripcion' => 'Casos en proceso que debo concluir',
                'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                'acciones' => ['ver', 'editar'],
                'mostrarFiltros' => false
            ]);
            ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 3: INSPECCIONES CONCLUIDAS (Estado: INSPECCION_CONCLUIDA)    -->
    <!-- Permiso: Solo lectura • Pendientes de análisis por DNS               -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($inspeccionesConcluidas) && !empty($inspeccionesConcluidas)): ?>
    <section id="inspecciones-concluidas" class="bg-gradient-to-br from-tertiary-fixed/20 to-tertiary-fixed/5 rounded-2xl p-8 border-l-4 border-tertiary shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-tertiary/10 p-3 rounded-xl">
                <span class="material-symbols-outlined text-tertiary text-4xl" data-icon="task_alt">task_alt</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">✅ Inspecciones Concluidas</h2>
                <p class="text-sm text-secondary mt-1">Casos finalizados por mí • Estado: INSPECCION_CONCLUIDA • Pendientes de revisión DNS</p>
            </div>
            <div class="ml-auto bg-tertiary text-on-tertiary px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalConcluidas ?>
            </div>
        </div>
        
        <div class="bg-tertiary-container/30 rounded-xl p-4 mb-4 border-l-2 border-tertiary/50">
            <p class="text-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="check_circle">check_circle</span>
                <strong>Completado:</strong> Estos casos ya fueron inspeccionados y concluidos. Están en espera de análisis por Normativa y Sanciones (DNS).
            </p>
        </div>
        
        <?php
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $inspeccionesConcluidas,
            'titulo' => 'Casos Concluidos por Mí',
            'descripcion' => 'Inspecciones finalizadas - Solo lectura',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
            'acciones' => ['ver'],
            'mostrarFiltros' => false
        ]);
        ?>
    </section>
    <?php endif; ?>

    <!-- Mensaje si no hay denuncias -->
    <?php if (empty($denunciasTurnadas) && empty($inspeccionesActivas) && empty($inspeccionesConcluidas)): ?>
    <div class="bg-surface-container-lowest rounded-xl p-12 text-center">
        <span class="material-symbols-outlined text-secondary text-6xl mb-4" data-icon="engineering">engineering</span>
        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay inspecciones pendientes</h3>
        <p class="text-sm text-secondary">Todas las inspecciones han sido procesadas o no hay asignaciones nuevas.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_asignar') ?>
<?= view('admin/components/modal_gestionar_caso') ?>
<?= view('admin/components/modal_concluir_inspeccion') ?>
