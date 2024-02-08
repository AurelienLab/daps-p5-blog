<?php

namespace App\Model;

use App\Model\Trait\TimestampableTrait;

class Comment
{

    use TimestampableTrait;

    const TABLE = 'comments';

    private int $id;
    private User $user;
    private Post $post;
    private string $content;


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Comment
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Comment
    {
        $this->user = $user;
        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): Comment
    {
        $this->post = $post;
        return $this;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }
}
