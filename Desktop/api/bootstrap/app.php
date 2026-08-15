<?php

declare(strict_types=1);

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\BorrowRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\ApiTokenRepository;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use Core\Container;
use Core\Database;
use Core\Router;

require __DIR__ . '/autoload.php';

$container = new Container();

$pdo = Database::connect();

$container->bind(ApiTokenRepositoryInterface::class, fn () => new ApiTokenRepository($pdo));
$container->bind(UserRepositoryInterface::class,     fn () => new UserRepository($pdo));
$container->bind(CategoryRepositoryInterface::class, fn () => new CategoryRepository($pdo));
$container->bind(BookRepositoryInterface::class,     fn () => new BookRepository($pdo));
$container->bind(BorrowRepositoryInterface::class,   fn () => new BorrowRepository($pdo));

$router = new Router($container);

require __DIR__ . '/../routes/api.php';

return $router;
