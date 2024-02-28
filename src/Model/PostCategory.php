<?php

namespace App\Model;

use App\Core\Database\EntityCollection;

class PostCategory
{

    const TABLE = 'post_categories';

    /**
     * @var integer
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var EntityCollection
     */
    private EntityCollection $posts;

    /**
     *
     */
    public function __construct()
    {
        $this->posts = new EntityCollection(
            Post::class,
            EntityCollection::TYPE_MANY_TO_ONE,
            'category',
        );
    }

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
    public function setId(int $id): PostCategory
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): PostCategory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug(string $slug): PostCategory
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return EntityCollection
     */
    public function getPosts(): EntityCollection
    {
        return $this->posts;
    }


}
