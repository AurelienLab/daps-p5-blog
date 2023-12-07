<?php

namespace App\Core\Abstracts;

use App\Core\Router\Router;
use MarcW\Heroicons\Twig\HeroiconsExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
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

    private $formErrors = [];


    public function __construct()
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->twig = new Environment($loader);
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addExtension(new HeroiconsExtension());
        $this->twig->addFunction(new \Twig\TwigFunction('config', 'config'));
        $this->twig->addFunction(new \Twig\TwigFunction('dump', 'twigDump'));
        $this->twig->addFunction(new \Twig\TwigFunction('route', 'route'));
    }


    /**
     * Render given template with passed data via twig instance
     *
     * @param string $template path of twig template
     * @param array $data array of data to pass to twig template
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function render(string $template, array $data = []): Response
    {
        $response = new Response();

        $data = array_merge($data, [
            '_form_errors' => $this->formErrors
        ]);

        $response->setContent($this->twig->render($template, $data));

        return $response;
    }

    /**
     * Redirect to given route
     *
     * @throws \Exception
     */
    protected function redirect(string $routeName, array $args = []): RedirectResponse
    {
        $uri = Router::getInstance()->getUriByName($routeName, $args);
        return new RedirectResponse($uri);
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

    protected function addFormError(string $fieldName, string $errorMessage): void
    {
        $this->formErrors[$fieldName] = $errorMessage;
    }

    protected function hasFormErrors(): bool
    {
        return count($this->formErrors) > 0;
    }
}
