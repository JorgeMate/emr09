<?php

namespace App\Repository;

use App\Entity\Consult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Consult|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consult|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consult[]    findAll()
 * @method Consult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Consult::class);
    }

    // /**
    //  * @return Consult[] Returns an array of Consult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Consult
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
