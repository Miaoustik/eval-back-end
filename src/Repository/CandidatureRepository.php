<?php

namespace App\Repository;

use App\Entity\Candidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Candidature>
 *
 * @method Candidature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Candidature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Candidature[]    findAll()
 * @method Candidature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidature::class);
    }

    public function save(Candidature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Candidature $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findEager($id): ?Candidature
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'pc', 'a', 'r', 'pr')
            ->join('c.profilCandidat', 'pc')
            ->join('c.annonce', 'a')
            ->join('a.profilRecruteur', 'pr')
            ->join('pr.user', 'r')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findForDelete($id): ?Candidature
    {
        return $this->createQueryBuilder('c')
            ->select('c', "a", "p")
            ->join('c.annonce', 'a')
            ->join('c.profilCandidat', 'p')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function notAllowedQuery()
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'p', 'a', 'u')
            ->join('c.profilCandidat', 'p')
            ->join('c.annonce', 'a')
            ->join('p.user', 'u')
            ->where('c.allowed = false')
            ->getQuery();
    }

    public function countByAnnonce(mixed $annonce)
    {
        return $this->createQueryBuilder('c')
            ->select($this->createQueryBuilder('c')->expr()->count('c'))
            ->where('c.annonce = :a')
            ->setParameter('a', $annonce)
            ->getQuery()
            ->getScalarResult();
    }

    public function countByCandidatAnnonce($candidat, $annonce)
    {
        return $this->createQueryBuilder('c')
            ->select($this->createQueryBuilder('c')->expr()->count('c'))
            ->join('c.profilCandidat', 'p')
            ->join('p.user', 'u', Join::WITH, 'u = :u')
            ->join('c.annonce', 'a', Join::WITH, 'a = :a')
            ->setParameters([
                'u' => $candidat,
                'a' => $annonce,
            ])
            ->getQuery()
            ->getScalarResult();
    }

    /*public function findEagerByAnnonce(?\App\Entity\Annonce $annonce): array
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'p')
            ->join('c.profilCandidat', 'p')
            ->join('c.annonce', 'a', Join::WITH, 'a = :a')
            ->setParameter('a', $annonce)
            ->getQuery()
            ->getResult();
    }*/

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select($this->createQueryBuilder('c')->expr()->count('c'))
            ->where('c.allowed = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllowedEagerByAnnonce(?\App\Entity\Annonce $annonce)
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'p')
            ->join('c.profilCandidat', 'p')
            ->join('c.annonce', 'a', Join::WITH, 'a = :a')
            ->setParameter('a', $annonce)
            ->where('c.allowed = true')
            ->getQuery()
            ->getResult();
    }
}
