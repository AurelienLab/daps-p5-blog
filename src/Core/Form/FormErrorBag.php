<?php

namespace App\Core\Form;

/**
 * Collection of errors to display in views
 */
class FormErrorBag
{

    private array $errors = [];

    /**
     * @param string $fieldName
     * @param string $errorMessage
     *
     * @return void
     */
    public function addError(string $fieldName, string $errorMessage): void
    {
        $this->errors[$fieldName] = $errorMessage;
    }

    /**
     * @param string $fieldName
     *
     * @return string|null
     */
    public function getError(string $fieldName): ?string
    {
        if (isset($this->errors[$fieldName]) === true) {
            return $this->errors[$fieldName];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return empty($this->errors) == false;
    }


}
