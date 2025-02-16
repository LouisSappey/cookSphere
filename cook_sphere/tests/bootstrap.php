<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.test');
}

// Create test database and schema
passthru(sprintf(
    'php "%s/../bin/console" doctrine:database:drop --force --env=test 2>&1',
    __DIR__
));

passthru(sprintf(
    'php "%s/../bin/console" doctrine:database:create --env=test 2>&1',
    __DIR__
));

passthru(sprintf(
    'php "%s/../bin/console" doctrine:schema:create --env=test 2>&1',
    __DIR__
));
