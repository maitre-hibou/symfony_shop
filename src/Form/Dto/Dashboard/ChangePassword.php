<?php

declare(strict_types=1);

namespace App\Form\Dto\Dashboard;

use App\Form\Dto\Common\PasswordTrait;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

final class ChangePassword
{
    use PasswordTrait;

    /**
     * @var string
     *
     * @UserPassword()
     */
    public $currentPassword;
}
