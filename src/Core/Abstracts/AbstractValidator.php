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

    abstract protected function getErrorMessage(): string;

    abstract protected function processData($data): bool;

    public function validate(): bool
    {
        if (!$this->processData($this->formData->get($this->fieldName))) {
            $this->errorBag->addError($this->fieldName, $this->getErrorMessage());
            return false;
        }

        return true;
    }

    public function transformData($newData)
    {
        $this->formData->set($this->fieldName, $newData);
    }
}
