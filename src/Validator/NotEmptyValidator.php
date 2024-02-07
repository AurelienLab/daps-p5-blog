<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class NotEmptyValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Le champ ne doit pas être vide.";
    }

    protected function processData($data): bool
    {
        if ($data === null) {
            return false;
        }

        if (is_array($data)) {
            return !empty($data);
        }

        return !empty(trim($data));
    }
}
