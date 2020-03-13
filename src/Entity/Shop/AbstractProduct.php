<?php

declare(strict_types=1);

namespace App\Entity\Shop;

use App\Entity\SeoManageableTrait;
use App\Shop\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="shop_product")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=32)
 * @ORM\DiscriminatorMap({
 *     App\Entity\Shop\DefaultProduct::TYPE="App\Entity\Shop\DefaultProduct"
 * })
 */
abstract class AbstractProduct
{
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_ONLINE = 'ONLINE';
    public const STATUS_OFFLINE = 'OFFLINE';

    use SeoManageableTrait;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", options={"default":App\Entity\Shop\AbstractProduct::STATUS_DRAFT})
     */
    protected $status;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    protected $unitPriceExclTax;

    /**
     * @var float
     *
     * @ORM\Column(type="float", options={"default":App\Shop\ProductInterface::VAT_20})
     */
    protected $vat;

    /**
     * @var ProductUnit
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop\ProductUnit")
     * @ORM\JoinColumn(name="shop_product_unit_id")
     */
    protected $productUnit;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Shop\Category", inversedBy="products", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *     name="shop_product_shop_category",
     *     joinColumns={@ORM\JoinColumn(name="shop_product_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="shop_category_id", referencedColumnName="id")}
     * )
     */
    protected $categories;

    public function __construct()
    {
        $this->status = self::STATUS_DRAFT;
        $this->vat = ProductInterface::VAT_20;
        $this->categories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle() ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUnitPriceExclTax(): ?float
    {
        return $this->unitPriceExclTax;
    }

    public function setUnitPriceExclTax(float $unitPriceExclTax): self
    {
        $this->unitPriceExclTax = $unitPriceExclTax;

        return $this;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function setVat(float $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getProductUnit(): ?ProductUnit
    {
        return $this->productUnit;
    }

    public function setProductUnit(ProductUnit $productUnit): self
    {
        $this->productUnit = $productUnit;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}
