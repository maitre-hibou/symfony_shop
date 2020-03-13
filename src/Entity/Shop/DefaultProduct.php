<?php

declare(strict_types=1);

namespace App\Entity\Shop;

use App\Shop\ProductInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class DefaultProduct extends AbstractProduct implements ProductInterface
{
    public const TYPE = ProductInterface::TYPE_DEFAULT;

    /**
     * @var ProductUnit
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop\ProductUnit")
     * @ORM\JoinColumn(name="shop_product_unit_id")
     */
    protected $productUnit;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $stock;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $weight;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $width;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $height;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $length;

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function setStock(float $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }
}
