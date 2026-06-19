<!DOCTYPE html>

<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Administrador | Sistema de Denuncias Ambientales</title>
    
    <!-- Stylesheets -->
    <link loading="preload" rel="stylesheet" href="<?php echo base_url('styles/admin.css'); ?>" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="icon" type="image/png" href="<?php echo base_url('img/favicon.ico'); ?>" />
    
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="<?php echo base_url('js/admin-actions.js'); ?>"></script>

    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary": "#515f74",
                        "outline": "#717973",
                        "on-secondary-fixed": "#0d1c2e",
                        "error-container": "#ffdad6",
                        "surface-container-lowest": "#ffffff",
                        "tertiary": "#002d1c",
                        "on-surface": "#191c1e",
                        "surface-tint": "#3f6653",
                        "surface-container-low": "#f2f4f6",
                        "on-error": "#ffffff",
                        "tertiary-fixed-dim": "#95d4b3",
                        "tertiary-fixed": "#b1f0ce",
                        "surface-container": "#eceef0",
                        "inverse-primary": "#a5d0b9",
                        "surface-variant": "#e0e3e5",
                        "primary-container": "#1b4332",
                        "secondary-fixed-dim": "#b9c7df",
                        "surface-bright": "#f7f9fb",
                        "secondary-container": "#d5e3fc",
                        "on-primary": "#ffffff",
                        "surface-container-high": "#e6e8ea",
                        "on-secondary-fixed-variant": "#3a485b",
                        "outline-variant": "#c1c8c2",
                        "on-secondary-container": "#57657a",
                        "on-secondary": "#ffffff",
                        "inverse-surface": "#2d3133",
                        "on-tertiary-fixed": "#002114",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#00452e",
                        "on-background": "#191c1e",
                        "primary-fixed-dim": "#a5d0b9",
                        "primary-fixed": "#c1ecd4",
                        "inverse-on-surface": "#eff1f3",
                        "secondary-fixed": "#d5e3fc",
                        "on-error-container": "#93000a",
                        "primary": "#012d1d",
                        "surface-dim": "#d8dadc",
                        "background": "#f7f9fb",
                        "on-tertiary-fixed-variant": "#0e5138",
                        "on-surface-variant": "#414844",
                        "surface": "#f7f9fb",
                        "surface-container-highest": "#e0e3e5",
                        "on-tertiary-container": "#75b393",
                        "on-primary-fixed": "#002114",
                        "on-primary-container": "#86af99",
                        "error": "#ba1a1a",
                        "on-primary-fixed-variant": "#274e3d"
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
                },
            },
        }
    </script>

</head>
<?php
// Determinar página activa
$currentPage = $currentPage ?? '';

// Obtener datos de sesión para control de acceso por rol
$session = session();
$codigoRol = $session->get('codigo_rol') ?? 'USR_CONSULTA';
$nivelAcceso = $session->get('nivel_acceso') ?? 3;
$esAdmin = in_array($codigoRol, ['ADM_GENERAL', 'ADM_DA', 'ADM']);

