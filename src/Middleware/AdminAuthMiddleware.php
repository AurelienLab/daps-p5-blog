<?php

namespace App\Middleware;

use App\Core\Abstracts\AbstractMiddleware;
use App\Core\Exception\ForbiddenException;
use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Check if user is logged in and admin
 */
class AdminAuthMiddleware extends AbstractMiddleware
{

    /**
     * @inheritDoc
     * @throws ForbiddenException
     */
    public function handle(Request $request): void
    {
        if ($request->getSession()->get('userId') !== null) {
            $userId = $request->getSession()->get('userId');
            if ($userId) {
                $user = UserRepository::get($userId);
                if ($user && $user->isAdmin()) {
                    return;
                }
            }
        }

        throw new ForbiddenException();
    }


}
