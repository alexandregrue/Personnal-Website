<?php

namespace App\Repository;

use App\Entity\StackImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StackImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StackImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StackImage[]    findAll()
 * @method StackImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StackImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StackImage::class);
    }

    // /**
    //  * @return StackImage[] Returns an array of StackImage objects
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
    public function findOneBySomeField($value): ?StackImage
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
