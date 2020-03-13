<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200313162626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adds SEO related fields to shop products and categories.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shop_product ADD slug VARCHAR(64) DEFAULT NULL, ADD meta_title VARCHAR(128) DEFAULT NULL, ADD meta_description TINYTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE shop_category ADD slug VARCHAR(64) DEFAULT NULL, ADD meta_title VARCHAR(128) DEFAULT NULL, ADD meta_description TINYTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shop_category DROP slug, DROP meta_title, DROP meta_description');
        $this->addSql('ALTER TABLE shop_product DROP slug, DROP meta_title, DROP meta_description');
    }
}
