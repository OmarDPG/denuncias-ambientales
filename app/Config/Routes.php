<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/',  'Inicio::index');
$routes->post('inicio/registrarDenuncia',        'Inicio::registrarDenuncia');
$routes->post('inicio/verificarCodigo',          'Inicio::verificarCodigoOTP');
$routes->post('inicio/reenviarCodigo',           'Inicio::reenviarCodigoOTP');
$routes->get('inicio/buscarReporte',  'Inicio::buscarReporte');
$routes->get('inicio/descargarDocumentoResolucion/(:num)', 'Inicio::descargarDocumentoResolucion/$1');
$routes->get('inicio/getTemasPorTipo', 'Inicio::getTemasPorTipo');
$routes->get('inicio/getCentrosVerificacion', 'Inicio::getCentrosVerificacion');

// ─── Rutas de prueba (solo desarrollo) ─────────────────────────────────────
if (ENVIRONMENT === 'development') {
    $routes->get('inicio/testPDF', 'Inicio::testPDF');   // Generar y mostrar PDF de prueba
    $routes->get('inicio/testHTML', 'Inicio::testHTML'); // Ver HTML del acuse sin PDF
}

$routes->post('admin/actualizarEstado', 'Admin::actualizarEstado');
$routes->get('admin/verEvidencia/(:num)', 'Admin::verEvidencia/$1');

$routes->setAutoRoute(true);
