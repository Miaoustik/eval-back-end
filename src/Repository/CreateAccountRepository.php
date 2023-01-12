<?php

namespace App\Repository;

use App\Entity\CreateAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CreateAccount>
 *
 * @method CreateAccount|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreateAccount|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreateAccount[]    findAll()
 * @method CreateAccount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreateAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreateAccount::class);
    }

    public function save(CreateAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select($this->createQueryBuilder('c')->expr()->count('c'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'u')
            ->join('c.user', 'u')
            ->getQuery();
    }



    public function remove(CreateAccount $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



//    /**
//     * @return CreateAccount[] Returns an array of CreateAccount objects
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

//    public function findOneBySomeField($value): ?CreateAccount
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
