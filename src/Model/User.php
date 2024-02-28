<?php

namespace App\Model;

use App\Core\Utils\Encryption;
use App\Model\Trait\SoftDeleteTrait;
use App\Model\Trait\TimestampableTrait;

class User
{

    use TimestampableTrait,
        SoftDeleteTrait;

    const TABLE = 'users';

    /**
     * @var integer|null
     */
    private ?int $id = null;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $email = null;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string|null
     */
    private ?string $rememberMeToken = null;

    /**
     * @var boolean
     */
    private bool $isAdmin = false;

    /**
     * @var string|null
     */
    private ?string $profilePicture = null;

    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $emailValidatedAt = null;


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
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRememberMeToken(): ?string
    {
        return $this->rememberMeToken;
    }

    /**
     * @param string|null $rememberMeToken
     *
     * @return $this
     */
    public function setRememberMeToken(?string $rememberMeToken): User
    {
        $this->rememberMeToken = $rememberMeToken;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     *
     * @return $this
     */
    public function setIsAdmin(bool $isAdmin): User
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    /**
     * @param string|null $profilePicture
     *
     * @return $this
     */
    public function setProfilePicture(?string $profilePicture): User
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEmailValidatedAt(): ?\DateTimeImmutable
    {
        return $this->emailValidatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $emailValidatedAt
     *
     * @return $this
     */
    public function setEmailValidatedAt(?\DateTimeImmutable $emailValidatedAt): User
    {
        $this->emailValidatedAt = $emailValidatedAt;
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateRememberMeToken()
    {

        $data = [
            'userId' => $this->getId(),
            'email' => $this->getEmail(),
            'generated_at' => time()
        ];

        $token = Encryption::encrypt($data);

        $this->setRememberMeToken($token);

        return $token;
    }

    /**
     * Check if the user is allowed to log in
     *
     * @return bool
     */
    public function canConnect(): bool
    {
        if ($this->getEmailValidatedAt() === null) {
            return false;
        }

        return true;
    }


}