// Clases CSS para links
$activeClass = 'flex items-center gap-3 px-4 py-3 text-emerald-900 dark:text-emerald-50 font-semibold bg-white dark:bg-emerald-800/20 rounded-lg transition-all duration-200 hover:translate-x-1 active:opacity-80';
$inactiveClass = 'flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-all duration-200 hover:translate-x-1 active:opacity-80';
?>
<body class="bg-surface text-on-surface antialiased flex flex-col min-h-screen">
    <!-- SideNavBar -->
    <aside
        class="hidden md:flex flex-col h-screen w-64 fixed left-0 top-0 bg-[#eceef0] dark:bg-slate-900 z-40 border-r-0">
        <div class="flex flex-col h-full py-6">
            <!-- Brand Identity -->
            <div class="px-8 mb-10">
                <span class="font-manrope font-bold text-[#012d1d] text-xl tracking-tight">Administrador</span>
                <p class="text-xs text-on-surface-variant opacity-70 mt-1 uppercase tracking-widest">Sistema de Denuncias Ambientales</p>
            </div>
            <!-- Main Navigation Tabs -->
            <nav class="flex flex-col gap-1 flex-1" aria-label="Navegación principal">
                <!-- Nuevas denuncias - Visible para TODOS -->
                <a class="<?= $currentPage === 'inicio' ? $activeClass : $inactiveClass ?>"
                    href="<?= base_url('admin/inicio') ?>"
                    <?= $currentPage === 'inicio' ? 'aria-current="page"' : '' ?>>
                    <span class="material-symbols-outlined" data-icon="description">description</span>
                    <span class="font-inter text-sm antialiased">Nuevas denuncias</span>
                </a>

                <!-- Denuncias asignadas - SOLO ADMINISTRADORES -->
                <?php if ($esAdmin): ?>
                <a class="<?= $currentPage === 'denuncias-asignadas' ? $activeClass : $inactiveClass ?>"
                    href="<?= base_url('admin/denuncias-asignadas') ?>"
                    <?= $currentPage === 'denuncias-asignadas' ? 'aria-current="page"' : '' ?>>
                    <span class="material-symbols-outlined" data-icon="assignment">assignment</span>
                    <span class="font-inter text-sm antialiased">Denuncias asignadas</span>
                </a>
                <?php endif; ?>

                <!-- Archivo - Visible para TODOS -->
                <a class="<?= $currentPage === 'archivo' ? $activeClass : $inactiveClass ?>"
                    href="<?= base_url('admin/archivo') ?>"
                    <?= $currentPage === 'archivo' ? 'aria-current="page"' : '' ?>>
                    <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
                    <span class="font-inter text-sm antialiased">Archivo</span>
                </a>

                <!-- Gestión de Usuarios - SOLO ADMINISTRADORES -->
                <?php if ($esAdmin): ?>
                <a class="<?= $currentPage === 'usuarios' ? $activeClass : $inactiveClass ?>"
                    href="<?= base_url('admin/usuarios') ?>"
                    <?= $currentPage === 'usuarios' ? 'aria-current="page"' : '' ?>>
                    <span class="material-symbols-outlined" data-icon="group">group</span>
                    <span class="font-inter text-sm antialiased">Gestión de Usuarios</span>
                </a>
                <?php endif; ?>
                <!-- <a class="<?= $currentPage === 'configuracion' ? $activeClass : $inactiveClass ?>"
                    href="<?= base_url('admin/configuracion') ?>"
                    <?= $currentPage === 'configuracion' ? 'aria-current="page"' : '' ?>>
                    <span class="material-symbols-outlined" data-icon="settings">settings</span>
                    <span class="font-inter text-sm antialiased">Configuración</span>
                </a> -->
            </nav>
            <!-- CTA -->
            <div class="px-6 mt-6">
                <button
                    class="w-full py-3 bg-gradient-to-br from-primary to-primary-container text-white text-sm font-semibold rounded-lg shadow-sm hover:opacity-90 transition-all active:scale-95">
                    Generar Informe
                </button>
            </div>
            <!-- Footer Tabs -->
            <div class="mt-auto border-t border-outline-variant/10 pt-4">
                <a class="flex items-center px-6 py-2 text-[#515f74] dark:text-slate-400 hover:bg-[#f2f4f6] dark:hover:bg-slate-800/50 transition-all"
                    href="<?= base_url('admin/logout') ?>">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                    <span class="font-inter text-xs antialiased uppercase tracking-widest">Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="flex-1 md:ml-64 flex flex-col">
        <!-- TopAppBar -->
        <header class="docked full-width top-0 sticky z-50 bg-[#f7f9fb] dark:bg-slate-900/80 backdrop-blur-md">
            <div class="flex justify-between items-center w-full px-8 py-4 max-w-full">
                <div class="flex items-center gap-8">
                    <span class="text-2xl font-extrabold text-[#012d1d] dark:text-[#b1f0ce] tracking-tighter">Nuevas Denuncias</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative hidden sm:block">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm"
                            data-icon="search">search</span>
                        <input
                            class="pl-10 pr-4 py-2 bg-surface-container-lowest border-0 border-b border-outline-variant/30 focus:ring-0 focus:border-primary text-sm w-64 transition-all"
                            placeholder="Buscar en el archivo..." type="text" />
                    </div>
                    <!-- <button
                        class="material-symbols-outlined p-2 text-secondary hover:bg-surface-container transition-all rounded-full"
                        data-icon="notifications">notifications</button> -->
                    <div class="flex items-center gap-3 pl-4 border-l border-outline-variant/20">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-primary leading-tight"><?= esc($adminNombre ?? 'Administrador') ?></p>
                            <p class="text-[10px] text-secondary">Inspector Principal</p>
                        </div>
                        <span class="material-symbols-outlined text-3xl text-primary" data-icon="account_circle"
                            style="font-variation-settings: 'FILL' 1;">account_circle</span>
                    </div>
                </div>
            </div>
        </header>