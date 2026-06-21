<?php
declare(strict_types=1);

class AuthController
{
    private PDO   $db;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db     = Database::connect($config['db']);
    }

    public function register(Request $request, Response $response): void
    {
        $data = $request->body();

        $v = (new Validator())
            ->required($data, ['name', 'email', 'password'])
            ->email($data, 'email')
            ->min($data, 'name', 2)
            ->max($data, 'name', 100)
            ->min($data, 'password', 8)
            ->max($data, 'password', 72);

        if ($v->fails()) {
            $response->error('Validation failed', 422, $v->errors());
        }

        $userModel = new User($this->db);

        if ($userModel->findByEmail($data['email'])) {
            $response->error('Email already registered', 409);
        }

        $id   = $userModel->create(trim($data['name']), strtolower(trim($data['email'])), $data['password']);
        $user = $userModel->findById($id);

        $response->success([
            'user'  => $user,
            'token' => $this->token($user),
        ], 'Account created successfully', 201);
    }

    public function login(Request $request, Response $response): void
    {
        $data = $request->body();

        $v = (new Validator())
            ->required($data, ['email', 'password'])
            ->email($data, 'email');

        if ($v->fails()) {
            $response->error('Validation failed', 422, $v->errors());
        }

        $userModel = new User($this->db);
        $user      = $userModel->findByEmail(strtolower(trim($data['email'])));

        if (!$user || !password_verify($data['password'], $user['password'])) {
            $response->error('Invalid credentials', 401);
        }

        unset($user['password']);

        $response->success([
            'user'  => $user,
            'token' => $this->token($user),
        ], 'Login successful');
    }

    public function me(Request $request, Response $response): void
    {
        $payload = $this->auth($request, $response);
        $user    = (new User($this->db))->findById($payload['sub']);

        if (!$user) $response->error('User not found', 404);

        $response->success($user);
    }

    public function refresh(Request $request, Response $response): void
    {
        $payload = $this->auth($request, $response);
        $user    = (new User($this->db))->findById($payload['sub']);

        if (!$user) $response->error('User not found', 404);

        $response->success(['token' => $this->token($user)], 'Token refreshed');
    }

    private function auth(Request $request, Response $response): array
    {
        $payload = (new AuthMiddleware($this->config))->handle($request);
        if (!$payload) $response->error('Unauthorized', 401);
        return $payload;
    }

    private function token(array $user): string
    {
        return JWT::encode([
            'sub'  => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'iat'  => time(),
            'exp'  => time() + $this->config['jwt_ttl'],
        ], $this->config['jwt_secret']);
    }
}
