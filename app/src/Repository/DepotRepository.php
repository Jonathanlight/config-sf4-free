<?php

namespace App\Repository;

use App\Entity\Depot;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[]    findAll()
 * @method Depot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    /**
     * DepotRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    /**
     * Return All Depot by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getDepotEnCoursByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('depot');
        $qb->where('depot.utilisateur = :user');
        $qb->andWhere('depot.disable = :disable');
        $qb->andWhere('depot.state = :state');
        $qb->setParameter('user', $user);
        $qb->setParameter('disable', 0);
        $qb->setParameter('state', 0);
        $qb->orderBy('depot.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Depot by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getDepotValideByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('depot');
        $qb->where('depot.utilisateur = :user');
        $qb->andWhere('depot.disable = :disable');
        $qb->andWhere('depot.state = :state');
        $qb->setParameter('user', $user);
        $qb->setParameter('disable', 0);
        $qb->setParameter('state', 1);
        $qb->orderBy('depot.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Depot by User en cours
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getDepotByUserLoad(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('depot');
        $qb->where('depot.utilisateur = :user');
        $qb->andWhere('depot.state = :state');
        $qb->andWhere('depot.disable = :disable');
        $qb->setParameter('user', $user);
        $qb->setParameter('state', 0);
        $qb->setParameter('disable', 0);
        $qb->orderBy('depot.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $user
     * @return mixed
     */
    public function getDepotByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('depot');
        $qb->where('depot.utilisateur = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('depot.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Depot by Carte Bancaire
     * @return array|mixed
     */
    public function getDepotByCarteBancaire()
    {
        $qb = $this->createQueryBuilder('depot');
        $qb->where('depot.source = :source');
        $qb->andWhere('depot.disable = :disable');
        $qb->setParameter('source', 'lydia');
        $qb->setParameter('disable', 0);
        $qb->orderBy('depot.created', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
