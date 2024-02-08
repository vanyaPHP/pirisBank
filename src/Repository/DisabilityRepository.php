<?php

namespace App\Repository;

use App\Entity\Disability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Disability>
 *
 * @method Disability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disability[]    findAll()
 * @method Disability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disability::class);
    }

//    /**
//     * @return Disability[] Returns an array of Disability objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Disability
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
