<?php

namespace App\Repository;

use App\Entity\Soldecrypto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Soldecrypto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Soldecrypto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Soldecrypto[]    findAll()
 * @method Soldecrypto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoldecryptoRepository extends ServiceEntityRepository
{
    /**
     * SoldecryptoRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Soldecrypto::class);
    }
}
