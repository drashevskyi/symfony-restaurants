<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use App\Entity\Restaurant;

class RestaurantFixture extends Fixture implements OrderedFixtureInterface
{
    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }
    
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $restaurant1 = new Restaurant();
        $restaurant1->setTitle('title1');
        $restaurant1->setMaxActiveTables(1);
        $restaurant1->setStatus(true);
        $restaurant1->setUser($this->getReference('user1'));
        $manager->persist($restaurant1);
        
        $restaurant2 = new Restaurant();
        $restaurant2->setTitle('title2');
        $restaurant2->setMaxActiveTables(1);
        $restaurant2->setStatus(true);
        $restaurant2->setUser($this->getReference('user1'));
        $manager->persist($restaurant2);

        $manager->flush();
        
        $this->addReference('restaurant1', $restaurant1);
        $this->addReference('restaurant2', $restaurant2);
    }
}
