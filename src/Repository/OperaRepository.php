<?php

namespace App\Repository;

use App\Entity\Opera;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Opera|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opera|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opera[]    findAll()
 * @method Opera[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Opera::class);
    }

    // /**
    //  * @return Opera[] Returns an array of Opera objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Opera
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
