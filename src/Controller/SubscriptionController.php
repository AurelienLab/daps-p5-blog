<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Model\User;
use App\Repository\UserRepository;
use App\Validator\EmailValidator;
use App\Validator\ExistingEmailValidator;
use App\Validator\NicknameLengthValidator;
use App\Validator\NotEmptyValidator;
use App\Validator\PasswordStrengthValidator;
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

        $result = $this->save($user, $request);
        if ($result) {
            $request->getSession()->set('userId', $result->getId());
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


        $this->validateForm($request, 'subscription_form', [
            'name' => [NotEmptyValidator::class, NicknameLengthValidator::class],
            'email' => [NotEmptyValidator::class, EmailValidator::class, ExistingEmailValidator::class],
            'password1' => [NotEmptyValidator::class, PasswordStrengthValidator::class]
        ]);

        $user
            ->setName($data->get('name'))
            ->setEmail(strtolower(trim($data->get('email'))));

        if ($this->hasFormErrors()) {
            return false;
        }

        if ($data->get('password1') != $data->get('password2')) {
            $this->addFormError('password2', 'Les mots de passe ne correspondent pas');
        }


        if ($this->hasFormErrors()) {
            return false;
        }

        $passwordHash = password_hash($data->get('password1'), PASSWORD_BCRYPT);

        $user->setPassword($passwordHash);

        return UserRepository::save($user);
    }
}
