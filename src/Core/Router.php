<?php
declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler): void    { $this->add('GET',    $path, $handler); }
    public function post(string $path, array|callable $handler): void   { $this->add('POST',   $path, $handler); }
    public function put(string $path, array|callable $handler): void    { $this->add('PUT',    $path, $handler); }
    public function patch(string $path, array|callable $handler): void  { $this->add('PATCH',  $path, $handler); }
    public function delete(string $path, array|callable $handler): void { $this->add('DELETE', $path, $handler); }

    private function add(string $method, string $path, array|callable $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(Request $request, Response $response, array $config): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) continue;

            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            if (!preg_match("#^{$pattern}$#", $request->path(), $matches)) continue;

            $request->setParams(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));

            $handler = $route['handler'];

            if ($handler instanceof Closure) {
                $handler($request, $response, $config);
            } else {
                [$class, $method] = $handler;
                (new $class($config))->$method($request, $response);
            }

            return;
        }

        $response->json(['success' => false, 'message' => 'Route not found'], 404);
    }
}
