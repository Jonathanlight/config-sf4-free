<?php

namespace App\Repository;

use App\Entity\Parainage;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Parainage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parainage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parainage[]    findAll()
 * @method Parainage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParainageRepository extends ServiceEntityRepository
{
    /**
     * ParainageRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Parainage::class);
    }

    /**
     * Return lien de parainnage
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getCheckParainUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.utilisateurParrain = :user');
        $qb->andWhere('p.etat = :etat');
        $qb->setParameter('user', $user);
        $qb->setParameter('etat', 0);
        $qb->orderBy('p.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $userP
     * @param Utilisateur $userF
     * @return array|mixed
     */
    public function getCheckParainUserFilleul(Utilisateur $userP, Utilisateur $userF)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.utilisateurParrain = :parrain');
        $qb->andWhere('p.utilisateurFilleul = :filleul');
        $qb->andWhere('p.etat = :etat');
        $qb->setParameter('parrain', $userP);
        $qb->setParameter('filleul', $userF);
        $qb->setParameter('etat', 0);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return lien de parainnage pour gestion des historiques
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getStorieParainUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.utilisateurParrain = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('p.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Return le parain lié a un filleul
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getStorieFilleulUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.utilisateurFilleul = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('p.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Solde Parrainage Current by user
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getAllSoldeParrainageByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.solde) as total');
        $qb->where('t.utilisateurParrain = :user');
        $qb->andWhere('t.etat = :etat');
        $qb->setParameter('user', $user);
        $qb->setParameter('etat', 1);

        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Filleul By Current by user
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getAllFilleulByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('COUNT(t.id) as filleuls');
        $qb->where('t.utilisateurParrain = :user');
        $qb->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}
