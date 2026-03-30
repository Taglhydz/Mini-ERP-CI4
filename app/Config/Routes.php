<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Tableau de bord ─────────────────────────────────────────────────────────
$routes->get('/', 'DashboardController::index');

// ─── Clients ─────────────────────────────────────────────────────────────────
$routes->get ('clients',                  'ClientController::index');
$routes->post('clients/ajax',             'ClientController::ajax');
$routes->get ('clients/create',           'ClientController::create');
$routes->post('clients/store',            'ClientController::store');
$routes->get ('clients/(:num)/edit',      'ClientController::edit/$1');
$routes->post('clients/(:num)/update',    'ClientController::update/$1');
$routes->post('clients/(:num)/delete',    'ClientController::delete/$1');

// ─── Produits ────────────────────────────────────────────────────────────────
$routes->get ('produits',                 'ProduitController::index');
$routes->post('produits/ajax',            'ProduitController::ajax');
$routes->get ('produits/create',          'ProduitController::create');
$routes->post('produits/store',           'ProduitController::store');
$routes->get ('produits/(:num)/edit',     'ProduitController::edit/$1');
$routes->post('produits/(:num)/update',   'ProduitController::update/$1');
$routes->post('produits/(:num)/delete',   'ProduitController::delete/$1');

// ─── Commandes ───────────────────────────────────────────────────────────────
$routes->get ('commandes',                'CommandeController::index');
$routes->post('commandes/ajax',           'CommandeController::ajax');
$routes->get ('commandes/create',         'CommandeController::create');
$routes->post('commandes/store',          'CommandeController::store');
$routes->get ('commandes/(:num)',         'CommandeController::show/$1');
$routes->get ('commandes/(:num)/edit',    'CommandeController::edit/$1');
$routes->post('commandes/(:num)/update',  'CommandeController::update/$1');
$routes->post('commandes/(:num)/delete',  'CommandeController::delete/$1');
$routes->get ('commandes/(:num)/pdf',     'CommandeController::pdf/$1');
