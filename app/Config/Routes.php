<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->post('/registrasi', 'AuthController::registrasi');
$routes->post('/login', 'AuthController::login');
$routes->post('/test-token', 'AuthController::testToken');

$routes->group('buku', function ($routes) {
    $routes->post('/', 'InventarisBukuController::create');
    $routes->get('/', 'InventarisBukuController::list');
    $routes->get('statistik', 'InventarisBukuController::statistik');  // Pindah ke atas
    $routes->get('(:segment)', 'InventarisBukuController::detail/$1');
    $routes->put('(:segment)', 'InventarisBukuController::ubah/$1');
    $routes->delete('(:segment)', 'InventarisBukuController::hapus/$1');
});
