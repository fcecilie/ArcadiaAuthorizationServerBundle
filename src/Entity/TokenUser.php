<?php

namespace Arcadia\Bundle\AuthorizationBundle\Entity;

use Arcadia\Bundle\AuthorizationBundle\Repository\TokenUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=TokenUserRepository::class)
 * @ORM\Table(name="token_user")
 */
class TokenUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected string $password;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    protected string $username;

    /**
     * @ORM\Column(type="json")
     */
    protected array $roles;

    public function __construct()
    {
        $this->password = bin2hex(random_bytes(64));
        $this->roles = [];
    }

    public function getId(): ?int
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

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_TOKEN';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
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

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}