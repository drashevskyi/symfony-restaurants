<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Restaurant;
use App\Service\FileService;

class RestaurantService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var Security
     */
    private $security;
    
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * @param EntityManager $em
     * @param Security      $security
     * @param FileService   $fileService
     */
    public function __construct(EntityManagerInterface $em, Security $security, FileService $fileService)
    {
        $this->em = $em;
        $this->security = $security;
        $this->fileService = $fileService;
    }

    /**
     * @param Restaurant   $restaurant
     * @param UploadedFile $photo
     * @param bool         $create
     *
     * @return Restaurant
     */
    public function saveRestaurant(Restaurant $restaurant, UploadedFile $photo = null, $create = true): Restaurant
    {
        if ($photo) {
            $previousPhoto = $restaurant->getPhoto();
            
            if ($previousPhoto) {
                $this->fileService->remove($previousPhoto);
            }
            
            $fileName = $this->fileService->upload($photo);
            $restaurant->setPhoto($fileName);
        }
        
        $create ? $restaurant->setUser($this->security->getUser()) : false;
        
        $this->em->persist($restaurant);
        $this->em->flush();

        return $restaurant;
    }
}