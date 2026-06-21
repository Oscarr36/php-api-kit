<?php
declare(strict_types=1);

class RateLimitMiddleware
{
    private int    $max;
    private int    $window;
    private string $storageDir;

    public function __construct(array $config)
    {
        $this->max        = $config['max_requests'];
        $this->window     = $config['window'];
        $this->storageDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-api-kit-rl' . DIRECTORY_SEPARATOR;

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0700, true);
        }
    }

    public function handle(Request $request): bool
    {
        $key  = md5($request->ip());
        $file = $this->storageDir . $key . '.json';
        $now  = time();

        $data = file_exists($file)
            ? (json_decode(file_get_contents($file), true) ?? [])
            : [];

        if (empty($data) || $now >= $data['reset']) {
            $data = ['count' => 0, 'reset' => $now + $this->window];
        }

        $data['count']++;
        file_put_contents($file, json_encode($data), LOCK_EX);

        $remaining = max(0, $this->max - $data['count']);

        header("X-RateLimit-Limit: {$this->max}");
        header("X-RateLimit-Remaining: {$remaining}");
        header("X-RateLimit-Reset: {$data['reset']}");

        return $data['count'] <= $this->max;
    }
}
