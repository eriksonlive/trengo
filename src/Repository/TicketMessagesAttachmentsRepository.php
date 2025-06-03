<?php

namespace App\Repository;

use App\Entity\TicketMessagesAttachments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketMessagesAttachments>
 *
 * @method TicketMessagesAttachments|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketMessagesAttachments|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketMessagesAttachments[]    findAll()
 * @method TicketMessagesAttachments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketMessagesAttachmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketMessagesAttachments::class);
    }

//    /**
//     * @return TicketMessagesAttachments[] Returns an array of TicketMessagesAttachments objects
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

//    public function findOneBySomeField($value): ?TicketMessagesAttachments
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
