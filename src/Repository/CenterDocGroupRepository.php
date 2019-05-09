<?php

namespace App\Repository;

use App\Entity\CenterDocGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CenterDocGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CenterDocGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CenterDocGroup[]    findAll()
 * @method CenterDocGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CenterDocGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CenterDocGroup::class);
    }

    // /**
    //  * @return CenterDocGroup[] Returns an array of CenterDocGroup objects
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
    public function findOneBySomeField($value): ?CenterDocGroup
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
