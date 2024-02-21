<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Check if nickname is at least 3 chars
 */
class NicknameLengthValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le nom d'utilisateur doit contenir au moins 3 caractères";
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        return strlen(trim($data)) >= 3;
    }
}
