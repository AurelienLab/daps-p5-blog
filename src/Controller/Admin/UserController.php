<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Core\Utils\Str;
use App\Model\PostCategory;
use App\Model\User;
use App\Repository\PostCategoryRepository;
use App\Repository\UserRepository;
use App\Validator\EmailValidator;
use App\Validator\ExistingEmailValidator;
use App\Validator\NicknameLengthValidator;
use App\Validator\NotEmptyValidator;
use App\Validator\PasswordStrengthValidator;
use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{

    
    /**
     * Users list
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        $users = UserRepository::getAll();

        return $this->render('Admin/user/index.html.twig', [
            'users' => $users
        ]);
    }


    /**
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add(): Response
    {
        return $this->render('Admin/user/add.html.twig');
    }


    /**
     *
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request): Response
    {
        $user = new User();

        if ($this->save($user, $request)) {
            return $this->redirect('admin.user.index');
        }

        return $this->render('Admin/user/add.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * Display edit user form
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(int $id): Response
    {
        $user = UserRepository::getOrError($id);

        return $this->render(
            'Admin/user/edit.html.twig',
            [
                'user' => $user
            ]
        );
    }


    /**
     * Handle edit user form post
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function update(int $id, Request $request): Response
    {
        $user = UserRepository::getOrError($id);

        if ($this->save($user, $request)) {
            return $this->redirect('admin.user.index');
        }

        return $this->render(
            'Admin/user/edit.html.twig',
            [
                'user' => $user
            ]
        );
    }


    /**
     * Delete user
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundException
     */
    public function remove(int $id)
    {
        $user = UserRepository::getOrError($id);

        UserRepository::remove($user);

        return $this->redirect('admin.user.index');
    }


    /**
     * Validate & save data from a form post
     *
     * @param PostCategory $postCategory
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    private function save(User $user, Request $request): bool|User
    {
        $data = $this->validateForm($request, 'user_form', [
            'name' => [NotEmptyValidator::class, NicknameLengthValidator::class],
            'email' => $request->request->get('email') != $user->getEmail() ? [NotEmptyValidator::class, EmailValidator::class, ExistingEmailValidator::class] : [NotEmptyValidator::class, EmailValidator::class],
            'password' => $request->request->get('password') !== null && !empty($request->request->get('password')) ?
                [NotEmptyValidator::class, PasswordStrengthValidator::class]
                : []

        ]);
        $profilePicture = $request->files->get('profile_picture');

        $uploadImage = false;
        if ($profilePicture != null) {
            if (!str_starts_with($profilePicture->getMimeType(), 'image/')) {
                $this->addFormError('profile_picture', 'Format incompatible');
            } else {
                $uploadImage = true;
            }
        }

        $user
            ->setName(trim($data->get('name')))
            ->setEmail(trim($data->get('email')));

        if ($user->getId() != $this->getUser()->getId()) {
            $user->setIsAdmin(!empty($data->get('is_admin')));
        }

        if ($this->hasFormErrors()) {
            return false;
        }

        // New user
        if (empty($user->getId())) {
            $password = Str::rand(12);
        } elseif ($data->get('password') !== null && !empty($data->get('password'))) {
            $password = trim($data->get('password'));
        }

        if (isset($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($passwordHash);
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


}
