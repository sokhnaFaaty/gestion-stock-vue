# API REST — Bibliothèque du Savoir

API **JSON** du projet fil rouge, construite en **PHP orienté objet** avec la même
architecture en couches (inspirée de Laravel) que l'application web `bibliotheque/`.
Elle expose les mêmes données et règles métier, mais en **mode headless** :
elle peut être consommée par une application mobile, un site en JavaScript, etc.

L'authentification repose sur des **tokens Bearer** (concept de l'atelier 19) :
`POST /api/auth/login` renvoie un jeton que le client envoie dans l'en-tête
`Authorization` pour les routes protégées.

## Fonctionnalités

| Module | Routes |
|---|---|
| **Authentification** | login (token Bearer, 24 h), déconnexion (révocation), profil de l'utilisateur connecté |
| **Livres** | lister / rechercher / filtrer par catégorie (public), consulter une fiche (public), créer / modifier / supprimer (Admin) |
| **Catégories** | lister (public), livres d'une catégorie (public) |
| **Emprunts** | emprunter un livre, retourner, mes emprunts (Lecteur), tous les emprunts (Admin / Biblio) |
| **Utilisateurs** | lister (Admin) |

### Sécurité

- mots de passe vérifiés avec `password_verify()` (jamais transmis, jamais renvoyés par l'API) ;
- token généré avec `random_bytes(32)` (64 caractères hexadécimaux), **expiration 24 h** ;
- seul le **hash SHA-256** du token est stocké en base (`api_tokens`) : une fuite de base ne permet pas d'usurper les jetons ;
- déconnexion = suppression du token en base (**révocation immédiate**) ;
- requêtes SQL préparées (PDO), validation des entrées (erreur 422).

### Règles métier (identiques au projet web)

- un lecteur ne peut pas emprunter plus de **3 livres** en même temps ;
- un livre **indisponible** (quantité à 0) ne peut pas être emprunté ;
- un lecteur ne peut pas emprunter **le même livre deux fois** simultanément ;
- lors d'un emprunt, le **stock diminue automatiquement** ; lors d'un retour, il **augmente** ;
- la date de retour prévue est fixée à **+21 jours** ;
- seul l'**emprunteur** (ou un membre du personnel) peut rendre un emprunt.

## Format des réponses

Toutes les réponses sont en JSON.

**Succès (200/201)**

```json
{ "data": { "id": 1, "titre": "Le Petit Prince", "disponible": true } }
```

**Succès sans contenu (204)** : corps vide (suppression, déconnexion).

**Erreur (4xx/5xx)**

```json
{ "error": "Le champ titre est requis.", "code": 422, "errors": { "titre": "Le champ titre est requis." } }
```

Codes HTTP utilisés : `200` OK · `201` créé · `204` sans contenu · `400` requête invalide ·
`401` non authentifié · `403` accès refusé · `404` introuvable · `409` conflit métier ·
`422` validation · `500` erreur serveur.

## Comptes de démonstration

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Administrateur | `admin@biblio.fr` | `admin123` |
| Bibliothécaire | `biblio@biblio.fr` | `biblio123` |
| Lecteur | `alice.martin@mail.fr` | `lecteur123` |

## Installation

### 1. Prérequis

- PHP 8.1+ (extension PDO MySQL activée)
- MySQL / MariaDB (XAMPP, WAMP ou Laragon)

### 2. Créer les données

L'API partage la base `bibliotheque` avec l'application web. Depuis `bibliotheque/` :

```bash
php database/seed.php        # crée la base, les tables et les données de démo
```

Puis depuis le dossier `api/` :

```bash
php database/seed.php        # crée la table api_tokens
```

### 3. Configurer la base

Éditer `config/database.php` et renseigner vos identifiants MySQL (mêmes que le projet web).

### 4. Lancer l'API

```bash
php -S localhost:8001 -t public public/index.php
```

Puis ouvrir <http://localhost:8001/api/books> : vous devriez voir la liste des livres en JSON.

> Avec Apache, copier le dossier `api/` dans `htdocs/` : le `.htaccess` renvoie tout vers `public/index.php`.

## Utilisation (exemples `curl`)

**1. Se connecter et récupérer un token**

```bash
curl -X POST http://localhost:8001/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email": "admin@biblio.fr", "password": "admin123"}'
```

```json
{ "data": { "token": "f3a9c1d4e5b6…", "type": "Bearer", "expires_at": "2026-08-09 12:00:00", "user": { … } } }
```

> Conservez le token (64 caractères hexadécimaux) : il n'est montré **qu'une seule fois**,
> seule son empreinte est en base.

**2. Consulter des données publiques**

```bash
curl http://localhost:8001/api/books
curl "http://localhost:8001/api/books?q=petit&page=1&per_page=5"
curl http://localhost:8001/api/categories
curl http://localhost:8001/api/books/1
```

**3. Appeler une route protégée** (en-tête `Authorization`)

```bash
curl http://localhost:8001/api/auth/me -H "Authorization: Bearer <TOKEN>"
```

**4. Créer un livre (Admin)**

```bash
curl -X POST http://localhost:8001/api/books \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer <TOKEN_ADMIN>" \
     -d '{"isbn":"978-0000000001","titre":"Mon livre","auteur":"Moi","description":"…","date_publication":"2026-01-01","quantite":2,"categorie_id":1}'
```

**5. Emprunter puis rendre un livre (Lecteur)**

```bash
curl -X POST http://localhost:8001/api/books/3/borrow -H "Authorization: Bearer <TOKEN_LECTEUR>"
curl http://localhost:8001/api/loans/my -H "Authorization: Bearer <TOKEN_LECTEUR>"
curl -X POST http://localhost:8001/api/loans/12/return -H "Authorization: Bearer <TOKEN_LECTEUR>"
```

**6. Se déconnecter (révoquer le token)**

```bash
curl -X POST http://localhost:8001/api/auth/logout -H "Authorization: Bearer <TOKEN>"
```

## Tableau des routes

| Méthode | Route | Protection | Description |
|---|---|---|---|
| POST | `/api/auth/login` | publique | renvoie un token (24 h) |
| POST | `/api/auth/logout` | token | révoque le token courant |
| GET | `/api/auth/me` | token | profil de l'utilisateur connecté |
| GET | `/api/books` | publique | liste paginée (`q`, `categorie`, `page`, `per_page`) |
| GET | `/api/books/{id}` | publique | fiche d'un livre |
| POST | `/api/books` | Admin | crée un livre |
| PUT | `/api/books/{id}` | Admin | modifie un livre |
| DELETE | `/api/books/{id}` | Admin | supprime un livre |
| GET | `/api/categories` | publique | liste des catégories |
| GET | `/api/categories/{id}/books` | publique | livres d'une catégorie |
| GET | `/api/loans/my` | token | emprunts de l'utilisateur connecté |
| GET | `/api/loans` | Admin, Biblio | tous les emprunts |
| POST | `/api/books/{id}/borrow` | token | emprunte un livre |
| POST | `/api/loans/{id}/return` | token | rend un livre (emprunteur ou personnel) |
| GET | `/api/users` | Admin | liste des utilisateurs (sans mot de passe) |

## Tests

Depuis le dossier `api/`, après avoir (re)initialisé la base :

```bash
php tests/run.php
```

45 tests fonctionnels s'exécutent **sans serveur** (appels in-process au routeur) et
vérifient : authentification (200/401/422, révocation), lecture publique, CRUD livres
(201/204/404/422, rôles 401/403), règles d'emprunt (409 double emprunt, livre indisponible,
retour par autrui → 403) et droits d'accès.

