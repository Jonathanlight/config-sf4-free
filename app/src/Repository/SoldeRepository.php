<?php

namespace App\Repository;

use App\Entity\Solde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Solde|null find($id, $lockMode = null, $lockVersion = null)
 * @method Solde|null findOneBy(array $criteria, array $orderBy = null)
 * @method Solde[]    findAll()
 * @method Solde[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoldeRepository extends ServiceEntityRepository
{
    /**
     * SoldeRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Solde::class);
    }

    /**
     * Return All Sum Operation
     * @return mixed
     */
    public function getCostSumAllTransaction()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.soldeOld) as soldeAll');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return mixed
     */
    public function soldeDisponible()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.solde) as soldeDispo');

        return $qb->getQuery()->getOneOrNullResult();
    }
}
