<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if the password fulfill requirements
 */
class PasswordStrengthValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le mot de passe doit contenir au minimum: 8 caractères, 1 lettre minuscule, 1 caractère special";
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        $passwordPattern = '/^';
        $passwordPattern .= '(?=.*?[0-9])'; // At least 1 number
        $passwordPattern .= '(?=.*?[#?!@$%^&*-])'; // At least 1 special char
        $passwordPattern .= '.{8,}'; // At least 8 chars
        $passwordPattern .= '$/';

        return preg_match($passwordPattern, $data);
    }


}
