<?php

declare(strict_types=1);

namespace App\Form\Dto\Security;

use App\Form\Dto\DataTransferObjectInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ResetPasswordRequest implements DataTransferObjectInterface
{
    /**
     * @var string
     *
     * @Assert\Email()
     */
    public $email;
}
