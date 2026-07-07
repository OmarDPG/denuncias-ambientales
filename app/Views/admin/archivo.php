        <!-- Page Content -->
        <div class="p-8 space-y-12">
            <!-- Page Header Section -->
            <section class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="font-headline font-extrabold text-4xl text-primary tracking-tight mb-2">Reportes Archivados</h2>
                    <p class="text-on-surface-variant max-w-2xl font-body">
                        Registro histórico de denuncias ambientales resueltas. Este archivo contiene todos los casos 
                        que han sido completamente atendidos y cerrados con documentación oficial de resolución.
                    </p>
                </div>
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_20px_40px_rgba(25,28,30,0.04)] flex flex-col items-center justify-center min-w-[200px]">
                    <span class="text-3xl font-headline font-extrabold text-primary"><?= number_format($totalArchivadas) ?></span>
                    <span class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold mt-1">Total Archivadas</span>
                </div>
            </section>

            <!-- Stats Cards -->
            <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                if (!empty($denuncias)) {
                    $tiposDenuncia = array_count_values(array_column($denuncias, 'tipo_denuncia'));
                    arsort($tiposDenuncia);
                    $topTipos = array_slice($tiposDenuncia, 0, 4, true);
                } else {
                    $topTipos = [];
                }
                
                $icons = [
                    'Impacto Ambiental' => 'nature',
                    'Residuos Especiales' => 'delete',
                    'Contaminación Atmosférica' => 'air',
                    'Contaminación Auditiva' => 'volume_up',
                    'Contaminación Visual' => 'visibility',
                    'Ordenamiento Territorial' => 'map'
                ];
                
                if (empty($topTipos)):
                ?>
                <div class="col-span-4 bg-surface-container-low p-6 rounded-xl text-center">
                    <p class="text-secondary">No hay estadísticas disponibles</p>
                </div>
                <?php else:
                    foreach ($topTipos as $tipo => $cantidad):
                        $icon = $icons[$tipo] ?? 'eco';
                    ?>
                <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-xl"><?= $icon ?></span>
                        </div>
                        <span class="text-2xl font-headline font-bold text-primary"><?= $cantidad ?></span>
                    </div>
                    <p class="text-xs text-secondary uppercase tracking-wider"><?= esc($tipo) ?></p>
                </div>
                    <?php 
                    endforeach;
                endif;
                ?>
            </section>

            <!-- Archive List Table -->
            <section
                class="bg-surface-container-lowest rounded-xl shadow-[0px_20px_40px_rgba(25,28,30,0.06)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="archivoTable">
                        <thead>
                            <tr class="bg-surface-container">
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Folio</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Fecha de Resolución</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Tipo de Denuncia</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Denunciante</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Ubicación</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            <?php if (empty($denuncias)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">
                                    No se encontraron denuncias archivadas.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php 
                                $rowClass = '';
                                foreach ($denuncias as $index => $denuncia): 
                                    $rowClass = ($index % 2 === 0) ? 'bg-surface-container-low' : '';
                                    
                                    $fechaResolucion = $denuncia['fecha_resolucion'] ?? $denuncia['fecha_actualizacion'] ?? '';
                                    $fechaFormateada = $fechaResolucion ? date('d M, Y', strtotime($fechaResolucion)) : '—';
                                    
                                    $ubicacion = trim(($denuncia['municipio'] ?? '') . ', ' . ($denuncia['estado'] ?? ''));
                                    $ubicacion = trim($ubicacion, ', ') ?: '—';
                                ?>
                            <tr class="<?= $rowClass ?> hover:bg-surface transition-colors border-b border-outline-variant/5"
                                data-denuncia-id="<?= esc($denuncia['folio']) ?>">
                                <td class="px-6 py-5 text-sm font-medium text-primary">
                                    <?= esc($denuncia['folio']) ?>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary">
                                    <?= esc($fechaFormateada) ?>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-tertiary-fixed/20 text-tertiary rounded-full text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm">eco</span>
                                        <?= esc($denuncia['tipo_denuncia']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary">
                                    <?= esc($denuncia['nombre_completo'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary">
                                    <?= esc($ubicacion) ?>
                                </td>
                                <td class="px-6 py-5 text-right space-x-2">
                                    <button onclick="openDetailModal(<?= esc($denuncia['id_denuncia']) ?>)"
                                        class="inline-flex items-center gap-1 px-3 py-2 bg-primary text-white rounded-lg text-xs font-bold hover:opacity-90 transition-all"
                                        title="Ver detalles completos">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                        Ver Detalles
                                    </button>
                                </td>
                            </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

<!-- Modales -->
<?= view('admin/components/modal_detalle') ?>
                    
                    <!-- Información del Reporte -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Información del Reporte</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Estado</p>
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed rounded-full text-xs font-bold">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    Resuelta
                                </span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Fecha de Captura</p>
                                <p class="font-medium text-on-surface" id="detailFechaCaptura">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Fecha de Resolución</p>
                                <p class="font-medium text-on-surface" id="detailFechaResolucion">—</p>
                            </div>
                        </div>
                    </section>

                    <!-- Datos del Denunciante -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Datos del Denunciante</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                        <!-- Dirección del Denunciante -->
                        <div class="pt-4">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Dirección</p>
                            <p class="font-medium text-on-surface" id="detailDireccion">—</p>
                        </div>

                        <!-- Datos Representante Legal (si aplica) -->
                        <div class="pt-4" id="detailRepresentanteContainer" style="display: none;">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Representante Legal</p>
                            <p class="font-medium text-on-surface" id="detailRepresentante">—</p>
                        </div>
                    </section>

                    <!-- Detalles de la Denuncia -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Detalles de la Denuncia</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Denuncia</p>
                                <p class="font-medium text-on-surface" id="detailTipoDenuncia">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Coordenadas</p>
                                <p class="font-mono text-sm text-on-surface" id="detailCoordenadas">—</p>
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
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Datos del Denunciado</h3>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre</p>
                                <p class="font-medium text-on-surface" id="detailNombreDenunciado">—</p>
                            </div>
                            <div class="space-y-1" id="detailRazonSocialContainer" style="display: none;">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Razón Social</p>
                                <p class="font-medium text-on-surface" id="detailRazonSocial">—</p>
                            </div>
                            <!-- Dirección del Denunciado -->
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Dirección</p>
                                <p class="font-medium text-on-surface" id="detailDireccionDenunciado">—</p>
                            </div>
                        </div>
                    </section>

                    <!-- Evidencias -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Evidencias Adjuntas</h3>
                        <div id="detailEvidencias" class="space-y-2">
                            <p class="text-secondary text-sm">No se adjuntaron evidencias</p>
                        </div>
                    </section>

                    <!-- Documento de Resolución -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Documento de Resolución</h3>
                        <div id="detailDocumentoResolucion" class="space-y-2">
                            <p class="text-secondary text-sm">No se encontró documento de resolución</p>
                        </div>
                    </section>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-end items-center gap-3">
                    <button onclick="closeDetailModal()" class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </main>

<script>
// Función para filtrar la tabla
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('archivoTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let txtValue = tr[i].textContent || tr[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = '';
        } else {
            tr[i].style.display = 'none';
        }
    }
}
</script>
