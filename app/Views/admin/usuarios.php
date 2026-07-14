<?php
// ─── Guardia de sesión ─────────────────────────────────────────────────────────
if (! session()->get('id_adm')) {
    header('Location: ' . base_url('admin'));
    exit();
}

// Datos del administrador en sesión
$adminNombre   = esc(session()->get('nombre') . ' ' . session()->get('apellidoP'));
$adminUsuario  = esc(session()->get('usuario'));
$adminEsAdmin  = (bool) session()->get('adm');
?>

        <!-- ── Área de contenido ───────────────────────────────────────────────── -->
        <div class="p-8 space-y-12">

            <!-- Encabezado de página -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div class="space-y-2">
                    <h1 class="text-4xl font-extrabold font-manrope text-primary tracking-tight">Gestión de Usuarios</h1>
                    <p class="text-secondary max-w-xl body-lg">
                        Supervise el acceso institucional, asigne roles de administración y gestione credenciales para la plataforma.
                    </p>
                </div>
                <?php if ($adminEsAdmin): ?>
                <button type="button" onclick="openAddUserModal()"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-primary to-primary-container text-white font-manrope font-bold rounded-lg shadow-lg hover:shadow-primary/20 transition-all scale-100 active:scale-95">
                    <span class="material-symbols-outlined" data-icon="person_add" aria-hidden="true">person_add</span>
                    Agregar Nuevo Usuario
                </button>
                <?php endif; ?>
            </div>

            <!-- Notificaciones flash -->
            <?php if ($success = session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 bg-tertiary-fixed/30 border border-on-tertiary-fixed-variant/20 rounded-lg flex items-start gap-3" role="alert">
                <span class="material-symbols-outlined text-on-tertiary-fixed-variant text-xl">check_circle</span>
                <p class="text-on-tertiary-fixed-variant text-sm font-medium"><?= esc($success) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($error = session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 bg-error-container/30 border border-error/20 rounded-lg flex items-start gap-3" role="alert">
                <span class="material-symbols-outlined text-error text-xl">error</span>
                <p class="text-on-error-container text-sm font-medium"><?= esc($error) ?></p>
            </div>
            <?php endif; ?>

            <!-- ── Módulo de gestión ─────────────────────────────────────────────── -->
            <div class="bg-surface-container-lowest rounded-xl shadow-[0px_20px_40px_rgba(25,28,30,0.06)] overflow-hidden">

                <!-- Barra de filtros -->
                <div class="px-8 py-6 flex flex-col sm:flex-row gap-4 items-center justify-between bg-surface-container-low/30">
                    <div class="relative w-full sm:w-96">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline"
                            data-icon="search" aria-hidden="true">search</span>
                        <input id="searchInput" type="text"
                            class="w-full pl-12 pr-4 py-3 bg-surface-container-lowest border-none shadow-sm rounded-lg text-on-surface focus:ring-2 focus:ring-primary-fixed-dim transition-all"
                            placeholder="Buscar por nombre o correo electrónico..."
                            oninput="filtrarUsuarios()"
                            aria-label="Buscar usuarios" />
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <label for="filtroRol" class="text-sm font-label font-medium text-on-surface-variant">Filtrar por Rol:</label>
                        <select id="filtroRol" onchange="filtrarUsuarios()"
                            class="bg-surface-container-lowest border-none shadow-sm rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed-dim min-w-[140px]">
                            <option value="">Todos los Roles</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Inspector">Inspector</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="tablaUsuarios">
                        <thead>
                            <tr class="bg-surface-container text-on-surface-variant">
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest font-label" scope="col">Nombre</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest font-label" scope="col">Correo Electrónico</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest font-label" scope="col">Rol</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest font-label text-center" scope="col">Estado</th>
                                <th class="px-8 py-4 text-xs font-bold uppercase tracking-widest font-label text-right" scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            <?php if (! empty($usuarios)): ?>
                                <?php
                                $rowColors = [
                                    'even' => 'bg-surface-container-lowest',
                                    'odd'  => 'bg-surface-container-low/50',
                                ];
                                $avatarColors = [
                                    'bg-primary-fixed text-on-primary-fixed-variant',
                                    'bg-secondary-fixed-dim text-on-secondary-fixed',
                                    'bg-primary-fixed-dim text-on-primary-fixed-variant',
                                    'bg-tertiary-fixed-dim text-on-tertiary-fixed-variant',
                                ];
                                ?>
                                <?php foreach ($usuarios as $i => $usuario): ?>
                                    <?php
                                    $iniciales   = mb_strtoupper(mb_substr($usuario['nombre'], 0, 1) . mb_substr($usuario['apellidoP'], 0, 1), 'UTF-8');
                                    $nombreFull  = esc($usuario['nombre'] . ' ' . $usuario['apellidoP'] . ' ' . $usuario['apellidoM']);
                                    $rol         = $usuario['adm'] ? 'Administrador' : 'Inspector';
                                    $activo      = (bool) $usuario['activo'];
                                    $avatarClass = $avatarColors[$i % count($avatarColors)];
                                    $rowClass    = ($i % 2 === 0) ? $rowColors['even'] : $rowColors['odd'];
                                    ?>
                                    <tr class="<?= $rowClass ?> hover:bg-surface transition-colors"
                                        data-nombre="<?= mb_strtolower($usuario['nombre'] . ' ' . $usuario['apellidoP'], 'UTF-8') ?>"
                                        data-email="<?= esc(mb_strtolower($usuario['email'], 'UTF-8')) ?>"
                                        data-rol="<?= esc($rol) ?>">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-full <?= $avatarClass ?> flex items-center justify-center font-bold"
                                                    aria-hidden="true"><?= $iniciales ?></div>
                                                <span class="font-medium text-on-surface"><?= $nombreFull ?></span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-secondary"><?= esc($usuario['email']) ?></td>
                                        <td class="px-8 py-5">
                                            <span class="px-3 py-1 bg-secondary-container text-on-secondary-fixed-variant rounded-full text-xs font-semibold">
                                                <?= esc($rol) ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <?php if ($activo): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-fixed-variant" aria-hidden="true"></span>
                                                Activo
                                            </span>
                                            <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-surface-variant text-on-surface-variant rounded-full text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-outline" aria-hidden="true"></span>
                                                Inactivo
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <?php if ($adminEsAdmin): ?>
                                                <button type="button"
                                                    onclick="openEditUserModal('<?= esc($usuario['id_adm']) ?>')"
                                                    class="p-2 text-outline hover:text-primary transition-colors"
                                                    title="Editar usuario"
                                                    aria-label="Editar <?= $nombreFull ?>">
                                                    <span class="material-symbols-outlined text-xl" data-icon="edit">edit</span>
                                                </button>
                                                <button type="button"
                                                    onclick="openResetPasswordModal('<?= esc($usuario['id_adm']) ?>', '<?= $nombreFull ?>')"
                                                    class="p-2 text-outline hover:text-primary transition-colors"
                                                    title="Restablecer contraseña"
                                                    aria-label="Restablecer contraseña de <?= $nombreFull ?>">
                                                    <span class="material-symbols-outlined text-xl" data-icon="lock_reset">lock_reset</span>
                                                </button>
                                                <?php if ($activo): ?>
                                                <button type="button"
                                                    onclick="openDeactivateModal('<?= esc($usuario['id_adm']) ?>', '<?= $nombreFull ?>')"
                                                    class="p-2 text-outline hover:text-error transition-colors"
                                                    title="Desactivar usuario"
                                                    aria-label="Desactivar <?= $nombreFull ?>">
                                                    <span class="material-symbols-outlined text-xl" data-icon="person_off">person_off</span>
                                                </button>
                                                <?php else: ?>
                                                <button type="button"
                                                    onclick="openActivateModal('<?= esc($usuario['id_adm']) ?>', '<?= $nombreFull ?>')"
                                                    class="p-2 text-outline hover:text-primary transition-colors"
                                                    title="Activar usuario"
                                                    aria-label="Activar <?= $nombreFull ?>">
                                                    <span class="material-symbols-outlined text-xl" data-icon="person_check">person_check</span>
                                                </button>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center text-secondary">
                                        <span class="material-symbols-outlined text-4xl block mb-3 text-outline-variant">group_off</span>
                                        No se encontraron usuarios registrados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4 bg-surface-container-low/30">
                    <p class="text-sm text-on-surface-variant">
                        Mostrando <span class="font-bold"><?= count($usuarios ?? []) ?></span>
                        <?php if (! empty($totalUsuarios) && $totalUsuarios > count($usuarios ?? [])): ?>
                            de <span class="font-bold"><?= esc($totalUsuarios) ?></span>
                        <?php endif; ?>
                        usuario(s) institucional(es)
                    </p>
                    <?php if (! empty($pager)): ?>
                    <div class="flex items-center gap-2">
                        <?= $pager->links() ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════════
             MODALES
        ════════════════════════════════════════════════════════════════════════ -->

        <!-- Modal: Agregar Usuario -->
        <div id="addUserModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeModalOnOverlay(event, 'addUserModal')"
            role="dialog" aria-modal="true" aria-labelledby="addUserModalTitle">
            <div class="bg-surface-container-lowest rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <!-- Cabecera del modal -->
                <div class="bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 id="addUserModalTitle" class="font-headline font-extrabold text-2xl">Agregar Nuevo Usuario</h2>
                        <p class="text-sm text-primary-fixed opacity-90 mt-1">Complete los datos del nuevo usuario del sistema</p>
                    </div>
                    <button type="button" onclick="closeAddUserModal()"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors"
                        aria-label="Cerrar modal">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="overflow-y-auto max-h-[calc(90vh-200px)] px-8 py-6">
                    <form id="addUserForm" class="space-y-6" novalidate>
                        <?= csrf_field() ?>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label for="addNombre" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Nombre(s) <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <input type="text" id="addNombre" name="nombre" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: María" autocomplete="given-name" />
                            </div>
                            <div class="space-y-2">
                                <label for="addApellidoP" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Apellido Paterno <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <input type="text" id="addApellidoP" name="apellidoP" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: García" autocomplete="family-name" />
                            </div>
                            <div class="space-y-2">
                                <label for="addApellidoM" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Apellido Materno
                                </label>
                                <input type="text" id="addApellidoM" name="apellidoM"
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: López" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="addCargo" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Cargo / Nivel de Acceso <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <select id="addCargo" name="adm" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Seleccione un cargo...</option>
                                    <option value="1">Administrador (Total)</option>
                                    <option value="0">Inspector / Usuario</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="addRol" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Rol en el Sistema <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <select id="addRol" name="id_rol" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Seleccione un rol...</option>
                                    <?php if (!empty($roles)): ?>
                                        <?php foreach($roles as $rolItem): ?>
                                            <option value="<?= esc($rolItem['id_rol']) ?>"><?= esc($rolItem['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="addCurp" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                CURP <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="addCurp" name="expediente" required maxlength="18"
                                pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}"
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase font-mono"
                                placeholder="GAML920515MDFRRR03"
                                autocomplete="off" />
                            <p class="text-xs text-secondary">18 caracteres. Ej: GAML920515MDFRRR03</p>
                        </div>

                        <div class="space-y-2">
                            <label for="addEmail" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Correo Electrónico <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="email" id="addEmail" name="email" required
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="usuario@institucion.org"
                                autocomplete="email" />
                        </div>

                        <div class="space-y-2">
                            <label for="addPassword" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Contraseña <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="password" id="addPassword" name="password" required minlength="8"
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Mínimo 8 caracteres"
                                autocomplete="new-password" />
                            <p class="text-xs text-secondary">Mínimo 8 caracteres. Se recomienda incluir mayúsculas, números y símbolos.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="addPasswordConfirm" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Confirmar Contraseña <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="password" id="addPasswordConfirm" name="password_confirm" required minlength="8"
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Repita la contraseña"
                                autocomplete="new-password" />
                        </div>
                    </form>
                </div>

                <!-- Pie del modal -->
                <div class="bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
                    <button type="button" onclick="closeAddUserModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveAddUser()"
                        class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">person_add</span>
                        Crear Usuario
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Usuario -->
        <div id="editUserModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeModalOnOverlay(event, 'editUserModal')"
            role="dialog" aria-modal="true" aria-labelledby="editUserModalTitle">
            <div class="bg-surface-container-lowest rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <div class="bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 id="editUserModalTitle" class="font-headline font-extrabold text-2xl">Editar Usuario</h2>
                        <p id="editUserSubtitle" class="text-sm text-primary-fixed opacity-90 mt-1">Modificar información del usuario</p>
                    </div>
                    <button type="button" onclick="closeEditUserModal()"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors"
                        aria-label="Cerrar modal">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-200px)] px-8 py-6">
                    <form id="editUserForm" class="space-y-6" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" id="editUserId" name="id_adm" />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <label for="editNombre" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Nombre(s) <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <input type="text" id="editNombre" name="nombre" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: María" autocomplete="given-name" />
                            </div>
                            <div class="space-y-2">
                                <label for="editApellidoP" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Apellido Paterno <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <input type="text" id="editApellidoP" name="apellidoP" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: García" autocomplete="family-name" />
                            </div>
                            <div class="space-y-2">
                                <label for="editApellidoM" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Apellido Materno
                                </label>
                                <input type="text" id="editApellidoM" name="apellidoM"
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Ej: López" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="editCargo" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Cargo / Nivel de Acceso <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <select id="editCargo" name="adm" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Seleccione un cargo...</option>
                                    <option value="1">Administrador (Total)</option>
                                    <option value="0">Inspector / Usuario</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="editRol" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                    Rol en el Sistema <span class="text-error" aria-hidden="true">*</span>
                                </label>
                                <select id="editRol" name="id_rol" required
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Seleccione un rol...</option>
                                    <?php if (!empty($roles)): ?>
                                        <?php foreach($roles as $rolItem): ?>
                                            <option value="<?= esc($rolItem['id_rol']) ?>"><?= esc($rolItem['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="editCurp" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                CURP <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="text" id="editCurp" name="expediente" required maxlength="18"
                                pattern="[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}"
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent uppercase font-mono"
                                autocomplete="off" />
                        </div>

                        <div class="space-y-2">
                            <label for="editEmail" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Correo Electrónico <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <input type="email" id="editEmail" name="email" required
                                class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                autocomplete="email" />
                        </div>
                    </form>
                </div>

                <div class="bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
                    <button type="button" onclick="closeEditUserModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveEditUser()"
                        class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">save</span>
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Confirmar Desactivar / Activar Usuario -->
        <div id="confirmActionModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeModalOnOverlay(event, 'confirmActionModal')"
            role="dialog" aria-modal="true" aria-labelledby="confirmActionTitle">
            <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <div class="bg-gradient-to-br from-error to-error/80 text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 id="confirmActionTitle" class="font-headline font-extrabold text-2xl">Confirmar Acción</h2>
                        <p id="confirmActionSubtitle" class="text-sm text-white/90 mt-1">Esta acción requiere confirmación</p>
                    </div>
                    <button type="button" onclick="closeConfirmActionModal()"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors"
                        aria-label="Cerrar modal">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <div class="px-8 py-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-error-container rounded-full" aria-hidden="true">
                            <span class="material-symbols-outlined text-error text-2xl" id="confirmActionIcon">warning</span>
                        </div>
                        <div class="flex-1">
                            <p id="confirmActionMessage" class="text-on-surface font-medium mb-2">
                                ¿Está seguro que desea continuar con esta acción?
                            </p>
                            <p id="confirmActionDetail" class="text-sm text-secondary">
                                Esta acción puede revertirse posteriormente.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeConfirmActionModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button type="button" onclick="executeAction()" id="confirmActionButton"
                        class="px-6 py-2 bg-error text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">check</span>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Restablecer Contraseña -->
        <div id="resetPasswordModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeModalOnOverlay(event, 'resetPasswordModal')"
            role="dialog" aria-modal="true" aria-labelledby="resetPasswordModalTitle">
            <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <div class="bg-gradient-to-br from-primary to-primary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 id="resetPasswordModalTitle" class="font-headline font-extrabold text-2xl">Restablecer Contraseña</h2>
                        <p id="resetPasswordSubtitle" class="text-sm text-primary-fixed opacity-90 mt-1">Cambiar contraseña del usuario</p>
                    </div>
                    <button type="button" onclick="closeResetPasswordModal()"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors"
                        aria-label="Cerrar modal">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <div class="px-8 py-6">
                    <div class="bg-surface-container-low rounded-lg p-4 mb-6 border-l-4 border-primary">
                        <p class="text-xs font-label uppercase tracking-widest text-secondary mb-1">Usuario</p>
                        <p id="resetPasswordUserName" class="font-headline font-bold text-lg text-primary"></p>
                    </div>

                    <form id="resetPasswordForm" class="space-y-6" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" id="resetPasswordUserId" name="id_adm" />

                        <div class="space-y-2">
                            <label for="resetNewPassword" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Nueva Contraseña <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="resetNewPassword" name="new_password" required minlength="8"
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pr-12"
                                    placeholder="Mínimo 8 caracteres"
                                    autocomplete="new-password" />
                                <button type="button"
                                    onclick="togglePasswordVisibility('resetNewPassword', 'toggleResetNewIcon')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-secondary hover:text-primary transition-colors"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <span class="material-symbols-outlined text-xl" id="toggleResetNewIcon">visibility</span>
                                </button>
                            </div>
                            <p class="text-xs text-secondary">Mínimo 8 caracteres. Se recomienda incluir mayúsculas, minúsculas, números y símbolos.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="resetConfirmPassword" class="font-headline font-bold text-sm text-primary uppercase tracking-wider">
                                Confirmar Nueva Contraseña <span class="text-error" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="resetConfirmPassword" name="new_password_confirm" required minlength="8"
                                    class="w-full px-4 py-3 bg-surface-container border border-outline-variant/30 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent pr-12"
                                    placeholder="Repita la nueva contraseña"
                                    autocomplete="new-password" />
                                <button type="button"
                                    onclick="togglePasswordVisibility('resetConfirmPassword', 'toggleResetConfirmIcon')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-secondary hover:text-primary transition-colors"
                                    aria-label="Mostrar u ocultar confirmación">
                                    <span class="material-symbols-outlined text-xl" id="toggleResetConfirmIcon">visibility</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-secondary-container/20 rounded-lg p-4 flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary text-xl mt-0.5" aria-hidden="true">info</span>
                            <div class="text-xs text-secondary leading-relaxed">
                                <p class="font-semibold mb-1">Recomendaciones de seguridad:</p>
                                <ul class="list-disc list-inside space-y-0.5">
                                    <li>Use al menos 8 caracteres</li>
                                    <li>Combine mayúsculas y minúsculas</li>
                                    <li>Incluya números y símbolos especiales</li>
                                    <li>Evite información personal obvia</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-between items-center">
                    <button type="button" onclick="closeResetPasswordModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmResetPassword()"
                        class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">lock_reset</span>
                        Restablecer Contraseña
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal: Confirmar Restablecimiento de Contraseña -->
        <div id="confirmResetPasswordModal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            onclick="closeModalOnOverlay(event, 'confirmResetPasswordModal')"
            role="dialog" aria-modal="true" aria-labelledby="confirmResetPasswordModalTitle">
            <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full overflow-hidden shadow-2xl"
                onclick="event.stopPropagation()">
                <div class="bg-gradient-to-br from-secondary to-secondary-container text-white px-8 py-6 flex justify-between items-center">
                    <div>
                        <h2 id="confirmResetPasswordModalTitle" class="font-headline font-extrabold text-2xl">Confirmar Cambio de Contraseña</h2>
                        <p class="text-sm text-white/90 mt-1">Verificación final requerida</p>
                    </div>
                    <button type="button" onclick="closeConfirmResetPasswordModal()"
                        class="text-white hover:bg-white/20 p-2 rounded-full transition-colors"
                        aria-label="Cerrar modal">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>
                </div>

                <div class="px-8 py-6">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-secondary-container/30 rounded-full" aria-hidden="true">
                            <span class="material-symbols-outlined text-secondary text-2xl">lock_reset</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-on-surface font-medium mb-2">
                                ¿Está seguro que desea restablecer la contraseña para
                                <span class="font-bold" id="confirmResetUserName"></span>?
                            </p>
                            <p class="text-sm text-secondary">
                                El usuario deberá usar la nueva contraseña en su próximo inicio de sesión.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container border-t border-outline-variant/20 px-8 py-4 flex justify-end gap-3">
                    <button type="button" onclick="closeConfirmResetPasswordModal()"
                        class="px-6 py-2 text-secondary border-2 border-secondary rounded-lg font-headline font-bold text-sm hover:bg-surface-container transition-all">
                        Cancelar
                    </button>
                    <button type="button" onclick="executeResetPassword()"
                        class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">check</span>
                        Confirmar Cambio
                    </button>
                </div>
            </div>
        </div>

    </main>

    <!-- Scripts -->
    <script>
        // URL base para peticiones AJAX (disponible para usuarios.js)
        const BASE_URL = '<?= base_url() ?>';
        const CSRF_TOKEN_NAME = '<?= csrf_token() ?>';
        const CSRF_HASH = '<?= csrf_hash() ?>';
    </script>
    <script src="<?= base_url('js/usuarios.js') ?>"></script>
