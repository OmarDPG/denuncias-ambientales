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
$routes->get('inicio/getMotivosVerificacion', 'Inicio::getMotivosVerificacion');

// ─── Rutas de prueba (solo desarrollo) ─────────────────────────────────────
if (ENVIRONMENT === 'development') {
    $routes->get('inicio/testPDF', 'Inicio::testPDF');   // Generar y mostrar PDF de prueba
    $routes->get('inicio/testHTML', 'Inicio::testHTML'); // Ver HTML del acuse sin PDF
}

$routes->post('admin/actualizarEstado', 'Admin::actualizarEstado');
$routes->get('admin/verEvidencia/(:num)', 'Admin::verEvidencia/$1');

// ─── Rutas de Vistas Adicionales ───────────────────────────────────────────
$routes->get('admin/denuncias-asignadas', 'Admin::denunciasAsignadas');

// ─── Rutas Fase 2: Lógica de Negocio ───────────────────────────────────────

// Transiciones de Estados
$routes->post('admin/turnarDenuncia', 'Admin::turnarDenuncia');
$routes->post('admin/tomarCaso', 'Admin::tomarCaso');
$routes->post('admin/asignarCaso', 'Admin::asignarCaso');
$routes->post('admin/aprobarInspeccion', 'Admin::aprobarInspeccion');
$routes->post('admin/rechazarDenuncia', 'Admin::rechazarDenuncia');
$routes->post('admin/concluirInspeccion', 'Admin::concluirInspeccion');
$routes->post('admin/regresarADNS', 'Admin::regresarADNS');
$routes->post('admin/emitirSancion', 'Admin::emitirSancion');
$routes->post('admin/finalizarCaso', 'Admin::finalizarCaso');

// Consultas
$routes->get('admin/obtenerDenunciaDetalle/(:num)', 'Admin::obtenerDenunciaDetalle/$1');
$routes->get('admin/obtenerUsuariosArea/(:num)', 'Admin::obtenerUsuariosArea/$1');

// Gestión Documental
$routes->post('admin/subirDocumento', 'Admin::subirDocumento');
$routes->get('admin/verDocumento/(:num)', 'Admin::verDocumento/$1');
$routes->get('admin/descargarDocumento/(:num)', 'Admin::descargarDocumento/$1');

$routes->setAutoRoute(true);
