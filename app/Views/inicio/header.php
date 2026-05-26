<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Denuncias Ambientales - Sistema de Atención</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('styles/styles.css') ?>" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="icon" type="image/png" href="<?php echo base_url('img/favicon.ico'); ?>" />
    <meta name="base-url" content="<?= base_url() ?>">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-error": "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "error": "#ba1a1a",
                        "secondary-fixed": "#d5e3fc",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#57657a",
                        "secondary-fixed-dim": "#b9c7df",
                        "surface-container-lowest": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "on-primary-fixed": "#002114",
                        "on-primary": "#ffffff",
                        "on-surface-variant": "#414844",
                        "surface-container-highest": "#e0e3e5",
                        "outline-variant": "#c1c8c2",
                        "outline": "#717973",
                        "primary-fixed-dim": "#a5d0b9",
                        "on-primary-container": "#86af99",
                        "inverse-primary": "#a5d0b9",
                        "secondary": "#515f74",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#d5e3fc",
                        "tertiary": "#002d1c",
                        "surface": "#f7f9fb",
                        "on-secondary-fixed": "#0d1c2e",
                        "surface-container": "#eceef0",
                        "tertiary-fixed": "#b1f0ce",
                        "surface-container-high": "#e6e8ea",
                        "inverse-surface": "#2d3133",
                        "primary-container": "#1b4332",
                        "on-surface": "#191c1e",
                        "on-primary-fixed-variant": "#274e3d",
                        "surface-bright": "#f7f9fb",
                        "on-tertiary-container": "#75b393",
                        "on-tertiary": "#ffffff",
                        "surface-tint": "#3f6653",
                        "tertiary-container": "#00452e",
                        "on-tertiary-fixed-variant": "#0e5138",
                        "on-tertiary-fixed": "#002114",
                        "primary": "#012d1d",
                        "background": "#f7f9fb",
                        "primary-fixed": "#c1ecd4",
                        "on-background": "#191c1e",
                        "surface-variant": "#e0e3e5",
                        "on-secondary-fixed-variant": "#3a485b",
                        "tertiary-fixed-dim": "#95d4b3",
                        "surface-dim": "#d8dadc",
                        "on-error-container": "#93000a"
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

<body class="bg-surface font-body text-on-surface flex flex-col min-h-screen">
    <!-- TopNavBar -->
    <header class="docked full-width top-0 sticky z-50 bg-[#1b4332] text-white">
        <nav class="flex justify-between items-center w-full px-8 py-4 max-w-full font-manrope font-bold tracking-tight">
            <div class="flex items-center gap-8">
                <!-- Government Logos -->
                <div class="flex items-center gap-4">
                    <img src="<?= base_url('img/PueblaBeige.png') ?>" alt="Amor a Puebla" class="h-12 md:h-12 w-auto">
                    <div class="hidden sm:block w-px h-10 bg-outline-variant/30"></div>
                    <img src="<?= base_url('img/DesarrolloBeigue.png') ?>" alt="Pensar en Grande" class="h-12 md:h-12 w-auto">
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="openReportModal()"
                    class="text-white border-2 border-white px-5 py-2 rounded-lg font-headline text-sm tracking-tight hover:bg-white hover:text-primary active:opacity-80 active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">search</span>
                    <span class="hidden sm:inline">Consultar Reporte</span>
                    <span class="sm:hidden">Consultar</span>
                </button>
                <button onclick="document.getElementById('formSection').scrollIntoView({behavior: 'smooth'})"
                    class="bg-[#c79b66] text-white px-5 py-2 rounded-lg font-headline text-sm tracking-tight active:opacity-80 active:scale-95 transition-all">
                    <span class="hidden sm:inline">Enviar denuncia</span>
                    <span class="sm:hidden">Enviar</span>
                </button>
            </div>
        </nav>
    </header>