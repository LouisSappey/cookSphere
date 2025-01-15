<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = ['Vegetarian', 'Vegan', 'Gluten-Free', 'Dairy-Free', 'Quick Meals', 'Desserts', 'Healthy', 'Spicy', 'Comfort Food', 'Low Carb'];

        foreach ($tags as $tagName) {
            // Check if the tag already exists
            $existingTag = $manager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
            if (!$existingTag) {
                $tag = new Tag();
                $tag->setName($tagName);
                $manager->persist($tag);
            }
        }

        $manager->flush();
    }
} 