<?php

namespace App\Repository;

use App\Entity\UserDoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserDoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDoc[]    findAll()
 * @method UserDoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDocRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserDoc::class);
    }

    // /**
    //  * @return UserDoc[] Returns an array of UserDoc objects
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
    public function findOneBySomeField($value): ?UserDoc
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
