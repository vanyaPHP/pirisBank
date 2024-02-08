<?php

namespace App\Repository;

use App\Entity\DepositPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DepositPlan>
 *
 * @method DepositPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositPlan[]    findAll()
 * @method DepositPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositPlan::class);
    }

//    /**
//     * @return DepositPlan[] Returns an array of DepositPlan objects
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

//    public function findOneBySomeField($value): ?DepositPlan
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
