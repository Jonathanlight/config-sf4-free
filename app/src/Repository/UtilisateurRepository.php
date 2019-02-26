<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    /**
     * UtilisateurRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('a')
            ->where('a.username = :username')
            ->orWhere('a.username = :username2')
            ->setParameter('username', $username)
            ->setParameter('username2', $username)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param UserInterface $user
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }


    /**
     * @return array|mixed
     */
    public function getAllUserByRole()
    {
        return $this->createQueryBuilder('a')
            ->where('a.roles LIKE :role')
            ->setParameter('role', '%"ROLE_USER"%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSumAllUser()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a) as sumUser')
            ->where('a.roles LIKE :role')
            ->setParameter('role', '%"ROLE_USER"%')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
