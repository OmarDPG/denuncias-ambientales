<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD: Usuario DNS (Departamento de Normativa y Sanciones)              -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Este archivo se incluye entre header.php y footer.php - NO debe tener       -->
<!-- estructura HTML completa, solo el contenido del dashboard                   -->
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
<div class="p-8 space-y-12">
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
            'estilo' => 'warning'
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

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 1: NUEVAS ASIGNACIONES (Estado: TURNADA_DNS)                 -->
    <!-- Permiso: Ver denuncias sin asignar en área DNS                       -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($denunciasTurnadas) && !empty($denunciasTurnadas)): ?>
    <section id="denuncias-turnadas" class="bg-gradient-to-br from-secondary-container/30 to-secondary-container/10 rounded-2xl p-8 border-l-4 border-secondary shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-secondary/10 p-3 rounded-xl">
                <span class="material-symbols-outlined text-secondary text-4xl" data-icon="inbox">inbox</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">Nuevas Asignaciones</h2>
                <p class="text-sm text-secondary mt-1">Denuncias turnadas pendientes de tomar • Estado: TURNADA_DNS</p>
            </div>
            <div class="ml-auto bg-secondary text-on-secondary px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalTurnadas ?>
            </div>
        </div>
        
        <div class="bg-surface/50 rounded-xl p-4 mb-4 border-l-2 border-secondary/30">
            <p class="text-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="info">info</span>
                <strong>Acción requerida:</strong> Tome un caso para asignarlo a su usuario e iniciar la revisión normativa.
            </p>
        </div>
        
        <?php
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $denunciasTurnadas,
            'titulo' => 'Denuncias Disponibles',
            'descripcion' => 'Casos sin asignar que requieren revisión normativa',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
            'acciones' => ['ver', 'asignar'],
            'mostrarFiltros' => false,
            'pager' => $pager ?? null
        ]);
        ?>
    </section>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 2: CASOS EN REVISIÓN (Estado: EN_REVISION_DNS)               -->
    <!-- Permiso: Editar casos asignados al usuario DNS                       -->
    <!-- Vista exclusiva: Solo usuario DNS asignado puede gestionar          -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($misRevisiones) && !empty($misRevisiones)): ?>
    <section id="mis-revisiones" class="bg-gradient-to-br from-warning-container/30 to-warning-container/10 rounded-2xl p-8 border-l-4 border-warning shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-warning/10 p-3 rounded-xl animate-pulse">
                <span class="material-symbols-outlined text-warning text-4xl" data-icon="assignment">assignment</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">⚖️ Mis Casos en Revisión</h2>
                <p class="text-sm text-secondary mt-1">Casos asignados a mí • Estado: EN_REVISION_DNS • Requieren análisis y decisión</p>
            </div>
            <div class="ml-auto bg-warning text-on-warning px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalRevisiones ?>
            </div>
        </div>
        
        <div class="bg-warning-container/40 rounded-xl p-4 mb-4 border-l-2 border-warning">
            <p class="text-sm text-on-warning-container">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="gavel">gavel</span>
                <strong>Pendiente de revisión:</strong> Estos casos están bajo tu responsabilidad. Debes analizar la normativa aplicable y decidir: aprobar para inspección o rechazar por no competencia.
            </p>
        </div>
        
        <div class="bg-surface rounded-xl p-6 shadow-inner">
            <?php
            echo view('admin/components/tabla_denuncias', [
                'denuncias' => $misRevisiones,
                'titulo' => 'Mis Casos en Revisión Normativa',
                'descripcion' => 'Casos que debo analizar y resolver',
                'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                'acciones' => ['ver', 'editar'],
                'mostrarFiltros' => false
            ]);
            ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 3: REGRESADAS DE INSPECCIÓN (Estado: REGRESADA_DNS)          -->
    <!-- Permiso: Analizar resultados de inspección y decidir siguiente paso  -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($regresadasDS) && !empty($regresadasDS)): ?>
    <section class="bg-gradient-to-br from-tertiary-fixed/20 to-tertiary-fixed/5 rounded-2xl p-8 border-l-4 border-tertiary shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-tertiary/10 p-3 rounded-xl">
                <span class="material-symbols-outlined text-tertiary text-4xl" data-icon="assignment_return">assignment_return</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">↩️ Regresadas de Inspección</h2>
                <p class="text-sm text-secondary mt-1">Casos post-inspección • Estado: REGRESADA_DNS • Requieren análisis de resultados</p>
            </div>
            <div class="ml-auto bg-tertiary text-on-tertiary px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalRegresadas ?>
            </div>
        </div>
        
        <div class="bg-tertiary-container/30 rounded-xl p-4 mb-4 border-l-2 border-tertiary/50">
            <p class="text-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="find_in_page">find_in_page</span>
                <strong>Análisis post-inspección:</strong> Revisar acta de inspección y hallazgos. Determinar si procede iniciar procedimiento sancionador o concluir el caso.
            </p>
        </div>
        
        <?php
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $regresadasDS,
            'titulo' => 'Casos Post-Inspección',
            'descripcion' => 'Analizar resultados y decidir siguiente acción',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
            'acciones' => ['ver', 'editar'],
            'mostrarFiltros' => false
        ]);
        ?>
    </section>
    <?php endif; ?>

    <!-- ════════════════════════════════════════════════════════════════════ -->
    <!-- SECCIÓN 4: PROCEDIMIENTO SANCIONADOR (Estado: EN_ELABORACION_SANCION) -->
    <!-- Permiso: Elaborar y emitir actas de sanción                          -->
    <!-- ════════════════════════════════════════════════════════════════════ -->

    <?php if (isset($enSancion) && !empty($enSancion)): ?>
    <section id="sanciones" class="bg-gradient-to-br from-error-container/30 to-error-container/10 rounded-2xl p-8 border-l-4 border-error shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="bg-error/10 p-3 rounded-xl">
                <span class="material-symbols-outlined text-error text-4xl" data-icon="policy">policy</span>
            </div>
            <div>
                <h2 class="text-2xl font-headline font-extrabold text-primary">📋 Procedimiento Sancionador</h2>
                <p class="text-sm text-secondary mt-1">Elaboración de sanciones • Estado: EN_ELABORACION_SANCION/SANCIONADA</p>
            </div>
            <div class="ml-auto bg-error text-on-error px-4 py-2 rounded-full font-bold text-lg">
                <?= $totalSanciones ?>
            </div>
        </div>
        
        <div class="bg-error-container/40 rounded-xl p-4 mb-4 border-l-2 border-error">
            <p class="text-sm text-on-error-container">
                <span class="material-symbols-outlined text-sm align-middle" data-icon="description">description</span>
                <strong>Elaboración de documentos legales:</strong> Redactar acta de sanción, notificaciones y documentos oficiales del procedimiento sancionador.
            </p>
        </div>
        
        <div class="bg-surface rounded-xl p-6 shadow-inner">
            <?php
            echo view('admin/components/tabla_denuncias', [
                'denuncias' => $enSancion,
                'titulo' => 'Sanciones en Proceso',
                'descripcion' => 'Casos que requieren elaboración de actas y documentos legales',
                'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
                'acciones' => ['ver', 'editar'],
                'mostrarFiltros' => false
            ]);
            ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Mensaje si no hay denuncias -->
    <?php if (empty($denunciasTurnadas) && empty($misRevisiones) && empty($regresadasDS) && empty($enSancion)): ?>
    <div class="bg-surface-container-lowest rounded-xl p-12 text-center">
        <span class="material-symbols-outlined text-secondary text-6xl mb-4" data-icon="inbox">inbox</span>
        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay denuncias pendientes</h3>
        <p class="text-sm text-secondary">Todas las denuncias han sido procesadas o no hay asignaciones nuevas.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_asignar') ?>
<?= view('admin/components/modal_gestionar_caso') ?>
<?= view('admin/components/modal_aprobar_inspeccion') ?>
<?= view('admin/components/modal_rechazar') ?>
<?= view('admin/components/modal_emitir_sancion') ?>
<?= view('admin/components/modal_desechar') ?>
