<?php

namespace App\Repository;

use App\Entity\Citizenship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Citizenship>
 *
 * @method Citizenship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Citizenship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Citizenship[]    findAll()
 * @method Citizenship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CitizenshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Citizenship::class);
    }

//    /**
//     * @return Citizenship[] Returns an array of Citizenship objects
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

//    public function findOneBySomeField($value): ?Citizenship
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
