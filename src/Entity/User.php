<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 5, 'max' => 25])]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $username;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 5, 'max' => 25])]
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $password;

    #[ORM\Column(type: 'json')]
    private array $roles;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createdToken;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $addedAt;

    public function __construct(string $username)
    {
        $this->id = Uuid::v7();
        $this->username = $username;
        $this->password = null;
        $this->token = null;
        $this->createdToken = null;
        $this->roles = ['ROLE_USER'];
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->token;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAddedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedToken(): \DateTimeImmutable
    {
        return $this->createdToken;
    }

    public function setCreatedToken(): self
    {
        $this->createdToken = new \DateTimeImmutable();

        return $this;
    }
}

