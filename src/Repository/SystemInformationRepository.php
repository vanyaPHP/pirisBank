<?php

namespace App\Repository;

use App\Entity\SystemInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SystemInformation>
 *
 * @method SystemInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemInformation[]    findAll()
 * @method SystemInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemInformation::class);
    }

//    /**
//     * @return SystemInformationService[] Returns an array of SystemInformationService objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SystemInformationService
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
