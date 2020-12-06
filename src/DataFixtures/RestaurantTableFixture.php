<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\Entity\RestaurantTable;

class RestaurantTableFixture extends Fixture implements OrderedFixtureInterface
{
    /**
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }
    
    public function load(ObjectManager $manager)
    {
        $restaurantTable1 = new RestaurantTable();
        $restaurantTable1->setCapacity(1);
        $restaurantTable1->setNumber(1);
        $restaurantTable1->setStatus(true);
        $restaurantTable1->setRestaurant($this->getReference('restaurant1'));
        $manager->persist($restaurantTable1);
        
        $restaurantTable2 = new RestaurantTable();
        $restaurantTable2->setCapacity(1);
        $restaurantTable2->setNumber(2);
        $restaurantTable2->setStatus(true);
        $restaurantTable2->setRestaurant($this->getReference('restaurant1'));
        $manager->persist($restaurantTable1);

        $manager->flush();
        
        $this->addReference('restaurantTable1', $restaurantTable1);
        $this->addReference('restaurantTable2', $restaurantTable2);
    }
}
