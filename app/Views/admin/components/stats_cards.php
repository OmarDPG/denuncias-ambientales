<!-- ════════════════════════════════════════════════════════════════════════════ -->
<!-- COMPONENTE: Tarjetas de Estadísticas                                        -->
<!-- Compatible con: PHP 7.4+, CodeIgniter 4                                     -->
<!-- ════════════════════════════════════════════════════════════════════════════ -->
<?php
/**
 * Componente de tarjetas de estadísticas para dashboards
 * 
 * Variables esperadas:
 * @var array $stats Array con las estadísticas a mostrar
 *     Estructura: [
 *         ['titulo' => string, 'valor' => int, 'icono' => string, 'estilo' => string],
 *         ...
 *     ]
 * 
 * Estilos disponibles:
 * - 'primary' (default): Fondo hover con color primario
 * - 'success': Para casos resueltos
 * - 'warning': Para casos pendientes
 * - 'error': Para casos críticos o urgentes
 */

$stats = $stats ?? [];
?>

<!-- Hero Stats Section (Bento Inspired) -->
<section class="grid grid-cols-1 md:grid-cols-<?= count($stats) ?> gap-6">
    <?php foreach ($stats as $index => $stat): ?>
        <?php
        $titulo = esc($stat['titulo'] ?? 'Sin título');
        $valor = esc($stat['valor'] ?? 0);
        $icono = esc($stat['icono'] ?? 'bar_chart');
        $estilo = $stat['estilo'] ?? 'default';

        // Mapeo de estilos
        $estilosMap = [
            'primary' => 'bg-surface-container-lowest rounded-xl flex flex-col justify-between group hover:bg-primary transition-all duration-300 border border-outline-variant/5',
            'success' => 'bg-surface-container-lowest rounded-xl flex flex-col justify-between border border-outline-variant/5',
            'warning' => 'bg-surface-container-lowest rounded-xl flex flex-col justify-between border border-outline-variant/5',
            'error' => 'bg-error-container/20 border border-error/10 rounded-xl flex flex-col justify-between',
            'default' => 'bg-surface-container-lowest rounded-xl flex flex-col justify-between border border-outline-variant/5'
        ];

        $claseContenedor = isset($estilosMap[$estilo]) ? $estilosMap[$estilo] : $estilosMap['default'];

        // Estilos de icono y texto
        $iconoClass = 'text-primary group-hover:text-primary-fixed transition-colors';
        $tituloClass = 'text-sm font-label text-secondary mt-4 group-hover:text-primary-fixed/70';
        $valorClass = 'text-4xl font-headline font-extrabold text-primary group-hover:text-white mt-2';

        if ($estilo === 'success') {
            $iconoClass = 'text-tertiary-fixed-dim';
            $tituloClass = 'text-sm font-label text-secondary mt-4';
            $valorClass = 'text-4xl font-headline font-extrabold text-primary';
        } elseif ($estilo === 'warning') {
            $iconoClass = 'text-secondary';
            $tituloClass = 'text-sm font-label text-secondary mt-4';
            $valorClass = 'text-4xl font-headline font-extrabold text-primary';
        } elseif ($estilo === 'error') {
            $iconoClass = 'text-error';
            $tituloClass = 'text-sm font-label text-error mt-4 font-semibold uppercase tracking-wider';
            $valorClass = 'text-4xl font-headline font-extrabold text-error';
        }

        $iconFill = ($estilo === 'success' || $estilo === 'warning' || $estilo === 'error') ? '1' : '0';
        ?>

        <div class="md:col-span-1 p-6 <?= $claseContenedor ?>">
            <div>
                <span class="material-symbols-outlined <?= $iconoClass ?>" 
                      data-icon="<?= $icono ?>"
                      style="font-variation-settings: 'FILL' <?= $iconFill ?>;"><?= $icono ?></span>
                <p class="<?= $tituloClass ?>"><?= $titulo ?></p>
            </div>
            <h3 class="<?= $valorClass ?>">
                <?= $valor ?>
            </h3>
        </div>
    <?php endforeach; ?>
</section>
