<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Utils\Str;
use App\Model\User;
use App\Repository\UserRepository;
use App\Validator\NicknameLengthValidator;
use App\Validator\NotEmptyValidator;
use App\Validator\PasswordStrengthValidator;
use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

    /**
     * Display profile forms (profile & password)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editProfile()
    {
        $this->setTitle('Mon compte');
        $user = $this->getUser();
        return $this->render('user/edit-profile.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * Handle forms post (profile & password)
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updateProfile(Request $request)
    {
        $this->setTitle('Mon compte');
        $user = $this->getUser();

        if ($request->request->get('save-profile') !== null) {
            if ($this->saveProfile($user, $request)) {
                $this->addFlash('success', 'Vos informations ont été mises à jour.');
            }
        }

        if ($request->request->get('save-password') !== null) {
            if ($this->savePassword($user, $request)) {
                $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            }
        }

        return $this->render('user/edit-profile.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * Handle save from profile form
     *
     * @param User $user
     * @param Request $request
     *
     * @return false|mixed|object|null
     * @throws \Exception
     */
    private function saveProfile(User $user, Request $request)
    {
        $data = $this->validateForm($request, 'user_profile_form', [
            'name' => [NotEmptyValidator::class, NicknameLengthValidator::class],
        ]);
        $profilePicture = $request->files->get('profile_picture');

        $profilePicturePath = $user->getProfilePicture() ?? '';
        $uploadImage = false;
        if ($profilePicture != null) {
            if (!str_starts_with($profilePicture->getMimeType(), 'image/')) {
                $this->addFormError('profile_picture', 'Format incompatible');
            } else {
                $uploadImage = true;
            }
        }

        $user->setName(trim($data->get('name')));

        if ($this->hasFormErrors()) {
            return false;
        }

        if ($uploadImage) {
            // Upload file
            $filename = Transliterator::urlize($user->getName()).'-'.Str::rand(4);
            $filename .= '.'.$profilePicture->getClientOriginalExtension();
            $profilePicturePath = '/'.$profilePicture->move(config('uploads.user.image'), $filename);

            $user->setProfilePicture($profilePicturePath);
        }

        return UserRepository::save($user);
    }


    /**
     * Handle save from password form
     *
     * @param User $user
     * @param Request $request
     *
     * @return false|mixed|object|null
     * @throws \Exception
     */
    private function savePassword(User $user, Request $request)
    {
        $data = $this->validateForm($request, 'user_password_form', [
            'password1' => [NotEmptyValidator::class, PasswordStrengthValidator::class],
        ]);

        if (!password_verify($data->get('old-password'), $user->getPassword())) {
            $this->addFormError('old-password', 'Mot de passe invalide');
        }

        if ($data->get('password1') !== $data->get('password2')) {
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
