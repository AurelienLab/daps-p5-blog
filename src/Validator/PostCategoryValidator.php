<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;
use App\Repository\PostCategoryRepository;

/**
 * Check if the value is a valid category id
 * transforms data into PostCategory object
 */
class PostCategoryValidator extends AbstractValidator
{


    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Vous devez sélectionner une catégorie";
    }


    /**
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    protected function processData($data): bool
    {
        if (!is_numeric($data) || empty($data)) {
            $this->transformData(null);
            return false;
        }

        $category = null;

        $category = PostCategoryRepository::get((int) $data);
        if ($category !== null) {
            $this->transformData($category);
            return true;
        }

        $this->transformData(null);
        return false;
    }


}
