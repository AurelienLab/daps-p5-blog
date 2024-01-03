<?php

namespace App\Model\Trait;

trait SoftDeleteTrait
{

    /**
     * @var \DateTime|null
     */
    private ?\DateTime $deletedAt = null;

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}