<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixture extends Fixture implements OrderedFixtureInterface
{
    private static $USERS_CATALOG = [
        'user1@example.com' => ['Secret@1', 'Foo', 'Bar', true, false, User::STATUS_ACTIVE, [User::ROLE_USER]],
        'user2@example.com' => ['Secret@2', 'Bar', 'Baz', true, true, User::STATUS_ACTIVE, [User::ROLE_USER]],
        'user3@example.com' => ['Secret@3', 'Baz', 'Foo', true, false, User::STATUS_INACTIVE, [User::ROLE_USER]],
        'user4@example.com' => ['Secret@4', 'Baz', 'Bar', true, false, User::STATUS_LOCKED, [User::ROLE_USER]],
        'superadmin@example.com' => ['Secret@12', 'Test', 'Admin', true, false, User::STATUS_ACTIVE, [User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_SUPERADMIN]],
        'admin1@example.com' => ['Secret@12', 'Test', 'Admin', true, false, User::STATUS_ACTIVE, [User::ROLE_USER, User::ROLE_ADMIN]],
    ];

    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $userCount = 1;
        foreach (self::$USERS_CATALOG as $email => $userData) {
            list($password, $firstname, $lastname, $isConditionsAccepted, $isOptinCommercial, $status, $roles) = $userData;

            $user = new User();

            $user
                ->setEmail($email)
                ->setPassword($this->userPasswordEncoder->encodePassword($user, $password))
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setConditionsAccepted($isConditionsAccepted)
                ->setOptinCommercial($isOptinCommercial)
                ->setStatus($status)
                ->setRoles($roles)
            ;

            $manager->persist($user);
            $this->addReference(sprintf('User_%d', $userCount), $user);

            $userCount++;
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
