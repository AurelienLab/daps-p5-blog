<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Components\Mailer\Mailer;
use App\Core\Router\Router;
use App\Repository\PostRepository;
use App\Validator\EmailValidator;
use App\Validator\NotEmptyValidator;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{

    /**
     * Homepage
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(): Response
    {
        $lastPost = PostRepository::getLastPublished(['category']);
        $this->setTitle('Accueil');
        return $this->render(
            'homepage/index.html.twig',
            [
                'lastPost' => $lastPost
            ]
        );
    }

    /**
     * Contact form post handle
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function contact(Request $request)
    {
        $data = $this->validateForm($request, 'contact_form', [
            'firstname' => [NotEmptyValidator::class],
            'lastname' => [NotEmptyValidator::class],
            'email' => [NotEmptyValidator::class, EmailValidator::class],
            'message' => [NotEmptyValidator::class]
        ]);

        if ($this->hasFormErrors()) {
            $lastPost = PostRepository::getLastPublished(['category']);

            return $this->render(
                'homepage/index.html.twig',
                [
                    'lastPost' => $lastPost,
                    'contact' => $data->toArray()
                ]
            );
        }

        $mailer = new Mailer();

        $mailer->sendMail(
            config('app.contact.email'),
            'Nouveau message de '.$data->get('firstname'),
            'email/contact.html.twig',
            [
                'data' => $data->toArray(),
            ]
        );

        return $this->render('homepage/contact-confirm.html.twig');
    }


}
