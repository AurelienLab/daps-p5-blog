<?php

namespace App\Model;

use App\Model\Trait\TimestampableTrait;

class Comment
{

    use TimestampableTrait;

    const TABLE = 'comments';

    /**
     * @var integer
     */
    private int $id;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var Post
     */
    private Post $post;

    /**
     * @var string
     */
    private string $content;


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
    public function setId(int $id): Comment
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user): Comment
    {
        $this->user = $user;
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
    public function setPost(Post $post): Comment
    {
        $this->post = $post;
        return $this;
    }


    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }


    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }


}
