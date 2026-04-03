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

        $companyId = $this->getCompanyId();

        // ── Statistiques filtrées par company_id pour un manager ─────────────
        $clientsB  = $userModel->builder()->where('role', 'client');
        $productsB = $productModel->builder();
        $ordersB   = $orderModel->builder();
        $caB       = $orderModel->builder();

        if ($companyId !== null) {
            $clientsB->where('company_id', $companyId);
            $productsB->where('company_id', $companyId);
            $ordersB->where('company_id', $companyId);
            $caB->where('company_id', $companyId);
        }

        $stats = [
            'clients'  => $clientsB->countAllResults(),
            'products' => $productsB->countAllResults(),
            'orders'   => $ordersB->countAllResults(),
            'ca_total' => (float) ($caB->selectSum('amount_ht')->get()->getRow()->amount_ht ?? 0),
        ];

        $latestQ = $orderModel->withUser()->orderBy('orders.id', 'DESC');
        if ($companyId !== null) {
            $latestQ->where('orders.company_id', $companyId);
        }
        $latest_orders = $latestQ->limit(5)->findAll();

        // ── Produits en rupture ou stock faible (≤ 5) ─────────────────────────
        $lowStockB = $productModel->builder()
            ->where('deleted_at', null)
            ->where('stock <=', 5)
            ->orderBy('stock', 'ASC');
        if ($companyId !== null) {
            $lowStockB->where('company_id', $companyId);
        }
        $low_stock_products = $lowStockB->get()->getResultArray();

        return view('dashboard/index', [
            'titre'              => 'Tableau de bord',
            'stats'              => $stats,
            'latest_orders'      => $latest_orders,
            'low_stock_products' => $low_stock_products,
        ]);
    }
}
