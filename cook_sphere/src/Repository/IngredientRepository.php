<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    /**
     * Find ingredients by partial name match
     */
    public function findByPartialName(string $query): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find ingredients by allergen
     */
    public function findByAllergen(string $allergen): array
    {
        return $this->createQueryBuilder('i')
            ->where('JSON_CONTAINS(i.allergens, :allergen) = 1')
            ->setParameter('allergen', '"' . $allergen . '"')
            ->getQuery()
            ->getResult();
    }
} 