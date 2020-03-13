<?php

declare(strict_types=1);

namespace App\Shop;

interface ProductUnitInterface
{
    public const TYPE_UNIT_FLOAT = 'float';
    public const TYPE_UNIT_INT = 'int';

    public function getType(): string;
}
