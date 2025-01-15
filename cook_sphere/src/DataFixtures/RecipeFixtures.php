<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\BaseIngredient;
use App\Entity\User;
use App\Entity\RecipeStep;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $recipes = [
            [
                'title' => 'Classic Spaghetti Carbonara',
                'description' => 'Traditional Italian pasta dish with eggs, cheese, pancetta and black pepper',
                'category' => 'Main Courses',
                'tags' => ['Italian', 'Pasta'],
                'difficulty' => 'Medium',
                'preparationTime' => 15,
                'cookingTime' => 20,
                'servings' => 4,
                'ingredients' => [
                    ['Spaghetti', 400, 'grams'],
                    ['Eggs', 4, 'units'],
                    ['Pecorino Romano', 100, 'grams'],
                    ['Pancetta', 150, 'grams'],
                    ['Black Pepper', 2, 'teaspoons']
                ],
                'steps' => [
                    'Cook spaghetti in salted water according to package instructions',
                    'Dice pancetta and fry until crispy',
                    'Beat eggs with grated cheese and pepper',
                    'Mix hot pasta with egg mixture and pancetta',
                    'Serve immediately with extra cheese and pepper'
                ]
            ],
            [
                'title' => 'Chocolate Chip Cookies',
                'description' => 'Soft and chewy cookies loaded with chocolate chips',
                'category' => 'Desserts',
                'tags' => ['Baking', 'Sweet'],
                'difficulty' => 'Easy',
                'preparationTime' => 20,
                'cookingTime' => 12,
                'servings' => 24,
                'ingredients' => [
                    ['Flour', 300, 'grams'],
                    ['Butter', 200, 'grams'],
                    ['Brown Sugar', 200, 'grams'],
                    ['Chocolate Chips', 200, 'grams'],
                    ['Eggs', 2, 'units'],
                    ['Vanilla Extract', 1, 'teaspoon']
                ],
                'steps' => [
                    'Cream butter and sugars until light and fluffy',
                    'Add eggs and vanilla, mix well',
                    'Gradually add flour mixture',
                    'Fold in chocolate chips',
                    'Drop spoonfuls onto baking sheets',
                    'Bake at 180°C for 12 minutes'
                ]
            ],
            [
                'title' => 'Fresh Summer Salad',
                'description' => 'Light and refreshing salad with seasonal vegetables',
                'category' => 'Salads',
                'tags' => ['Healthy', 'Vegetarian'],
                'difficulty' => 'Easy',
                'preparationTime' => 15,
                'cookingTime' => 0,
                'servings' => 4,
                'ingredients' => [
                    ['Mixed Greens', 200, 'grams'],
                    ['Cherry Tomatoes', 200, 'grams'],
                    ['Cucumber', 1, 'unit'],
                    ['Avocado', 2, 'units'],
                    ['Olive Oil', 3, 'tablespoons']
                ],
                'steps' => [
                    'Wash and dry all vegetables',
                    'Slice cucumber and tomatoes',
                    'Cut avocados into chunks',
                    'Combine all ingredients in a bowl',
                    'Drizzle with olive oil and season'
                ]
            ]
        ];

        $user = $manager->getRepository(User::class)->findOneBy(['email' => 'user1@example.com']);

        foreach ($recipes as $recipeData) {
            $recipe = new Recipe();
            $recipe->setTitle($recipeData['title']);
            $recipe->setDescription($recipeData['description']);
            $recipe->setPreparationTime($recipeData['preparationTime']);
            $recipe->setCookingTime($recipeData['cookingTime']);
            $recipe->setServings($recipeData['servings']);
            $recipe->setDifficulty($recipeData['difficulty']);
            $recipe->setAuthor($user);

            // Set category
            $category = $manager->getRepository(Category::class)->findOneBy(['name' => $recipeData['category']]);
            $recipe->setCategory($category);

            // Add tags
            foreach ($recipeData['tags'] as $tagName) {
                $tag = $manager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
                if ($tag) {
                    $recipe->addTag($tag);
                }
            }

            // Add ingredients
            foreach ($recipeData['ingredients'] as $ingredientData) {
                $ingredient = $manager->getRepository(BaseIngredient::class)->findOneBy(['name' => $ingredientData[0]]);
                if ($ingredient) {
                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setQuantity($ingredientData[1]);
                    $recipeIngredient->setUnit($ingredientData[2]);
                    $manager->persist($recipeIngredient);
                }
            }

            // Add steps
            foreach ($recipeData['steps'] as $index => $stepDescription) {
                $step = new RecipeStep();
                $step->setRecipe($recipe);
                $step->setDescription($stepDescription);
                $step->setOrderNumber($index + 1);
                $manager->persist($step);
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
            IngredientFixtures::class,
        ];
    }
} 