<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;
use App\Repository\PostCategoryRepository;

class PostCategoryValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Vous devez sélectionner une catégorie";
    }

    protected function processData($data): bool
    {
        if (!is_numeric($data) || empty($data)) {
            $this->transformData(null);
            return false;
        }

        $category = null;

        $category = PostCategoryRepository::get(intval($data));
        if ($category !== null) {
            $this->transformData($category);
            return true;
        }

        $this->transformData(null);
        return false;
    }
}
