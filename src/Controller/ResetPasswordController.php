<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Components\Mailer\Mailer;
use App\Core\Utils\Encryption;
use App\Model\PasswordRequest;
use App\Model\User;
use App\Repository\PasswordRequestRepository;
use App\Repository\UserRepository;
use App\Validator\EmailValidator;
use App\Validator\NotEmptyValidator;
use App\Validator\PasswordStrengthValidator;
use DateInterval;
use Symfony\Component\HttpFoundation\Request;

class ResetPasswordController extends AbstractController
{

    /**
     * Password reset request form
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        if ($this->getUser()) {
            return $this->redirect('user.profile.edit');
        }

        $this->setTitle('Mot de passe oublié');
        return $this->render('reset-password/request-form.html.twig');
    }

    /**
     * Handle password request form post
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function post(Request $request)
    {
        $this->setTitle('Mot de passe oublié');

        $data = $this->validateForm($request, 'reset_password_form', [
            'email' => [NotEmptyValidator::class, EmailValidator::class]
        ]);

        $user = UserRepository::getByEmail($data->get('email'));

        if ($user) {
            // Remove previous requests
            $oldRequest = PasswordRequestRepository::findByUser($user);
            if ($oldRequest) {
                PasswordRequestRepository::remove($oldRequest);
            }

            // Generate request & send email
            $token = Encryption::encrypt(['user_id' => $user->getId()]);
            $expiration = new \DateTime();
            $expiration->add(new DateInterval('PT'.config('app.password_request_validity').'H'));
            $passwordRequest = new PasswordRequest();

            $passwordRequest
                ->setUser($user)
                ->setToken($token)
                ->setExpiresAt($expiration);

            PasswordRequestRepository::save($passwordRequest);

            $mailer = new Mailer();

            $mailer->sendMail(
                $user->getEmail(),
                'Récupérer mon mot de passe',
                'email/password-request.html.twig',
                [
                    'user' => $user,
                    'link' => url('password-request.reset', ['token' => $token]),
                    'expiration' => $passwordRequest->getExpiresAt()
                ]
            );
        } else {
            // Simulate email sending and avoid bruteforce
            sleep(2);
        }

        return $this->render('reset-password/request-confirm.html.twig');
    }

    /**
     * Accessed via link in request password email
     * Check token and display password change form
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function resetPassword(Request $request)
    {
        $token = $request->query->get('token');

        $this->setTitle('Mot de passe oublié');

        if (empty($token)) {
            return $this->render('reset-password/error.html.twig', [
                'error' => 'Le lien est invalide, merci de vérifier qu\'il
                correspond bien à ce qui est contenu dans le mail.'
            ]);
        }

        $passwordRequest = PasswordRequestRepository::findByToken($token);

        if (!$passwordRequest) {
            return $this->render('reset-password/error.html.twig', [
                'error' => 'Aucune demande ne correspond à ce lien de récupération.
                Si besoin, merci de refaire une demande.'
            ]);
        }

        if ($passwordRequest->getExpiresAt() < (new \DateTime())) {
            return $this->render('reset-password/error.html.twig', [
                'error' => 'Cette demande a expiré, merci de la renouveler.'
            ]);
        }

        $data = Encryption::decrypt($token);

        if (!isset($data['user_id']) || $data['user_id'] != $passwordRequest->getUser()->getId()) {
            return $this->render('reset-password/error.html.twig', [
                'error' => 'Le lien est invalide, merci de vérifier qu\'il
                correspond bien à ce qui est contenu dans le mail.'
            ]);
        }

        return $this->render('reset-password/reset-form.html.twig', [
            'token' => $token
        ]);
    }

    /**
     * Handle change password form post
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function resetPasswordPost(Request $request)
    {
        $this->setTitle('Mot de passe oublié');
        // Check for form errors
        $formData = $this->validateForm($request, 'reset_password_form', [
            'password1' => [NotEmptyValidator::class, PasswordStrengthValidator::class]
        ]);

        if ($formData->get('password1') != $formData->get('password2')) {
            $this->addFormError('password2', 'Les mots de passe ne correspondent pas');
        }

        if ($this->hasFormErrors()) {
            return $this->render('reset-password/reset-form.html.twig', [
                'token' => $formData->get('reset-token')
            ]);
        }

        // Check for token validity

        $token = $formData->get('reset-token');

        if (empty($token)) {
            throw new \Exception('Error with request token');
        }

        $passwordRequest = PasswordRequestRepository::findByToken($token);
        $data = Encryption::decrypt($token);

        if (!$passwordRequest
            || $passwordRequest->getExpiresAt() < (new \DateTime())
            || !isset($data['user_id'])
            || $data['user_id'] != $passwordRequest->getUser()->getId()
        ) {
            throw new \Exception('Error with request token');
        }

        $passwordHash = password_hash($formData->get('password1'), PASSWORD_BCRYPT);
        $user = $passwordRequest->getUser();
        $user->setPassword($passwordHash);
        UserRepository::save($user);
        PasswordRequestRepository::remove($passwordRequest);

        return $this->render('reset-password/update-confirm.html.twig');
    }


}
