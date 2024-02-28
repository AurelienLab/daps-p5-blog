<?php

namespace App\Core\Abstracts;

use App\Core\Form\FormData;
use App\Core\Form\FormErrorBag;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractValidator
{

    public function __construct(
        private string       $fieldName,
        private FormErrorBag $errorBag,
        private FormData     $formData
    )
    {
    }

    /**
     * Error message to display in case of the value is not validated
     *
     * @return string
     */
    abstract protected function getErrorMessage(): string;

    /**
     * Logical to apply to validate data
     *
     * @param $data
     *
     * @return bool
     */
    abstract protected function processData($data): bool;

    /**
     * Called by the controller and automatically set error message if the field is not valid
     *
     * @return bool
     */
    public function validate(): bool
    {
        if (!$this->processData($this->formData->get($this->fieldName))) {
            $this->errorBag->addError($this->fieldName, $this->getErrorMessage());
            return false;
        }

        return true;
    }

    /**
     * Can be used to transfer a modified data to the next validator
     *
     * @param $newData
     *
     * @return void
     */
    public function transformData($newData)
    {
        $this->formData->set($this->fieldName, $newData);
    }


}
