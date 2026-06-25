<?php
/**
 * 
 * Variables disponibles desde el controlador:
 * @var array $denunciasRecibidas Denuncias en estado RECIBIDA
 * @var array $todasDenuncias Todas las denuncias del sistema
 * @var int $totalRecibidas Cantidad de denuncias recibidas
 * @var int $totalDenuncias Total de denuncias
 * @var int $casosResueltos Casos finalizados exitosamente
 * @var int $casosPendientes Casos aún en proceso
 * @var int $casosCriticos Casos de prioridad alta
 * @var string $adminNombre Nombre del administrador (usado en header)
 * @var string $nombreRol Nombre del rol
 * @var string $nombreArea Nombre del área
 */
?>

<!-- Dashboard Body -->
<div class="p-8 space-y-12">
    <?php
    // Preparar datos para tarjetas de estadísticas
    $stats = [
        [
            'titulo' => 'Total de Denuncias',
            'valor' => $totalDenuncias ?? 0,
            'icono' => 'report',
            'estilo' => 'primary'
        ],
        [
            'titulo' => 'Casos Resueltos',
            'valor' => $casosResueltos ?? 0,
            'icono' => 'check_circle',
            'estilo' => 'success'
        ],
        [
            'titulo' => 'Pendientes de Revisión',
            'valor' => $casosPendientes ?? 0,
            'icono' => 'pending',
            'estilo' => 'warning'
        ],
        [
            'titulo' => 'Prioridad Crítica',
            'valor' => $casosCriticos ?? 0,
            'icono' => 'warning',
            'estilo' => 'error'
        ]
    ];
    ?>
    
    <!-- Stats Cards Component -->
    <?= view('admin/components/stats_cards', ['stats' => $stats]) ?>

    <!-- Denuncias Recibidas (Sin Turnar) -->
    <?php if (isset($denunciasRecibidas) && !empty($denunciasRecibidas)): ?>
    <div class="bg-secondary-container/20 rounded-xl p-6 border-l-4 border-secondary">
        <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-secondary text-3xl" data-icon="inbox">inbox</span>
            <div>
                <h3 class="text-xl font-headline font-extrabold text-primary">Denuncias Recibidas</h3>
                <p class="text-sm text-secondary">Requieren revisión y asignación a departamento</p>
            </div>
            <div class="ml-auto">
                <span class="bg-secondary text-white px-4 py-2 rounded-full text-lg font-bold">
                    <?= esc($totalRecibidas ?? count($denunciasRecibidas)) ?>
                </span>
            </div>
        </div>
        
        <?php
        // Configurar tabla para denuncias recibidas (sin paginación)
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $denunciasRecibidas,
            'titulo' => 'Nuevas Denuncias por Turnar',
            'descripcion' => 'Asignar a DNS o DS según corresponda',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'],
            'acciones' => ['ver', 'turnar'],
            'mostrarFiltros' => false
        ]);
        ?>
    </div>
    <?php endif; ?>

    <!-- Acceso rápido a Denuncias Asignadas -->
    <div class="bg-gradient-to-r from-tertiary/10 to-primary/10 rounded-xl p-6 border border-outline-variant/20">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-tertiary/10 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-tertiary text-2xl" data-icon="assignment">assignment</span>
                </div>
                <div>
                    <h3 class="text-lg font-headline font-bold text-primary">Denuncias Asignadas</h3>
                    <p class="text-sm text-secondary">Ver denuncias turnadas a departamentos</p>
                </div>
            </div>
            <a href="<?= base_url('admin/denuncias-asignadas') ?>" 
               class="primary-gradient text-white px-6 py-3 rounded-lg font-headline font-bold text-sm tracking-widest uppercase shadow-lg hover:shadow-primary/20 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
                Ver Denuncias
            </a>
        </div>
    </div>
</div>
    </main>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_turnar') ?>
<?= view('admin/components/modal_asignar') ?>
<?= view('admin/components/modal_desechar') ?>