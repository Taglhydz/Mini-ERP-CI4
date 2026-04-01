<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $userModel    = model(UserModel::class);
        $productModel = model(ProductModel::class);
        $orderModel   = model(OrderModel::class);

        $stats = [
            'clients'  => $userModel->where('role', 'client')->countAllResults(),
            'products' => $productModel->countAllResults(),
            'orders'   => $orderModel->countAllResults(),
            'ca_total' => (float) ($orderModel->selectSum('amount_ht')->get()->getRow()->amount_ht ?? 0),
        ];

        $latest_orders = $orderModel->withUser()
            ->orderBy('orders.id', 'DESC')
            ->limit(5)
            ->findAll();

        return view('dashboard/index', [
            'titre'         => 'Tableau de bord',
            'stats'         => $stats,
            'latest_orders' => $latest_orders,
        ]);
    }
}
