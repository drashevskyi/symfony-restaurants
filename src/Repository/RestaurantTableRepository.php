<?php

namespace App\Repository;

use App\Entity\RestaurantTable;
use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RestaurantTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantTable[]    findAll()
 * @method RestaurantTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantTable::class);
    }

     /**
      * @return Restaurant[] Returns an array of Restaurant objects
      */
    public function countRestaurantActiveTables(Restaurant $restaurant)
    {        
        $qb = $this->createQueryBuilder('rt')
            ->select('COUNT(rt)')
            ->where('rt.restaurantId = :restaurant')
            ->andWhere('rt.status = 1')
            ->setParameter('restaurant', $restaurant);
        
        return intval($qb->getQuery()->getSingleScalarResult());
    }
}