> Les tests **mutent la base** (livres créés/supprimés, emprunts, tokens) :
> relancez les deux `seed.php` avant chaque passage pour repartir d'un état propre.

## Architecture

```
api/
├── Core/                  Le mini-framework : Router, Container, Database, Request, Response
├── Exceptions/            Hiérarchie : Api, Auth, NotFound, Validation, Business
├── app/
│   ├── Controllers/       Traitent la requête et renvoient une Response JSON
│   ├── Models/            Modèles métier (Book, Category, User, Loan) avec toArray()
│   ├── Repositories/      Accès aux données (SQL), programmés contre des interfaces
│   ├── Interfaces/        Contrats des repositories (dépendance d'abstraction)
│   ├── Services/          Logique métier (AuthService, BorrowService)
│   └── Middleware/        Contrôle d'accès par token (auth) et par rôle (role:)
├── bootstrap/             Autoloading (PSR-4 simplifié) + assemblage du container
├── config/                Configuration (base de données)
├── database/              api_tokens.sql + script de création de la table
├── public/                Point d'entrée unique (front controller) + CORS
├── routes/                Déclaration des routes et de leurs middlewares
└── tests/                 Jeu de tests fonctionnels
```

## Parcourir le code (bon point de départ)

1. `public/index.php` — le point d'entrée : CORS, front controller, envoi de la réponse.
2. `routes/api.php` — toutes les routes avec leurs protections.
3. `Core/Router.php` — correspondance URL → contrôleur, middlewares, gestion des exceptions.
4. `Core/Response.php` — le format JSON uniforme (succès / erreur).
5. `app/Services/AuthService.php` — génération du token, hachage SHA-256, révocation.
6. `app/Services/BorrowService.php` — les règles métier des emprunts.
7. `app/Middleware/ApiMiddleware.php` — validation du Bearer token et des rôles.
8. `tests/run.php` — les tests fonctionnels, exemples d'appels complets.
