<?php

namespace App\Model;

use App\Model\Trait\TimestampableTrait;

class PasswordRequest
{

    const TABLE = 'password_requests';

    use TimestampableTrait;

    private int $id;
    private User $user;
    private string $token;
    private \DateTime $expiresAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PasswordRequest
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): PasswordRequest
    {
        $this->user = $user;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): PasswordRequest
    {
        $this->token = $token;
        return $this;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTime $expiresAt): PasswordRequest
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }
}
