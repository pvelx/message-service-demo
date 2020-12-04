<?php

namespace App\Domain\Push\Repository;

use App\Domain\Push\Entity\Push;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Push|null find($id, $lockMode = null, $lockVersion = null)
 * @method Push|null findOneBy(array $criteria, array $orderBy = null)
 * @method Push[]    findAll()
 * @method Push[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PushRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Push::class);
    }

    // /**
    //  * @return Push[] Returns an array of Push objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Push
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
