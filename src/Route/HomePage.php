<?php

namespace Jekamars\BlogPhp\Route;

use Jekamars\BlogPhp\Database;
use Jekamars\BlogPhp\PostMapper;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment;

class HomePage
{
    private PostMapper $postMapper;
    private Environment $view;

    public function __construct(PostMapper $postMapper, Environment $view)
    {
        $this->postMapper = $postMapper;
        $this->view = $view;
    }

    public function execute(Request $request, Response $response): Response
    {
        $posts = $this->postMapper->getLatestPosts(4);

        $body = $this->view->render('index.twig', [
            'posts' => $posts,
        ]);
        $response->getBody()->write($body);
        return $response;

    }
}