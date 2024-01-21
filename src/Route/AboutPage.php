<?php

namespace Jekamars\BlogPhp\Route;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Twig\Environment;

class AboutPage
{
    private Environment $view;

    public function __construct(Environment $view)
    {
        $this->view = $view;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $this->view->render('about.twig', [
            'name' => 'About',
        ]);
        $response->getBody()->write($body);
        return $response;
    }
}