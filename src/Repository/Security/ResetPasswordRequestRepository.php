<?php

declare(strict_types=1);

namespace App\Repository\Security;

use App\Entity\Security\ResetPasswordRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResetPasswordRequest|null findOneBy(array $criteria, array $orderBy = null)
 */
final class ResetPasswordRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }
}
