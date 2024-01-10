<?php

namespace App\Core\Form;

class FormErrorBag
{

    private array $errors = [];

    public function addError(string $fieldName, string $errorMessage): void
    {
        $this->errors[$fieldName] = $errorMessage;
    }

    public function getError(string $fieldName): ?string
    {
        if (isset($this->errors[$fieldName]) === true) {
            return $this->errors[$fieldName];
        }

        return null;
    }

    public function hasError()
    {
        return empty($this->errors) == false;
    }
}
