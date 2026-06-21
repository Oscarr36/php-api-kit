<?php
declare(strict_types=1);

class AuthMiddleware
{
    private string $secret;

    public function __construct(array $config)
    {
        $this->secret = $config['jwt_secret'];
    }

    public function handle(Request $request): ?array
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        return JWT::decode($token, $this->secret);
    }
}
