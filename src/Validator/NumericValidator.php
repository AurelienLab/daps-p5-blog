<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if the value is a number
 */
class NumericValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Vous devez entrer une valeur numérique";
    }


    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        return is_numeric($data);
    }


}
