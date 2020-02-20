<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Form\Dto\Security\Registration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class RegistrationHandler
{
    private $userPasswordEncoder;

    private $entityManager;

    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
    }

    public function handleRegistration(Registration $registrationData): User
    {
        $user = new User();

        $user
            ->setEmail($registrationData->email)
            ->setConditionsAccepted($registrationData->conditionsAccepted)
            ->setFirstname($registrationData->firstname)
            ->setLastname($registrationData->lastname)
            ->setOptinCommercial($registrationData->optinCommercial)
            ->setStatus(User::STATUS_INACTIVE)
        ;

        $user->setPassword(
            $this->userPasswordEncoder->encodePassword($user, $registrationData->password)
        );

        $emailConfirmationToken = (new EmailConfirmationToken())
            ->setToken(sha1(uniqid($user->getEmail(), true)))
            ->setUser($user);

        $user->setEmailConfirmationToken($emailConfirmationToken);

        $this->entityManager->persist($user);
        $this->entityManager->persist($emailConfirmationToken);

        $this->entityManager->flush();

        return $user;
    }
}
