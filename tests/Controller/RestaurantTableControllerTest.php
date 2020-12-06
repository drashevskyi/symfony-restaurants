<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use App\Repository\UserRepository;
use App\Repository\RestaurantRepository;
use App\Repository\RestaurantTableRepository;

class RestaurantTableControllerTest extends WebTestCase
{        
    /**
     * Test visit restarant table page
     */
    public function testIndexLoggedIn()
    {
        //login
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $userEmail = 'admin@admin.com';
        $testUser = $userRepository->findOneByEmail($userEmail);
        $client->loginUser($testUser);
        //request
        $restaurant = static::$container->get(RestaurantRepository::class)->findOneByStatus(true);//fixture restaurant
        $client->request('GET', '/restaurant/table/'.$restaurant->getId());
        //assert
        $this->assertResponseIsSuccessful();
        $this->assertContains('table', $client->getResponse()->getContent());
    }
    
    /**
     * Test add restaurant table
     */
    public function testAdd()
    {
        //login
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $userEmail = 'admin@admin.com';
        $testUser = $userRepository->findOneByEmail($userEmail);
        $client->loginUser($testUser);
        //get token & form data
        $restaurant = static::$container->get(RestaurantRepository::class)->findOneByStatus(true);//fixture restaurant
        $crawler = $client->request('GET', '/restaurant/table/'.$restaurant->getId().'/add');
        $extract = $crawler->filter('input[name="restaurant_table[_token]"]')->extract(['value']);
        $token = $extract[0];
        $number = rand(1, 99);
        $formData = [
            'restaurant_table' => [
                'number' => $number,
                'capacity' => 1,
                '_token' => $token,
            ],
        ];
        //request
        $crawler = $client->request('POST', '/restaurant/table/'.$restaurant->getId().'/add', $formData);
        //assert
        $newRestaurantTable = static::$container->get(RestaurantTableRepository::class)->findOneByNumber($number);
        $this->assertNotNull($newRestaurantTable);
        //add active to exceed the limit of active tables for restaurant
        $number = 100;
        $formData = [
            'restaurant_table' => [
                'number' => $number,
                'capacity' => 1,
                'status' => true,
                '_token' => $token,
            ],
        ];
        $crawler = $client->request('POST', '/restaurant/table/'.$restaurant->getId().'/add', $formData);
        $restaurantTableNotCreated = static::$container->get(RestaurantTableRepository::class)->findOneByNumber($number);
        $this->assertResponseRedirects();
        $this->assertNull($restaurantTableNotCreated);
    }
    
    /**
     * Test update restaurant table
     */
    public function testUpdate()
    {
        //login
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $userEmail = 'admin@admin.com';
        $testUser = $userRepository->findOneByEmail($userEmail);
        $client->loginUser($testUser);
        //get token & form data
        $restaurantTable = static::$container->get(RestaurantTableRepository::class)->findOneByNumber(1);//fixture restaurant table
        $crawler = $client->request('GET', '/restaurant/table/update/'.$restaurantTable->getId());
        $extract = $crawler->filter('input[name="restaurant_table[_token]"]')->extract(['value']);
        $token = $extract[0];
        $newNumber = 2;
        $formData = [
            'restaurant_table' => [
                'number' => $newNumber,
                'capacity' => 1,
                '_token' => $token,
            ],
        ];
        //request
        $crawler = $client->request('POST', '/restaurant/table/update/'.$restaurantTable->getId(), $formData);
        //assert
        $updatedRestaurantTable = static::$container->get(RestaurantTableRepository::class)->findOneByNumber($newNumber);
        $this->assertNotNull($updatedRestaurantTable);
    }
    
    /**
     * Test delete restaurant table
     */
    public function testDelete()
    {
        //login
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $userEmail = 'admin@admin.com';
        $testUser = $userRepository->findOneByEmail($userEmail);
        $client->loginUser($testUser);
        //get restaurant & request
        $restaurantTable = static::$container->get(RestaurantTableRepository::class)->findOneByNumber(2);//fixture restaurant table
        $restaurantTableId = $restaurantTable->getId();
        $crawler = $client->request('GET', '/restaurant/table/delete/'.$restaurantTableId);
        //assert
        $deletedRestaurantTable = static::$container->get(RestaurantTableRepository::class)->find($restaurantTableId);
        $this->assertNull($deletedRestaurantTable);
    }
}
