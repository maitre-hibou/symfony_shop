<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM\Shop;

use App\Entity\Shop\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($categoryCount = 1; $categoryCount <= 10; ++$categoryCount) {
            $category = new Category();

            $category
                ->setName(sprintf('Category %s', $categoryCount))
                ->setDescription('Lorem ipsum dolor sit amet')
            ;

            $manager->persist($category);

            $this->addReference(sprintf('Shop_Category_%s', $categoryCount), $category);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}
