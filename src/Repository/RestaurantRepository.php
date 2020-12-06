<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Restaurant[]    findRestaurantsBy(string $likeTitle = null, User $user)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }
    
    public function findRestaurantsBy(string $likeTitle = null, User $user)
    {        
        $qb = $this->createQueryBuilder('r')
            ->addSelect('r.id, r.photo, r.title, r.status')
            ->leftJoin('r.tables', 'rt', 'WITH', 'rt.status = 1')
            ->addSelect('COUNT(rt.id) AS count')
            ->leftJoin('r.user', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user);
        
        if ($likeTitle) {
            $qb->andWhere('r.title LIKE :likeTitle')
                ->setParameter('likeTitle', '%'.$likeTitle.'%');
        }
        
        $qb->groupBy('r.id');
        
        $query = $qb->getQuery();
        
        return $query->getResult();
    }

}
