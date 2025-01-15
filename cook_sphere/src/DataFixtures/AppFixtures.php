<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Enum\UserAccountStatusEnum;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    private function loadUsers(ObjectManager $manager): array
    {
        $filePath = realpath(__DIR__ . '/../../fixtures/users.yaml');
        $fixturesData = Yaml::parseFile($filePath);
        $userEntities = [];

        foreach ($fixturesData['users'] as $data) {
            $user = new User();
            $user->setUsername($data['username'])
                 ->setEmail($data['email'])
                 ->setPassword($this->passwordHasher->hashPassword($user, $data['password']))
                 ->setAccountStatus(UserAccountStatusEnum::VALID)
                 ->setRoles($data['roles']);

            $manager->persist($user);
            $userEntities[$data['email']] = $user;
        }

        return $userEntities;
    }
    public function load(ObjectManager $manager): void
    {
        $manager->flush();
        
        // Load individual fixtures
        $users = $this->loadUsers($manager);
        (new TagFixtures())->load($manager);
        (new CategoryFixtures())->load($manager);
        (new IngredientFixtures())->load($manager);
        (new RecipeFixtures())->load($manager);
    }
}
