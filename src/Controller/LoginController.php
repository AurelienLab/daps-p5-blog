<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AbstractController
{

    public function login(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirect('homepage.index');
        }

        return $this->render('user/login.html.twig');
    }

    public function loginPost(Request $request)
    {
        $user = null;
        $data = $request->request;
        if (empty(trim($data->get('email')))) {
            $this->addFormError('email', 'L\'adresse email ne doit pas être vide');
        }

        if (empty(trim($data->get('password')))) {
            $this->addFormError('password', 'Le mot de passe ne doit pas être vide');
        }

        if (!$this->hasFormErrors()) {
            $user = UserRepository::getByEmail(strtolower($data->get('email')));
            if (!$user || !password_verify($data->get('password'), $user->getPassword())) {
                $this->addFormError('form', 'Identifiants invalides');
            }
        }

        if (!$this->hasFormErrors() && $user !== null) {
            $request->getSession()->set('userId', $user->getId());
            return $this->redirect('homepage.index');
        }

        $credentials = [
            'email' => $data->get('email'),
            'password' => $data->get('password')
        ];

        return $this->render('user/login.html.twig', [
            'credentials' => $credentials
        ]);
    }

    public function logout(Request $request)
    {
        $request->getSession()->remove('userId');

        return $this->redirect('homepage.index');
    }
}
