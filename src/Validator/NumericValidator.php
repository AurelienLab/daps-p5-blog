<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class NumericValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Vous devez entrer une valeur numérique";
    }

    protected function processData($data): bool
    {
        return is_numeric($data);
    }
}
