<?php

namespace App\Repository;

use App\Entity\UserLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLog[]    findAll()
 * @method UserLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserLog::class);
    }

    // /**
    //  * @return UserLog[] Returns an array of UserLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserLog
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
