# Mini-ERP — CodeIgniter 4

Application ERP pédagogique développée avec **CodeIgniter 4**, Bootstrap 5, jQuery et DataTables.

## Fonctionnalités

| Module | Fonctions |
|---|---|
| **Clients** | CRUD complet, liste DataTables Ajax |
| **Produits** | CRUD complet, gestion des stocks |
| **Commandes** | CRUD avec lignes, calcul automatique HT/TVA/TTC |
| **Factures PDF** | Génération via dompdf |

## Stack technique

- **Backend** : PHP 8.4.0 / CodeIgniter 4.7.2
- **Frontend** : Bootstrap 5.3.8, jQuery 4.0.0, DataTables 2.3.7 (mode Ajax server-side)
- **PDF** : dompdf 3.x
- **BDD** : MySQL (via MySQLi)

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Taglhydz/Mini-ERP-CI4.git
cd Mini-ERP-CI4

# 2. Installer les dépendances
composer install

# 3. Copier et configurer l'environnement
cp env .env
# Éditer .env : CI_ENVIRONMENT, app.baseURL, database.*

# 4. Créer la base de données MySQL
# CREATE DATABASE mini_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. Exécuter les migrations
php spark migrate

# 6. Lancer le serveur de développement
php spark serve
```

L'application sera disponible sur [http://localhost:8080](http://localhost:8080).

## Structure du projet

```
app/
├── Config/
│   └── Routes.php          # Toutes les routes ERP
├── Controllers/
│   ├── DashboardController.php
│   ├── ClientController.php
│   ├── ProduitController.php
│   └── CommandeController.php
├── Models/
│   ├── ClientModel.php
│   ├── ProduitModel.php
│   ├── CommandeModel.php
│   └── LigneCommandeModel.php
├── Database/
│   └── Migrations/         # 4 migrations (clients, produits, commandes, lignes)
└── Views/
    ├── layouts/main.php    # Layout Bootstrap 5 + jQuery + DataTables
    ├── partials/           # Composants réutilisables (badge statut…)
    ├── dashboard/
    ├── clients/
    ├── produits/
    └── commandes/          # Inclut pdf_facture.php pour dompdf
```

## Branches Git

| Branche | Rôle |
|---|---|
| `main` | Code stable, intégration continue |
| `feature/*` | Développement de fonctionnalités |
| `fix/*` | Correctifs |
