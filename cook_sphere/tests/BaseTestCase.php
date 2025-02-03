<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class BaseTestCase extends WebTestCase
{
    protected $client;
    protected ?ContainerInterface $container = null;
    protected ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $this->truncateEntities();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
        $this->container = null;
    }

    protected function truncateEntities(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        
        $connection->executeStatement('SET CONSTRAINTS ALL DEFERRED');
        
        $tables = $connection->createSchemaManager()->listTableNames();
        
        foreach ($tables as $tableName) {
            if ($tableName === 'doctrine_migration_versions') {
                continue;
            }
            
            $connection->executeStatement($platform->getTruncateTableSQL($tableName, true));
        }
        
        $connection->executeStatement('SET CONSTRAINTS ALL IMMEDIATE');
    }
} 