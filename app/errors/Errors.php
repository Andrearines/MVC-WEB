<?php

namespace errors;

class Errors
{
    private array $errors = [];

    public function addError(string $field, string $message): void
    {
        // Si no existe la llave para este campo, la inicializamos
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors(): object
    {
        return (object) $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }
}
