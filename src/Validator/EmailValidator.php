<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if email is valid
 * Transforms data to lower trimmed email
 */
class EmailValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le format de l'adresse email est incorrect";
    }


    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        $cleanEmail = trim(strtolower($data));
        $this->transformData($cleanEmail);
        return filter_var(trim(strtolower($data)), FILTER_VALIDATE_EMAIL);
    }


}
