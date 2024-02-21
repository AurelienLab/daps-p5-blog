<?php

namespace App\Model;

class PostTag
{

    const TABLE = 'posts_tags';

    /**
     * @var int
     */
    private int $id;
    /**
     * @var Post
     */
    private Post $post;
    /**
     * @var Tag
     */
    private Tag $tag;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): PostTag
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post): PostTag
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return Tag
     */
    public function getTag(): Tag
    {
        return $this->tag;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function setTag(Tag $tag): PostTag
    {
        $this->tag = $tag;
        return $this;
    }
}
