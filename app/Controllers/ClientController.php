<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ClientModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ClientController extends BaseController
{
    protected ClientModel $clientModel;

    public function __construct()
    {
        $this->clientModel = model(ClientModel::class);
    }

    // ─── Liste (vue + DataTables Ajax) ──────────────────────────────────────

    public function index(): string
    {
        return view('clients/index', ['titre' => 'Clients']);
    }

    /**
     * Point d'entrée Ajax pour DataTables (POST, server-side).
     */
    public function ajax(): ResponseInterface
    {
        $request = $this->request;

        $draw    = (int) $request->getPost('draw');
        $start   = (int) $request->getPost('start');
        $length  = (int) $request->getPost('length');
        $search  = $request->getPost('search')['value'] ?? '';

        $columns = ['id', 'nom', 'email', 'telephone', 'ville'];

        $orderColIndex = (int) ($request->getPost('order')[0]['column'] ?? 0);
        $orderDir      = strtoupper($request->getPost('order')[0]['dir'] ?? 'ASC');
        $orderDir      = in_array($orderDir, ['ASC', 'DESC']) ? $orderDir : 'ASC';
        $orderCol      = $columns[$orderColIndex] ?? 'id';

        $builder = $this->clientModel->builder();

        if ($search !== '') {
            $builder->groupStart()
                ->like('nom', $search)
                ->orLike('email', $search)
                ->orLike('ville', $search)
                ->groupEnd();
        }

        $total    = $this->clientModel->countAllResults(false);
        $filtered = $builder->countAllResults(false);

        $rows = $builder->orderBy($orderCol, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $data = array_map(function (array $row): array {
            $row['actions'] = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<form action="%s" method="post" class="d-inline" onsubmit="return confirm(\'Supprimer ce client ?\');">'
                . csrf_field()
                . '<button class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="bi bi-trash"></i></button>'
                . '</form>',
                base_url('clients/' . $row['id'] . '/edit'),
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
        $data = $this->request->getPost(['nom', 'email', 'telephone', 'adresse', 'ville', 'code_postal']);

        if (! $this->clientModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client créé avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $client = $this->clientModel->findOrFail($id);

        return view('clients/form', ['titre' => 'Modifier le client', 'client' => $client]);
    }

    public function update(int $id): RedirectResponse
    {
        $client = $this->clientModel->findOrFail($id);
        $data   = $this->request->getPost(['nom', 'email', 'telephone', 'adresse', 'ville', 'code_postal']);

        $this->clientModel->setValidationRule('email',
            "required|valid_email|max_length[150]|is_unique[clients.email,id,{$id}]");

        if (! $this->clientModel->update($client['id'], $data)) {
            return redirect()->back()->withInput()->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client mis à jour.');
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): RedirectResponse
    {
        $this->clientModel->findOrFail($id);
        $this->clientModel->delete($id);

        return redirect()->to(base_url('clients'))->with('success', 'Client supprimé.');
    }
}
