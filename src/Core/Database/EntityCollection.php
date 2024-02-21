<?php

namespace App\Core\Database;

/**
 * This class allow Many To One and Many to Many relationships in Model object
 * and can be automatically retrieved by Database class
 * Example: a Post has a Collection of Tags
 */
class EntityCollection implements \Iterator, \Countable
{

    const TYPE_MANY_TO_ONE = 0;
    const TYPE_MANY_TO_MANY = 1;

    private array $collection = [];

    /**
     * @param string $relatedEntity
     * @param int $relationType
     * @param string|null $originEntityProperty
     * @param string|null $targetEntityProperty
     * @param string|null $relationModel
     * @param array $targetRelations
     */
    public function __construct(
        private string  $relatedEntity,
        private int     $relationType,
        private ?string $originEntityProperty = null,
        private ?string $targetEntityProperty = null,
        private ?string $relationModel = null,
        private array   $targetRelations = []
    )
    {
    }

    /**
     * Add and object to the collection
     *
     * @param $entity
     *
     * @return $this
     * @throws \Exception
     */
    public function add($entity): self
    {
        if (($entity instanceof $this->relatedEntity) === false) {
            throw new \Exception('Entity must be of type %s', $this->relatedEntity);
        }

        $this->collection[] = $entity;

        return $this;
    }

    /**
     * Returns
     *
     * @return string
     */
    public function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }

    public function getRelationType(): int
    {
        return $this->relationType;
    }

    public function getOriginEntityProperty(): ?string
    {
        return $this->originEntityProperty;
    }


    public function getTargetEntityProperty(): ?string
    {
        return $this->targetEntityProperty;
    }

    public function getRelationModel(): ?string
    {
        return $this->relationModel;
    }

    public function toArray(): array
    {
        return $this->collection;
    }

    public function getTargetRelations(): array
    {
        return $this->targetRelations;
    }

    /* ITERATION METHODS */

    public function rewind(): void
    {
        reset($this->collection);
    }

    public function current(): mixed
    {
        return current($this->collection);
    }

    public function key(): string|int|null
    {
        return key($this->collection);
    }

    public function next(): void
    {
        next($this->collection);
    }

    public function valid(): bool
    {
        return key($this->collection) !== null;
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
