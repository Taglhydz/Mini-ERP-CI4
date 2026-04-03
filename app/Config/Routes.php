<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ─── Routes publiques (sans authentification) ────────────────────────────────
$routes->get('/', 'HomeController::index');
$routes->match(['get', 'post'], 'login',  'AuthController::login');
$routes->get('logout',                    'AuthController::logout');
$routes->get('shop/(:segment)/catalog',   'ShopController::catalog/$1');
$routes->match(['get', 'post'], 'shop/(:segment)/register', 'ShopController::register/$1');
$routes->post('shop/(:segment)/cart/add',         'CartController::add/$1');
$routes->post('shop/(:segment)/cart/update',      'CartController::update/$1');
$routes->post('shop/(:segment)/cart/remove',      'CartController::remove/$1');
$routes->get('shop/(:segment)/cart/summary',      'CartController::summary/$1');
$routes->get('shop/(:segment)/cart',              'CartController::index/$1');
$routes->get('shop/(:segment)/checkout',          'OrderController::checkout/$1');
$routes->post('shop/(:segment)/checkout/confirm', 'OrderController::confirm/$1');

// ─── Groupe auth/* (compatibilité backward) ──────────────────────────────────
$routes->group('auth', static function (RouteCollection $routes) {
    $routes->match(['get', 'post'], 'login',    'AuthController::login');
    $routes->match(['get', 'post'], 'register', 'AuthController::register');
    $routes->get('logout',                      'AuthController::logout');
});

// ─── Routes protégées (filtre auth requis) ───────────────────────────────────
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {

    // Dashboard back-office
    $routes->get('admin/dashboard', 'DashboardController::index');

    // ─── Espace client (rôle client uniquement) ──────────────────────────────
    $routes->group('', ['filter' => 'role:client'], static function (RouteCollection $routes) {
        $routes->get('espace-client', 'EspaceClientController::index');
    });

    // ─── Back-office (Admin + Manager) ───────────────────────────────────────
    $routes->group('', ['filter' => 'role:manager'], static function (RouteCollection $routes) {
        // Paramètres boutique
        $routes->get ('admin/company/settings', 'CompanyController::settings');
        $routes->post('admin/company/settings', 'CompanyController::updateSettings');

        // Clients
        $routes->get ('clients',               'ClientController::index');
        $routes->post('clients/ajax',          'ClientController::ajax');
        $routes->get ('clients/create',        'ClientController::create');
        $routes->post('clients/store',         'ClientController::store');
        $routes->get ('clients/(:num)/edit',   'ClientController::edit/$1');
        $routes->post('clients/(:num)/update', 'ClientController::update/$1');
        $routes->post('clients/(:num)/delete', 'ClientController::delete/$1');

        // Products
        $routes->get ('products',                    'ProductController::index');
        $routes->post('products/ajax',               'ProductController::ajax');
        $routes->get ('products/create',             'ProductController::create');
        $routes->post('products/store',              'ProductController::store');
        $routes->get ('products/(:num)/edit',        'ProductController::edit/$1');
        $routes->post('products/(:num)/update',      'ProductController::update/$1');
        $routes->post('products/(:num)/delete',      'ProductController::delete/$1');
        $routes->post('products/(:num)/stock',       'ProductController::updateStock/$1');

        // Orders
        $routes->get ('orders',               'OrderController::index');
        $routes->post('orders/ajax',          'OrderController::ajax');
        $routes->get ('orders/create',        'OrderController::create');
        $routes->post('orders/store',         'OrderController::store');
        $routes->get ('orders/(:num)',        'OrderController::show/$1');
        $routes->get ('orders/(:num)/edit',   'OrderController::edit/$1');
        $routes->post('orders/(:num)/update', 'OrderController::update/$1');
        $routes->post('orders/(:num)/delete', 'OrderController::delete/$1');
        $routes->get ('orders/(:num)/pdf',    'OrderController::pdf/$1');
    });

    // ─── Boutiques (Admin uniquement) ─────────────────────────────────────────
    $routes->group('', ['filter' => 'role:admin'], static function (RouteCollection $routes) {
        $routes->get ('admin/companies',                 'AdminCompanyController::index');
        $routes->get ('admin/companies/create',          'AdminCompanyController::create');
        $routes->post('admin/companies/store',           'AdminCompanyController::store');
        $routes->get ('admin/companies/(:num)/edit',     'AdminCompanyController::edit/$1');
        $routes->post('admin/companies/(:num)/update',   'AdminCompanyController::update/$1');
        $routes->post('admin/companies/(:num)/delete',   'AdminCompanyController::delete/$1');
        $routes->post('admin/companies/(:num)/toggle',   'AdminCompanyController::toggle/$1');
    });
});

