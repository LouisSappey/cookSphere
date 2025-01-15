<?php

namespace App\DataFixtures;

use App\Entity\RecipeStep;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RecipeStepFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Assuming you have already created some recipes
        $recipe = $manager->getRepository(Recipe::class)->findOneBy(['title' => 'Grilled Chicken Salad']);

        if ($recipe) {
            $steps = [
                'Prepare the ingredients.',
                'Season the chicken breast with salt and pepper.',
                'Heat olive oil in a pan over medium heat.',
                'Cook the chicken for 6-7 minutes on each side until golden brown.',
                'Slice the chicken and serve it over a bed of mixed greens.',
            ];

            foreach ($steps as $index => $stepDescription) {
                $step = new RecipeStep();
                $step->setRecipe($recipe);
                $step->setDescription($stepDescription);
                $step->setOrderNumber($index + 1);
                $manager->persist($step);
            }

            $manager->flush();
        }
    }
} 