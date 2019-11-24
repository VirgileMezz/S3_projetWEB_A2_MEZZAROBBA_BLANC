<?php

namespace App\Repository;

use App\Entity\CategorieDepense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategorieDepense|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieDepense|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieDepense[]    findAll()
 * @method CategorieDepense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieDepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieDepense::class);
    }

    // /**
    //  * @return CategorieDepense[] Returns an array of CategorieDepense objects
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
    public function findOneBySomeField($value): ?CategorieDepense
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
