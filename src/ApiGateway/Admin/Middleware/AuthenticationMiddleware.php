<?php

namespace ApiGateway\Admin\Middleware;

use ApiGateway\Admin\Client;
use ApiGateway\Admin\Entity\ApiUser;
use Psr\Http\Server\RequestHandlerInterface as DelegateInterface;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouteResult;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class AuthenticationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var Client
     */
    protected $apiClient;

    /**
     * @var ProblemDetailsResponseFactory
     */
    protected $problemDetailsResponseFactory;

    /**
     * AuthenticationMiddleware constructor.
     *
     * @param Client $apiClient
     * @param ProblemDetailsResponseFactory $problemDetailsResponseFactory
     */
    public function __construct(Client $apiClient, ProblemDetailsResponseFactory $problemDetailsResponseFactory)
    {
        $this->apiClient = $apiClient;
        $this->problemDetailsResponseFactory = $problemDetailsResponseFactory;
    }

    /**
     * this middleware validates the http header X_API_USER_ID (sent by api gateway) in order to get
     * detailed user information (userId and agentId)
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if (!$route = $request->getAttribute(RouteResult::class, false)) {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                400,
                'BAD REQUEST'
            );
        }

        if (!isset($request->getServerParams()['HTTP_X_API_USER_ID'])) {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                500,
                'MISSING X-API-USER-ID'
            );
        }

        try {
            $user = $this->apiClient->getUserById(
                $request->getServerParams()['HTTP_X_API_USER_ID'] ?? null,
                $route->getMatchedParams()['agentId'] ?? null
            );
        } catch (\Exception $e) {
            return $this->problemDetailsResponseFactory->createResponse(
                $request,
                500,
                $e->getMessage()
            );
        }

        return $handler->handle($request->withAttribute(ApiUser::class, $user));
    }
}
