<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Tabla de Denuncias                                              -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Componente de tabla de denuncias para dashboards
 * 
 * Variables esperadas:
 * @var array $denuncias Array de denuncias a mostrar
 * @var string $titulo Título de la tabla
 * @var string $descripcion Descripción de la tabla
 * @var array $columnas Array con las columnas a mostrar
 *     Opciones: 'folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'area', 'responsable', 'acciones'
 * @var array $acciones Array con las acciones disponibles
 *     Opciones: 'ver', 'turnar', 'asignar', 'aprobar', 'rechazar', 'editar'
 * @var bool $mostrarFiltros Mostrar botones de filtro
 * @var object|null $pager Objeto paginador de CodeIgniter (opcional)
 */

$denuncias = $denuncias ?? [];
$titulo = $titulo ?? 'Registro de Denuncias';
$descripcion = $descripcion ?? 'Monitoreo y gestión de denuncias ambientales';
$columnas = $columnas ?? ['folio', 'categoria', 'ubicacion', 'fecha', 'estado', 'acciones'];
$acciones = $acciones ?? ['ver'];
$mostrarFiltros = $mostrarFiltros ?? false;
$pager = $pager ?? null;
?>

<!-- Management Table Section -->
<section class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-headline font-extrabold text-primary tracking-tight"><?= esc($titulo) ?></h2>
            <p class="text-sm text-secondary"><?= esc($descripcion) ?></p>
        </div>

        <?php if ($mostrarFiltros): ?>
        <!-- Filters -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
            <button onclick="filterByStatus('all')" data-filter="all" 
                    class="filter-btn px-4 py-2 bg-primary text-white text-xs font-bold rounded-full transition-all">
                Todos
            </button>
            <button onclick="filterByStatus('RECIBIDA')" data-filter="RECIBIDA"
                    class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">
                Recibidas
            </button>
            <button onclick="filterByStatus('EN_REVISION')" data-filter="EN_REVISION"
                    class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">
                En Revisión
            </button>
            <button onclick="filterByStatus('EN_INSPECCION')" data-filter="EN_INSPECCION"
                    class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">
                En Inspección
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Authoritative Data Table -->
    <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container">
                        <?php if (in_array('folio', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Folio</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('categoria', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Categoría</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('ubicacion', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Ubicación</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('fecha', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Fecha</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('area', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Área</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('responsable', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Responsable</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('estado', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant whitespace-nowrap">Estado</th>
                        <?php endif; ?>
                        
                        <?php if (in_array('acciones', $columnas)): ?>
                        <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant text-right whitespace-nowrap">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y-0">
                    <?php if (!empty($denuncias)): ?>
                        <?php foreach ($denuncias as $i => $denuncia): ?>
                            <tr data-denuncia-id="<?= esc($denuncia['id_denuncia'] ?? '') ?>" 
                                data-estado="<?= esc($denuncia['codigo_estado'] ?? '') ?>"
                                class="<?= $i % 2 !== 0 ? 'bg-surface-container-low' : '' ?> hover:bg-surface transition-colors border-b border-outline-variant/5">
                                
                                <?php if (in_array('folio', $columnas)): ?>
                                <td class="px-6 py-5 text-sm font-medium text-primary whitespace-nowrap">
                                    #<?= esc($denuncia['folio'] ?? $denuncia['id_denuncia'] ?? 'N/A') ?>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('categoria', $columnas)): ?>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-lg" data-icon="report">report</span>
                                        <span class="text-sm text-on-surface"><?= esc($denuncia['tipo_denuncia'] ?? 'Sin categoría') ?></span>
                                    </div>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('ubicacion', $columnas)): ?>
                                <td class="px-6 py-5 text-sm text-secondary max-w-xs truncate">
                                    <?= esc($denuncia['ubicacion'] ?? $denuncia['municipio'] ?? '—') ?>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('fecha', $columnas)): ?>
                                <td class="px-6 py-5 text-sm text-secondary whitespace-nowrap">
                                    <?= esc($denuncia['fecha_denuncia'] ?? $denuncia['fecha_creacion'] ?? '—') ?>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('area', $columnas)): ?>
                                <td class="px-6 py-5 text-sm text-secondary whitespace-nowrap">
                                    <?= esc($denuncia['nombre_area'] ?? '—') ?>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('responsable', $columnas)): ?>
                                <td class="px-6 py-5 text-sm text-secondary whitespace-nowrap">
                                    <?= esc($denuncia['responsable'] ?? 'Sin asignar') ?>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('estado', $columnas)): ?>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <?php
                                    $nombreEstado = $denuncia['nombre_estado'] ?? 'Pendiente';
                                    $codigoEstado = $denuncia['codigo_estado'] ?? '';
                                    
                                    // Mapeo de estilos de badges
                                    $badgeClasses = [
                                        'RECIBIDA' => 'bg-secondary-container text-on-secondary-container',
                                        'TURNADA_DNS' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'EN_REVISION_DNS' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'APROBADA_INSPECCION' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'TURNADA_DS' => 'bg-secondary-container text-on-secondary-container',
                                        'EN_INSPECCION' => 'bg-error-container text-on-error-container',
                                        'INSPECCION_CONCLUIDA' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'REGRESADA_DNS' => 'bg-secondary-container text-on-secondary-container',
                                        'EN_ELABORACION_SANCION' => 'bg-error-container text-on-error-container',
                                        'SANCIONADA' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'FINALIZADA' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                        'RECHAZADA' => 'bg-surface-container-high text-secondary',
                                        'DESESTIMADA' => 'bg-surface-container-high text-secondary',
                                        'ARCHIVADA' => 'bg-surface-container-high text-secondary',
                                    ];
                                    
                                    $badgeClass = isset($badgeClasses[$codigoEstado]) 
                                        ? $badgeClasses[$codigoEstado] 
                                        : 'bg-secondary-container text-on-secondary-container';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                                        <?= esc($nombreEstado) ?>
                                    </span>
                                </td>
                                <?php endif; ?>
                                
                                <?php if (in_array('acciones', $columnas)): ?>
                                <td class="px-6 py-5 text-right space-x-2 whitespace-nowrap">
                                    <?php if (in_array('ver', $acciones)): ?>
                                    <button class="p-2 text-secondary hover:text-primary transition-colors" 
                                            title="Ver Detalles"
                                            onclick="openDetailModal('<?= esc($denuncia['id_denuncia'] ?? '') ?>')">
                                        <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('turnar', $acciones)): ?>
                                    <button class="p-2 text-secondary hover:text-primary transition-colors" 
                                            title="Turnar"
                                            onclick="openTurnarModal('<?= esc($denuncia['id_denuncia'] ?? '') ?>')">
                                        <span class="material-symbols-outlined" data-icon="send">send</span>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('asignar', $acciones)): ?>
                                    <button class="p-2 text-secondary hover:text-primary transition-colors" 
                                            title="Asignar"
                                            onclick="openAsignarModal('<?= esc($denuncia['id_denuncia'] ?? '') ?>')">
                                        <span class="material-symbols-outlined" data-icon="person_add">person_add</span>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if (in_array('editar', $acciones)): ?>
                                    <button class="p-2 text-secondary hover:text-primary transition-colors" 
                                            title="Editar"
                                            onclick="editarDenuncia('<?= esc($denuncia['id_denuncia'] ?? '') ?>')">
                                        <span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
                                    </button>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= count($columnas) ?>" class="px-6 py-12 text-center text-sm text-secondary">
                                No se encontraron denuncias.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pager && is_object($pager) && $pager->getPageCount() > 0): ?>
        <div class="flex flex-col md:flex-row justify-between items-center px-6 py-4 bg-surface-container-lowest border-t border-outline-variant/10 gap-4">
            <p class="text-xs text-secondary font-medium uppercase tracking-widest">
                Mostrando <?= esc($pager->getPerPage()) ?> de <?= esc($pager->getTotal()) ?> registros
                (Página <?= esc($pager->getCurrentPage()) ?> de <?= esc($pager->getPageCount()) ?>)
            </p>
            
            <?php if ($pager->getPageCount() > 1): ?>
            <div class="flex gap-2 items-center">
                <!-- Botón Primera Página -->
                <?php if ($pager->getCurrentPage() > 1): ?>
                <a href="<?= esc($pager->getPageURI(1)) ?>" 
                   class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all"
                   title="Primera página">
                    <span class="material-symbols-outlined text-sm" data-icon="first_page">first_page</span>
                </a>
                <?php endif; ?>
                
                <!-- Botón Anterior -->
                <?php if ($pager->getCurrentPage() > 1): ?>
                <a href="<?= esc($pager->getPreviousPageURI()) ?>" 
                   class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all"
                   title="Página anterior">
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
                </a>
                <?php else: ?>
                <button class="p-2 border border-outline-variant/10 rounded opacity-50 cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
                </button>
                <?php endif; ?>
                
                <!-- Números de Página -->
                <?php 
                $currentPage = $pager->getCurrentPage();
                $pageCount = $pager->getPageCount();
                $range = 2; // Mostrar 2 páginas a cada lado de la actual
                
                $start = max(1, $currentPage - $range);
                $end = min($pageCount, $currentPage + $range);
                
                // Mostrar primera página si no está en el rango
                if ($start > 1): ?>
                    <a href="<?= esc($pager->getPageURI(1)) ?>" 
                       class="px-3 py-1 border border-outline-variant/30 rounded hover:bg-surface-container text-xs font-bold transition-all">
                        1
                    </a>
                    <?php if ($start > 2): ?>
                    <span class="text-secondary">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Páginas en rango -->
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                    <button class="px-3 py-1 bg-primary text-white text-xs font-bold rounded shadow-sm">
                        <?= esc($i) ?>
                    </button>
                    <?php else: ?>
                    <a href="<?= esc($pager->getPageURI($i)) ?>" 
                       class="px-3 py-1 border border-outline-variant/30 rounded hover:bg-surface-container text-xs font-bold transition-all">
                        <?= esc($i) ?>
                    </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- Mostrar última página si no está en el rango -->
                <?php if ($end < $pageCount): ?>
                    <?php if ($end < $pageCount - 1): ?>
                    <span class="text-secondary">...</span>
                    <?php endif; ?>
                    <a href="<?= esc($pager->getPageURI($pageCount)) ?>" 
                       class="px-3 py-1 border border-outline-variant/30 rounded hover:bg-surface-container text-xs font-bold transition-all">
                        <?= esc($pageCount) ?>
                    </a>
                <?php endif; ?>
                
                <!-- Botón Siguiente -->
                <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                <a href="<?= esc($pager->getNextPageURI()) ?>" 
                   class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all"
                   title="Página siguiente">
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
                </a>
                <?php else: ?>
                <button class="p-2 border border-outline-variant/10 rounded opacity-50 cursor-not-allowed" disabled>
                    <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
                </button>
                <?php endif; ?>
                
                <!-- Botón Última Página -->
                <?php if ($pager->getCurrentPage() < $pager->getPageCount()): ?>
                <a href="<?= esc($pager->getPageURI($pager->getPageCount())) ?>" 
                   class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all"
                   title="Última página">
                    <span class="material-symbols-outlined text-sm" data-icon="last_page">last_page</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Sin paginación: mostrar solo contador -->
        <div class="flex justify-between items-center px-6 py-4 bg-surface-container-lowest border-t border-outline-variant/10">
            <p class="text-xs text-secondary font-medium uppercase tracking-widest">
                Mostrando <?= esc(count($denuncias)) ?> registro<?= count($denuncias) !== 1 ? 's' : '' ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Función de filtrado por estado
function filterByStatus(status) {
    const rows = document.querySelectorAll('tbody tr[data-denuncia-id]');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Actualizar estilos de botones
    buttons.forEach(btn => {
        if (btn.getAttribute('data-filter') === status) {
            btn.classList.remove('bg-surface-container', 'text-secondary', 'hover:bg-surface-container-high');
            btn.classList.add('bg-primary', 'text-white');
        } else {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-surface-container', 'text-secondary', 'hover:bg-surface-container-high');
        }
    });
    
    // Filtrar filas
    rows.forEach(row => {
        const estadoRow = row.getAttribute('data-estado');
        if (status === 'all' || estadoRow.includes(status)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
