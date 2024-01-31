<?php

namespace App\Core\Abstracts;

use App\Core\Classes\TwigEnvironment;
use App\Core\Form\FormErrorBag;
use App\Core\Router\Router;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
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

    private $formErrors;

    private $user = null;


    public function __construct(Request $request)
    {
        // Get User if logged in
        $userId = $request->getSession()->get('userId');
        if ($userId) {
            $user = UserRepository::get($userId);
            if ($user) {
                $this->user = $user;
            }
        }

        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');

        $this->formErrors = new FormErrorBag();

        $this->twig = new TwigEnvironment($loader, [
            'formErrors' => $this->formErrors,
            'user' => $this->getUser()
        ]);
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

    protected function getUser(): mixed
    {
        return $this->user;
    }
}
