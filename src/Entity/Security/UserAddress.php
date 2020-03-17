<?php

declare(strict_types=1);

namespace App\Entity\Security;

use App\Entity\Address;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\UserAddressRepository")
 * @ORM\Table(name="security_user_address")
 */
class UserAddress
{
    public const DEFAULT_LABEL = 'Home address';

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
    private $label;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $defaultAddress;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\User")
     * @ORM\JoinColumn(name="security_user_id")
     */
    private $user;

    /**
     * @var Address
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     */
    private $address;

    public function getId(): int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function isDefaultAddress(): ?bool
    {
        return $this->defaultAddress;
    }

    public function setDefaultAddress(bool $defaultAddress): self
    {
        $this->defaultAddress = $defaultAddress;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}
