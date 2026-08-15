<?php

declare(strict_types=1);

use App\Controllers\ApiInfoController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\CategoryController;
use App\Controllers\LoanController;
use App\Controllers\UserController;

// ---------------------------- Page d'accueil de l'API -----------------------
$router->get('/api', [ApiInfoController::class, 'index']);

// ---------------------------- Authentification -----------------------------
$router->post('/api/auth/login', [AuthController::class, 'login']);
$router->post('/api/auth/logout', [AuthController::class, 'logout'], ['auth']);
$router->get('/api/auth/me', [AuthController::class, 'me'], ['auth']);

// ---------------------------- Livres (lecture publique) --------------------
$router->get('/api/books', [BookController::class, 'index']);
$router->get('/api/books/{id}', [BookController::class, 'show']);

// ---------------------------- Livres (écriture : admin) ---------------------
$router->post('/api/books', [BookController::class, 'store'], ['auth', 'role:Admin']);
$router->put('/api/books/{id}', [BookController::class, 'update'], ['auth', 'role:Admin']);
$router->delete('/api/books/{id}', [BookController::class, 'destroy'], ['auth', 'role:Admin']);

// ---------------------------- Catégories ------------------------------------
$router->get('/api/categories', [CategoryController::class, 'index']);
$router->get('/api/categories/{id}/books', [CategoryController::class, 'books']);

// ---------------------------- Emprunts --------------------------------------
$router->get('/api/loans/my', [LoanController::class, 'my'], ['auth']);
$router->get('/api/loans', [LoanController::class, 'index'], ['auth', 'role:Admin,Bibliothecaire']);
$router->post('/api/books/{id}/borrow', [LoanController::class, 'borrow'], ['auth']);
$router->post('/api/loans/{id}/return', [LoanController::class, 'returnBook'], ['auth']);

// ---------------------------- Utilisateurs ----------------------------------
$router->get('/api/users', [UserController::class, 'index'], ['auth', 'role:Admin']);
