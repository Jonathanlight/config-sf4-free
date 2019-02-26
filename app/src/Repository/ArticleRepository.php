<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    /**
     * ArticleRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Return All Artcile Active
     * @return array|mixed
     */
    public function findAllArticleActive()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->andWhere('a.etat = :etat');
        $qb->setParameter('etat', 1);
        $qb->orderBy('a.created', 'DESC');
        return $qb->getQuery()->getResult();
    }

    /**
     * Return Last entity
     * @return mixed
     */
    public function findLast()
    {
        $qb = $this->createQueryBuilder('entity');
        $qb->where('entity.etat = 1');
        $qb->setMaxResults(1);
        $qb->orderBy('entity.id', 'DESC');
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Return First entity
     * @return mixed
     */
    public function findFirst()
    {
        $qb = $this->createQueryBuilder('entity');
        $qb->where('entity.etat = 1');
        $qb->setMaxResults(1);
        $qb->orderBy('entity.id', 'ASC');

        return $qb->getQuery()->getOneOrNullResult();
    }
}
