<?php

namespace App\Repository;

use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tickets>
 *
 * @method Tickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tickets[]    findAll()
 * @method Tickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    public function getTicketsPaginator(int $offset, int $limit, array $params = []): Paginator
    {
        $sql = $this->createQueryBuilder('t');

        if (isset($params['status']) && !empty($params['status'])) {
            $sql->andWhere('t.status = :status')
                ->setParameter('status', $params['status']);
        }

        if (isset($params['id_ticket']) && !empty($params['id_ticket'])) {
            $sql->andWhere('t.id like :id_ticket')
                ->setParameter('id_ticket', '%' . $params['id_ticket'] . '%');
        }

        if (!empty($params['date_start']) && !empty($params['date_end'])) {
            $sql->andWhere('t.createdAt BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', new \DateTime($params['date_start']))
                ->setParameter('endDate', new \DateTime($params['date_end']));
        }

        if (isset($params['search']) && !empty($params['search'])) {
            $sql->andWhere('LOWER(t.subject) LIKE :search')
                ->setParameter('search', '%' . strtolower($params['search']) . '%');
        }

        $query = $sql->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('t.id', 'DESC')
            ->getQuery();

        return new Paginator($query);
    }

    public function getStatesMap(): array
    {
        // obtenemos solo id y status
        $rows = $this->createQueryBuilder('t')
            ->select('t.id, t.status')
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['id']] = $r['status'];
        }

        return $map;
    }

    //    /**
    //     * @return Tickets[] Returns an array of Tickets objects
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

    //    public function findOneBySomeField($value): ?Tickets
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
