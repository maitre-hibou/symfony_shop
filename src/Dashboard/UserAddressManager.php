<?php

declare(strict_types=1);

namespace App\Dashboard;

use App\Entity\Address;
use App\Entity\Security\User;
use App\Entity\Security\UserAddress;
use App\Form\Dto\Dashboard\CreateUserAddress;
use Doctrine\ORM\EntityManagerInterface;

final class UserAddressManager
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function saveUserAddress(CreateUserAddress $createUserAddress, User $user)
    {
        $address = new Address();

        $address
            ->setCivility($createUserAddress->address->civility)
            ->setFirstname($createUserAddress->address->firstname)
            ->setLastname($createUserAddress->address->lastname)
            ->setCompany($createUserAddress->address->company)
            ->setAddress($createUserAddress->address->address)
            ->setAddress2($createUserAddress->address->address2)
            ->setPostcode($createUserAddress->address->postcode)
            ->setCity($createUserAddress->address->city)
            ->setPhone($createUserAddress->address->phone)
        ;

        $userAddress = new UserAddress();

        $userAddress
            ->setAddress($address)
            ->setLabel(null === $createUserAddress->label ? UserAddress::DEFAULT_LABEL : $createUserAddress->label)
            ->setDefaultAddress($createUserAddress->default)
            ->setUser($user)
        ;

        /** @var UserAddress $currentUserAddress */
        foreach ($user->getUserAddresses() as $currentUserAddress) {
            if ($userAddress->isDefaultAddress()) {
                $currentUserAddress->setDefaultAddress(false);
            }
        }

        $this->entityManager->persist($userAddress);

        $this->entityManager->flush();
    }
}
