<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\User;

class UserRepository extends AbstractRepository
{

    const MODEL = User::class;

    public static function getByEmail(string $email)
    {
        $query = new Query(static::MODEL);

        $query->select()
            ->where('email', '=', $email)
            ->first();

        return Database::query($query);
    }

    public static function isEmailExist($email): bool
    {
        $result = static::getByEmail($email);

        return $result != null;
    }
}
