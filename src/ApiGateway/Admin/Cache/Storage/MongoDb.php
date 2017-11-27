<?php

namespace ApiGateway\Admin\Cache\Storage;

use ApiGateway\Admin\Entity\ApiUser;
use MongoDB\Client;
use MongoDB\Collection;

class MongoDb implements StorageInterface
{
	protected $client;

	protected $collection;

	public function __construct(Client $client, Collection $collection)
	{
		$this->client = $client;
		$this->collection = $collection;
	}

	public function set(ApiUser $apiUser)
    {
		$this->collection->insertOne([
			'_id' => $apiUser->getId(),
			'userId' => $apiUser->getUserId(),
			'agentId' => $apiUser->getApiKey(),
		]);
	}

	public function get(string $id)
    {
		if ($result = $this->collection->findOne(['_id' => $id])) {
			$apiUser = new ApiUser();
			$apiUser->setId($result->_id);
			$apiUser->setUserId($result->userId);
			$apiUser->setApiKey($result->agentId);

			return $apiUser;
		}

		return false;
	}

    public function remove(ApiUser $apiUser)
    {
        return $this->collection->deleteOne(['_id' => $apiUser->getId()]);
    }
}