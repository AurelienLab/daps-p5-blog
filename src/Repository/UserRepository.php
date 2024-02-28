<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\User;

class UserRepository extends AbstractRepository
{

    const MODEL = User::class;


    /**
     * @param $email
     *
     * @return Query
     */
    private static function getByEmailQuery($email): Query
    {
        $query = new Query(static::MODEL);

        $query->select()
            ->where('email', '=', $email)
            ->first();

        return $query;
    }


    /**
     * @param string $email
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getByEmail(string $email)
    {
        $query = self::getByEmailQuery($email);

        return Database::query($query);
    }


    /**
     * @param $email
     *
     * @return bool
     * @throws \Exception
     */
    public static function isEmailExist($email): bool
    {
        $query = self::getByEmailQuery($email);
        $query->withTrashed();

        $result = Database::query($query);

        return $result != null;
    }


}
