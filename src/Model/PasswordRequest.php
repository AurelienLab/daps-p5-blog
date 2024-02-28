<?php

namespace App\Model;

use App\Model\Trait\TimestampableTrait;

class PasswordRequest
{

    const TABLE = 'password_requests';

    use TimestampableTrait;

    /**
     * @var integer
     */
    private int $id;
    /**
     * @var User
     */
    private User $user;
    /**
     * @var string
     */
    private string $token;
    /**
     * @var \DateTime
     */
    private \DateTime $expiresAt;

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
    public function setId(int $id): PasswordRequest
    {
        $this->id = $id;
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
    public function setUser(User $user): PasswordRequest
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken(string $token): PasswordRequest
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expiresAt): PasswordRequest
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }


}
