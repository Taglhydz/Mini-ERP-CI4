<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CartController extends BaseController
{
    // ─── Ajouter / incrémenter un produit dans le panier ─────────────────────

    public function add(string $slug): RedirectResponse
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('quantity') ?? 1));

        $product = model(ProductModel::class)
            ->where('id', $productId)
            ->where('deleted_at', null)
            ->where('stock >', 0)
            ->first();

        if (! $product) {
            return redirect()->back()->with('error', 'Produit introuvable ou indisponible.');
        }

        $cart = session()->get('cart') ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'qty'   => $qty,
                'price' => (float) $product['unit_price'],
                'name'  => $product['name'],
            ];
        }

        session()->set('cart', $cart);

        return redirect()->back()
            ->with('success', esc($product['name']) . ' ajouté au panier.');
    }

    // ─── Supprimer une ligne du panier ────────────────────────────────────────

    public function remove(string $slug): RedirectResponse
    {
        $productId = (int) $this->request->getPost('product_id');

        $cart = session()->get('cart') ?? [];
        unset($cart[$productId]);
        session()->set('cart', $cart);

        return redirect()->to(base_url('shop/' . $slug . '/cart'));
    }

    // ─── Afficher le panier ───────────────────────────────────────────────────

    public function index(string $slug): string
    {
        $company = $this->findCompanyOrFail($slug);

        // Maintenir le contexte boutique en session (nécessaire pour le login)
        session()->set('current_company_id',   $company['id']);
        session()->set('current_company_slug', $company['slug']);

        $cart      = session()->get('cart') ?? [];
        $amountHt  = 0.0;

        foreach ($cart as $item) {
            $amountHt += (float) $item['qty'] * (float) $item['price'];
        }

        $vatRate   = 20;
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        return view('shop/cart', [
            'titre'      => 'Mon panier — ' . esc($company['name']),
            'company'    => $company,
            'cart'       => $cart,
            'amountHt'   => $amountHt,
            'vatRate'    => $vatRate,
            'amountTtc'  => $amountTtc,
            'cartCount'  => count($cart),
            'isLoggedIn' => (bool) session()->get('isLoggedIn'),
            'username'   => session()->get('username'),
            'role'       => session()->get('role'),
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function findCompanyOrFail(string $slug): array
    {
        $company = model(CompanyModel::class)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $company) {
            throw PageNotFoundException::forPageNotFound('Boutique introuvable.');
        }

        return $company;
    }
}
