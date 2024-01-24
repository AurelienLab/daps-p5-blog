<?php

namespace App\Model;

class PostTag
{

    const TABLE = 'posts_tags';

    private int $id;
    private Post $post;
    private Tag $tag;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PostTag
    {
        $this->id = $id;
        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): PostTag
    {
        $this->post = $post;
        return $this;
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function setTag(Tag $tag): PostTag
    {
        $this->tag = $tag;
        return $this;
    }
}
