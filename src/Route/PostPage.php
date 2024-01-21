<?php

namespace Jekamars\BlogPhp\Route;

use Jekamars\BlogPhp\PostMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment;

class PostPage
{
    private PostMapper $postMapper;
    private Environment $view;

    public function __construct(PostMapper $postMapper, Environment $view)
    {
        $this->postMapper = $postMapper;
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $responseку): Response
    {
        $post = $this->postMapper->getByUrlKey((string) $args['url_key']);

        if (empty($post)) {
            $body = $this->view->render('404.twig');
        } else {
            $body = $this->view->render('post.twig', [
                'post' => $post,
            ]);
        }

        $response->getBody()->write($body);
        return $response;
    }
}