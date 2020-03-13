<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Shop\ProductUnitInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200303143506 extends AbstractMigration
{
    private $firtsUnits = [
        ['Unit', ProductUnitInterface::TYPE_UNIT_INT],
        ['Kilogram (Kg)', ProductUnitInterface::TYPE_UNIT_FLOAT],
        ['Gram (g)', ProductUnitInterface::TYPE_UNIT_FLOAT],
    ];

    public function getDescription() : string
    {
        return 'Insert first products units.';
    }

    public function up(Schema $schema) : void
    {
        foreach ($this->firtsUnits as $unitData) {
            list ($label, $unitType) = $unitData;

            $this->addSql('INSERT INTO `shop_product_unit` (label, type) VALUES (:label, :type);', [
                'label' => $label,
                'type' => $unitType,
            ]);
        }
    }

    public function down(Schema $schema) : void
    {
        // Nothing to do here ...
    }
}
