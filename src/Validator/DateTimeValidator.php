<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

/**
 * Check if the value is a valid datetime
 */
class DateTimeValidator extends AbstractValidator
{

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        return "Le champs de date / heure est invalide";
    }

    /**
     * @param $data
     *
     * @return bool
     */
    protected function processData($data): bool
    {
        try {
            $dateTime = new \DateTime($data);
            $this->transformData($dateTime);
            return true;
        } catch (\Exception) {
            return false;
        }
    }


}
