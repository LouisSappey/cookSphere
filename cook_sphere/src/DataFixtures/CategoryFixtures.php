<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Main Courses' => 'Hearty dishes that form the centerpiece of a meal',
            'Desserts' => 'Sweet treats to end your meal on a high note',
            'Salads' => 'Fresh and healthy combinations of vegetables, fruits, and more',
            'Appetizers' => 'Small dishes to start your meal or serve as snacks',
            'Soups' => 'Warming bowls of comfort food for any season',
            'Breakfast' => 'Start your day with these morning favorites'
        ];

        foreach ($categories as $name => $description) {
            // Check if category already exists
            $existingCategory = $manager->getRepository(Category::class)->findOneBy(['name' => $name]);
            if (!$existingCategory) {
                $category = new Category();
                $category->setName($name);
                $category->setDescription($description);
                $manager->persist($category);
            }
        }

        $manager->flush();
    }
} 