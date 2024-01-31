<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class PostContentBlocksValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "L'article doit avoir un contenu";
    }

    protected function processData($data): bool
    {
        $data = json_decode($data, true);
        if ($data['blocks'] === null) {
            return false;
        }

        return !empty($data['blocks']);
    }
}
