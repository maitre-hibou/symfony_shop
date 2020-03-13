<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Security\User;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200227162117 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add superadmin user.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO `security_user` (firstname, lastname, email, password, created_at, updated_at, status, roles) VALUES (:firstname, :lastname, :email, :password, NOW(), NOW(), :status, :roles);', [
            'firstname' => 'SUPER',
            'lastname' => 'ADMIN',
            'email' => 'superadmin@example.com',
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$5Rwr0sDs2FhTdN7DbaZ1ZQ$OUWjkAa5jtiNNmCKYoEBKe3fq5Lm76+GoBwMAZAcSdI', //Secret@12
            'status' => User::STATUS_ACTIVE,
            'roles' => serialize(['ROLE_USER', 'ROLE_SUPERADMIN'])
        ]);
    }

    public function down(Schema $schema) : void
    {
        // Nothing to do here ...
    }
}
