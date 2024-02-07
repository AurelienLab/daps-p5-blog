<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class ChapoLengthValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Le chapo doit contenir au moins 50 caractères";
    }

    protected function processData($data): bool
    {
        return strlen(trim($data)) >= 50;
    }
}
