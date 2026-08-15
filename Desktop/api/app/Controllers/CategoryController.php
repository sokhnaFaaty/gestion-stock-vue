<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use Core\Request;
use Core\Response;
use Exceptions\NotFoundException;

class CategoryController
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
        private BookRepositoryInterface $books,
    ) {}

    public function index(Request $request): Response
    {
        return Response::json(array_map(fn ($c) => $c->toArray(), $this->categories->all()));
    }

    public function books(Request $request, int $id): Response
    {
        if ($this->categories->findById($id) === null) {
            throw new NotFoundException('Catégorie introuvable.');
        }

        $books = $this->books->search('', $id, 100, 0);

        return Response::json(array_map(fn ($book) => $book->toArray(), $books));
    }
}
