<?php

namespace App\Repository;

use App\Entity\Operation;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    /**
     * OperationRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    /**
     * Return All Operation by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getOperationByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.utilisateur = :user');
        $qb->orderBy('t.dateBuy', 'DESC');
        $qb->setParameter('user', $user);
        $qb->orderBy('t.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Operation by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getOperationByUserForGraph(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.utilisateur = :user');
        $qb->orderBy('t.dateBuy', 'DESC');
        $qb->setParameter('user', $user);
        $qb->orderBy('t.dateBuy', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All price by Operation
     * @param Utilisateur $user
     * @param $idCrypto
     * @return array|mixed
     */
    public function getPriceByOperation(Utilisateur $user, $idCrypto)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as amountAll, t');
        $qb->where('t.utilisateur = :user');
        $qb->andWhere('t.crypto = :crypto');
        $qb->setParameter('user', $user);
        $qb->setParameter('crypto', $idCrypto);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Operation Current
     * @return array|mixed
     */
    public function getAllOperationOnline()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.state != 1');
        $qb->orderBy('t.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Operation Current
     * @return array|mixed
     */
    public function getAllOperationSuccess()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.state != 0');
        $qb->orderBy('t.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Operation Current by user
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getAllOperationSuccessByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.cost) as total');
        $qb->where('t.utilisateur = :user');
        $qb->andWhere('t.state != :state');
        $qb->setParameter('user', $user);
        $qb->setParameter('state', 0);
        return $qb->getQuery()->getResult();
    }
}
