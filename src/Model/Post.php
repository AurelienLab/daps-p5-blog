<?php

namespace App\Model;

use App\Model\Trait\TimestampableTrait;

class Post
{

    use TimestampableTrait;

    const TABLE = 'posts';

    const STATE_DRAFT = 0;
    const STATE_PUBLISHED = 1;

    /**
     * @var int
     */
    private int $id;

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
     * @var int
     */
    private int $categoryId;

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
    private int $userId;

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

    public function getId(): int
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

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): Post
    {
        $this->categoryId = $categoryId;
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): Post
    {
        $this->userId = $userId;
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
}
