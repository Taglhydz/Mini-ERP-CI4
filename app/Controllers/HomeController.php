<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;

class HomeController extends BaseController
{
    public function index(): string
    {
        $companies = model(CompanyModel::class)
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        return view('home/index', [
            'titre'     => 'Bienvenue',
            'companies' => $companies,
        ]);
    }
}
