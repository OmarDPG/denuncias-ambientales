<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- VISTA: Denuncias Asignadas (ADM_GENERAL / ADM_DA / ADM)                     -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- Este archivo se incluye entre header.php y footer.php - NO debe tener       -->
<!-- estructura HTML completa, solo el contenido del dashboard                   -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Vista de Denuncias Asignadas para roles ADM_GENERAL, ADM_DA y ADM
 * 
 * Funcionalidades:
 * - Vista de denuncias que ya han sido turnadas a departamentos
 * - Seguimiento de denuncias en proceso
 * - Visualización de área y usuario responsable
 * 
 * Variables disponibles desde el controlador:
 * @var array $denunciasAsignadas Denuncias que ya fueron turnadas (no RECIBIDA)
 * @var int $totalAsignadas Total de denuncias asignadas
 * @var object $pager Objeto de paginación
 * @var string $adminNombre Nombre del administrador (usado en header)
 * @var string $nombreRol Nombre del rol
 * @var string $nombreArea Nombre del área
 */
?>

<!-- Vista de Denuncias Asignadas -->
<div class="p-8 space-y-8">
    <!-- Encabezado de la sección -->
    <div class="bg-gradient-to-r from-primary/10 to-tertiary/10 rounded-2xl p-8 border border-outline-variant/20">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-primary text-4xl" data-icon="assignment">assignment</span>
            </div>
            <div class="flex-1">
                <h1 class="text-3xl font-headline font-extrabold text-primary">Denuncias Asignadas</h1>
                <p class="text-secondary mt-1">Seguimiento de denuncias turnadas a departamentos responsables</p>
            </div>
            <div class="text-right">
                <div class="text-4xl font-headline font-extrabold text-primary">
                    <?= esc($totalAsignadas ?? 0) ?>
                </div>
                <p class="text-sm text-secondary uppercase tracking-wider">Total Asignadas</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Denuncias Asignadas -->
    <?php
    echo view('admin/components/tabla_denuncias', [
        'denuncias' => $denunciasAsignadas ?? [],
        'titulo' => 'Denuncias en Proceso',
        'descripcion' => 'Denuncias turnadas con seguimiento de área y responsable asignado',
        'columnas' => ['folio', 'estado', 'area', 'responsable', 'fecha', 'acciones'],
        'acciones' => ['ver', 'asignar'],
        'mostrarFiltros' => true,
        'pager' => $pager ?? null
    ]);
    ?>
</div>
    </main>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
<?= view('admin/components/modal_asignar') ?>
