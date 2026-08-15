<?php

declare(strict_types=1);

namespace Core;

use App\Middleware\ApiMiddleware;
use Exceptions\ApiException;
use Exceptions\ValidationException;

/**
 * Routeur HTTP : associe une méthode + un chemin à un contrôleur, avec des
 * paramètres {id} (entiers) et des middlewares ('auth', 'role:Admin', …).
 * Renvoie toujours une Response.
 */
class Router
{
    /** @var array<int, array{method: string, path: string, action: array{0: string, 1: string}, middleware: string[]}> */
    private array $routes = [];

    public function __construct(private Container $container) {}

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    public function put(string $path, array $action, array $middleware = []): void
    {
        $this->add('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, array $action, array $middleware = []): void
    {
        $this->add('DELETE', $path, $action, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            $params = $this->match($route['path'], $request->path());
            if ($params === null) {
                continue;
            }

            try {
                $this->runMiddleware($route['middleware'], $request);

                [$class, $action] = $route['action'];
                $controller = $this->container->make($class);
                $output = $controller->{$action}($request, ...$params);

                return $output instanceof Response ? $output : Response::json($output);
            } catch (ApiException $e) {
                return Response::error(
                    $e->getMessage(),
                    $e->status,
                    $e instanceof ValidationException ? $e->errors : []
                );
            } catch (\Throwable $e) {
                return Response::error('Erreur interne du serveur.', 500);
            }
        }

        return Response::error('Ressource introuvable.', 404);
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    /** @return int[] */
    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('#\{[a-zA-Z_]+\}#', '(\d+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return null;
        }

        array_shift($matches);
        return array_map('intval', $matches);
    }

    /** @param string[] $middleware */
    private function runMiddleware(array $middleware, Request $request): void
    {
        if ($middleware === []) {
            return;
        }

        $instance = $this->container->make(ApiMiddleware::class);

        foreach ($middleware as $entry) {
            if (str_contains($entry, ':')) {
                [$name, $args] = array_pad(explode(':', $entry, 2), 2, '');
                $instance->{$name}($request, ...explode(',', $args));
            } else {
                $instance->{$entry}($request);
            }
        }
    }
}
