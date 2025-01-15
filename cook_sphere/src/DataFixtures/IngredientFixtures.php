<?php

namespace App\DataFixtures;

use App\Entity\BaseIngredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ingredients = [
            ['name' => 'Tomato', 'description' => 'A red fruit used in salads and sauces.', 'measurementUnit' => 'pieces', 'calories' => 18],
            ['name' => 'Chicken Breast', 'description' => 'Lean meat from the chicken.', 'measurementUnit' => 'grams', 'calories' => 165],
            ['name' => 'Rice', 'description' => 'A staple food in many cultures.', 'measurementUnit' => 'grams', 'calories' => 130],
            ['name' => 'Olive Oil', 'description' => 'A healthy fat used in cooking.', 'measurementUnit' => 'ml', 'calories' => 884],
        ];

        foreach ($ingredients as $ingredientData) {
            $ingredient = new BaseIngredient();
            $ingredient->setName($ingredientData['name']);
            $ingredient->setDescription($ingredientData['description']);
            $ingredient->setMeasurementUnit($ingredientData['measurementUnit']);
            $ingredient->setCalories($ingredientData['calories']);
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
} 