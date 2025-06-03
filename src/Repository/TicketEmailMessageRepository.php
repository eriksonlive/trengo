<?php

namespace App\Repository;

use App\Entity\TicketEmailMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketEmailMessage>
 *
 * @method TicketEmailMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketEmailMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketEmailMessage[]    findAll()
 * @method TicketEmailMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketEmailMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketEmailMessage::class);
    }

//    /**
//     * @return TicketEmailMessage[] Returns an array of TicketEmailMessage objects
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

//    public function findOneBySomeField($value): ?TicketEmailMessage
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
