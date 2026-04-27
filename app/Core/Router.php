<?php

declare(strict_types=1);

namespace App\Core;

class Router {
    private array $routes = [];

    /**
     * Add a route to the list
     */
    public function add(string $uri, callable $action): void {
        $this->routes[$uri] = $action;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(string $uri): void {
        // Clean the URI
        $uri = '/' . ltrim($uri, '/');

        if (isset($this->routes[$uri])) {
            $this->routes[$uri]();
            return;
        }

        // Check for Dynamic Routes (Profile usernames)
        $username = ltrim($uri, '/');
        if (!empty($username) && !str_contains($username, '/')) {
            (new \App\Controllers\ProfileController())->show($username);
            return;
        }

        header("HTTP/1.0 404 Not Found");
        // We will call the 404 view from here later if needed
        (new \App\Controllers\ProfileController())->view('errors/404');
    }
}
