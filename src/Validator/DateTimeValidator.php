<?php

namespace App\Validator;

use App\Core\Abstracts\AbstractValidator;

class DateTimeValidator extends AbstractValidator
{

    protected function getErrorMessage(): string
    {
        return "Le champs de date / heure est invalide";
    }

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
