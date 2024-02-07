<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;
use App\Repository\UserRepository;

class ExistingEmailValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "L'adresse email est déjà utilisée";
    }

    protected function processData($data): bool
    {
        return !UserRepository::isEmailExist($data);
    }
}
