<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Interfaces\UserRepositoryInterface;
use Core\Request;
use Core\Response;

class UserController
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function index(Request $request): Response
    {
        $users = array_map(fn ($user) => $user->toArray(), $this->users->all());

        return Response::json($users);
    }
}
