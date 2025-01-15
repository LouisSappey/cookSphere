<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Assuming you have already created some recipes and users
        $recipe = $manager->getRepository(Recipe::class)->findOneBy(['title' => 'Grilled Chicken Salad']);
        $user = $manager->getRepository(User::class)->findOneBy(['email' => 'user1@example.com']);

        if ($recipe && $user) {
            $comments = [
                'This recipe is fantastic! I loved it.',
                'Easy to make and very delicious.',
                'I added some extra spices, and it turned out great!',
                'My family enjoyed this meal very much.',
            ];

            foreach ($comments as $commentContent) {
                $comment = new Comment();
                $comment->setRecipe($recipe);
                $comment->setUser($user);
                $comment->setContent($commentContent);
                $manager->persist($comment);
            }

            $manager->flush();
        }
    }
} 