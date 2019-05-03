<?php

namespace App\Repository;

use App\Entity\StoredImg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StoredImg|null find($id, $lockMode = null, $lockVersion = null)
 * @method StoredImg|null findOneBy(array $criteria, array $orderBy = null)
 * @method StoredImg[]    findAll()
 * @method StoredImg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StoredImgRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StoredImg::class);
    }

    // /**
    //  * @return StoredImg[] Returns an array of StoredImg objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StoredImg
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
