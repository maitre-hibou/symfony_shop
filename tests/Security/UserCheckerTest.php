<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;

final class UserCheckerTest extends TestCase
{
    public function testPreAuthMethodThrowsExceptionWithLockedUser()
    {
        $this->expectException(AccountExpiredException::class);
        $this->expectExceptionMessage('Your account is disabled.');

        $user = new User();

        $user->setStatus(User::STATUS_LOCKED);

        $userChecker = new UserChecker();

        $userChecker->checkPreAuth($user);
    }

    public function testPostAuthMethodThrowsExceptionWithLockedUser()
    {
        $this->expectException(AccountExpiredException::class);
        $this->expectExceptionMessage('Your account is disabled.');

        $user = new User();

        $user->setStatus(User::STATUS_LOCKED);

        $userChecker = new UserChecker();

        $userChecker->checkPreAuth($user);
    }
}
