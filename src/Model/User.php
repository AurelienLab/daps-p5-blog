<?php

namespace App\Model;

use App\Model\Trait\SoftDeleteTrait;
use App\Model\Trait\TimestampableTrait;

class User
{

    use TimestampableTrait,
        SoftDeleteTrait;

    const TABLE = 'users';

    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private ?string $rememberMeToken = null;

    private bool $isAdmin = false;

    private ?string $profilePicture = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getRememberMeToken(): ?string
    {
        return $this->rememberMeToken;
    }

    public function setRememberMeToken(?string $rememberMeToken): User
    {
        $this->rememberMeToken = $rememberMeToken;
        return $this;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): User
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): User
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }


}
