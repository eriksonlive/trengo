<?php

namespace App\Repository;

use App\Entity\TicketMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketMessages>
 *
 * @method TicketMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketMessages[]    findAll()
 * @method TicketMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketMessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMessages::class);
    }

//    /**
//     * @return TicketMessages[] Returns an array of TicketMessages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TicketMessages
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
