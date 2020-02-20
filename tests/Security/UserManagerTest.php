<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserManagerTest extends TestCase
{
    public function testUserManagerChangeUserPasswordMethod()
    {
        $user = new User();
        $password = 'secret';

        $userPasswordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $userPasswordEncoder->expects($this->once())
            ->method('encodePassword')
            ->with($user, $password)
            ->willReturn('passwordEncoded');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('flush');

        $userManager = new UserManager($userPasswordEncoder, $entityManager);

        $user = $userManager->changeUserPassword($user, $password);

        $this->assertEquals('passwordEncoded', $user->getPassword());
    }
}
