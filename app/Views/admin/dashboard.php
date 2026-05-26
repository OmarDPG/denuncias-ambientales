
        <!-- Dashboard Body -->
        <div class="p-8 space-y-12">
            <!-- Hero Stats Section (Bento Inspired) -->
            <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div
                    class="md:col-span-1 p-6 bg-surface-container-lowest rounded-xl flex flex-col justify-between group hover:bg-primary transition-all duration-300 border border-outline-variant/5">
                    <div>
                        <span class="material-symbols-outlined text-primary group-hover:text-primary-fixed transition-colors"
                            data-icon="report">report</span>
                        <p class="text-sm font-label text-secondary mt-4 group-hover:text-primary-fixed/70">Total de Denuncias</p>
                    </div>
                    <h3 class="text-4xl font-headline font-extrabold text-primary group-hover:text-white mt-2">
                        <?= esc($totalDenuncias ?? 0) ?>
                    </h3>
                </div>
                <div
                    class="md:col-span-1 p-6 bg-surface-container-lowest rounded-xl flex flex-col justify-between border border-outline-variant/5">
                    <div>
                        <span class="material-symbols-outlined text-tertiary-fixed-dim" data-icon="check_circle"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <p class="text-sm font-label text-secondary mt-4">Casos Resueltos</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-headline font-extrabold text-primary"><?= esc($casosResueltos ?? 0) ?></h3>
                    </div>
                </div>
                <div
                    class="md:col-span-1 p-6 bg-surface-container-lowest rounded-xl flex flex-col justify-between border border-outline-variant/5">
                    <div>
                        <span class="material-symbols-outlined text-secondary" data-icon="pending"
                            style="font-variation-settings: 'FILL' 1;">pending</span>
                        <p class="text-sm font-label text-secondary mt-4">Pendientes de Revisión</p>
                    </div>
                    <h3 class="text-4xl font-headline font-extrabold text-primary"><?= esc($casosPendientes ?? 0) ?></h3>
                </div>
                <div
                    class="md:col-span-1 p-6 bg-error-container/20 border border-error/10 rounded-xl flex flex-col justify-between">
                    <div>
                        <span class="material-symbols-outlined text-error" data-icon="warning"
                            style="font-variation-settings: 'FILL' 1;">warning</span>
                        <p class="text-sm font-label text-error mt-4 font-semibold uppercase tracking-wider">Prioridad Crítica</p>
                    </div>
                    <h3 class="text-4xl font-headline font-extrabold text-error"><?= esc($casosCriticos ?? 0) ?></h3>
                </div>
            </section>

            <!-- Management Table Section -->
            <section class="space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-headline font-extrabold text-primary tracking-tight">Registro de Denuncias
                            Ambientales</h2>
                        <p class="text-sm text-secondary">Monitoreo de integridad ecológica en distintas jurisdicciones.</p>
                    </div>
                    <!-- Filters -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
                        <button onclick="filterByStatus('all')" data-filter="all" class="filter-btn px-4 py-2 bg-primary text-white text-xs font-bold rounded-full transition-all">Todos los
                            Casos</button>
                        <!-- <button onclick="filterByStatus('Pendiente')" data-filter="Pendiente"
                        class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">Pendiente</button> -->
                        <button onclick="filterByStatus('En Revisión')" data-filter="En Revisión"
                        class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">En revisión</button>
                        <button onclick="filterByStatus('Investigación')" data-filter="Investigación"
                        class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">Investigación</button>
                        <!-- <button onclick="filterByStatus('Resuelta')" data-filter="Resuelta"
                        class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">Resueltos</button> -->
                        <button onclick="filterByStatus('Desechada')" data-filter="Desechada"
                        class="filter-btn px-4 py-2 bg-surface-container text-secondary text-xs font-bold rounded-full hover:bg-surface-container-high transition-all">Desechados</button>
                    </div>
                </div>

                <!-- Authoritative Data Table -->
                <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm border border-outline-variant/10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container">
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">ID</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Categoría</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Ubicación</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Fecha Reportada</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant">Estado</th>
                                <th class="px-6 py-4 text-xs font-label uppercase tracking-widest text-on-surface-variant text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            <?php if (!empty($denuncias)): ?>
                                <?php foreach ($denuncias as $i => $denuncia): ?>
                                    <tr data-denuncia-id="<?= $denuncia['id_denuncia'] ?>" class="<?= $i % 2 !== 0 ? 'bg-surface-container-low' : '' ?> hover:bg-surface transition-colors border-b border-outline-variant/5">
                                        <td class="px-6 py-5 text-sm font-medium text-primary">#<?= esc($denuncia['folio'] ?? $denuncia['id_denuncia']) ?></td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-primary text-lg" data-icon="report">report</span>
                                                <span class="text-sm text-on-surface"><?= esc($denuncia['tipo_denuncia'] ?? 'Sin categoría') ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-sm text-secondary"><?= esc($denuncia['ubicacion'] ?? '—') ?></td>
                                        <td class="px-6 py-5 text-sm text-secondary"><?= esc($denuncia['fecha_denuncia'] ?? '—') ?></td>
                                        <td class="px-6 py-5">
                                            <?php
                                            $estatus = mb_strtolower($denuncia['estatus'] ?? 'pendiente', 'UTF-8');

                                            $badgeMap = [
                                                'investigación'     => ['label' => 'Investigación',      'class' => 'bg-error-container text-on-error-container'],
                                                'resuelta'    => ['label' => 'Resuelta',     'class' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant'],
                                                'desechada'   => ['label' => 'Desechada',    'class' => 'bg-surface-container-high text-secondary'],
                                                'en revisión' => ['label' => 'En Revisión',  'class' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant'],
                                                'pendiente'   => ['label' => 'Pendiente',    'class' => 'bg-secondary-container text-on-secondary-container'],
                                            ];

                                            $badge = isset($badgeMap[$estatus])
                                                ? $badgeMap[$estatus]
                                                : ['label' => ucfirst($estatus), 'class' => 'bg-secondary-container text-on-secondary-container'];
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $badge['class'] ?>">
                                                <?= esc($badge['label']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-right space-x-2">
                                            <button class="p-2 text-secondary hover:text-primary transition-colors" title="Ver Detalles"
                                                onclick="openDetailModal('<?= esc($denuncia['id_denuncia']) ?>')">
                                                <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                                            </button>
                                            <button class="p-2 text-secondary hover:text-primary transition-colors" title="Cambiar Estado"
                                                onclick="openStatusModal('<?= esc($denuncia['id_denuncia']) ?>')">
                                                <span class="material-symbols-outlined" data-icon="edit_note">edit_note</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-secondary">No se encontraron denuncias registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center px-6 py-4 bg-surface-container-lowest border-t border-outline-variant/10">
                        <p class="text-xs text-secondary font-medium uppercase tracking-widest">
                            Mostrando <?= esc(count($denuncias ?? [])) ?> registros
                        </p>
                        <div class="flex gap-2">
                            <button class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all">
                                <span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
                            </button>
                            <button class="px-3 py-1 bg-primary text-white text-xs font-bold rounded">1</button>
                            <button class="p-2 border border-outline-variant/30 rounded hover:bg-surface-container transition-all">
                                <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal de Detalles de Denuncia -->
        <div id="complaintDetailModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeDetailModalOnOverlay(event)">
            <div class="bg-surface-container-lowest rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div
                    class="sticky top-0 bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 class="font-headline font-extrabold text-2xl">Detalles de la Denuncia</h2>
                        <p class="text-sm text-primary-fixed opacity-90 mt-1" id="detailModalFolio">Folio: —</p>
                    </div>
                    <button onclick="closeDetailModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="overflow-y-auto max-h-[calc(80vh-140px)] px-8 py-6 space-y-8">

                    <!-- Información del Reporte -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Información del Reporte</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Info del reporte -->
                        </div>
                    </section>

                    <!-- Datos del Denunciante -->
                    <section class="space-y-4">
                        <h3 class="font-headline font-bold text-lg text-primary border-b-2 border-primary/20 pb-2">Datos del Denunciante</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Tipo de Persona</p>
                                <p class="font-medium text-on-surface" id="detailTipoPersona" style="text-transform: capitalize;">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre Completo</p>
                                <p class="font-medium text-on-surface" id="detailNombre">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Género</p>
                                <p class="font-medium text-on-surface" id="detailGenero" style="text-transform: capitalize;">—</p>
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
                        <div class="pt-4">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Dirección</p>
                            <p class="font-medium text-on-surface" id="detailDireccion">—</p>
                        </div>
                        <div class="pt-4">
                            <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Representante Legal</p>
                            <p class="font-medium text-on-surface" id="detailRepresentante">N/A</p>
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
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre de la Entidad</p>
                                <p class="font-medium text-on-surface" id="detailNombreEntidad">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Nombre del Representante</p>
                                <p class="font-medium text-on-surface" id="detailNombreRepresentante">—</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-label uppercase tracking-widest text-secondary">Dirección del Denunciado</p>
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
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
                    <button onclick="closeDetailModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cerrar
                    </button>
                    <div class="flex gap-3">
                        <button
                            class="px-6 py-2 bg-surface-container text-primary border border-outline-variant/30 rounded-lg font-headline font-bold text-sm hover:bg-surface-container-high transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">edit_note</span>
                            Cambiar Estado
                        </button>
                        <button
                            class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                            Exportar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Cambio de Estado -->
        <div id="changeStatusModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeStatusModalOnOverlay(event)">
            <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col"
                onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div
                    class="flex-shrink-0 bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 class="font-headline font-extrabold text-2xl">Cambiar Estado de Denuncia</h2>
                        <p class="text-sm text-primary-fixed opacity-90 mt-1" id="statusModalFolio">Folio: —</p>
                    </div>
                    <button onclick="closeStatusModal()" class="text-white hover:bg-white/20 p-2 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">
                    <!-- Estado Actual -->
                    <div class="bg-surface-container-low rounded-lg p-4 border-l-4 border-primary">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary mb-2">Estado Actual</p>
                        <span id="currentStatusBadge"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-secondary-container text-on-secondary-container">
                            Pendiente
                        </span>
                    </div>

                    <!-- Formulario de Cambio de Estado -->
                    <form id="changeStatusForm" class="space-y-6">
                        <?= csrf_field() ?>
                        <input type="hidden" id="statusIdDenuncia" name="id_denuncia" value="" />
                        <div class="space-y-2">
                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Nuevo Estado <span class="text-error">*</span>
                            </label>
                            <select id="newStatus" name="estatus" required
                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body">
                                <option value="">Seleccione un estado...</option>
                                <option value="En Revisión">En Revisión</option>
                                <option value="Investigación">Investigación</option>
                                <option value="Desechada">Desechada</option>
                                <option value="Resuelta">Resuelta</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Resolución / Comentarios <span class="text-error">*</span>
                            </label>
                            <textarea id="resolutionNotes" name="notas_internas" required
                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body resize-none"
                                placeholder="Describa la resolución, acciones tomadas o comentarios relevantes..." rows="6"></textarea>
                            <p class="text-xs text-secondary mt-1">
                                Proporcione información detallada sobre el cambio de estado y las acciones realizadas.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Fecha de Resolución (opcional)
                            </label>
                            <input type="date" id="resolutionDate" name="fecha_resolucion"
                                class="w-full bg-surface-container-low border-0 border-b-2 border-outline-variant/40 focus:border-primary focus:ring-0 py-3 text-on-surface font-body" />
                        </div>

                        <!-- Campo de Documentación Adjunta (solo para estado Resuelta) -->
                        <div id="resolutionDocsField" class="space-y-2" style="display: none;">
                            <label class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Documentación Adjunta <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" id="resolutionDocs" name="documentos_resolucion[]" multiple
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="w-full bg-surface-container-low border-2 border-dashed border-outline-variant/40 focus:border-primary rounded-lg py-3 px-4 text-on-surface font-body file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-bold file:text-xs hover:file:opacity-90 transition-all" />
                            </div>
                            <div class="flex items-start gap-2 mt-2">
                                <span class="material-symbols-outlined text-secondary text-sm">info</span>
                                <p class="text-xs text-secondary">
                                    Adjunte los documentos oficiales de resolución (oficios, notificaciones, etc.). Formatos permitidos: PDF, JPG, PNG. Máximo 25MB por archivo.
                                </p>
                            </div>
                            <div id="resolutionDocsPreview" class="mt-3 space-y-2"></div>
                        </div>

                        <div class="bg-secondary-container/20 rounded-lg p-4 flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl mt-0.5">info</span>
                            <div class="text-xs text-secondary leading-relaxed">
                                <p class="font-bold mb-1">Nota Importante:</p>
                                <p>El cambio de estado quedará registrado en el historial de la denuncia. Se enviará una notificación
                                    automática al denunciante informando sobre la actualización.</p>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="flex-shrink-0 bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
                    <button onclick="closeStatusModal()" type="button"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button onclick="saveStatusChange()" type="button"
                        class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="<?= base_url('js/admin.js') ?>"></script>
    <script>
        const BASE_URL = '<?= base_url() ?>';
        <?php
        $complaintsJs = [];
        foreach ($denuncias ?? [] as $d) {
            $key = (string) $d['id_denuncia'];
            $esMoral = !empty($d['denunciado_es_moral']);
            
            // Filtrar evidencias para esta denuncia
            $evidenciasActuales = [];
            foreach ($archivosDenuncias ?? [] as $archivo) {
                if ((int)$archivo['id_denuncia'] === (int)$d['id_denuncia']) {
                    $evidenciasActuales[] = [
                        'id'             => $archivo['id_evidencia'],
                        'nombre'         => $archivo['nombre_original'],
                        'ruta'           => $archivo['ruta_archivo'],
                        'tipo'           => $archivo['tipo_archivo'],
                        'peso'           => $archivo['peso_bytes'],
                        'fechaSubida'    => $archivo['fecha_subida'] ?? '',
                    ];
                }
            }
            
            $complaintsJs[$key] = [
                'folio'      => $d['folio'] ?? ('#' . $d['id_denuncia']),
                'status'     => $d['estatus'] ?? 'pendiente',
                'statusText' => ucfirst($d['estatus'] ?? 'pendiente'),
                'denunciante' => [
                    'tipoPersona'       => $d['tipo_persona'] ?? '',
                    'nombre'            => $d['nombre_completo'] ?? '',
                    'genero'            => $d['genero'] ?? '',
                    'email'             => $d['email'] ?? '',
                    'telefono'          => $d['telefono'] ?? '',
                    'direccion'         => [
                        'asentamiento'   => $d['colonia'] ?? '',
                        'calle'          => $d['calle'] ?? '',
                        'numeroExterior' => $d['numero_exterior'] ?? '',
                        'numeroInterior' => $d['numero_interior'] ?? '',
                        'codigoPostal'   => $d['codigo_postal'] ?? '',
                        'localidad'      => $d['municipio'] ?? '',
                        'municipio'      => $d['municipio'] ?? '',
                        'estado'         => $d['estado'] ?? '',
                    ],
                    'representanteLegal' => !empty($d['nombre_representante']) ? $d['nombre_representante'] : 'N/A',
                ],
                'denuncia' => [
                    'tipoDenuncia' => $d['tipo_denuncia'] ?? '',
                    'hechos'       => $d['hechos_denunciados'] ?? '',
                    'lat'          => $d['latitud'] ?? '',
                    'lon'          => $d['longitud'] ?? '',
                ],
                'denunciado' => [
                    'nombreEntidad'       => $esMoral ? ($d['razon_social_denunciado'] ?? '') : ($d['nombre_denunciado'] ?? ''),
                    'nombreRepresentante' => $d['nombre_denunciado'] ?? 'N/A',
                    'direccion'           => [
                        'asentamiento'   => $d['colonia_denunciado'] ?? '',
                        'calle'          => $d['calle_denunciado'] ?? '',
                        'numeroExterior' => $d['numero_exterior_denunciado'] ?? '',
                        'codigoPostal'   => $d['codigo_postal_denunciado'] ?? '',
                        'localidad'      => $d['municipio_denunciado'] ?? '',
                        'municipio'      => $d['municipio_denunciado'] ?? '',
                        'estado'         => $d['estado'] ?? '',
                    ],
                ],
                'evidencias' => $evidenciasActuales,
                'fecha'      => $d['fecha_captura'] ?? '',
            ];
        }
        ?>
        Object.assign(complaintsDatabase, <?= json_encode($complaintsJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
    </script>