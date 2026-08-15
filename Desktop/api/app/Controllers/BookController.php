<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use Core\Request;
use Core\Response;
use Exceptions\NotFoundException;
use Exceptions\ValidationException;

class BookController
{
    public function __construct(
        private BookRepositoryInterface $books,
        private CategoryRepositoryInterface $categories,
    ) {}

    public function index(Request $request): Response
    {
        $term     = trim((string) $request->query('q', ''));
        $category = $request->query('categorie');
        $categoryId = is_numeric($category) ? (int) $category : null;

        $page    = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(1, (int) $request->query('per_page', 10)));

        $total = $this->books->searchCount($term, $categoryId);
        $books = $this->books->search($term, $categoryId, $perPage, ($page - 1) * $perPage);

        return Response::json([
            'items' => array_map(fn ($book) => $book->toArray(), $books),
            'meta'  => [
                'total'    => $total,
                'page'     => $page,
                'per_page' => $perPage,
                'pages'    => (int) ceil($total / $perPage),
            ],
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $book = $this->books->findById($id);

        if ($book === null) {
            throw new NotFoundException('Livre introuvable.');
        }

        return Response::json($book->toArray());
    }

    public function store(Request $request): Response
    {
        $data = $this->validate($request->all());

        return Response::json($this->books->create($data)->toArray(), 201);
    }

    public function update(Request $request, int $id): Response
    {
        if ($this->books->findById($id) === null) {
            throw new NotFoundException('Livre introuvable.');
        }

        $data = $this->validate($request->all());
        $this->books->update($id, $data);

        return Response::json($this->books->findById($id)->toArray());
    }

    public function destroy(Request $request, int $id): Response
    {
        if ($this->books->findById($id) === null) {
            throw new NotFoundException('Livre introuvable.');
        }

        $this->books->delete($id);

        return Response::noContent();
    }

    /** @param array<string, mixed> $input */
    private function validate(array $input): array
    {
        $errors = [];

        foreach (['isbn', 'titre', 'auteur'] as $field) {
            if (trim((string) ($input[$field] ?? '')) === '') {
                $errors[$field] = "Le champ $field est requis.";
            }
        }

        $quantite = $input['quantite'] ?? null;
        if ($quantite === null || !is_numeric($quantite) || (int) $quantite < 0) {
            $errors['quantite'] = 'La quantité doit être un entier positif.';
        }

        $categorieId = $input['categorie_id'] ?? null;
        if ($categorieId !== null && $categorieId !== '') {
            if (!is_numeric($categorieId) || $this->categories->findById((int) $categorieId) === null) {
                $errors['categorie_id'] = 'La catégorie n\'existe pas.';
            }
        }

        $datePublication = $input['date_publication'] ?? null;
        if ($datePublication !== null && $datePublication !== ''
            && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $datePublication)) {
            $errors['date_publication'] = 'Format attendu : AAAA-MM-JJ.';
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return [
            'isbn'              => trim((string) $input['isbn']),
            'titre'             => trim((string) $input['titre']),
            'auteur'            => trim((string) $input['auteur']),
            'description'       => trim((string) ($input['description'] ?? '')),
            'date_publication'  => $datePublication !== '' ? (string) $datePublication : null,
            'quantite'          => (int) $quantite,
            'categorie_id'      => is_numeric($categorieId) ? (int) $categorieId : null,
            'couverture'        => ($input['couverture'] ?? '') !== '' ? (string) $input['couverture'] : null,
        ];
    }
}
