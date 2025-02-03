<?php

namespace App\Tests\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Entity\Category;
use App\Tests\BaseTestCase;
use App\Enum\UserAccountStatusEnum;

class RecipeControllerTest extends BaseTestCase
{
    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = new User();
        $this->user->setEmail('recipe@test.com')
            ->setUsername('recipeuser')
            ->setPassword('$2y$13$hK85NTGiV.qxW1XNsGXJQ.IA.eQWF8hovqx3UVWjnV3VJ/QxQQzXy')
            ->setRoles(['ROLE_USER'])
            ->setAccountStatus(UserAccountStatusEnum::VALID);
        
        // Create test category
        $this->category = new Category();
        $this->category->setName('Test Category')
            ->setDescription('Test Category Description');

        $this->entityManager->persist($this->user);
        $this->entityManager->persist($this->category);
        $this->entityManager->flush();
    }

    public function testRecipeIndex(): void
    {
        $this->client->request('GET', '/recipes');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateRecipe(): void
    {
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/recipe/new');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Create Recipe', [
            'recipe[title]' => 'Test Recipe',
            'recipe[description]' => 'Test Description',
            'recipe[preparationTime]' => 30,
            'recipe[cookingTime]' => 45,
            'recipe[servings]' => 4,
            'recipe[difficulty]' => 'Medium',
            'recipe[category]' => $this->category->getId(),
        ]);

        $this->assertResponseRedirects('/recipes');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'Test Recipe');
    }
}