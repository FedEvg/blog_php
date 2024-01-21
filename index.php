<?php

use DevCoder\DotEnv;
use DI\ContainerBuilder;
use Jekamars\BlogPhp\Database;
use Jekamars\BlogPhp\Route\AboutPage;
use Jekamars\BlogPhp\Route\BlogPage;
use Jekamars\BlogPhp\Route\HomePage;
use Jekamars\BlogPhp\Route\PostPage;
use Jekamars\BlogPhp\Slim\TwigMiddleware;
use Slim\Factory\AppFactory;
use Twig\Environment;

require __DIR__ . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

$container = $builder->build();

AppFactory::setContainer($container);

$absolutePathToEnvFile = __DIR__ . '/.env';

(new DotEnv($absolutePathToEnvFile))->load();

$app = AppFactory::create();

$app->add($container->get(TwigMiddleware::class));

$app->get('/', HomePage::class . ':execute');
$app->get('/about', AboutPage::class);
$app->get('/blog[/{page}]', BlogPage::class);
$app->get('/{url_key}', PostPage::class);

$app->run();