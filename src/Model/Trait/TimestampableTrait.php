<?php

namespace App\Model\Trait;

trait TimestampableTrait
{


    /**
     * @var \DateTime|null
     */
    private ?\DateTime $createdAt = null;

    /**
     * @var \DateTime|null
     */
    private ?\DateTime $updatedAt = null;

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}
