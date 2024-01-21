<?php

namespace Jekamars\BlogPhp\Route;

use Jekamars\BlogPhp\PostMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class BlogPage
{
    private PostMapper $postMapper;
    private Environment $view;

    public function __construct(PostMapper $postMapper, Environment $view)
    {
        $this->postMapper = $postMapper;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $limit = 2;

        $page = !empty($args['page']) ? $args['page'] : 2;

        $posts = $this->postMapper->getPosts('DESC', $page, $limit);

        $totalCount = $this->postMapper->getTotalCount();

        $body = $this->view->render('blog.twig', [
            'posts' => $posts,
            'pagination' => [
                'current' => $page,
                'paging' => ceil($totalCount / $limit),
            ],
        ]);
        $response->getBody()->write($body);
        return $response;
    }
}