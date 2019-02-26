<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use App\Entity\Vendre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Vendre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vendre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vendre[]    findAll()
 * @method Vendre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendreRepository extends ServiceEntityRepository
{
    /**
     * VendreRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Vendre::class);
    }

    /**
     * Return All Vente Current
     * @return array|mixed
     */
    public function getAllVenteOnline()
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.state != 1');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Vente by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getVenteByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.utilisateur = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('t.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Vente
     * @return array|mixed
     */
    public function getVentes()
    {
        $qb = $this->createQueryBuilder('v');
        $qb->orderBy('v.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBySumAllVirement(Utilisateur $utilisateur)
    {
        return $this->createQueryBuilder('v')
            ->select('SUM(v.sendVirement) as sendVirement')
            ->andWhere('v.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
