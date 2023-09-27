<?php

namespace App\Core\Router;

class RouteParameter
{

    /**
     * @var string
     */
    private $value;


    /**
     * @param string $name
     * @param bool $isNullable
     */
    public function __construct(
        private readonly string $name,
        private readonly bool   $isNullable
    )
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;

    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->isNullable;

    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;

    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;

    }

}
