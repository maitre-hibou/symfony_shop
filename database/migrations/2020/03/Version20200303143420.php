<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200303143420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create basic products tables.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE shop_product (id INT AUTO_INCREMENT NOT NULL, shop_product_unit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, status VARCHAR(255) DEFAULT \'DRAFT\' NOT NULL, unit_price_excl_tax DOUBLE PRECISION NOT NULL, vat DOUBLE PRECISION DEFAULT \'20\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, type VARCHAR(32) NOT NULL, stock DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, length DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D0794487C0976F89 (shop_product_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_product_shop_category (shop_product_id INT NOT NULL, shop_category_id INT NOT NULL, INDEX IDX_5F924EB83FF78B7C (shop_product_id), INDEX IDX_5F924EB8C0316BF2 (shop_category_id), PRIMARY KEY(shop_product_id, shop_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_product_unit (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(64) NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop_product ADD CONSTRAINT FK_D0794487C0976F89 FOREIGN KEY (shop_product_unit_id) REFERENCES shop_product_unit (id)');
        $this->addSql('ALTER TABLE shop_product_shop_category ADD CONSTRAINT FK_5F924EB83FF78B7C FOREIGN KEY (shop_product_id) REFERENCES shop_product (id)');
        $this->addSql('ALTER TABLE shop_product_shop_category ADD CONSTRAINT FK_5F924EB8C0316BF2 FOREIGN KEY (shop_category_id) REFERENCES shop_category (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE shop_product_shop_category DROP FOREIGN KEY FK_5F924EB83FF78B7C');
        $this->addSql('ALTER TABLE shop_product DROP FOREIGN KEY FK_D0794487C0976F89');
        $this->addSql('ALTER TABLE shop_product_shop_category DROP FOREIGN KEY FK_5F924EB8C0316BF2');
        $this->addSql('DROP TABLE shop_product');
        $this->addSql('DROP TABLE shop_product_shop_category');
        $this->addSql('DROP TABLE shop_product_unit');
        $this->addSql('DROP TABLE shop_category');
    }
}
