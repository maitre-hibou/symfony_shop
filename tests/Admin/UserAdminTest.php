<?php

declare(strict_types=1);

namespace App\Tests\Admin;

final class UserAdminTest extends AbstractAdminTest
{
    public function testList()
    {
        $this->listAllowed('app/security-user');
    }

    public function testCreate()
    {
        $this->createAllowed('app/security-user', [
            'email' => 'user_test@example.com',
            'firstname' => 'Test',
            'lastname' => 'User',
            'conditionsAccepted' => true,
        ]);
    }

    public function testEdit()
    {
        $this->editAllowed('app/security-user', 2);
    }

    public function testDelete()
    {
        $this->deleteAllowed('app/security-user', 2);
    }
}
