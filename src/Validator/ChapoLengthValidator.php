<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Check the length of post excerpt
 */
class ChapoLengthValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le chapo doit contenir au moins 50 caractères";
    }


    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        return strlen(trim($data)) >= 50;
    }


}
