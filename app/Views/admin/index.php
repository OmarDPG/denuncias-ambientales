<!DOCTYPE html>

<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Iniciar Sesión | Denuncias Ambientales</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo base_url('styles/admin.css'); ?>" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="icon" type="image/png" href="<?php echo base_url('img/favicon.ico'); ?>" />

    <style>
        body {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }

        body.loaded {
            visibility: visible;
            opacity: 1;
        }
    </style>

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                },
            },
        }
    </script>
</head>

<body
    class="bg-surface font-body text-on-surface min-h-screen flex flex-col selection:bg-primary-fixed selection:text-on-primary-fixed">
    <main class="flex-grow flex items-center justify-center p-6 md:p-12 relative overflow-hidden">
        <!-- Background Accents -->
        <div
            class="absolute top-0 right-0 w-[50%] h-[50%] bg-primary-container opacity-[0.03] rounded-full blur-[120px] -mr-32 -mt-32">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[40%] h-[40%] bg-secondary opacity-[0.05] rounded-full blur-[100px] -ml-24 -mb-24">
        </div>
        <div class="w-full max-w-md z-10">
            <!-- Institutional Card -->
            <div
                class="bg-surface-container-lowest rounded-xl p-8 md:p-10 shadow-[0px_20px_40px_rgba(25,28,30,0.06)] ring-1 ring-outline-variant/10">
                <!-- Brand Header -->
                <div class="mb-10 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-container rounded-lg mb-6">
                        <span class="material-symbols-outlined text-on-primary text-2xl"
                            data-icon="account_balance">account_balance</span>
                    </div>
                    <h1 class="font-headline font-extrabold text-2xl tracking-tight text-primary">Denuncias Ambientales
                    </h1>
                    <p class="text-on-surface-variant font-body text-sm mt-2">Administración y Gestión de Denuncias Ambientales</p>
                </div>
                <!-- Mensaje de error -->
                <?php if ($error = session()->getFlashdata('error')): ?>
                    <div class="mb-6 p-3 bg-error-container border border-error/20 rounded-lg flex items-start gap-2">
                        <span class="material-symbols-outlined text-error text-base mt-0.5">error</span>
                        <p class="text-on-error-container text-sm font-body"><?= esc($error) ?></p>
                    </div>
                <?php endif; ?>
                <!-- Login Form -->
                <form method="post" action="<?php echo base_url('admin/login'); ?>" class="space-y-6">
                    <?= csrf_field() ?>
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label
                            class="block font-label text-xs font-semibold uppercase tracking-widest text-on-secondary-fixed-variant"
                            for="email">Correo Electrónico</label>
                        <div class="relative group">
                            <input
                                class="w-full bg-surface-container-low border-b border-outline-variant/30 py-3 px-4 focus:outline-none focus:border-primary transition-all text-on-surface placeholder:text-outline/40 font-body"
                                id="email" name="email" placeholder="nombre@institucion.org" required="" type="email" />
                        </div>
                    </div>
                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label
                                class="block font-label text-xs font-semibold uppercase tracking-widest text-on-secondary-fixed-variant"
                                for="password">Contraseña</label>
                            <a class="text-xs font-medium text-primary hover:underline decoration-primary/30 transition-all"
                                href="#">¿Olvidó su contraseña?</a>
                        </div>
                        <div class="relative group">
                            <input
                                class="w-full bg-surface-container-low border-b border-outline-variant/30 py-3 px-4 focus:outline-none focus:border-primary transition-all text-on-surface placeholder:text-outline/40 font-body"
                                id="password" name="password" placeholder="••••••••" required="" type="password" />
                        </div>
                    </div>
                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            class="w-4 h-4 rounded-sm border-outline text-primary focus:ring-primary-fixed-dim cursor-pointer"
                            id="remember" name="remember" type="checkbox" />
                        <label class="ml-2 text-sm text-secondary cursor-pointer font-body" for="remember">Recordar esta
                            sesión</label>
                    </div>
                    <!-- Actions -->
                    <div class="space-y-4 pt-2">
                        <button
                            class="w-full bg-gradient-to-br from-primary to-primary-container text-on-primary font-headline font-semibold py-4 rounded-lg shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2 group"
                            type="submit">
                            Iniciar Sesión
                            <span
                                class="material-symbols-outlined text-sm transition-transform group-hover:translate-x-1"
                                data-icon="arrow_forward">arrow_forward</span>
                        </button>
                    </div>
                </form>
                <div class="mt-8 flex items-center justify-center gap-4">
                    <span class="material-symbols-outlined text-outline text-lg"
                        data-icon="verified_user">verified_user</span>
                    <p class="text-[10px] text-outline-variant leading-tight max-w-[200px] font-body">
                        Este sistema está monitoreado para cumplimiento autoritativo y administración segura.
                    </p>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer Component -->
    <footer
        class="w-full mt-auto bg-[#012d1d] dark:bg-[#001a11] text-white">
        <div class="flex flex-col md:flex-row justify-between items-center px-12 py-8">
            <div class="mb-4 md:mb-0">
                <span class="text-white font-manrope font-bold text-lg">Denuncias Ambientales</span>
                <p class="font-inter text-xs uppercase tracking-widest text-slate-300 opacity-80 mt-1">© 2026 Sistema de recepción, atención y seguimiento de denuncias populares en materia ambiental y ordenamiento
                    territorial.</p>
            </div>
            <!-- <div class="flex flex-wrap justify-center gap-6">
                <a class="font-inter text-xs uppercase tracking-widest text-slate-300 opacity-80 hover:opacity-100 transition-opacity"
                    href="#">Política de Privacidad</a>
                <a class="font-inter text-xs uppercase tracking-widest text-slate-300 opacity-80 hover:opacity-100 transition-opacity"
                    href="#">Términos de Servicio</a>
                <a class="font-inter text-xs uppercase tracking-widest text-slate-300 opacity-80 hover:opacity-100 transition-opacity"
                    href="#">API de Datos Abiertos</a>
                <a class="font-inter text-xs uppercase tracking-widest text-slate-300 opacity-80 hover:opacity-100 transition-opacity"
                    href="#">Registro de Contacto</a>
            </div> -->
        </div>
    </footer>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('loaded');
    });
</script>
</html>