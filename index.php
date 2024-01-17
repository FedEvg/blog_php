<?php

use DI\ContainerBuilder;
use Jekamars\BlogPhp\PostMapper;
use Jekamars\BlogPhp\Slim\TwigMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require __DIR__ . '/vendor/autoload.php';

//$loader = new FilesystemLoader('templates');
//$view = new Environment($loader);

$builder = new ContainerBuilder();
$builder->addDefinitions('config/di.php');

$container = $builder->build();

AppFactory::setContainer($container);

$config = include 'config/database.php';

$dsn = $config['dsn'];
$username = $config['username'];
$password = $config['password'];

try {
    $connection = new PDO($dsn, $username, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo $exception->getMessage();
    die();
}

$app = AppFactory::create();

$view = $container->get(Environment::class);
$app->add(new TwigMiddleware($view));

$app->get('/', function (Request $request, Response $response) use ($view, $connection) {
    $postMapper = new PostMapper($connection);
    $posts = $postMapper->getLatestPosts(4);

    $body = $view->render('index.twig', [
        'posts' => $posts,
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/about', function (Request $request, Response $response) use ($view) {
    $body = $view->render('about.twig', [
        'name' => 'About',
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/blog[/{page}]', function (Request $request, Response $response, $args) use ($view, $connection) {
    $postMapper = new PostMapper($connection);

    $limit = 2;

    $page = !empty($args['page']) ? $args['page'] : 2;

    $posts = $postMapper->getPosts('DESC', $page, $limit);

    $totalCount = $postMapper->getTotalCount();

    $body = $view->render('blog.twig', [
        'posts' => $posts,
        'pagination' => [
            'current' => $page,
            'paging' => ceil($totalCount / $limit),
        ],
    ]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{url_key}', function (Request $request, Response $response, $args) use ($view, $connection) {
    $postMapper = new PostMapper($connection);
    $post = $postMapper->getByUrlKey((string)$args['url_key']);

    if (empty($post)) {
        $body = $view->render('404.twig');
    } else {
        $body = $view->render('post.twig', [
            'post' => $post,
        ]);
    }

    $response->getBody()->write($body);
    return $response;
});

$app->run();