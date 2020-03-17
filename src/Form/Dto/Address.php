<?php

declare(strict_types=1);

namespace App\Form\Dto;

final class Address implements DataTransferObjectInterface
{
    /**
     * @var int
     */
    public $civility;

    /**
     * @var string
     */
    public $firstname;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string|null
     */
    public $company;

    /**
     * @var string
     */
    public $address;

    /**
     * @var string|null
     */
    public $address2;

    /**
     * @var string
     */
    public $postcode;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $phone;
}
