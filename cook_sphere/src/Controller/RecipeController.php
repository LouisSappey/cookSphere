<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Tag;

class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipe_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoryRepository = $entityManager->getRepository(Category::class);
        $tagRepository = $entityManager->getRepository(Tag::class);
        $recipeRepository = $entityManager->getRepository(Recipe::class);

        // Get all categories and tags for the filter form
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();

        // Get filter parameters
        $search = $request->query->get('search');
        $categoryId = $request->query->get('category') ? (int) $request->query->get('category') : null;
        $tagIds = array_map('intval', $request->query->all('tags', []));

        // Get filtered recipes
        $recipes = $recipeRepository->findBySearchCriteria($search, $categoryId, $tagIds);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'categories' => $categories,
            'tags' => $tags,
            'currentSearch' => $search,
            'currentCategory' => $categoryId,
            'currentTags' => $tagIds,
        ]);
    }

    #[Route('/recipe/new', name: 'recipe_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user) {
                $recipe->setAuthor($user);
            } else {
                // Handle the case when no user is logged in
                $this->addFlash('error', 'You must be logged in to create a recipe.');
                return $this->redirectToRoute('app_login'); // Redirect to login page
            }
    
            // Persist the recipe and its steps
            $entityManager->persist($recipe);
            foreach ($recipe->getSteps() as $step) {
                $step->setRecipe($recipe); // Set the recipe for each step
                $entityManager->persist($step);
            }
            $entityManager->flush();
    
            return $this->redirectToRoute('recipe_index');
        }
    
        return $this->render('recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recipe/{id}/edit', name: 'recipe_edit')]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipe/{id}/delete', name: 'recipe_delete')]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        return $this->redirectToRoute('recipe_index');
    }

    // Add more methods for editing, deleting, etc.
} 