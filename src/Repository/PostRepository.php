<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\Post;
use App\Model\PostTag;
use App\Model\Tag;

class PostRepository extends AbstractRepository
{

    const DEFAULT_RELATIONS = ['user', 'category'];
    const MODEL = Post::class;

    public static function getAll($relations = []): false|array
    {
        if (empty($relations)) {
            $relations = static::DEFAULT_RELATIONS;
        }

        $query = new Query(static::MODEL);
        $query->select()
            ->orderBy('published_at', 'DESC');

        self::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }

    public static function getPublished($relations = [], $limit = null, $filters = []): false|array
    {
        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query
            ->select()
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->orderBy('published_at', 'DESC');


        if (!is_null($limit)) {
            $query->limit($limit);
        }

        static::addRelationsToQuery($relations, $query);

        if (!empty($filters)) {
            if (isset($filters['tag'])) {
                $query->leftJoin(PostTag::class, [
                        PostTag::TABLE.'.post_id',
                        static::MODEL::TABLE.'.id'
                    ]
                )
                    ->where(PostTag::TABLE.'.tag_id', '=', $filters['tag']);
            }

            if (isset($filters['category'])) {
                $query->where('category_id', '=', $filters['category']);
            }
        }

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

    public static function getRelatedPosts(Post $post, int $amount = 3)
    {
        $tagIds = [];
        foreach ($post->getTags() as $tag) {
            $tagIds[] = $tag->getId();
        }

        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query->select()
            ->groupBy(static::MODEL::TABLE.'.id')
            ->leftJoin(PostTag::class, [
                    PostTag::TABLE.'.post_id',
                    static::MODEL::TABLE.'.id'
                ]
            )
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->where(Post::TABLE.'.id', '!=', $post->getId())
            ->where(PostTag::TABLE.'.tag_id', 'IN', $tagIds)
            ->orderBy('published_at', 'DESC')
            ->limit($amount);

        return Database::query($query);
    }

    public static function getPublishedByTag(Tag $tag): array
    {

        $query = new Query(static::MODEL);
        $now = new \DateTime();
        $query->select()
            ->groupBy(static::MODEL::TABLE.'.id')
            ->leftJoin(PostTag::class, [
                    PostTag::TABLE.'.post_id',
                    static::MODEL::TABLE.'.id'
                ]
            )
            ->where('published_at', '<', $now)
            ->where('status', '=', Post::STATE_PUBLISHED)
            ->where(PostTag::TABLE.'.tag_id', '=', $tag->getId())
            ->orderBy('published_at', 'DESC');

        return Database::query($query);
    }
}
