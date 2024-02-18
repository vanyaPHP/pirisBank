<?php

namespace App\Repository;

use App\Entity\CreditPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreditPlan>
 *
 * @method CreditPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditPlan[]    findAll()
 * @method CreditPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreditPlan::class);
    }

//    /**
//     * @return CreditPlan[] Returns an array of CreditPlan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CreditPlan
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
