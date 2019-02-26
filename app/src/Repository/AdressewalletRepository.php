<?php

namespace App\Repository;

use App\Entity\Adressewallet;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Adressewallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adressewallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adressewallet[]    findAll()
 * @method Adressewallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdressewalletRepository extends ServiceEntityRepository
{
    /**
     * AdressewalletRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Adressewallet::class);
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     */
    public function getByUser(Utilisateur $utilisateur)
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->where('a.utilisateur = :utilisateur');
        $queryBuilder->setParameter('utilisateur', $utilisateur);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Utilisateur $utilisateur
     * @return mixed
     */
    public function getAdressewalletByUser(Utilisateur $utilisateur)
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->andWhere('a.utilisateur = :utilisateur');
        $queryBuilder->setParameter('utilisateur', $utilisateur);

        return $queryBuilder->getQuery()->getResult();
    }
}
