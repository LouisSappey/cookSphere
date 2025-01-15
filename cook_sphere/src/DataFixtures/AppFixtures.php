<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserAccountStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $users = $this->loadUsers($manager);

        $manager->flush();
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
                 ->setAccountStatus(UserAccountStatusEnum::from($data['account_status']))
                 ->setRoles($data['roles']);

            $manager->persist($user);
            $userEntities[$data['email']] = $user;
        }

        return $userEntities;
    }
}
