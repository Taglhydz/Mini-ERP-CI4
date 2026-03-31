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

# 6. Ajouter les comptes d'exemple
php spark db:seed UsersSeeder

# 7. Lancer le serveur de développement
php spark serve
```

L'application sera disponible sur [http://localhost:8080](http://localhost:8080).

## Authentification

- Pages publiques : `/auth/login` pour se connecter et `/auth/register` pour créer un compte client ou user. Les rôles Admin/User/Client sont persistés dans la table `users`.
- Par défaut, la seed `UsersSeeder` ajoute trois comptes (email / mot de passe) :
    - `admin@mini-erp.test` / `Admin123!` (Admin — accès complet)
    - `user@mini-erp.test` / `User123!` (User — back-office identique à Admin)
    - `client@mini-erp.test` / `Client123!` (Client — accès restreint au dashboard)
- Les comptes Admin sont réservés à la seed : les nouveaux utilisateurs peuvent choisir le rôle `Client` ou `User` depuis le formulaire d'inscription.
- Les filtres `auth` et `role` appliquent la vérification côté routing pour bloquer l'accès aux blocs clients, produits et commandes aux rôles autorisés.
- Un jeton « remember me » (`auth_token`) est créé à chaque connexion réussie puis stocké dans la table `auth_tokens`. La valeur du token, composée du sélecteur et du validateur (`selector:validator`), est envoyée au navigateur en cookie `HttpOnly` (7 jours) et permet de réhydrater la session même après un redémarrage du navigateur.
- Les jetons expirés sont automatiquement purgés et invalidés lorsqu’ils sont détectés côté serveur.

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
