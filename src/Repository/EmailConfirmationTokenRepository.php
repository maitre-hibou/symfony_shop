<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmailConfirmationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EmailConfirmationToken|null findOneBy(array $criteria, array $orderBy = null)
 */
final class EmailConfirmationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailConfirmationToken::class);
    }
}
