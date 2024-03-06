<?php

namespace App\Repository;

use App\Core\Abstracts\AbstractRepository;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Model\Post;
use App\Model\PostTag;
use App\Model\Tag;

class PostTagRepository extends AbstractRepository
{

    const MODEL = PostTag::class;


    /**
     * @param Post $post
     * @param Tag $tag
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getByPostAndTag(Post $post, Tag $tag)
    {
        $query = new Query(static::MODEL);

        $query->select()
            ->where('post_id', '=', $post->getId())
            ->where('tag_id', '=', $tag->getId())
            ->first();

        return Database::query($query);
    }


}
