<?php

namespace ApiGateway;

use ApiGateway\Cache\Storage\NullStorage;
use ApiGateway\Cache\Storage\StorageInterface;
use ApiGateway\Entity\ApiUser;
use ApiGateway\Exception\InvalidArgumentException;
use ApiGateway\Exception\RuntimeException;
use Zend\Http;

class Client
{
	/**
	 * api gateway internal api endpoint
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * @var string
	 */
	protected $adminAuthToken;

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var StorageInterface
	 */
	protected $cache;

	/**
	 * Client constructor.
	 */
	public function __construct()
	{
		$this->cache = new NullStorage([]);
	}

	/**
	 * gets user information by api gateway's internal user id.
	 * an api key is unique
	 *
	 * @param string $id
	 *
	 * @return ApiUser
	 */
	public function getUserById(string $id) : ApiUser
	{
		if ($apiUser = $this->cache->get($id)) {
			return $apiUser;
		}

		$client = $this->getClient('users/' . $id);
		$client->setMethod('GET');

		$response = $client->send();

		if (!$response = json_decode($response->getBody())) {
			throw new RuntimeException('invalid api gateway response');
		}

		if (!isset($response->user)) {
			throw new RuntimeException('invalid api gateway response. does not contain user information');
		}

		$parts = explode('|', $response->user->use_description);

		if (count($parts) < 2) {
			throw new RuntimeException('unable to get user information from api gateway response');
		}

		$user = new ApiUser();
		$user->setId($id);
		$user->setUserId($parts[0]);
		$user->setAgentId($parts[1]);

		$this->cache->set($user);

		return $user;
	}

	/**
	 * returns prepared http client
	 *
	 * @return Http\Client
	 */
	protected function getClient($resource) : Http\Client
	{
		if (!$this->endpoint) {
			throw new InvalidArgumentException('no api gateway endpoint configured');
		}

		if (!$this->adminAuthToken) {
			throw new InvalidArgumentException('no admin auth token configured');
		}

		if (!$this->key) {
			throw new InvalidArgumentException('no api key configured');
		}

		$client = new Http\Client($this->endpoint . '/' . $resource);

		$headers = $client->getRequest()->getHeaders();
		$headers->addHeaderLine('X-Admin-Auth-Token', $this->adminAuthToken);
		$headers->addHeaderLine('X-API-KEY', $this->key);

		return $client;
	}

	/**
	 * @return string
	 */
	public function getEndpoint(): string {
		return $this->endpoint;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return Client
	 */
	public function setEndpoint(string $endpoint): Client {
		$this->endpoint = $endpoint;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAdminAuthToken(): string {
		return $this->adminAuthToken;
	}

	/**
	 * @param string $adminAuthToken
	 *
	 * @return Client
	 */
	public function setAdminAuthToken(string $adminAuthToken): Client {
		$this->adminAuthToken = $adminAuthToken;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getKey(): string {
		return $this->key;
	}

	/**
	 * @param string $key
	 *
	 * @return Client
	 */
	public function setKey(string $key): Client {
		$this->key = $key;

		return $this;
	}

	/**
	 * @return StorageInterface
	 */
	public function getCache(): StorageInterface {
		return $this->cache;
	}

	/**
	 * @param StorageInterface $cache
	 *
	 * @return Client
	 */
	public function setCache(StorageInterface $cache): Client {
		$this->cache = $cache;

		return $this;
	}
}