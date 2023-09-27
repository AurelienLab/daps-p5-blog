<?php

namespace App\Core\Abstracts;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{
    private $twig;

    public function __construct()
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->twig = new Environment($loader);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    protected function render($template, $data = []): string
    {

        return $this->twig->render($template, $data);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function display($template, $data = []): void
    {
        $this->twig->display($template, $data);
    }
}
