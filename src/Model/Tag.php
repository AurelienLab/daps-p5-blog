<?php

namespace App\Model;

use App\Core\Database\EntityCollection;

class Tag implements \JsonSerializable
{

    const TABLE = 'tags';

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var string
     */
    private string $name;

    private EntityCollection $posts;

    public function __construct()
    {
        $this->posts = new EntityCollection(
            Post::class,
            EntityCollection::TYPE_MANY_TO_MANY,
            'post',
            PostTag::class
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
    public function setId(int $id): Tag
    {
        $this->id = $id;
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
    public function setSlug(string $slug): Tag
    {
        $this->slug = $slug;
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
    public function setName(string $name): Tag
    {
        $this->name = $name;
        return $this;
    }

    public function getPosts(): EntityCollection
    {
        return $this->posts;
    }
    
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'slug' => $this->getSlug(),
            'name' => $this->getName()
        ];
    }
}
