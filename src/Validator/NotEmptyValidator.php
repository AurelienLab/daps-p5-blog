<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if the value is not empty
 */
class NotEmptyValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le champ ne doit pas être vide.";
    }

    /**
     * @param $data
     *
     * @return bool
     */
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
