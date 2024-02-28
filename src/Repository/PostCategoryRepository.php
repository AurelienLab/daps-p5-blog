<?php

namespace App\Repository;

use App\Core\Database\Database;
use App\Core\Database\Query;

class PostCategoryRepository extends \App\Core\Abstracts\AbstractRepository
{

    const MODEL = \App\Model\PostCategory::class;

    /**
     * @param string $slug
     *
     * @return mixed
     * @throws \Exception
     */
    public static function findBySlug(string $slug)
    {
        $query = new Query(static::MODEL);
        $query->select()
            ->where('slug', '=', $slug)
            ->first();

        return Database::query($query);
    }


}
