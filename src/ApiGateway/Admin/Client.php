<?php

namespace ApiGateway\Admin;

use ApiGateway\Admin\Cache\Storage\NullStorage;
use ApiGateway\Admin\Cache\Storage\StorageInterface;
use ApiGateway\Admin\Entity\ApiUser;
use ApiGateway\Exception\InvalidArgumentException;
use ApiGateway\Exception\RuntimeException;
use MongoDB\Collection;
use Laminas\Http;

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
     * @var Collection
     */
    protected $userAgentCollection;

    /**
     * Client constructor.
     * @param Collection $userAgentCollection
     */
    public function __construct() // Collection $userAgentCollection)
    {
        $this->cache = new NullStorage([]);
    }

    public function deleteUser(ApiUser $apiUser)
    {
        $client = $this->getClient('users/' . $apiUser->id);
        $client->setMethod('DELETE');

        $client->send();
    }

    /**
     * returns prepared http client
     *
     * @param $resource
     * @return Http\Client
     */
    protected function getClient($resource): Http\Client
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

        $client = new Http\Client(rtrim($this->endpoint, '/') . '/' . $resource);

        $headers = $client->getRequest()->getHeaders();
        $headers->addHeaderLine('X-Admin-Auth-Token', $this->adminAuthToken);
        $headers->addHeaderLine('X-API-KEY', $this->key);

        return $client;
    }

    public function getOrAddUser(ApiUser $apiUser): ApiUser
    {
        foreach (array('firstName', 'lastName', 'email', 'roles') as $key) {
            if (empty($apiUser->{$key})) {
                throw new InvalidArgumentException('Key ' . $key . ' is missing');
            }
        }

        $user = $this->getUserByEmail($apiUser->email);

        if ($user === null) {
            $user = $this->addUser($apiUser);
        }

        return $user;
    }

    /**
     * gets user information by api gateway's internal user email address.
     *
     * @param string $value
     *
     * @return ApiUser|null
     */
    public function getUserByEmail(string $value)
    {
        if ($apiUser = $this->cache->get($value)) {
            return $apiUser;
        }

        $client = $this->getClient('users/?search[value]=' . $value);
        $client->setMethod('GET');

        $response = $client->send();

        if (!$response = json_decode($response->getBody())) {
            throw new RuntimeException('invalid api gateway response');
        }

        if (!isset($response->data)) {
            throw new RuntimeException('invalid api gateway response. does not contain user information');
        }

        foreach ($response->data as $user) {
            if ($user->email == $value) {
                return $this->getUserById($user->id);
            }
        }

        return null;
    }

    /**
     * gets user information by api gateway's internal user id.
     * an api key is unique
     *
     * @param string $apiUserId
     *
     * @return ApiUser
     */
    public function getUserById(string $apiUserId): ApiUser
    {
        if ($user = $this->cache->get($apiUserId)) {
            return $user;
        }

        $client = $this->getClient('users/' . $apiUserId);
        $client->setMethod('GET');

        $response = $client->send();

        if (!$jsonObject = json_decode($response->getBody())) {
            throw new RuntimeException('invalid api gateway response');
        }

        $user = new ApiUser();
        $user->parse($jsonObject);

        $this->cache->set($user);

        return $user;
    }

    public function addUser(ApiUser $apiUser)
    {
        $body = array(
            'user' => array(
                'email' => $apiUser->email,
                'first_name' => $apiUser->firstName,
                'last_name' =>$apiUser->lastName,
                'terms_and_conditions' => true,
                'enabled' => true,
                'registration_source' => __CLASS__,
                'roles' => $apiUser->roles,
            ),
        );

        $client = $this->getClient('users');
        $client->setMethod('POST');
        $client->setRawBody(json_encode($body));

        $accept = new Http\Header\Accept();
        $accept->addMediaType('application/json');

        $client->getRequest()->getHeaders()->addHeader(new Http\Header\ContentType('application/json'));
        $client->getRequest()->getHeaders()->addHeader($accept);

        $response = $client->send();

        if (!$response->isSuccess() || ($jsonObject = @json_decode($response->getBody())) === null) {
            throw new RuntimeException('invalid api gateway response');
        }

        $user = new ApiUser();
        $user->parse($jsonObject);

        $this->cache->get($user->id);
        $this->cache->set($user);

        return $user;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     *
     * @return Client
     */
    public function setEndpoint(string $endpoint): Client
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdminAuthToken(): string
    {
        return $this->adminAuthToken;
    }

    /**
     * @param string $adminAuthToken
     *
     * @return Client
     */
    public function setAdminAuthToken(string $adminAuthToken): Client
    {
        $this->adminAuthToken = $adminAuthToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Client
     */
    public function setKey(string $key): Client
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getCache(): StorageInterface
    {
        return $this->cache;
    }

    /**
     * @param StorageInterface $cache
     *
     * @return Client
     */
    public function setCache(StorageInterface $cache): Client
    {
        $this->cache = $cache;

        return $this;
    }
}
