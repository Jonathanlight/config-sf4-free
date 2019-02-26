<?php

namespace App\Repository;

use App\Entity\Friends;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Friends|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friends|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friends[]    findAll()
 * @method Friends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendsRepository extends ServiceEntityRepository
{
    /**
     * FriendsRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Friends::class);
    }

    /**
     * Return All Friends by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getFriendByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.utilisateur = :user');
        $qb->andWhere('f.active = :active');
        $qb->setParameter('user', $user);
        $qb->setParameter('active', 1);
        $qb->orderBy('f.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return All Ask Friends by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getFriendAskByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.utilisateurFriend = :user');
        $qb->andWhere('f.active = :active');
        $qb->setParameter('user', $user);
        $qb->setParameter('active', 0);
        $qb->orderBy('f.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Verifie if UserFriend is already my User
     * @param Utilisateur $user
     * @param Utilisateur $friend
     * @return array|mixed
     */
    public function checkUserIsAlreadyMyFriend(Utilisateur $user, Utilisateur $friend)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.utilisateur = :user');
        $qb->andWhere('f.utilisateurFriend = :friend');
        $qb->setParameter('user', $user);
        $qb->setParameter('friend', $friend);
        return $qb->getQuery()->getResult();
    }
}
