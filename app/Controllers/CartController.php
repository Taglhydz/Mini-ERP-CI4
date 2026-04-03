<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;

class CartController extends BaseController
{
    // ─── Ajouter / incrémenter ────────────────────────────────────────────────

    public function add(string $slug): RedirectResponse|Response
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = max(1, (int) ($this->request->getPost('quantity') ?? 1));

        $product = model(ProductModel::class)
            ->where('id', $productId)
            ->where('deleted_at', null)
            ->first();

        if (! $product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Produit introuvable.']);
            }
            return redirect()->back()->with('error', 'Produit introuvable ou indisponible.');
        }

        $stock = (int) $product['stock'];

        if ($stock === 0) {
            $message = esc($product['name']) . ' est en rupture de stock.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            return redirect()->back()->with('error', $message);
        }

        $cart       = session()->get('cart') ?? [];
        $currentQty = isset($cart[$productId]) ? (int) $cart[$productId]['qty'] : 0;

        if ($currentQty + $qty > $stock) {
            $message = 'Stock insuffisant pour ' . esc($product['name'])
                . '. Stock disponible\u00a0: ' . $stock
                . ($currentQty > 0 ? ' (dont ' . $currentQty . ' déjà au panier)' : '') . '.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $message]);
            }
            return redirect()->back()->with('error', $message);
        }

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

        if ($this->request->isAJAX()) {
            $ttc = $this->calcTtc($cart);
            return $this->response->setJSON([
                'success'     => true,
                'cart_count'  => count($cart),
                'cart_html'   => $this->buildCartHtml($slug, $cart),
                'total_ttc'   => number_format($ttc, 2, ',', ' ') . ' €',
                'cart_limits' => $this->buildCartLimits($cart),
            ]);
        }

        return redirect()->back()
            ->with('success', esc($product['name']) . ' ajouté au panier.');
    }

    // ─── Modifier la quantité (AJAX uniquement) ────────────────────────────────

    public function update(string $slug): Response
    {
        $productId = (int) $this->request->getPost('product_id');
        $qty       = (int) $this->request->getPost('quantity');

        $cart = session()->get('cart') ?? [];

        if ($qty <= 0) {
            unset($cart[$productId]);
        } elseif (isset($cart[$productId])) {
            // Vérifier le stock disponible
            $product = model(ProductModel::class)
                ->where('id', $productId)
                ->where('deleted_at', null)
                ->first();
            if ($product) {
                $qty = min($qty, (int) $product['stock']);
            }
            if ($qty > 0) {
                $cart[$productId]['qty'] = $qty;
            } else {
                unset($cart[$productId]);
            }
        }

        session()->set('cart', $cart);

        $ttc = $this->calcTtc($cart);
        return $this->response->setJSON([
            'success'      => true,
            'cart_count'   => count($cart),
            'cart_html'    => $this->buildCartHtml($slug, $cart),
            'total_ttc'    => number_format($ttc, 2, ',', ' ') . ' €',
            'cart_limits'  => $this->buildCartLimits($cart),
        ]);
    }

    // ─── Supprimer une ligne ───────────────────────────────────────────────────

    public function remove(string $slug): RedirectResponse|Response
    {
        $productId = (int) $this->request->getPost('product_id');

        $cart = session()->get('cart') ?? [];
        unset($cart[$productId]);
        session()->set('cart', $cart);

        if ($this->request->isAJAX()) {
            $ttc = $this->calcTtc($cart);
            return $this->response->setJSON([
                'success'      => true,
                'cart_count'   => count($cart),
                'cart_html'    => $this->buildCartHtml($slug, $cart),
                'total_ttc'    => number_format($ttc, 2, ',', ' ') . ' €',
                'cart_limits'  => $this->buildCartLimits($cart),
            ]);
        }

        return redirect()->to(base_url('shop/' . $slug . '/cart'));
    }

    // ─── Résumé panier (GET AJAX) ─────────────────────────────────────────────

    public function summary(string $slug): Response
    {
        $cart = session()->get('cart') ?? [];
        $ttc  = $this->calcTtc($cart);

        return $this->response->setJSON([
            'cart_count'  => count($cart),
            'cart_html'   => $this->buildCartHtml($slug, $cart),
            'total_ttc'   => number_format($ttc, 2, ',', ' ') . ' €',
            'cart_limits' => $this->buildCartLimits($cart),
        ]);
    }

    // ─── Afficher la page panier ──────────────────────────────────────────────

    public function index(string $slug): string
    {
        $company = $this->findCompanyOrFail($slug);

        session()->set('current_company_id',   $company['id']);
        session()->set('current_company_slug', $company['slug']);

        $cart     = session()->get('cart') ?? [];
        $amountHt = 0.0;

        foreach ($cart as $item) {
            $amountHt += (float) $item['qty'] * (float) $item['price'];
        }

        $vatRate   = 20;
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        // Récupérer les stocks actuels
        $stocks = $this->fetchStocks($cart);

        // Détecter si un article est en rupture/stock insuffisant
        $hasStockIssue = false;
        foreach ($cart as $productId => $item) {
            $currentStock = $stocks[(int)$productId] ?? PHP_INT_MAX;
            if ($currentStock < (int) $item['qty']) {
                $hasStockIssue = true;
                break;
            }
        }

        return view('shop/cart', [
            'titre'         => 'Mon panier — ' . esc($company['name']),
            'company'       => $company,
            'cart'          => $cart,
            'stocks'        => $stocks,
            'hasStockIssue' => $hasStockIssue,
            'amountHt'      => $amountHt,
            'vatRate'       => $vatRate,
            'amountTtc'     => $amountTtc,
            'cartCount'     => count($cart),
            'isLoggedIn'    => (bool) session()->get('isLoggedIn'),
            'username'      => session()->get('username'),
            'role'          => session()->get('role'),
        ]);
    }

    // ─── Helpers privés ───────────────────────────────────────────────────────

    private function buildCartLimits(array $cart): array
    {
        $limits = [];
        $stocks = $this->fetchStocks($cart);
        foreach ($cart as $productId => $item) {
            $stock              = $stocks[(int) $productId] ?? PHP_INT_MAX;
            $qty                = (int) $item['qty'];
            $limits[(string) $productId] = [
                'qty'    => $qty,
                'stock'  => $stock,
                'at_max' => $qty >= $stock,
            ];
        }
        return $limits;
    }

    private function buildCartHtml(string $slug, array $cart): string
    {
        return view('shop/_cart_items', [
            'cart'   => $cart,
            'slug'   => $slug,
            'stocks' => $this->fetchStocks($cart),
        ]);
    }

    private function fetchStocks(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }
        $productIds = array_map('intval', array_keys($cart));
        $rows       = model(ProductModel::class)
            ->whereIn('id', $productIds)
            ->where('deleted_at', null)
            ->findAll();
        $stocks = [];
        foreach ($rows as $row) {
            $stocks[(int) $row['id']] = (int) $row['stock'];
        }
        return $stocks;
    }

    private function calcTtc(array $cart): float
    {
        $ht = 0.0;
        foreach ($cart as $item) {
            $ht += (float) $item['qty'] * (float) $item['price'];
        }
        return round($ht * 1.20, 2);
    }

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
