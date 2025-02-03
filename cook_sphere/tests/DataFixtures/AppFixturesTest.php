<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Tag;
use App\Tests\BaseTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AppFixturesTest extends BaseTestCase
{
    private ORMExecutor $executor;
    private Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loader = new Loader();
        $this->executor = new ORMExecutor($this->entityManager, new ORMPurger());
    }

    public function testLoadFixtures(): void
    {
        // Load fixtures
        $this->loader->addFixture(new AppFixtures($this->container->get('security.user_password_hasher')));
        $this->executor->execute($this->loader->getFixtures());

        $userCount = $this->entityManager->getRepository(User::class)->count([]);
        $recipeCount = $this->entityManager->getRepository(Recipe::class)->count([]);
        $categoryCount = $this->entityManager->getRepository(Category::class)->count([]);
        $tagCount = $this->entityManager->getRepository(Tag::class)->count([]);

        $this->assertGreaterThan(0, $userCount, 'No users were created');
        $this->assertGreaterThan(0, $recipeCount, 'No recipes were created');
        $this->assertGreaterThan(0, $categoryCount, 'No categories were created');
        $this->assertGreaterThan(0, $tagCount, 'No tags were created');
    }
} 