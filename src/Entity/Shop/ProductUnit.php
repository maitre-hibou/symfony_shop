<?php

declare(strict_types=1);

namespace App\Entity\Shop;

use App\Shop\ProductUnitInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Shop\ProductUnitRepository")
 * @ORM\Table(name="shop_product_unit")
 */
class ProductUnit implements ProductUnitInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private $type;

    public function __toString()
    {
        return $this->getLabel() ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
