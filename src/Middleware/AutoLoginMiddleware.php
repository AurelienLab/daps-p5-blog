<?php

namespace App\Middleware;

use App\Core\Abstracts\AbstractMiddleware;
use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class AutoLoginMiddleware extends AbstractMiddleware
{

    /**
     * @inheritDoc
     */
    public function handle(Request $request): void
    {
        if ($request->getSession()->get('userId') !== null) {
            return;
        }

        $rememberMeCookie = $request->cookies->get('_app_remember_me');

        if (!is_null($rememberMeCookie)) {
            $data = explode('//', base64_decode($rememberMeCookie));
            $iv = $data[0];
            $token = $data[1];

            $serializedUserData = openssl_decrypt($token, 'AES-256-CBC', config('app.key'), 0, $iv);

            $userData = unserialize($serializedUserData);

            /* @var User $user */
            $user = UserRepository::get($userData['userId']);

            if ($user) {
                if (
                    $user->getRememberMeToken() == $rememberMeCookie // Token is the same
                    && $user->getEmail() == $userData['email'] // Email in token is the same
                    && time() - $userData['generated_at'] < config('app.remember_me_lifetime') * 3600 // Token is not expired
                ) {
                    // Generate or replace session data with connected user
                    $request->getSession()->set('userId', $user->getId());
                    $request->getSession()->save();
                }
            }
        }
    }
}
