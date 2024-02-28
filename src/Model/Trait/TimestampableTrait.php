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


    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }


    /**
     * @param \DateTime|null $createdAt
     *
     * @return \App\Model\User|\App\Model\Comment|\App\Model\PasswordRequest|\App\Model\Post|TimestampableTrait
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }


    /**
     * @param \DateTime|null $updatedAt
     *
     * @return \App\Model\User|\App\Model\Comment|\App\Model\PasswordRequest|\App\Model\Post|TimestampableTrait
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}
