<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\CompanyModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class OrderController extends BaseController
{
    protected OrderModel     $orderModel;
    protected OrderItemModel $itemModel;

    public function __construct()
    {
        $this->orderModel = model(OrderModel::class);
        $this->itemModel  = model(OrderItemModel::class);
    }

    // ─── Liste ───────────────────────────────────────────────────────────────

    public function index(): string
    {
        return view('orders/index', ['titre' => 'Commandes']);
    }

    public function ajax(): ResponseInterface
    {
        $draw   = (int) $this->request->getPost('draw');
        $start  = (int) $this->request->getPost('start');
        $length = (int) $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $columns = [
            'orders.number', 'user_name', 'orders.order_date',
            'orders.amount_ht', 'orders.amount_ttc', 'orders.status',
        ];

        $orderColIndex = (int) ($this->request->getPost('order')[0]['column'] ?? 0);
        $orderDir      = strtoupper($this->request->getPost('order')[0]['dir'] ?? 'DESC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'DESC';
        $orderCol      = $columns[$orderColIndex] ?? 'orders.id';

        $companyId = $this->getCompanyId();

        $builder = $this->orderModel->db->table('orders')
            ->select("orders.*, CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) AS user_name")
            ->join('users', 'users.id = orders.user_id', 'left')
            ->where('orders.deleted_at', null);

        if ($companyId !== null) {
            $builder->where('orders.company_id', $companyId);
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('orders.number', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.first_name', $search)
                ->groupEnd();
        }

        $total    = (clone $builder)->countAllResults(false);
        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            $row['amount_ht_fmt']  = number_format((float) $row['amount_ht'], 2, ',', ' ') . ' €';
            $row['amount_ttc_fmt'] = number_format((float) $row['amount_ttc'], 2, ',', ' ') . ' €';
            $row['status_badge']   = view('partials/badge_status', ['status' => $row['status']]);
            $row['actions']        = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-secondary me-1" title="Voir"><i class="bi bi-eye"></i></a>'
                . '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<a href="%s" class="btn btn-sm btn-outline-info me-1" title="PDF" target="_blank" rel="noopener noreferrer"><i class="bi bi-file-pdf"></i></a>'
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer"'
                . ' data-confirm="Supprimer la commande &laquo;%s&raquo; ?"'
                . ' data-delete-url="%s" data-table="table-orders"><i class="bi bi-trash"></i></button>',
                base_url('orders/' . $row['id']),
                base_url('orders/' . $row['id'] . '/edit'),
                base_url('orders/' . $row['id'] . '/pdf'),
                htmlspecialchars($row['number'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                base_url('orders/' . $row['id'] . '/delete')
            );

            return $row;
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    // ─── Détail ──────────────────────────────────────────────────────────────

    public function show(int $id): string
    {
        $order = $this->orderModel->withUser()->where('orders.id', $id)->first();
        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($order['company_id'] ?? 0));

        $items = $this->itemModel->getByOrder($id);

        return view('orders/show', [
            'titre' => 'Commande ' . $order['number'],
            'order' => $order,
            'items' => $items,
        ]);
    }

    // ─── Création ────────────────────────────────────────────────────────────

    public function create(): string
    {
        $companyId = $this->getCompanyId();

        $clientsQ = model(UserModel::class)->where('role', 'client');
        if ($companyId !== null) {
            $clientsQ->where('company_id', $companyId);
        }

        $productsQ = model(ProductModel::class);
        if ($companyId !== null) {
            $productsQ->where('company_id', $companyId);
        }

        return view('orders/form', [
            'titre'    => 'Nouvelle commande',
            'users'    => $clientsQ->findAll(),
            'products' => $productsQ->findAll(),
        ]);
    }

    public function store(): RedirectResponse
    {
        $post      = $this->request->getPost();
        $items     = $this->parseItems($post);
        $amountHt  = array_sum(array_column($items, 'subtotal'));
        $vatRate   = (float) ($post['vat_rate'] ?? 20);
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        $data = [
            'number'     => $this->orderModel->generateNumber(),
            'user_id'    => (int) $post['user_id'],
            'status'     => $post['status'] ?? 'draft',
            'order_date' => $post['order_date'],
            'amount_ht'  => $amountHt,
            'vat_rate'   => $vatRate,
            'amount_ttc' => $amountTtc,
            'notes'      => $post['notes'] ?? null,
        ];

        $companyId = $this->getCompanyId();
        if ($companyId !== null) {
            $data['company_id'] = $companyId;
        }

        if (! $this->orderModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->orderModel->errors());
        }

        $orderId = $this->orderModel->getInsertID();
        $this->itemModel->syncItems($orderId, $items);

        return redirect()->to(base_url('orders/' . $orderId))
            ->with('success', 'Commande créée avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $order = $this->orderModel->find($id);
        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($order['company_id'] ?? 0));

        $items = $this->itemModel->getByOrder($id);

        $companyId = $this->getCompanyId();

        $clientsQ = model(UserModel::class)->where('role', 'client');
        if ($companyId !== null) {
            $clientsQ->where('company_id', $companyId);
        }

        $productsQ = model(ProductModel::class);
        if ($companyId !== null) {
            $productsQ->where('company_id', $companyId);
        }

        return view('orders/form', [
            'titre'    => 'Modifier la commande',
            'order'    => $order,
            'items'    => $items,
            'users'    => $clientsQ->findAll(),
            'products' => $productsQ->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $order = $this->orderModel->find($id);
        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($order['company_id'] ?? 0));

        $post      = $this->request->getPost();
        $items     = $this->parseItems($post);
        $amountHt  = array_sum(array_column($items, 'subtotal'));
        $vatRate   = (float) ($post['vat_rate'] ?? 20);
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        $newStatus = $post['status'];
        $oldStatus = $order['status'];

        $data = [
            'user_id'    => (int) $post['user_id'],
            'status'     => $newStatus,
            'order_date' => $post['order_date'],
            'amount_ht'  => $amountHt,
            'vat_rate'   => $vatRate,
            'amount_ttc' => $amountTtc,
            'notes'      => $post['notes'] ?? null,
        ];

        if (! $this->orderModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->orderModel->errors());
        }

        $this->itemModel->syncItems($id, $items);

        return redirect()->to(base_url('orders/' . $id))
            ->with('success', 'Commande mise à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): ResponseInterface
    {
        $order = $this->orderModel->find($id);
        if (! $order) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Commande introuvable.']);
        }

        if (! $this->isAdmin() && $this->getCompanyId() !== (int) ($order['company_id'] ?? 0)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Accès refusé.']);
        }

        $this->orderModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Commande supprimée.']);
    }

    // ─── Génération PDF ──────────────────────────────────────────────────────

    public function pdf(int $id): ResponseInterface
    {
        $order = $this->orderModel->withUser()->where('orders.id', $id)->first();
        if (! $order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($order['company_id'] ?? 0));

        $items = $this->itemModel->getByOrder($id);
        $html  = view('orders/pdf_invoice', ['order' => $order, 'items' => $items]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'invoice-' . $order['number'] . '.pdf';
        $output   = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($output);
    }

    // ─── Checkout boutique (front client) ────────────────────────────────────

    public function checkout(string $slug): string|RedirectResponse
    {
        $isLoggedIn = (bool) session()->get('isLoggedIn');

        if (! $isLoggedIn) {
            // Mémoriser la destination pour la reprendre après connexion
            session()->set('redirect_after_login', base_url('shop/' . $slug . '/checkout'));
            return redirect()->to(base_url('login'))
                ->with('info', 'Veuillez vous connecter pour finaliser votre commande.');
        }

        $company = model(CompanyModel::class)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $company) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Boutique introuvable.');
        }

        $cart = session()->get('cart') ?? [];
        if (empty($cart)) {
            return redirect()->to(base_url('shop/' . $slug . '/catalog'))
                ->with('info', 'Votre panier est vide.');
        }

        $amountHt = 0.0;
        foreach ($cart as $item) {
            $amountHt += (float) $item['qty'] * (float) $item['price'];
        }
        $vatRate   = 20;
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        return view('shop/checkout', [
            'titre'      => 'Finaliser la commande — ' . esc($company['name']),
            'company'    => $company,
            'cart'       => $cart,
            'amountHt'   => $amountHt,
            'vatRate'    => $vatRate,
            'amountTtc'  => $amountTtc,
            'isLoggedIn' => true,
            'username'   => session()->get('username'),
            'role'       => session()->get('role'),
        ]);
    }

    public function confirm(string $slug): string|RedirectResponse
    {
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        if (! $isLoggedIn) {
            return redirect()->to(base_url('login'));
        }

        $company = model(CompanyModel::class)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();

        if (! $company) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Boutique introuvable.');
        }

        // Vérifier que l'utilisateur appartient à cette boutique
        $userId         = (int) session()->get('user_id');
        $sessionCompany = (int) session()->get('company_id');

        if ((int) $company['id'] !== $sessionCompany) {
            return redirect()->to(base_url('shop/' . $slug . '/catalog'))
                ->with('error', 'Vous n\'avez pas accès à cette boutique.');
        }

        $cart = session()->get('cart') ?? [];
        if (empty($cart)) {
            return redirect()->to(base_url('shop/' . $slug . '/catalog'))
                ->with('info', 'Votre panier est vide.');
        }

        $amountHt = 0.0;
        foreach ($cart as $item) {
            $amountHt += (float) $item['qty'] * (float) $item['price'];
        }
        $vatRate   = 20.0;
        $amountTtc = round($amountHt * (1 + $vatRate / 100), 2);

        $productModel = model(ProductModel::class);
        $db           = \Config\Database::connect();

        // ── Vérification atomique des stocks ─────────────────────────────────
        $db->transStart();

        $stockErrors = [];
        $products    = [];

        foreach ($cart as $productId => $item) {
            $product = $productModel->where('id', (int) $productId)->where('deleted_at', null)->first();
            if (! $product) {
                $stockErrors[] = htmlspecialchars($item['name'] ?? 'Produit inconnu', ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    . ' est introuvable.';
                continue;
            }
            $products[(int) $productId] = $product;
            if ((int) $product['stock'] < (int) $item['qty']) {
                $stockErrors[] = htmlspecialchars($product['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    . ' (stock\u00a0: ' . $product['stock'] . ', demand\u00e9\u00a0: ' . $item['qty'] . ')';
            }
        }

        if (! empty($stockErrors)) {
            $db->transRollback();
            return redirect()->to(base_url('shop/' . $slug . '/checkout'))
                ->with('stock_errors', $stockErrors);
        }

        // ── Créer la commande ─────────────────────────────────────────────────
        $orderData = [
            'company_id' => $sessionCompany,
            'user_id'    => $userId,
            'number'     => $this->orderModel->generateNumber(),
            'status'     => 'confirmed',
            'order_date' => date('Y-m-d'),
            'amount_ht'  => round($amountHt, 2),
            'vat_rate'   => $vatRate,
            'amount_ttc' => $amountTtc,
        ];

        if (! $this->orderModel->save($orderData)) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur lors de la création de la commande.');
        }

        $orderId   = $this->orderModel->getInsertID();
        $itemsData = [];

        foreach ($cart as $productId => $item) {
            $itemsData[] = [
                'order_id'   => $orderId,
                'product_id' => (int) $productId,
                'name'       => $item['name'],
                'quantity'   => (int) $item['qty'],
                'unit_price' => (float) $item['price'],
                'subtotal'   => round((float) $item['qty'] * (float) $item['price'], 2),
            ];
        }

        $this->itemModel->insertBatch($itemsData);

        // ── Déduire les stocks ────────────────────────────────────────────────
        foreach ($cart as $productId => $item) {
            $product  = $products[(int) $productId] ?? null;
            if (! $product) {
                continue;
            }
            $newStock = max(0, (int) $product['stock'] - (int) $item['qty']);
            $productModel->update((int) $productId, ['stock' => $newStock]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Erreur lors de la validation de la commande. Veuillez réessayer.');
        }

        session()->remove('cart');

        $orderRow = $this->orderModel->find($orderId);

        return view('shop/order_confirmation', [
            'titre'      => 'Commande confirmée — ' . esc($company['name']),
            'company'    => $company,
            'order'      => $orderRow,
            'items'      => $itemsData,
            'isLoggedIn' => true,
            'username'   => session()->get('username'),
            'role'       => session()->get('role'),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function parseItems(array $post): array
    {
        $items      = [];
        $productIds = $post['product_id'] ?? [];
        $names      = $post['name']       ?? [];
        $quantities = $post['quantity']   ?? [];
        $prices     = $post['unit_price'] ?? [];

        foreach ($productIds as $i => $productId) {
            if (empty($productId)) {
                continue;
            }

            $qty       = max(1, (int) ($quantities[$i] ?? 1));
            $unitPrice = max(0, (float) ($prices[$i] ?? 0));

            $items[] = [
                'product_id' => (int) $productId,
                'name'       => $names[$i] ?? '',
                'quantity'   => $qty,
                'unit_price' => $unitPrice,
                'subtotal'   => round($qty * $unitPrice, 2),
            ];
        }

        return $items;
    }
}
