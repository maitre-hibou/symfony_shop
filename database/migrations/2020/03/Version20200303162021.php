<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200303162021 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add default shop category';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('INSERT INTO `shop_category` (name, description, created_at, updated_at) VALUES (:name, :description, NOW(), NOW());', [
            'name' => 'Uncategorized',
            'description' => 'Contains uncategorized products',
        ]);
    }

    public function down(Schema $schema) : void
    {
        // Nothing to do here
    }
}
