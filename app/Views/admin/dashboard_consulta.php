<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD: Usuario de Solo Consulta                                         -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Este archivo se incluye entre header.php y footer.php - NO debe tener       -->
<!-- estructura HTML completa, solo el contenido del dashboard                   -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Dashboard para rol USR_CONSULTA
 * 
 * Funcionalidades:
 * - Visualización de todas las denuncias (solo lectura)
 * - Consulta de detalles y documentos
 * - Sin capacidad de modificar estados
 * - Sin capacidad de asignar o turnar
 * 
 * Variables disponibles:
 * @var array $todasDenuncias Todas las denuncias del sistema (solo lectura)
 * @var string $adminNombre Nombre del usuario (usado en header)
 * @var string $nombreRol Nombre del rol
 */

$totalDenuncias = isset($todasDenuncias) ? count($todasDenuncias) : 0;

// Calcular estadísticas de solo lectura
$estadoRecibidas = 0;
$estadoEnProceso = 0;
$estadoFinalizadas = 0;

if (isset($todasDenuncias) && is_array($todasDenuncias)) {
    foreach ($todasDenuncias as $denuncia) {
        $idEstado = $denuncia['id_estado_actual'] ?? 0;
        
        if ($idEstado == 1) {
            $estadoRecibidas++;
        } elseif (in_array($idEstado, [11, 20, 21, 22])) {
            $estadoFinalizadas++;
        } else {
            $estadoEnProceso++;
        }
    }
}
?>

<!-- Dashboard Body -->
<div class="p-8 space-y-12">
    <?php
    // Estadísticas para usuario de consulta
    $stats = [
        [
            'titulo' => 'Total de Denuncias',
            'valor' => $totalDenuncias,
            'icono' => 'folder_open',
            'estilo' => 'primary'
        ],
        [
            'titulo' => 'Recibidas',
            'valor' => $estadoRecibidas,
            'icono' => 'inbox',
            'estilo' => 'default'
        ],
        [
            'titulo' => 'En Proceso',
            'valor' => $estadoEnProceso,
            'icono' => 'pending',
            'estilo' => 'warning'
        ],
        [
            'titulo' => 'Finalizadas',
            'valor' => $estadoFinalizadas,
            'icono' => 'check_circle',
            'estilo' => 'success'
        ]
    ];
    ?>
    
    <?= view('admin/components/stats_cards', ['stats' => $stats]) ?>

    <!-- Banner Informativo -->
    <div class="bg-secondary-container/20 rounded-xl p-6 border-l-4 border-secondary">
        <div class="flex items-start gap-4">
            <span class="material-symbols-outlined text-secondary text-3xl" data-icon="info">info</span>
            <div class="flex-1">
                <h3 class="text-lg font-headline font-bold text-primary mb-2">Modo de Solo Lectura</h3>
                <p class="text-sm text-secondary leading-relaxed">
                    Tiene acceso de consulta a todas las denuncias del sistema. Puede visualizar detalles, 
                    historial y documentos, pero no puede modificar estados, turnar o asignar denuncias.
                </p>
            </div>
        </div>
    </div>

    <!-- Todas las Denuncias -->
    <div id="todas-denuncias">
        <?php
        echo view('admin/components/tabla_denuncias', [
            'denuncias' => $todasDenuncias ?? [],
            'titulo' => 'Registro Completo de Denuncias',
            'descripcion' => 'Vista de todas las denuncias del sistema - Solo consulta',
            'columnas' => ['folio', 'categoria', 'ubicacion', 'fecha', 'area', 'responsable', 'estado', 'acciones'],
            'acciones' => ['ver'],
            'mostrarFiltros' => true,
            'pager' => $pager ?? null
        ]);
        ?>
    </div>

    <!-- Mensaje si no hay denuncias -->
    <?php if (empty($todasDenuncias)): ?>
    <div class="bg-surface-container-lowest rounded-xl p-12 text-center">
        <span class="material-symbols-outlined text-secondary text-6xl mb-4" data-icon="folder_open">folder_open</span>
        <h3 class="text-xl font-headline font-bold text-primary mb-2">No hay denuncias registradas</h3>
        <p class="text-sm text-secondary">Aún no se han registrado denuncias en el sistema.</p>
    </div>
    <?php endif; ?>

    <!-- Información Adicional -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ayuda -->
        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/10">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-primary text-2xl" data-icon="help">help</span>
                <h3 class="text-lg font-headline font-bold text-primary">¿Necesita Ayuda?</h3>
            </div>
            <p class="text-sm text-secondary mb-4">
                Si necesita realizar modificaciones o tiene preguntas sobre alguna denuncia, 
                contacte al administrador o al departamento correspondiente.
            </p>
            <ul class="space-y-2 text-sm text-secondary">
                <li class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-xs mt-0.5" data-icon="chevron_right">chevron_right</span>
                    <span>Departamento DNS: normativa@puebla.gob.mx</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-xs mt-0.5" data-icon="chevron_right">chevron_right</span>
                    <span>Departamento DS: supervision@puebla.gob.mx</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-xs mt-0.5" data-icon="chevron_right">chevron_right</span>
                    <span>Administración: admin@puebla.gob.mx</span>
                </li>
            </ul>
        </div>

        <!-- Leyenda de Estados -->
        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant/10">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-primary text-2xl" data-icon="label">label</span>
                <h3 class="text-lg font-headline font-bold text-primary">Leyenda de Estados</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-secondary-container text-on-secondary-container">
                        Recibida
                    </span>
                    <span class="text-xs text-secondary">Denuncia ingresada al sistema</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-tertiary-fixed text-on-tertiary-fixed-variant">
                        En Revisión
                    </span>
                    <span class="text-xs text-secondary">Análisis por departamento DNS</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-error-container text-on-error-container">
                        En Inspección
                    </span>
                    <span class="text-xs text-secondary">Verificación en campo</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-tertiary-fixed text-on-tertiary-fixed-variant">
                        Finalizada
                    </span>
                    <span class="text-xs text-secondary">Caso cerrado exitosamente</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle (Solo Lectura) -->
<?= view('admin/components/modal_detalle') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Consulta (Solo Lectura) cargado');
    
    // Deshabilitar cualquier botón de acción que no sea "ver"
    document.querySelectorAll('button[onclick*="turnar"], button[onclick*="asignar"], button[onclick*="editar"]').forEach(function(btn) {
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.title = 'No disponible en modo de consulta';
    });
});
</script>
