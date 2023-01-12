<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\ProfilRecruteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllAllowedQuery(): Query
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'p')
            ->join('a.profilRecruteur', 'p')
            ->where('a.allowed = true')
            ->getQuery();

    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select($this->createQueryBuilder('a')->expr()->count('a'))
            ->where('a.allowed = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getData($id): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setFetchMode(Annonce::class, 'user', ClassMetadataInfo::FETCH_EAGER)
            ->getOneOrNullResult();
    }

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.profilRecruteur', 'p')
            ->join('p.user', 'u', Query\Expr\Join::WITH, 'u = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function notAllowedQueryEager(): Query
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'p', 'u')
            ->join('a.profilRecruteur', 'p')
            ->join('p.user', 'u')
            ->where('a.allowed = false')
            ->getQuery();
    }

    public function notAllowedQuery(): Query
    {
        return $this->createQueryBuilder('a')
            ->where('a.allowed = false')
            ->getQuery();
    }

    public function findBySearch($data): Query
    {
        $query =  $this->allowedQuery()
            ->select('a', 'p')
            ->join('a.profilRecruteur', 'p');

        if (isset($data['city']) && trim($data['city']) !== "") {
            $query = $query->andWhere("p.city LIKE :city OR p.postalCode LIKE :code");
            $paramsCity = [
                    'city' => "%" . ($data['city'] ?? '') ."%",
                    'code' => str_replace(' ', '', $data['city'] ?? '') ."%",
                ];
        }

        if (isset($data['title']) && trim($data['title']) !== '') {
            $query = $query->andWhere("a.title LIKE :title");
            $paramsTitle = [
                'title' => "%" . ($data['title'] ?? '') ."%",
            ];
        }
        return $query->setParameters(array_merge($paramsTitle ?? [], $paramsCity ?? []))->getQuery();
    }

    private function allowedQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where('a.allowed = true');
    }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
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

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * @throws NonUniqueResultException
     */
    public function findEager($id)
    {
        return ($this->findEagerBuilder($id))
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function findEagerBuilder($id)
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'p', 'c')
            ->leftJoin('a.profilRecruteur', 'p')
            ->leftJoin('a.candidatures', 'c')
            ->where('a.id = :id')
            ->setParameter('id', $id);
    }

    public function findEagerShow($id): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'p', 'c')
            ->leftJoin('a.profilRecruteur', 'p')
            ->leftJoin('a.candidatures', 'c')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWithProfil($id): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->join('a.profilRecruteur', 'p')
            ->where('a.id = :id')
            ->setParameter("id", $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findWithCandidature($id)
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'c', 'p')
            ->leftJoin('a.candidatures', 'c')
            ->leftJoin('c.profilCandidat', 'p')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
