<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ClientController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    // ─── Liste (vue + DataTables Ajax) ──────────────────────────────────────

    public function index(): string
    {
        return view('clients/index', ['titre' => 'Clients']);
    }

    public function ajax(): ResponseInterface
    {
        $draw   = (int) $this->request->getPost('draw');
        $start  = (int) $this->request->getPost('start');
        $length = (int) $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';

        $columns = ['id', 'last_name', 'email', 'phone', 'city'];

        $orderColIndex = (int) ($this->request->getPost('order')[0]['column'] ?? 0);
        $orderDir      = strtoupper($this->request->getPost('order')[0]['dir'] ?? 'ASC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'ASC';
        $orderCol      = $columns[$orderColIndex] ?? 'id';

        $companyId = $this->getCompanyId();

        $builder = $this->userModel->builder();
        $builder->where('deleted_at', null)
                ->where('role', 'client');

        if ($companyId !== null) {
            $builder->where('company_id', $companyId);
        }

        $total = (clone $builder)->countAllResults(false);

        if ($search !== '') {
            $builder->groupStart()
                ->like('last_name', $search)
                ->orLike('first_name', $search)
                ->orLike('email', $search)
                ->orLike('city', $search)
                ->groupEnd();
        }

        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            if (! empty($row['phone'])) {
                $digits = preg_replace('/\D/', '', $row['phone']);
                if (strlen($digits) === 10) {
                    $row['phone'] = implode(' ', str_split($digits, 2));
                }
            }

            $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
            $row['nom_complet'] = $fullName !== '' ? $fullName : $row['username'];

            $row['actions'] = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer"'
                . ' data-confirm="Supprimer le client &laquo;%s&raquo; ?"'
                . ' data-delete-url="%s" data-table="table-clients"><i class="bi bi-trash"></i></button>',
                base_url('clients/' . $row['id'] . '/edit'),
                htmlspecialchars($row['nom_complet'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                base_url('clients/' . $row['id'] . '/delete')
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

    // ─── Création ────────────────────────────────────────────────────────────

    public function create(): string
    {
        return view('clients/form', ['titre' => 'Nouveau client']);
    }

    public function store(): RedirectResponse
    {
        $data = $this->request->getPost([
            'first_name', 'last_name', 'email', 'phone',
            'street_number', 'street_type', 'street_name',
            'city', 'postal_code', 'password',
        ]);

        $rules = [
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]',
            'password'   => 'required|min_length[8]',
            'phone'      => 'permit_empty|max_length[14]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Unicité email (globale pour l'instant)
        if ($this->userModel->where('email', $data['email'])->first()) {
            return redirect()->back()->withInput()
                ->with('errors', ['email' => 'Cet email est déjà utilisé.']);
        }

        $data = $this->normalizeUserData($data);
        $data['role']       = 'client';
        $data['company_id'] = $this->getCompanyId();
        $data['username']   = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))
                            ?: $data['email'];

        if (! $this->userModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client créé avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $client = $this->userModel->find($id);
        if (! $client || $client['role'] !== 'client') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($client['company_id'] ?? 0));

        return view('clients/form', ['titre' => 'Modifier le client', 'client' => $client]);
    }

    public function update(int $id): RedirectResponse
    {
        $client = $this->userModel->find($id);
        if (! $client || $client['role'] !== 'client') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->assertCompanyAccess((int) ($client['company_id'] ?? 0));

        $data = $this->request->getPost([
            'first_name', 'last_name', 'email', 'phone',
            'street_number', 'street_type', 'street_name',
            'city', 'postal_code', 'password',
        ]);

        $rules = [
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|max_length[150]',
            'phone'      => 'permit_empty|max_length[14]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Unicité email (exclure l'utilisateur courant)
        $existing = $this->userModel
            ->where('email', $data['email'])
            ->where('id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()
                ->with('errors', ['email' => 'Cet email est déjà utilisé.']);
        }

        // Ne pas écraser le mot de passe si le champ est vide
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data = $this->normalizeUserData($data);
        $data['username'] = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))
                            ?: $data['email'];

        if (! $this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->userModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client mis à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): ResponseInterface
    {
        $client = $this->userModel->find($id);
        if (! $client || $client['role'] !== 'client') {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Client introuvable.']);
        }

        if (! $this->isAdmin() && $this->getCompanyId() !== (int) ($client['company_id'] ?? 0)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Accès refusé.']);
        }

        $this->userModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Client supprimé.']);
    }

    // ─── Helpers privés ──────────────────────────────────────────────────────

    private function normalizeUserData(array $data): array
    {
        if (! empty($data['phone'])) {
            $data['phone'] = substr(preg_replace('/\D/', '', $data['phone']), 0, 10);
        }

        foreach (['last_name', 'first_name', 'city', 'street_type', 'street_name'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = $this->capitalizeWords($data[$field]);
            }
        }

        return $data;
    }

    private function capitalizeWords(string $str): string
    {
        $lower = mb_strtolower(trim($str), 'UTF-8');

        return preg_replace_callback(
            '/(?:^|[\s\-])\p{L}/u',
            static fn (array $m): string => mb_strtoupper($m[0], 'UTF-8'),
            $lower
        );
    }
}

