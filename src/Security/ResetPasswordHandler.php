<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Form\Dto\Security\ResetPassword;
use App\Form\Dto\Security\ResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class ResetPasswordHandler
{
    private $userRepository;

    private $resetPasswordRequestRepository;

    private $entityManager;

    private $userPasswordEncoder;

    public function __construct(
        UserRepository $userRepository,
        ResetPasswordRequestRepository $resetPasswordRequestRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->resetPasswordRequestRepository = $resetPasswordRequestRepository;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function getResetPasswordRequest(string $token): \App\Entity\ResetPasswordRequest
    {
        if (null === ($resetPasswordRequest = $this->resetPasswordRequestRepository->findOneBy(['token' => $token]))) {
            throw new \LogicException('This token does not exists.');
        }

        if (600 < time() - $resetPasswordRequest->getCreatedAt()->getTimestamp()) {
            $this->entityManager->remove($resetPasswordRequest);

            $this->entityManager->flush();

            throw new \LogicException('This request is no longer valid. Please ask for a new password change.');
        }

        return $resetPasswordRequest;
    }

    public function handleResetPasswordRequest(ResetPasswordRequest $resetPasswordRequest): ?\App\Entity\ResetPasswordRequest
    {
        if (null === $this->userRepository->findOneBy(['email' => $resetPasswordRequest->email])) {
            return null;
        }

        if (null !== ($resetPasswordRequestEntity = $this->resetPasswordRequestRepository->findOneBy(['email' => $resetPasswordRequest->email]))) {
            if (600 < time() - $resetPasswordRequestEntity->getCreatedAt()->getTimestamp()) {
                $this->entityManager->remove($resetPasswordRequestEntity);
                $this->entityManager->flush();

                $resetPasswordRequestEntity = $this->buildNewResetPasswordRequestEntity($resetPasswordRequest);

                $this->entityManager->persist($resetPasswordRequestEntity);

                $this->entityManager->flush();
            }
        } else {
            $resetPasswordRequestEntity = $this->buildNewResetPasswordRequestEntity($resetPasswordRequest);

            $this->entityManager->persist($resetPasswordRequestEntity);

            $this->entityManager->flush();
        }

        return $resetPasswordRequestEntity;
    }

    public function handleUserPasswordChange(\App\Entity\ResetPasswordRequest $resetPasswordRequest, ResetPassword $resetPassword)
    {
        /** @var User $user */
        if (null === ($user = $this->userRepository->findOneBy(['email' => $resetPasswordRequest->getEmail()]))) {
            throw new \LogicException('This user does not exist.');
        }

        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $resetPassword->password));

        $this->entityManager->flush();
    }

    private function buildNewResetPasswordRequestEntity(ResetPasswordRequest $resetPasswordRequest): \App\Entity\ResetPasswordRequest
    {
        $resetPasswordRequestEntity = new \App\Entity\ResetPasswordRequest();

        $resetPasswordRequestEntity->setEmail($resetPasswordRequest->email)
            ->setToken($this->generateToken($resetPasswordRequest));

        return $resetPasswordRequestEntity;
    }

    private function generateToken(ResetPasswordRequest $resetPasswordRequest): string
    {
        $token = sha1(uniqid($resetPasswordRequest->email, true));

        if (null !== $this->resetPasswordRequestRepository->findOneBy(['token' => $token])) {
            return $this->generateToken($resetPasswordRequest);
        }

        return $token;
    }
}
