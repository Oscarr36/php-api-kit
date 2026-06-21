<?php
declare(strict_types=1);

class App
{
    private Router $router;
    private array  $config;

    public function __construct()
    {
        $this->loadEnv();
        $this->config = require BASE_PATH . '/config/app.php';
        $this->router = new Router();
    }

    private function loadEnv(): void
    {
        $file = BASE_PATH . '/.env';
        if (!file_exists($file)) return;

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $_ENV[$key] = trim($value, '"\'');
        }
    }

    public function getRouter(): Router { return $this->router; }
    public function getConfig(): array  { return $this->config; }

    public function run(): void
    {
        (new CorsMiddleware($this->config['cors']))->handle();

        $request  = new Request();
        $response = new Response();

        $rateLimiter = new RateLimitMiddleware($this->config['rate_limit']);
        if (!$rateLimiter->handle($request)) {
            $response->error('Too Many Requests', 429);
            return;
        }

        $this->router->dispatch($request, $response, $this->config);
    }
}
