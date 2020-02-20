<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserManager
{
    private $userPasswordEncoder;

    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
    }

    public function changeUserPassword(User $user, string $password): User
    {
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $password)
        );

        $this->entityManager->flush();

        return $user;
    }
}
