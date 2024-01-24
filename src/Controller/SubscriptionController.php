<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends AbstractController
{

    public function subscribe(Request $request)
    {
        return $this->render('user/subscribe.html.twig');
    }

    public function register(Request $request)
    {
        $user = new User();


        $user = $this->save($user, $request);
        if ($user) {
            $request->getSession()->set('userId', $user->getId());
            return $this->redirect('user.subscribe.success');
        }

        return $this->render('user/subscribe.html.twig', [
            'user' => $user,
        ]);
    }

    public function success(Request $request)
    {
        return $this->render('user/subscribe_confirm.html.twig');
    }

    public function save(User $user, Request $request)
    {
        $data = $request->request;

        //Check CSRF Validity
        if (!$this->isCsrfValid('subscription_form', $data->get('_csrf'))) {
            throw new \Exception('Invalid CSRF token');
        }

        if (empty($data->get('name')) === true || strlen(trim($data->get('name'))) < 3) {
            $this->addFormError('name', 'Le nom doit être renseigné et contenir au moins 3 caractères');
        }

        if (empty(trim($data->get('email'))) === true) {
            $this->addFormError('email', 'L\'adresse email ne doit pas être vide');
        } elseif (filter_var(trim($data->get('email')), FILTER_VALIDATE_EMAIL) === false) {
            $this->addFormError('email', 'L\'adresse email est invalide');
        } else {
            if (UserRepository::isEmailExist(strtolower(trim($data->get('email'))))) {
                $this->addFormError('email', 'L\'adresse email est déjà utilisée');
            }
        }

        $passwordPattern = '/^';
        $passwordPattern .= '(?=.*?[0-9])'; // At least 1 number
        $passwordPattern .= '(?=.*?[#?!@$%^&*-])'; // At least 1 special char
        $passwordPattern .= '.{8,}'; // At least 8 chars
        $passwordPattern .= '$/';

        if (!preg_match($passwordPattern, $data->get('password1'))) {
            $this->addFormError(
                'password1',
                'Le mot de passe doit contenir au minimum: 8 caractères, 1 lettre minuscule, 1 caractère special'
            );
        } elseif ($data->get('password1') != $data->get('password2')) {
            $this->addFormError('password2', 'Les mots de passe ne correspondent pas');
        }

        $user
            ->setName($data->get('name'))
            ->setEmail(strtolower(trim($data->get('email'))));

        if ($this->hasFormErrors()) {
            return false;
        }

        $passwordHash = password_hash($data->get('password1'), PASSWORD_BCRYPT);

        $user->setPassword($passwordHash);

        return UserRepository::save($user);
    }
}
