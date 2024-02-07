<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class JsonValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "La valeur n'est pas un JSON valide";
    }

    protected function processData($data): bool
    {
        $decodedContent = json_decode($data, true);
        if ($decodedContent !== null) {
            return true;
        }

        return false;
    }
}
