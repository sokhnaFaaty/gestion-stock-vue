<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\BorrowRepositoryInterface;
use App\Services\BorrowService;
use Core\Request;
use Core\Response;

class LoanController
{
    public function __construct(
        private BorrowService $borrows,
        private BorrowRepositoryInterface $borrowRepository,
    ) {}

    /** Emprunts de l'utilisateur connecté. */
    public function my(Request $request): Response
    {
        $loans = $this->borrowRepository->byUser($request->user()->id);

        return Response::json(array_map(fn ($loan) => $loan->toArray(), $loans));
    }

    /** Tous les emprunts (personnel). */
    public function index(Request $request): Response
    {
        $loans = $this->borrowRepository->all();

        return Response::json(array_map(fn ($loan) => $loan->toArray(), $loans));
    }

    public function borrow(Request $request, int $bookId): Response
    {
        $loan = $this->borrows->borrow($request->user()->id, $bookId);

        return Response::json($loan->toArray(), 201);
    }

    public function returnBook(Request $request, int $loanId): Response
    {
        $user = $request->user();
        $this->borrows->return($loanId, $user->id, $user->role);

        return Response::json(['message' => 'Livre retourné avec succès.']);
    }
}
