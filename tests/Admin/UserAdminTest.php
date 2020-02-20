<?php

declare(strict_types=1);

namespace App\Tests\Admin;

final class UserAdminTest extends AbstractAdminTest
{
    public function testList()
    {
        $this->listAllowed('app/user');
    }

    public function testCreate()
    {
        $this->createAllowed('app/user', [
            'email' => 'user_test@example.com',
            'firstname' => 'Test',
            'lastname' => 'User',
            'conditionsAccepted' => true,
        ]);
    }

    public function testEdit()
    {
        $this->editAllowed('app/user', 2);
    }

    public function testDelete()
    {
        $this->deleteAllowed('app/user', 2);
    }
}
