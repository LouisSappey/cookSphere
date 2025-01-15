<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findBySearchCriteria(?string $search = null, ?int $categoryId = null, ?array $tagIds = []): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->leftJoin('r.tags', 't');

        if ($search) {
            $qb->andWhere('r.title LIKE :search OR r.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($categoryId) {
            $qb->andWhere('c.id = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }

        if (!empty($tagIds)) {
            $qb->andWhere('t.id IN (:tagIds)')
               ->setParameter('tagIds', $tagIds);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find recipes by category with pagination
     */
    public function findByCategory(int $categoryId, int $page = 1, int $limit = 10): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
} 