<?php

namespace App\Controller;

use App\Entity\RecipeStep;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RecipeStepType;

class RecipeStepController extends AbstractController
{
    #[Route('/recipe/{id}/steps', name: 'recipe_steps')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $recipeSteps = $entityManager->getRepository(RecipeStep::class)->findBy(['recipe' => $id]);
        $recipe = $entityManager->getRepository(Recipe::class)->find($id); // Get the recipe

        return $this->render('recipe/steps.html.twig', [
            'steps' => $recipeSteps,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/recipe/step/{id}/edit', name: 'recipe_step_edit')]
    public function edit(Request $request, RecipeStep $step, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('recipe_steps', ['id' => $step->getRecipe()->getId()]);
        }

        return $this->render('recipe_step/edit.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }

    #[Route('/recipe/step/{id}/delete', name: 'recipe_step_delete')]
    public function delete(RecipeStep $step, EntityManagerInterface $entityManager): Response
    {
        $recipeId = $step->getRecipe()->getId(); // Get the recipe ID before deletion
        $entityManager->remove($step);
        $entityManager->flush();
    
        return $this->redirectToRoute('recipe_steps', ['id' => $recipeId]);
    }

    #[Route('/recipe/{id}/step/new', name: 'recipe_step_new')]
    public function new(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $step = new RecipeStep();
        $form = $this->createForm(RecipeStepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $step->setRecipe($recipe); // Associate the step with the recipe
            $entityManager->persist($step);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_steps', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe_step/new.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

} 