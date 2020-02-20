<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class UserCreatedEvent extends Event
{
    private $user;

    private $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
