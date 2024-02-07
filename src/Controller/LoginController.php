<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Repository\UserRepository;
use App\Validator\EmailValidator;
use App\Validator\NotEmptyValidator;
use Symfony\Component\HttpFoundation\Cookie;
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

        $data = $this->validateForm($request, 'login_form', [
            'email' => [NotEmptyValidator::class, EmailValidator::class],
            'password' => [NotEmptyValidator::class]
        ]);


        if (!$this->hasFormErrors()) {
            $user = UserRepository::getByEmail(strtolower($data->get('email')));
            if (!$user || !password_verify($data->get('password'), $user->getPassword())) {
                $this->addFormError('form', 'Identifiants invalides');
            }
        }

        if (!$this->hasFormErrors() && $user !== null) {
            $request->getSession()->set('userId', $user->getId());
            if ($data->get('remember_me') == 'on') {
                $token = $user->generateRememberMeToken();
                UserRepository::save($user);
                $cookieExpiration = time() + (config('app.remember_me_lifetime') * 3600);
                $this->addCookie(new Cookie('_app_remember_me', $token, $cookieExpiration));
            }

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
        // Delete remember me cookie
        $this->addCookie(new Cookie('_app_remember_me', null));

        return $this->redirect('homepage.index');
    }
}