<?php

namespace App\Core\Abstracts;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{

    /**
     * @var Environment
     */
    private $twig;

    /**
     *
     */
    public function __construct()
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->twig = new Environment($loader);
    } // end __construct()


    /**
     * Render given template with passed data via twig instance
     *
     * @param $template
     * @param array $data
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function render($template, array $data = []): string
    {

        return $this->twig->render($template, $data);
    }

    /**
     * Display given template with passed data via twig instance (no print needed)
     *
     * @param $template
     * @param array $data
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function display($template, array $data = []): void
    {
        $this->twig->display($template, $data);
    }
}
