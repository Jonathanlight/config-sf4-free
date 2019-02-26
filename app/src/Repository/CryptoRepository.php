<?php

namespace App\Repository;

use App\Entity\Crypto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Crypto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Crypto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Crypto[]    findAll()
 * @method Crypto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryptoRepository extends ServiceEntityRepository
{
    /**
     * CryptoRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Crypto::class);
    }
}
