<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderModel;

class EspaceClientController extends BaseController
{
    public function index(): string
    {
        $userId  = (int) (session()->get('user_id') ?? 0);

        $orders = model(OrderModel::class)
            ->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('espace_client/index', [
            'titre'  => 'Mon espace',
            'orders' => $orders,
        ]);
    }
}
