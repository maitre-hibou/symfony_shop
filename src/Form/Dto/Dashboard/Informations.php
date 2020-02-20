<?php

declare(strict_types=1);

namespace App\Form\Dto\Dashboard;

use App\Entity\User;
use App\Form\Dto\DataTransferObjectInterface;
use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class Informations implements DataTransferObjectInterface, AutoMapperConfiguratorInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $firstname;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $lastname;

    public function configure(AutoMapperConfigInterface $config): void
    {
        $config
            ->registerMapping(User::class, self::class)
            ->reverseMap()
        ;
    }
}
