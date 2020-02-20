<?php

declare(strict_types=1);

namespace App\Dashboard;

use App\Entity\User;
use App\Form\Dto\Dashboard\Informations;
use AutoMapperPlus\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;

final class ProfileManager
{
    private $autoMapper;

    private $entityManager;

    public function __construct(AutoMapperInterface $autoMapper, EntityManagerInterface $entityManager)
    {
        $this->autoMapper = $autoMapper;
        $this->entityManager = $entityManager;
    }

    public function updateInformations(User $user, Informations $informations): User
    {
        $user = $this->autoMapper->mapToObject($informations, $user);

        $this->entityManager->flush();

        return $user;
    }
}
