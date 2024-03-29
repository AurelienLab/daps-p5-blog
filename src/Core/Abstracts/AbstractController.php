<?php

namespace App\Core\Abstracts;

use App\Core\Classes\TwigEnvironment;
use App\Core\Components\Flash\FlashesBag;
use App\Core\Components\Flash\FlashMessage;
use App\Core\Form\FormData;
use App\Core\Form\FormErrorBag;
use App\Core\Router\Router;
use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
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
    protected $twig;

    private $formErrors;

    private ?FlashesBag $flashesBag = null;

    private array $cookies = [];

    private $user = null;

    private $pageTitle = null;


    public function __construct(Request $request)
    {
        if ($request->hasSession()) {
            // Get User if logged in
            $userId = $request->getSession()->get('userId');
            if ($userId) {
                $user = UserRepository::get($userId);
                if ($user && $user->canConnect()) {
                    $this->user = $user;
                }
            }
            $this->flashesBag = new FlashesBag($request);
        }

        $this->formErrors = new FormErrorBag();

        // Initialize twig
        $this->twig = new TwigEnvironment([
            'formErrors' => $this->formErrors,
            'user' => $this->getUser(),
            'flashes' => $this->flashesBag
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
        $this->flashesBag->saveToSession();
        if ($this->pageTitle !== null) {
            $data['_title'] = $this->pageTitle;
        }
        $response->setContent($this->twig->render($template, $data));
        $this->setCookiesInResponse($response);
        return $response;
    }


    /**
     * Redirect to given route
     *
     * @throws \Exception
     */
    protected function redirect(string $routeName, array $args = []): RedirectResponse
    {
        $this->flashesBag->saveToSession();
        $uri = Router::getInstance()->getUriByName($routeName, $args);

        $response = new RedirectResponse($uri);
        $this->setCookiesInResponse($response);
        return $response;
    }


    /**
     * Redirect to given url
     *
     * @throws \Exception
     */
    protected function redirectUrl(string $url): RedirectResponse
    {
        $this->flashesBag->saveToSession();
        $response = new RedirectResponse($url);
        $this->setCookiesInResponse($response);
        return $response;
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


    /**
     * Verify each form data through validators passed in $fields array
     *
     * @param Request $request
     * @param string $csrfName
     * @param array $fields
     *
     * @return FormData
     * @throws \Exception
     */
    protected function validateForm(Request $request, string $csrfName, array $fields): FormData
    {
        $formData = new FormData($request);

        if (!$this->isCsrfValid($csrfName, $formData->get('_csrf'))) {
            throw new \Exception('Invalid CSRF token');
        }

        foreach ($fields as $fieldName => $validators) {
            foreach ($validators as $validator) {
                /* @var AbstractValidator $validator */
                $validator = new $validator(
                    $fieldName,
                    $this->formErrors,
                    $formData
                );
                if (!$validator->validate()) {
                    break;
                }
            }
        }

        return $formData;
    }


    /**
     * Check CSRF token validity
     *
     * @param string $name
     * @param string $tokenValue
     *
     * @return bool
     */
    protected function isCsrfValid(string $name, string $tokenValue): bool
    {
        $token = new CsrfToken($name, $tokenValue);

        $tokenManager = new CsrfTokenManager();
        return $tokenManager->isTokenValid($token);
    }


    /**
     * Get current user (null if not logged in)
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        return $this->user;
    }


    /**
     * Add a flash message that must be consumed in views
     *
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    protected function addFlash(string $type, string $message)
    {
        $flash = new FlashMessage($type, $message);
        $this->flashesBag->addFlash($flash);
    }


    /**
     * Add cookie that will be added to response
     *
     * @param Cookie $cookie
     *
     * @return void
     */
    protected function addCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
    }


    /**
     * Insert cookies in response object
     *
     * @param Response $response
     *
     * @return void
     */
    protected function setCookiesInResponse(Response $response)
    {
        foreach ($this->cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }
    }


    /**
     * Set meta tile
     *
     * @param string $title
     *
     * @return void
     */
    protected function setTitle(string $title)
    {
        $this->pageTitle = $title;
    }


}
