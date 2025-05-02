<?php

namespace App\Repository;

use App\Entity\Functionality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Functionality>
 *
 * @method Functionality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Functionality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Functionality[]    findAll()
 * @method Functionality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FunctionalityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Functionality::class);
    }

    public function getFuncPaginator(int $offset, int $limit, array $params = []): Paginator
    {
        $sql = $this->createQueryBuilder('f')
            ->innerJoin('f.menus', 'm')
            ->innerJoin('m.profile', 'p');

        // if (isset($params['profile']) && $params['profile'] !== null) {
        //     $sql->andWhere('p.id = :profile')
        //         ->setParameter('profile', $params['profile']);
        // }

        $query = $sql->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('f.id', 'ASC')
            ->getQuery();

        return new Paginator($query);
    }

    //    /**
    //     * @return Functionality[] Returns an array of Functionality objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Functionality
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
