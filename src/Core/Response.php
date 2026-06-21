<?php
declare(strict_types=1);

class Response
{
    public function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit();
    }

    public function success(mixed $data = null, string $message = 'OK', int $status = 200): never
    {
        $body = ['success' => true, 'message' => $message];
        if ($data !== null) $body['data'] = $data;
        $this->json($body, $status);
    }

    public function error(string $message, int $status = 400, array $errors = []): never
    {
        $body = ['success' => false, 'message' => $message];
        if ($errors) $body['errors'] = $errors;
        $this->json($body, $status);
    }

    public function paginated(array $items, int $total, int $page, int $perPage): never
    {
        $this->json([
            'success'     => true,
            'data'        => $items,
            'meta'        => [
                'total'       => $total,
                'page'        => $page,
                'per_page'    => $perPage,
                'total_pages' => (int)ceil($total / $perPage),
                'has_more'    => ($page * $perPage) < $total,
            ],
        ]);
    }
}
