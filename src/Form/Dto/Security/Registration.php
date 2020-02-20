<?php

declare(strict_types=1);

namespace App\Form\Dto\Security;

use App\Form\Dto\Common\PasswordTrait;
use App\Form\Dto\DataTransferObjectInterface;
use App\Validator\Constraint as AppConstraints;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AppConstraints\DtoUniqueEntity(entityClass="App\Entity\User", fieldMapping={"email": "email"}, message="There is already a user with email {{ value }}.")
 */
final class Registration implements DataTransferObjectInterface
{
    use PasswordTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

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

    /**
     * @var bool
     *
     * @Assert\IsTrue(message="You must accept terms & conditions to use our service.")
     */
    public $conditionsAccepted;

    /**
     * @var bool
     */
    public $optinCommercial;
}
