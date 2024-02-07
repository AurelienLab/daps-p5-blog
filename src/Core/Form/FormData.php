<?php

namespace App\Core\Form;

use Symfony\Component\HttpFoundation\Request;

class FormData
{

    private array $data;

    public function __construct(Request $request)
    {
        $this->data = $request->request->all();
    }

    public function get(string $field)
    {
        if (isset($this->data[$field])) {
            return $this->data[$field];
        }

        return null;
    }

    public function set(string $field, mixed $value = null)
    {
        $this->data[$field] = $value;
    }
}
