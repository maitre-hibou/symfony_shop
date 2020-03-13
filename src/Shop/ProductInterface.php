<?php

declare(strict_types=1);

namespace App\Shop;

interface ProductInterface
{
    public const TYPE_DEFAULT = 'DEFAULT';

    public const VAT_20 = 20.0;
    public const VAT_10 = 10.0;
    public const VAT_55 = 5.5;

    public function getUnitPriceExclTax(): ?float;
}
