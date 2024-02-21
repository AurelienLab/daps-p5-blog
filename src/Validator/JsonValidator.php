<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if data is valid json and transforms it to array
 */
class JsonValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "La valeur n'est pas un JSON valide";
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        $decodedContent = json_decode($data, true);
        if ($decodedContent !== null) {
            return true;
        }

        return false;
    }
}
