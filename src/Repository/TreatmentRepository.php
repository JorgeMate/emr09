<?php

namespace App\Repository;

use App\Entity\Treatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Treatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Treatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Treatment[]    findAll()
 * @method Treatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Treatment::class);
    }

    /**
     * @return Treatment[] Returns an array of Treatment objects
    */
    public function findByType($value)
    {

        return $this->createQueryBuilder('t')
            ->innerJoin('t.type', 'ty')
            ->andWhere('ty.id = :val')

            ->setParameter('val', $value)
            ->orderBy('t.name', 'ASC')
            
            ->getQuery()
            ->getResult()
        ;
    }    

    // /**
    //  * @return Treatment[] Returns an array of Treatment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Treatment
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
