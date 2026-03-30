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
        $builder->where('deleted_at', null);

        if ($search !== '') {
            $builder->groupStart()
                ->like('nom', $search)
                ->orLike('prenom', $search)
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
            // Formater le téléphone en XX XX XX XX XX pour l'affichage
            if (! empty($row['telephone'])) {
                $digits = preg_replace('/\D/', '', $row['telephone']);
                if (strlen($digits) === 10) {
                    $row['telephone'] = implode(' ', str_split($digits, 2));
                }
            }

            // Nom complet Prénom + Nom
            $row['nom_complet'] = trim(($row['prenom'] ?? '') . ' ' . $row['nom']);

            $row['actions'] = sprintf(
                '<a href="%s" class="btn btn-sm btn-outline-primary me-1" title="Modifier"><i class="bi bi-pencil"></i></a>'
                . '<form action="%s" method="post" class="d-inline">'
                . csrf_field()
                . '<button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" data-confirm="Supprimer le client &laquo;%s&raquo; ?"><i class="bi bi-trash"></i></button>'
                . '</form>',
                base_url('clients/' . $row['id'] . '/edit'),
                base_url('clients/' . $row['id'] . '/delete'),
                htmlspecialchars($row['nom_complet'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
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
        $data = $this->request->getPost(['prenom', 'nom', 'email', 'telephone', 'adresse_numero', 'adresse_type_voie', 'adresse_nom_voie', 'ville', 'code_postal']);

        $data = $this->normalizeClientData($data);

        if (! $this->clientModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client créé avec succès.');
    }

    // ─── Édition ─────────────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $client = $this->clientModel->find($id);
        if (! $client) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('clients/form', ['titre' => 'Modifier le client', 'client' => $client]);
    }

    public function update(int $id): RedirectResponse
    {
        $client = $this->clientModel->find($id);
        if (! $client) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data = $this->request->getPost(['prenom', 'nom', 'email', 'telephone', 'adresse_numero', 'adresse_type_voie', 'adresse_nom_voie', 'ville', 'code_postal']);

        $data = $this->normalizeClientData($data);

        $this->clientModel->setValidationRule('email',
            "required|valid_email|max_length[150]|is_unique[clients.email,id,{$id}]");

        if (! $this->clientModel->update($client['id'], $data)) {
            return redirect()->back()->withInput()->with('errors', $this->clientModel->errors());
        }

        return redirect()->to(base_url('clients'))->with('success', 'Client mis à jour.');
    }

    // ─── Helpers privés ──────────────────────────────────────────────────────

    /**
     * Normalise le téléphone (chiffres seuls) et capitalise nom/ville.
     */
    private function normalizeClientData(array $data): array
    {
        if (! empty($data['telephone'])) {
            $data['telephone'] = substr(preg_replace('/\D/', '', $data['telephone']), 0, 10);
        }

        foreach (['nom', 'prenom', 'ville', 'adresse_type_voie', 'adresse_nom_voie'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = $this->capitalizeWords($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Capitalise la première lettre de chaque mot (espaces et tirets).
     */
    private function capitalizeWords(string $str): string
    {
        $lower = mb_strtolower(trim($str), 'UTF-8');

        return preg_replace_callback(
            '/(?:^|[\s\-])\p{L}/u',
            static fn (array $m): string => mb_strtoupper($m[0], 'UTF-8'),
            $lower
        );
    }

    // ─── Suppression ─────────────────────────────────────────────────────────

    public function delete(int $id): RedirectResponse
    {
        if (! $this->clientModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->clientModel->delete($id);

        return redirect()->to(base_url('clients'))->with('success', 'Client supprimé.');
    }
}
