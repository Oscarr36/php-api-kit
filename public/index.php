<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// Simple PSR-4-style autoloader
spl_autoload_register(function (string $class): void {
    $dirs = [
        BASE_PATH . '/src/Core/',
        BASE_PATH . '/src/Controllers/',
        BASE_PATH . '/src/Models/',
        BASE_PATH . '/src/Middleware/',
        BASE_PATH . '/src/Helpers/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$app    = new App();
$router = $app->getRouter();

// ─── Auth ────────────────────────────────────────────────────────────────────
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login',    [AuthController::class, 'login']);
$router->get('/api/auth/me',        [AuthController::class, 'me']);
$router->post('/api/auth/refresh',  [AuthController::class, 'refresh']);

// ─── Users ───────────────────────────────────────────────────────────────────
$router->get('/api/users',             [UserController::class, 'index']);
$router->get('/api/users/{id}',        [UserController::class, 'show']);
$router->put('/api/users/{id}',        [UserController::class, 'update']);
$router->patch('/api/users/{id}',      [UserController::class, 'update']);
$router->delete('/api/users/{id}',     [UserController::class, 'destroy']);

// ─── Health check ────────────────────────────────────────────────────────────
$router->get('/api/health', function (Request $req, Response $res): void {
    $res->json([
        'status'    => 'ok',
        'timestamp' => date('c'),
        'php'       => PHP_VERSION,
    ]);
});

$app->run();
