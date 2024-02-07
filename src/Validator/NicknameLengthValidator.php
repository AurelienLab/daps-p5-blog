<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class NicknameLengthValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Le nom d'utilisateur doit contenir au moins 3 caractères";
    }

    protected function processData($data): bool
    {
        return strlen(trim($data)) >= 3;
    }
}
