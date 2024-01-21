<?php

use Jekamars\BlogPhp\Database;
use Jekamars\BlogPhp\Slim\TwigMiddleware;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\autowire;
use function DI\get;

return [
    FilesystemLoader::class => autowire()
        ->constructorParameter('paths', 'templates'),

    Environment::class => autowire()
        ->constructorParameter('loader', get(FilesystemLoader::class)),

    Database::class => autowire()
        ->constructorParameter('connection', get(PDO::class)),

    TwigMiddleware::class => autowire()
        ->constructorParameter('environment', get(Environment::class)),

    PDO::class => autowire()
        ->constructorParameter('dsn', getenv('DATABASE_DNS'))
        ->constructorParameter('username', getenv('DATABASE_USERNAME'))
        ->constructorParameter('password', getenv('DATABASE_PASSWORD'))
        ->constructorParameter('options', []),
];