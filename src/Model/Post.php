<?php

namespace App\Model;

use App\Core\Database\EntityCollection;
use App\Model\Trait\SoftDeleteTrait;
use App\Model\Trait\TimestampableTrait;

class Post
{

    use TimestampableTrait,
        SoftDeleteTrait;

    const TABLE = 'posts';

    const STATE_DRAFT = 0;
    const STATE_PUBLISHED = 1;

    /**
     * @var int
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $slug;

    /**
     * @var string
     */
    private string $chapo;

    /**
     * @var PostCategory
     */
    private PostCategory $category;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string
     */
    private string $featuredImage;

    /**
     * @var int
     */
    private User $user;

    /**
     * @var \DateTime
     */
    private \DateTime $publishedAt;
    /**
     * @var \DateTime
     */
    private \DateTime $validatedAt;

    /**
     * @var int|null
     */
    private ?int $validatorUserId;

    /**
     * @var int
     */
    private int $readTime;

    /**
     * @var int
     */
    private int $status;

    private EntityCollection $tags;

    public function __construct()
    {
        $this->tags = new EntityCollection(
            Tag::class,
            EntityCollection::TYPE_MANY_TO_MANY,
            'tag',
            PostTag::class
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Post
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Post
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Post
    {
        $this->slug = $slug;
        return $this;
    }

    public function getChapo(): string
    {
        return $this->chapo;
    }

    public function setChapo(string $chapo): Post
    {
        $this->chapo = $chapo;
        return $this;
    }

    public function getCategory(): ?PostCategory
    {
        return isset($this->category) ? $this->category : null;
    }

    public function setCategory(PostCategory $category): Post
    {
        $this->category = $category;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Post
    {
        $this->content = $content;
        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage ?? null;
    }

    public function setFeaturedImage(string $featuredImage): Post
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Post
    {
        $this->user = $user;
        return $this;
    }

    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt): Post
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getValidatedAt(): \DateTime
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(\DateTime $validatedAt): Post
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    public function getValidatorUserId(): ?int
    {
        return $this->validatorUserId;
    }

    public function setValidatorUserId(?int $validatorUserId): Post
    {
        $this->validatorUserId = $validatorUserId;
        return $this;
    }

    public function getReadTime(): int
    {
        return $this->readTime;
    }

    public function setReadTime(int $readTime): Post
    {
        $this->readTime = $readTime;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Post
    {
        $this->status = $status;
        return $this;
    }

    public function getTags(): EntityCollection
    {
        return $this->tags;
    }

    public function getTagsJson(): string
    {
        return json_encode($this->getTags()->toArray());
    }
}
