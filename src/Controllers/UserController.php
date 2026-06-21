<?php
declare(strict_types=1);

class UserController
{
    private PDO   $db;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db     = Database::connect($config['db']);
    }

    public function index(Request $request, Response $response): void
    {
        $this->requireRole($request, $response, 'admin');

        $perPage = max(1, min(100, (int) $request->input('per_page', 20)));
        $page    = max(1, (int) $request->input('page', 1));
        $offset  = ($page - 1) * $perPage;

        $model = new User($this->db);
        $response->paginated($model->all($perPage, $offset), $model->count(), $page, $perPage);
    }

    public function show(Request $request, Response $response): void
    {
        $payload = $this->requireAuth($request, $response);
        $id      = (int) $request->param('id');

        if ($payload['role'] !== 'admin' && $payload['sub'] !== $id) {
            $response->error('Forbidden', 403);
        }

        $user = (new User($this->db))->findById($id);
        if (!$user) $response->error('User not found', 404);

        $response->success($user);
    }

    public function update(Request $request, Response $response): void
    {
        $payload = $this->requireAuth($request, $response);
        $id      = (int) $request->param('id');

        if ($payload['role'] !== 'admin' && $payload['sub'] !== $id) {
            $response->error('Forbidden', 403);
        }

        $data = $request->body();

        $v = (new Validator())
            ->min($data, 'name', 2)
            ->max($data, 'name', 100)
            ->email($data, 'email');

        if ($v->fails()) {
            $response->error('Validation failed', 422, $v->errors());
        }

        (new User($this->db))->update($id, $data);
        $user = (new User($this->db))->findById($id);

        $response->success($user, 'User updated successfully');
    }

    public function destroy(Request $request, Response $response): void
    {
        $payload = $this->requireRole($request, $response, 'admin');
        $id      = (int) $request->param('id');

        if ($payload['sub'] === $id) {
            $response->error('You cannot delete your own account', 400);
        }

        $model = new User($this->db);
        if (!$model->findById($id)) $response->error('User not found', 404);

        $model->delete($id);
        $response->success(null, 'User deleted successfully');
    }

    private function requireAuth(Request $request, Response $response): array
    {
        $payload = (new AuthMiddleware($this->config))->handle($request);
        if (!$payload) $response->error('Unauthorized', 401);
        return $payload;
    }

    private function requireRole(Request $request, Response $response, string $role): array
    {
        $payload = $this->requireAuth($request, $response);
        if ($payload['role'] !== $role) $response->error('Forbidden', 403);
        return $payload;
    }
}
