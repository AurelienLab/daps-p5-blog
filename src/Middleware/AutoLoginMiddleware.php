<?php

namespace App\Middleware;

use App\Core\Abstracts\AbstractMiddleware;
use App\Core\Utils\Encryption;
use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Analyse token to auto reconnect user from cookies
 */
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

        if ($rememberMeCookie !== null) {
            $userData = Encryption::decrypt($rememberMeCookie);

            /* @var User $user */
            $user = UserRepository::get($userData['userId']);

            if ($user) {
                if ($user->getRememberMeToken() == $rememberMeCookie // Token is the same
                    && $user->getEmail() == $userData['email'] // Email in token is the same
                    && time() - $userData['generated_at'] < config('app.remember_me_lifetime') * 3600 // Token is not expired
                    && $user->canConnect()
                ) {
                    // Generate or replace session data with connected user
                    $request->getSession()->set('userId', $user->getId());
                    $request->getSession()->save();
                }
            }
        }
    }
}
