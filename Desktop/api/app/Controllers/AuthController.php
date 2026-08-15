<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Request;
use Core\Response;
use Exceptions\ValidationException;

class AuthController
{
    public function __construct(private AuthService $auth) {}

    public function login(Request $request): Response
    {
        $email    = trim((string) $request->input('email', ''));
        $password = (string) $request->input('password', '');

        if ($email === '' || $password === '') {
            throw new ValidationException(
                ['email' => 'Le champ email est requis.', 'password' => 'Le champ password est requis.']
            );
        }

        $result = $this->auth->attempt($email, $password);
        $result['user'] = $result['user']->toArray();

        return Response::json($result);
    }

    public function logout(Request $request): Response
    {
        $this->auth->revoke((string) ($request->bearerToken() ?? ''));
        return Response::noContent();
    }

    public function me(Request $request): Response
    {
        return Response::json($request->user()->toArray());
    }
}
