<?php

namespace App\Repository;

use App\Entity\Retrait;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Retrait|null find($id, $lockMode = null, $lockVersion = null)
 * @method Retrait|null findOneBy(array $criteria, array $orderBy = null)
 * @method Retrait[]    findAll()
 * @method Retrait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetraitRepository extends ServiceEntityRepository
{
    /**
     * RetraitRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Retrait::class);
    }

    /**
     * @param Utilisateur $user
     * @return mixed
     */
    public function getTransfertByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.utilisateur = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('t.created', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
