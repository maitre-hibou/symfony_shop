<?php

declare(strict_types=1);

namespace App\Form\Dto\Dashboard;

use App\Form\Dto\Address;
use App\Form\Dto\DataTransferObjectInterface;

final class CreateUserAddress implements DataTransferObjectInterface
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var bool
     */
    public $default;

    /**
     * @var Address
     */
    public $address;
}
