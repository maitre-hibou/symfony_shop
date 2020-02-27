<?php

declare(strict_types=1);

namespace App\Entity\Security;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Security\ResetPasswordRequestRepository")
 * @ORM\Table(name="security_reset_password_request")
 */
final class ResetPasswordRequest
{
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
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): ResetPasswordRequest
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): ResetPasswordRequest
    {
        $this->token = $token;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): ResetPasswordRequest
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
