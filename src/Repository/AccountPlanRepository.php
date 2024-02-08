<?php

namespace App\Repository;

use App\Entity\AccountPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountPlan>
 *
 * @method AccountPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountPlan[]    findAll()
 * @method AccountPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountPlan::class);
    }

//    /**
//     * @return AccountPlan[] Returns an array of AccountPlan objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccountPlan
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
