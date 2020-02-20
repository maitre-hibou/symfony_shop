<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190909131147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add security related tables.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email_confirmation_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C50AEE9BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE email_confirmation_token ADD CONSTRAINT FK_C50AEE9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(64) NOT NULL, token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7CE748AE7927C74 (email), UNIQUE INDEX UNIQ_7CE748A5F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE email_confirmation_token');
    }
}
