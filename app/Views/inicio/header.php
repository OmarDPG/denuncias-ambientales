<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Denuncias Ambientales - Sistema de Atención</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('styles/tailwind.css') ?>" />
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>

<body class="bg-surface font-body text-on-surface flex flex-col min-h-screen">
    <!-- TopNavBar -->
    <header class="full-width top-0 sticky z-50 bg-[#1b4332] text-white">
        <nav class="flex justify-between items-center w-full px-8 py-4 max-w-full font-manrope font-bold tracking-tight">
            <div class="flex items-center gap-8">
                <!-- Government Logos -->
                <div class="flex items-center gap-4">
                    <img src="<?= base_url('img/GOBPUE-DORADO.svg') ?>" alt="Amor a Puebla" class="h-16 md:h-16 w-auto">
                    <div class="hidden sm:block w-px h-10 bg-outline-variant/30"></div>
                    <img src="<?= base_url('img/DESARROLLO-SUSTENTABLE-DORADO.svg') ?>" alt="Pensar en Grande" class="h-16 md:h-16 w-auto">
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