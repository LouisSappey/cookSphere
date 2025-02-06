<?php

namespace App\Controller\Api;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/recipes', name: 'api_recipes_')]
class RecipeApiController extends AbstractController
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private SerializerInterface $serializer
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $recipes = $this->recipeRepository->findAll();
        return $this->json($recipes, 200, [], ['groups' => 'recipe:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Recipe $recipe): JsonResponse
    {
        return $this->json($recipe, 200, [], ['groups' => 'recipe:read']);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $search = $request->query->get('q');
        $categoryId = $request->query->get('category');
        $tagIds = $request->query->all('tags', []);

        $recipes = $this->recipeRepository->findBySearchCriteria($search, $categoryId, $tagIds);
        return $this->json($recipes, 200, [], ['groups' => 'recipe:read']);
    }
}