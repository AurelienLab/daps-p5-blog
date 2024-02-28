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
     * @var integer
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
     * @var integer
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
     * @var integer|null
     */
    private ?int $validatorUserId;

    /**
     * @var integer
     */
    private int $readTime;

    /**
     * @var integer
     */
    private int $status;

    /**
     * @var EntityCollection
     */
    private EntityCollection $tags;

    /**
     * @var EntityCollection
     */
    private EntityCollection $comments;

    /**
     *
     */
    public function __construct()
    {
        $this->tags = new EntityCollection(
            Tag::class,
            EntityCollection::TYPE_MANY_TO_MANY,
            'post',
            'tag',
            PostTag::class
        );

        $this->comments = new EntityCollection(
            Comment::class,
            EntityCollection::TYPE_MANY_TO_ONE,
            null,
            null,
            null,
            ['user']
        );
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): Post
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;
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
    public function setSlug(string $slug): Post
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getChapo(): string
    {
        return $this->chapo;
    }

    /**
     * @param string $chapo
     *
     * @return $this
     */
    public function setChapo(string $chapo): Post
    {
        $this->chapo = $chapo;
        return $this;
    }

    /**
     * @return PostCategory|null
     */
    public function getCategory(): ?PostCategory
    {
        return isset($this->category) ? $this->category : null;
    }

    /**
     * @param PostCategory $category
     *
     * @return $this
     */
    public function setCategory(PostCategory $category): Post
    {
        $this->category = $category;
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
    public function setContent(string $content): Post
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage ?? null;
    }

    /**
     * @param string $featuredImage
     *
     * @return $this
     */
    public function setFeaturedImage(string $featuredImage): Post
    {
        $this->featuredImage = $featuredImage;
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
    public function setUser(User $user): Post
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt): Post
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getValidatedAt(): \DateTime
    {
        return $this->validatedAt;
    }

    /**
     * @param \DateTime $validatedAt
     *
     * @return $this
     */
    public function setValidatedAt(\DateTime $validatedAt): Post
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getValidatorUserId(): ?int
    {
        return $this->validatorUserId;
    }

    /**
     * @param int|null $validatorUserId
     *
     * @return $this
     */
    public function setValidatorUserId(?int $validatorUserId): Post
    {
        $this->validatorUserId = $validatorUserId;
        return $this;
    }

    /**
     * @return int
     */
    public function getReadTime(): int
    {
        return $this->readTime;
    }

    /**
     * @param int $readTime
     *
     * @return $this
     */
    public function setReadTime(int $readTime): Post
    {
        $this->readTime = $readTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): Post
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return EntityCollection
     */
    public function getTags(): EntityCollection
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getTagsJson(): string
    {
        return json_encode($this->getTags()->toArray());
    }

    /**
     * @return EntityCollection
     */
    public function getComments(): EntityCollection
    {
        return $this->comments;
    }

    /**
     * @param EntityCollection $comments
     *
     * @return $this
     */
    public function setComments(EntityCollection $comments): Post
    {
        $this->comments = $comments;
        return $this;
    }


}
