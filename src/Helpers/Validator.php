<?php
declare(strict_types=1);

class Validator
{
    private array $errors = [];

    public function required(array $data, array $fields): static
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string)$data[$field]) === '') {
                $this->errors[$field][] = "The field '{$field}' is required.";
            }
        }
        return $this;
    }

    public function email(array $data, string $field): static
    {
        if (isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The field '{$field}' must be a valid email address.";
        }
        return $this;
    }

    public function min(array $data, string $field, int $min): static
    {
        if (isset($data[$field]) && strlen((string)$data[$field]) < $min) {
            $this->errors[$field][] = "The field '{$field}' must be at least {$min} characters.";
        }
        return $this;
    }

    public function max(array $data, string $field, int $max): static
    {
        if (isset($data[$field]) && strlen((string)$data[$field]) > $max) {
            $this->errors[$field][] = "The field '{$field}' must not exceed {$max} characters.";
        }
        return $this;
    }

    public function numeric(array $data, string $field): static
    {
        if (isset($data[$field]) && !is_numeric($data[$field])) {
            $this->errors[$field][] = "The field '{$field}' must be numeric.";
        }
        return $this;
    }

    public function in(array $data, string $field, array $values): static
    {
        if (isset($data[$field]) && !in_array($data[$field], $values, true)) {
            $this->errors[$field][] = "The field '{$field}' must be one of: " . implode(', ', $values) . '.';
        }
        return $this;
    }

    public function fails(): bool  { return !empty($this->errors); }
    public function passes(): bool { return empty($this->errors); }
    public function errors(): array { return $this->errors; }
}
