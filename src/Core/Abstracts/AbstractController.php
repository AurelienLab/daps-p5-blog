<?php

namespace App\Core\Abstracts;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{

    /**
     * @var Environment
     */
    private $twig;


    public function __construct()
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->twig = new Environment($loader);
        $this->twig->addExtension(new IntlExtension());
    }


    /**
     * Render given template with passed data via twig instance
     *
     * @param string $template path of twig template
     * @param array $data array of data to pass to twig template
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }


    /**
     * Display given template with passed data via twig instance (no print needed)
     *
     * @param string $template path of twig template
     * @param array $data array of data to pass to twig template
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function display(string $template, array $data = []): void
    {
        $this->twig->display($template, $data);
    }
}
