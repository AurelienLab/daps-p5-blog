<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Checks if data is a valid and not empty array of blocks (EditorJS)
 */
class PostContentBlocksValidator extends AbstractValidator
{

    
    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "L'article doit avoir un contenu";
    }


    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        $data = json_decode($data, true);
        if ($data['blocks'] === null) {
            return false;
        }

        return !empty($data['blocks']);
    }


}
