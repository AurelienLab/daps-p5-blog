<?php

namespace App\Core\Router;

class RouteParameter
{

    /**
     * @var string
     */
    private $value;


    /**
     * @param string $name Name of the param (will be passed to $name in the controller method
     * @param bool $isNullable Is the route param required ?
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
     * @param mixed $value Value retrieved from uri
     *
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }


}
