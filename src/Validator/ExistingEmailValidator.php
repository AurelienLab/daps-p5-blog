<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;
use App\Repository\UserRepository;

/**
 * Checks if email is already registered in users table
 */
class ExistingEmailValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "L'adresse email est déjà utilisée";
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    protected function processData($data): bool
    {
        return !UserRepository::isEmailExist($data);
    }


}
