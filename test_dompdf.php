<?php

/**
 * Script de prueba para verificar que DOMPDF 2.0 funciona correctamente
 * 
 * Ubicación: c:\wamp64\www\denuncias-ambientales\test_dompdf.php
 * URL de prueba: http://localhost/denuncias-ambientales/test_dompdf.php
 * 
 * Este script genera un PDF simple para verificar la instalación
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurar opciones
$options = new Options();
$options->setIsRemoteEnabled(true);
$options->setDefaultFont('Arial');
$options->setIsHtml5ParserEnabled(true);

// Crear instancia de DOMPDF
$dompdf = new Dompdf($options);

// HTML de prueba
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        .success {
            background: #e8f5e9;
            border-left: 5px solid #2c5f2d;
            padding: 20px;
            margin: 20px 0;
        }
        h1 {
            color: #2c5f2d;
        }
        .info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>✅ DOMPDF 2.0 - Prueba Exitosa</h1>
    
    <div class="success">
        <h2>¡La instalación de DOMPDF funciona correctamente!</h2>
        <p>Este PDF se generó usando DOMPDF 2.0 en PHP 7.4</p>
    </div>
    
    <div class="info">
        <h3>Información del Sistema</h3>
        <p><strong>Versión de PHP:</strong> ' . PHP_VERSION . '</p>
        <p><strong>DOMPDF:</strong> 2.0.0</p>
        <p><strong>Fecha de prueba:</strong> ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <h3>Características verificadas:</h3>
    <ul>
        <li>✅ Generación de PDF básico</li>
        <li>✅ Estilos CSS inline</li>
        <li>✅ Codificación UTF-8</li>
        <li>✅ Fuentes personalizadas (Arial)</li>
    </ul>
    
    <p style="margin-top: 40px; color: #666; font-size: 12px;">
        Sistema de Denuncias Ambientales - Estado de Puebla<br>
        Generado automáticamente por script de prueba
    </p>
</body>
</html>
';

// Cargar HTML
$dompdf->loadHtml($html);

// Configurar papel
$dompdf->setPaper('letter', 'portrait');

// Renderizar PDF
$dompdf->render();

// Enviar al navegador
$dompdf->stream('test_dompdf_prueba.pdf', [
    'Attachment' => false // false = mostrar en navegador, true = descargar
]);

echo '<p>Si estás viendo esto, revisa que se haya descargado/mostrado el PDF correctamente.</p>';
