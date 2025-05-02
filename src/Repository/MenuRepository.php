<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 *
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function getMenuPaginator(int $offset, int $limit, ?int $profile): Paginator
    {
        $sql = $this->createQueryBuilder('m');

        $query = $sql->setFirstResult($offset)
            ->innerJoin('m.profile', 'p');
        if ($profile) {
            $sql->andWhere('p.id = :profile')
                ->setParameter('profile', $profile);
        }
        $sql->setMaxResults($limit)
            ->orderBy('m.orden', 'ASC')
            ->getQuery();

        return new Paginator($query);
    }

    public function findRootByProfile(Profile $profile): array
    {
        return $this->createQueryBuilder('m')
            // Une a la propiedad profile (no profile)
            ->innerJoin('m.profile', 'p')
            ->andWhere('p = :profile')
            ->andWhere('m.IdMenuParent IS NULL')
            ->setParameter('profile', $profile)
            ->orderBy('m.orden', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Menu[]
     */
    public function findAllByProfile(Profile $profile): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.profile', 'p')
            ->andWhere('p = :profile')
            ->setParameter('profile', $profile)
            ->orderBy('m.orden', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Menu[] Returns an array of Menu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Menu
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
