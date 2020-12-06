<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use App\Repository\UserRepository;
use App\Repository\RestaurantRepository;

class RestaurantControllerTest extends WebTestCase
{    
    /**
     * Test visit restarants page for mot logged in user
     */
    public function testIndexNotLoggedIn()
    {
        //request
        $client = static::createClient();
        $client->request('GET', '/');
        //assert
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects();
    }
    
    /**
     * Test visit restarants page for logged in user
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
        $client->request('GET', '/');
        //assert
        $this->assertResponseIsSuccessful();
        $this->assertContains($userEmail, $client->getResponse()->getContent());
    }
    
    /**
     * Test add restaurant 
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
        $crawler = $client->request('GET', '/restaurant/add');
        $extract = $crawler->filter('input[name="restaurant[_token]"]')->extract(['value']);
        $token = $extract[0];
        $title = date('today');
        $formData = [
            'restaurant' => [
                'title' => $title,
                'max_active_tables' => 1,
                '_token' => $token,
            ],
        ];
        //request
        $crawler = $client->request('POST', '/restaurant/add', $formData);
        //assert
        $newRestaurant = static::$container->get(RestaurantRepository::class)->findOneByTitle($title);
        $this->assertNotNull($newRestaurant);
    }
    
    /**
     * Test update restaurant 
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
        $restaurant = static::$container->get(RestaurantRepository::class)->findOneByTitle('title1');//fixture restaurant
        $crawler = $client->request('GET', '/restaurant/update/'.$restaurant->getId());
        $extract = $crawler->filter('input[name="restaurant[_token]"]')->extract(['value']);
        $token = $extract[0];
        $newTitle = 'titleUpdated';
        $formData = [
            'restaurant' => [
                'title' => $newTitle,
                'max_active_tables' => 1,
                'status' => true,
                '_token' => $token,
            ],
        ];
        //request
        $crawler = $client->request('POST', '/restaurant/update/'.$restaurant->getId(), $formData);
        //assert
        $updatedRestaurant = static::$container->get(RestaurantRepository::class)->findOneByTitle($newTitle);
        $this->assertNotNull($updatedRestaurant);
        //updating restaurant with less max_active_tables than the actual active tables
        $formData = [
            'restaurant' => [
                'title' => $newTitle,
                'max_active_tables' => 0,
                'status' => true,
                '_token' => $token,
            ],
        ];
        //request
        $crawler = $client->request('POST', '/restaurant/update/'.$restaurant->getId(), $formData);
        //assert
        $restaurantNotUpdated = static::$container->get(RestaurantRepository::class)
            ->findOneBy(['max_active_tables' => 0, 'title' => $newTitle]);
        $this->assertNull($restaurantNotUpdated);
        $this->assertResponseRedirects();
    }
    
    /**
     * Test delete restaurant 
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
        $restaurant = static::$container->get(RestaurantRepository::class)->findOneByTitle('title2');//fixture restaurant
        $restaurantId = $restaurant->getId();
        $crawler = $client->request('GET', '/restaurant/delete/'.$restaurantId);
        //assert
        $deletedRestaurant = static::$container->get(RestaurantRepository::class)->find($restaurantId);
        $this->assertNull($deletedRestaurant);
    }
}
