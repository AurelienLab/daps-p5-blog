<?php

namespace App\Repository;

use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\Post;

class PostRepository extends \App\Core\Abstracts\AbstractRepository
{

    const MODEL = \App\Model\Post::class;

    public static function getPublished($relations = []): false|array
    {
        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query
            ->select()
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->orderBy('published_at', 'DESC');

        static::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }

    public static function getLastPublished($relations = []): ?Post
    {
        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query
            ->select()
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->orderBy('published_at', 'DESC')
            ->first();

        static::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }

    public static function getOnePublishedBySlug(string $slug, $relations = []): ?Post
    {
        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query
            ->select()
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->where('slug', '=', $slug)
            ->first();

        static::addRelationsToQuery($relations, $query);
        return Database::query($query);
    }
}
