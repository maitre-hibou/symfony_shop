<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        $this->checkUserStatus($user);
    }

    public function checkPostAuth(UserInterface $user)
    {
        $this->checkUserStatus($user);
    }

    private function checkUserStatus(UserInterface $user)
    {
        if ($user instanceof User && User::STATUS_LOCKED === $user->getStatus()) {
            throw new AccountExpiredException('Your account is disabled.');
        }
    }
}
