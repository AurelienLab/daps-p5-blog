<?php

namespace App\Core\Database;

class EntityCollection implements \Iterator
{

    const TYPE_MANY_TO_ONE = 0;
    const TYPE_MANY_TO_MANY = 1;

    private array $collection = [];

    public function __construct(
        private string  $relatedEntity,
        private int     $relationType,
        private ?string $targetEntityProperty = null,
        private ?string $relationModel = null,
        private array   $targetRelations = []
    )
    {
    }

    public function add($entity): self
    {
        if (($entity instanceof $this->relatedEntity) === false) {
            throw new \Exception('Entity must be of type %s', $this->relatedEntity);
        }

        $this->collection[] = $entity;

        return $this;
    }

    public function getRelatedEntity(): string
    {
        return $this->relatedEntity;
    }

    public function getRelationType(): int
    {
        return $this->relationType;
    }

    public function getTargetEntityProperty(): string
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
}
