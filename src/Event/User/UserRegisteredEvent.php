<?php

declare(strict_types=1);

namespace App\Event\User;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Thrown when a user has registered.
 */
final class UserRegisteredEvent extends Event
{
    public const NAME = 'app.user.registered';

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
