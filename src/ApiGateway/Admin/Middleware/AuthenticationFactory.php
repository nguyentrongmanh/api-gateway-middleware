<?php

namespace ApiGateway\Admin\Middleware;

use ApiGateway\Admin\Client;
use Psr\Container\ContainerInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationMiddleware(
            $container->get(Client::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
