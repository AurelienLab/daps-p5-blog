<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\PasswordRequest;
use App\Model\User;

class PasswordRequestRepository extends AbstractRepository
{

    const MODEL = PasswordRequest::class;

    const DEFAULT_RELATIONS = ['user'];

    public static function findByToken(string $token): ?PasswordRequest
    {
        $relations = static::DEFAULT_RELATIONS;

        $query = new Query(self::MODEL);

        $query->select()
            ->where('token', '=', $token)
            ->first();

        self::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }

    public static function findByUser(User $user): ?PasswordRequest
    {
        $relations = static::DEFAULT_RELATIONS;

        $query = new Query(self::MODEL);

        $query->select()
            ->where('user_id', '=', $user->getId())
            ->first();

        self::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }
}
