<?php

namespace App\Controller\Api;

use App\Service\ExternalRecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ExternalRecipeController extends AbstractController
{
    #[Route('/random-recipe', name: 'random_recipe', methods: ['GET'])]
    public function getRandomRecipe(ExternalRecipeService $externalRecipeService): JsonResponse
    {
        try {
            $recipe = $externalRecipeService->getRandomRecipe();
            return $this->json($recipe);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch recipe'], 500);
        }
    }
} 