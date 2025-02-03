<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\BaseTestCase;
use App\Enum\UserAccountStatusEnum;

class SecurityControllerTest extends BaseTestCase
{
    private function createTestUser(): User
    {
        $user = new User();
        $user->setEmail('test@example.com')
            ->setUsername('testuser')
            ->setPassword('$2y$13$hK85NTGiV.qxW1XNsGXJQ.IA.eQWF8hovqx3UVWjnV3VJ/QxQQzXy')
            ->setRoles(['ROLE_USER'])
            ->setAccountStatus(UserAccountStatusEnum::VALID);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testLoginSuccess(): void
    {
        $this->createTestUser();
        $crawler = $this->client->request('GET', '/login-symfony');
        
        $this->client->submitForm('Sign in', [
            '_username' => 'testuser',
            '_password' => 'password123',
        ]);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginFailure(): void
    {
        $crawler = $this->client->request('GET', '/login-symfony');
        
        $this->client->submitForm('Sign in', [
            '_username' => 'wronguser',
            '_password' => 'wrongpassword',
        ]);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
} 