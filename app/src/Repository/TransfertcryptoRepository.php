<?php

namespace App\Repository;

use App\Entity\Transfertcrypto;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transfertcrypto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transfertcrypto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transfertcrypto[]    findAll()
 * @method Transfertcrypto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransfertcryptoRepository extends ServiceEntityRepository
{
    /**
     * TransfertcryptoRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transfertcrypto::class);
    }

    /**
     * Return All Transfertcrypto by User
     * @param Utilisateur $user
     * @return array|mixed
     */
    public function getTransfertcryptoByUser(Utilisateur $user)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.utilisateur = :user');
        $qb->setParameter('user', $user);
        $qb->orderBy('f.created', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function collection(): array
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->andWhere('t.deletedAt IS NULL');

        return $queryBuilder->getQuery()->getResult();
    }
}
