<?php

declare(strict_types=1);

namespace App\Entity\Shop;

use App\Entity\SeoManageableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Shop\CategoryRepository")
 * @ORM\Table(name="shop_category")
 */
class Category
{
    use SeoManageableTrait;
    use TimestampableEntity;

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
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Shop\AbstractProduct", mappedBy="categories")
     * @ORM\JoinTable(
     *     name="shop_product_shop_category",
     *     joinColumns={@ORM\JoinColumn(name="shop_category_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="shop_product_id", referencedColumnName="id")}
     * )
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getProducts(): Collection
    {
        return $this->products;
    }
}
