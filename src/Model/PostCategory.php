<?php

namespace App\Model;

use App\Core\Database\EntityCollection;

class PostCategory
{

    const TABLE = 'post_categories';

    private int $id;

    private string $name;

    private string $slug;

    private EntityCollection $posts;

    public function __construct()
    {
        $this->posts = new EntityCollection(
            Post::class,
            EntityCollection::TYPE_MANY_TO_ONE,
            'category',
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PostCategory
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PostCategory
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): PostCategory
    {
        $this->slug = $slug;
        return $this;
    }

    public function getPosts(): EntityCollection
    {
        return $this->posts;
    }
}
