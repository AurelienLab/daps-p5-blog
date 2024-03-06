<?php

namespace App\Model\Trait;

trait SoftDeleteTrait
{

    /**
     * @var \DateTime|null
     */
    private ?\DateTime $deletedAt = null;


    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }


    /**
     * @param \DateTime|null $deletedAt
     *
     * @return SoftDeleteTrait|\App\Model\Post|\App\Model\User
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }


}
