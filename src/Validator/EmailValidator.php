<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class EmailValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Le format de l'adresse email est incorrect";
    }

    protected function processData($data): bool
    {
        $cleanEmail = trim(strtolower($data));
        $this->transformData($cleanEmail);
        return filter_var(trim(strtolower($data)), FILTER_VALIDATE_EMAIL);
    }
}
