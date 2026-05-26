<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/',  'Inicio::index');
$routes->post('inicio/registrarDenuncia',        'Inicio::registrarDenuncia');
$routes->get('inicio/buscarReporte',  'Inicio::buscarReporte');
$routes->get('inicio/descargarDocumentoResolucion/(:num)', 'Inicio::descargarDocumentoResolucion/$1');

$routes->post('admin/actualizarEstado', 'Admin::actualizarEstado');
$routes->get('admin/verEvidencia/(:num)', 'Admin::verEvidencia/$1');

$routes->setAutoRoute(true);
