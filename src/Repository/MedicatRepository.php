<?php

namespace App\Repository;

use App\Entity\Medicat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Medicat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicat[]    findAll()
 * @method Medicat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Medicat::class);
    }

    // /**
    //  * @return Medicat[] Returns an array of Medicat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Medicat
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
