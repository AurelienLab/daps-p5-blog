<?php

namespace App\Core\Abstracts;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMiddleware
{

    /**
     * Content of the middleware
     *
     * @param Request $request
     *
     * @return void
     */
    abstract public function handle(Request $request): void;


}
