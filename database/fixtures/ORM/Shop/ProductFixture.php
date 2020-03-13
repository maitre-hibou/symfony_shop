<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM\Shop;

use App\Entity\Shop\AbstractProduct;
use App\Entity\Shop\DefaultProduct;
use App\Repository\Shop\CategoryRepository;
use App\Repository\Shop\ProductUnitRepository;
use App\Shop\ProductInterface;
use App\Shop\ProductUnitInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class ProductFixture extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var Generator
     */
    private $faker;

    private $categoryRepository;

    private $productUnitRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductUnitRepository $productUnitRepository
    ){
        $this->faker = Factory::create();

        $this->categoryRepository = $categoryRepository;
        $this->productUnitRepository = $productUnitRepository;
    }

    public function load(ObjectManager $manager)
    {
        $productClasses = [DefaultProduct::class];
        $productStatuses = [AbstractProduct::STATUS_DRAFT, AbstractProduct::STATUS_ONLINE, AbstractProduct::STATUS_OFFLINE];
        $productUnits = $this->productUnitRepository->findAll();
        $vats = [ProductInterface::VAT_55, ProductInterface::VAT_10, ProductInterface::VAT_20];

        for ($productsCount = 1; $productsCount <= 30; ++$productsCount) {

            $productClass = $productClasses[rand(0, count($productClasses) - 1)];

            /** @var AbstractProduct $product */
            $product = new $productClass();

            $product
                ->setTitle(ucfirst($this->faker->words(rand(2, 5), true)))
                ->setDescription($this->faker->realText(500))
                ->setUnitPriceExclTax($this->faker->randomFloat(2, 1.0, 1500.0))
                ->setVat($vats[rand(0, count($vats) - 1)])
                ->setStatus($productStatuses[rand(0, count($productStatuses) - 1)])
                ->setProductUnit($productUnits[rand(0, count($productUnits) - 1)])
            ;

            $product->setCategories(new ArrayCollection([
                $this->categoryRepository->find(1)
            ]));

            if ($product instanceof DefaultProduct) {
                if ($product->getProductUnit()->getType() === ProductUnitInterface::TYPE_UNIT_FLOAT) {
                    $product->setStock($this->faker->randomFloat(2, 0.0, 2500.0));
                } else {
                    $product->setStock($this->faker->randomFloat(0, 0.0, 150.0));
                }

                switch ($product->getProductUnit()->getLabel()) {
                    case 'Gram (g)':
                        $product->setWeight(1);
                        break;
                    case 'Kilogram (Kg)':
                        $product->setWeight(1000);
                        break;
                    default:
                        $product->setWeight($this->faker->randomFloat(1, 1.0, 10500.0));
                        break;
                }

                if ($this->faker->boolean(30)) {
                    $product
                        ->setWidth($this->faker->randomFloat(1, 10.0, 1000.0))
                        ->setLength($this->faker->randomFloat(1, 10.0, 1000.0))
                        ->setHeight($this->faker->randomFloat(1, 10.0, 1000.0))
                    ;
                }
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 21;
    }
}
