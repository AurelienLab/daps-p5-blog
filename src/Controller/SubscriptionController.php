<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Components\Mailer\Mailer;
use App\Core\Exception\DisplayableException;
use App\Core\Utils\Encryption;
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
        $this->setTitle('Inscription');
        return $this->render('user/subscribe.html.twig');
    }

    public function register(Request $request)
    {
        $this->setTitle('Inscription');
        $user = new User();

        $result = $this->save($user, $request);
        if ($result) {
            $dataToEncrypt = [
                'userId' => $result->getId(),
                'userEmail' => $result->getEmail()
            ];

            $emailToken = Encryption::encrypt($dataToEncrypt);

            $mailer = new Mailer();

            $mailer->sendMail(
                $result->getEmail(),
                'Vérification de votre adresse email',
                'email/email-verification.html.twig',
                [
                    'user' => $result,
                    'link' => url('user.verify', ['token' => $emailToken, 'email' => $result->getEmail()])
                ]
            );

            return $this->redirect('user.subscribe.success');
        }

        return $this->render('user/subscribe.html.twig', [
            'user' => $user,
        ]);
    }

    public function success(Request $request)
    {
        $this->setTitle('Inscription');
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

    public function verifyEmail(Request $request)
    {
        $this->setTitle('Vérification de l\'adresse');
        $token = $request->query->get('token');
        $email = $request->query->get('email');

        if (empty($token) || empty($email)) {
            throw new DisplayableException('Element de validation manquant dans la requête.');
        }

        $data = Encryption::decrypt($token);

        $hasError = false;

        if ($data['userEmail'] != $email) {
            $hasError = true;
        }

        /* @var ?User $user */
        $user = UserRepository::get($data['userId']);

        if (empty($user)) {
            $hasError = true;
        } else {
            if ($user->getEmailValidatedAt() !== null) {
                return $this->render('user/verify_already.html.twig');
            }
            if ($data['userEmail'] != $user->getEmail() || $email != $user->getEmail()) {
                $hasError = true;
            }
        }

        if ($hasError === true) {
            throw new DisplayableException('Impossible de valider l\'adresse email');
        }

        $user->setEmailValidatedAt(new \DateTimeImmutable());

        UserRepository::save($user);

        $request->getSession()->set('userId', $user->getId());

        return $this->render('user/verify_confirm.html.twig');
    }
}
