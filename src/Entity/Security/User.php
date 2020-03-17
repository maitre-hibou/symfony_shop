<?php

declare(strict_types=1);

namespace App\Entity\Security;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\UserRepository")
 * @ORM\Table(name="security_user")
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    public const STATUS_LOCKED = -1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $conditionsAccepted;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $optinCommercial;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var EmailConfirmationToken|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Security\EmailConfirmationToken", mappedBy="user")
     */
    private $emailConfirmationToken;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", options={"default"=App\Entity\Security\User::STATUS_INACTIVE})
     */
    private $status;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Security\UserAddress", mappedBy="user")
     */
    private $userAddresses;

    public function __construct()
    {
        $this->roles = [self::ROLE_USER];
        $this->optinCommercial = false;
        $this->conditionsAccepted = false;
        $this->status = self::STATUS_ACTIVE;
        $this->userAddresses = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s %s (%s)', $this->getFirstname(), $this->getLastname(), $this->getEmail());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isConditionsAccepted(): bool
    {
        return $this->conditionsAccepted;
    }

    public function setConditionsAccepted(bool $conditionsAccepted): self
    {
        $this->conditionsAccepted = $conditionsAccepted;

        return $this;
    }

    public function isOptinCommercial(): bool
    {
        return $this->optinCommercial;
    }

    public function setOptinCommercial(bool $optinCommercial): self
    {
        $this->optinCommercial = $optinCommercial;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
    }

    public function getEmailConfirmationToken(): ?EmailConfirmationToken
    {
        return $this->emailConfirmationToken;
    }

    public function setEmailConfirmationToken(EmailConfirmationToken $emailConfirmationToken): self
    {
        $this->emailConfirmationToken = $emailConfirmationToken;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }
}
