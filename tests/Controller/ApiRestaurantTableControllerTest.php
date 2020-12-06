<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use App\Repository\UserRepository;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantTableRepository;

class ApiRestaurantTableControllerTest extends WebTestCase
{        
    /**
     * Test visit restarant table page
     */
    public function testIndexLoggedIn()
    {
        //request
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $userEmail = 'admin@admin.com';
        $testUser = $userRepository->findOneByEmail($userEmail);
        $restaurant = static::$container->get(RestaurantRepository::class)->findOneByStatus(true);//fixture restaurant
        $client->request('GET', '/api/restaurant/table/'.$restaurant->getId(), ['apikey' => $testUser->getApiToken()]);
        //assert
        $this->assertResponseIsSuccessful();
        $this->assertContains($restaurant->getTitle(), $client->getResponse()->getContent());
    }
}
