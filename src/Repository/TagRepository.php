<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\Tag;

class TagRepository extends AbstractRepository
{

    const MODEL = Tag::class;

    public static function getOneBySlug(string $slug): ?Tag
    {
        $query = new Query(static::MODEL);
        $query
            ->select()
            ->where('slug', '=', $slug)
            ->first();

        return Database::query($query);
    }

    public static function searchByName(string $searchString): array
    {
        $query = new Query(static::MODEL);
        $query->select()
            ->where('name', 'LIKE', '%'.$searchString.'%')
            ->orderBy('name', 'ASC');

        return Database::query($query);
    }
}
