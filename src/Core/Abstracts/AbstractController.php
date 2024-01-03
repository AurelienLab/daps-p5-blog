<?php

namespace App\Core\Abstracts;

use App\Core\Form\FormErrorBag;
use App\Core\Router\Router;
use MarcW\Heroicons\Twig\HeroiconsExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

abstract class AbstractController
{

    /**
     * @var Environment
     */
    private $twig;

    private $formErrors;


    public function __construct()
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->formErrors = new FormErrorBag();

        $this->twig = new Environment($loader);
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addExtension(new HeroiconsExtension());

        // Get a config value
        $this->twig->addFunction(new TwigFunction('config', 'config'));

        // Enable dump function in twig
        $this->twig->addFunction(new TwigFunction('dump', 'twigDump'));

        // Get Route url from name & args
        $this->twig->addFunction(new TwigFunction('route', 'route'));

        // Generate csrf token
        $this->twig->addFunction(new TwigFunction('csrf', 'generateCsrfField'));

        // Get form error
        $this->twig->addFunction(new TwigFunction('error', [$this->formErrors, 'getError']));

        // Get datetime value as (XX min ago)
        $this->twig->addFilter(new TwigFilter('diff', 'dateTimeAgo'));
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

    /**
     * Add an error to form error bag
     *
     * @param string $fieldName
     * @param string $errorMessage
     *
     * @return void
     */
    protected function addFormError(string $fieldName, string $errorMessage): void
    {
        $this->formErrors->addError($fieldName, $errorMessage);
    }

    /**
     * Is there any error in form error bag ?
     *
     * @return bool
     */
    protected function hasFormErrors(): bool
    {
        return $this->formErrors->hasError();
    }

    protected function isCsrfValid(string $name, string $tokenValue): bool
    {
        $token = new CsrfToken($name, $tokenValue);

        $tokenManager = new CsrfTokenManager();
        return $tokenManager->isTokenValid($token);
    }
}
