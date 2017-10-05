<?php

namespace ApiGateway\Middleware;

use ApiGateway\Client;
use ApiGateway\Entity\ApiUser;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
	public function process(ServerRequestInterface $request, DelegateInterface $delegate) : ResponseInterface
	{
		try {
			$user = $this->apiClient->getUserById($request->getServerParams()['HTTP_X_API_USER_ID'] ?? null);
		} catch (\Exception $e) {
			return $this->problemDetailsResponseFactory->createResponse(
				$request,
				500,
				$e->getMessage()
			);
		}

		return $delegate->process($request->withAttribute(ApiUser::class, $user));
	}
}
