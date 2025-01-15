<?php

namespace App\Repository;

use App\Entity\RecipeIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeIngredient::class);
    }

    /**
     * Find all ingredients for a specific recipe
     */
    public function findByRecipeWithDetails(int $recipeId): array
    {
        return $this->createQueryBuilder('ri')
            ->select('ri', 'i')
            ->join('ri.ingredient', 'i')
            ->where('ri.recipe = :recipeId')
            ->setParameter('recipeId', $recipeId)
            ->orderBy('ri.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recipes containing a specific ingredient
     */
    public function findRecipesByIngredient(int $ingredientId): array
    {
        return $this->createQueryBuilder('ri')
            ->select('r')
            ->join('ri.recipe', 'r')
            ->where('ri.ingredient = :ingredientId')
            ->setParameter('ingredientId', $ingredientId)
            ->getQuery()
            ->getResult();
    }
} 